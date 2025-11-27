<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\OrderRealert;
use App\Services\PushNotificationService;
use Carbon\Carbon;

class SendOrderRealerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:send-realerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send re-alert notifications for ready orders that have not been picked up';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting re-alerts process...');

        $now = Carbon::now();
        $sentCount = 0;
        $skippedCount = 0;

        // Get all ready orders that are linked to a mobile user
        $readyOrders = Order::where('status', 'ready')
            ->whereNotNull('mobile_user_id')
            ->whereNotNull('ready_at')
            ->with(['business.plan', 'mobileUser', 'realerts'])
            ->get();

        $this->info("Found {$readyOrders->count()} ready orders");

        foreach ($readyOrders as $order) {
            // Check if business plan has realerts enabled
            $plan = $order->business->plan;

            if (!$plan || !$plan->has_realerts) {
                $skippedCount++;
                continue;
            }

            // Get realert configuration from plan
            $intervalMinutes = $plan->realert_interval_minutes ?? 15;
            $maxAlerts = $plan->realert_max_count ?? 4;

            // Count how many realerts have been sent for this order
            $alertsSent = $order->realerts()->count();

            if ($alertsSent >= $maxAlerts) {
                // Max alerts reached
                $skippedCount++;
                continue;
            }

            // Get the last realert sent
            $lastRealert = $order->realerts()->latest('sent_at')->first();

            // Calculate when the next alert should be sent
            $nextAlertTime = $lastRealert
                ? $lastRealert->sent_at->addMinutes($intervalMinutes)
                : $order->ready_at->addMinutes($intervalMinutes);

            // Check if it's time to send the next alert
            if ($now->greaterThanOrEqualTo($nextAlertTime)) {
                // Send the notification
                $mobileUser = $order->mobileUser;

                if (!$mobileUser || !$mobileUser->fcm_token) {
                    $skippedCount++;
                    continue;
                }

                try {
                    // Calculate next alert number
                    $nextAlertNumber = $alertsSent + 1;

                    // Send push notification
                    $result = PushNotificationService::sendReadyReminder(
                        $mobileUser->fcm_token,
                        $order,
                        $nextAlertNumber
                    );

                    // Record the realert
                    OrderRealert::create([
                        'order_id' => $order->order_id,
                        'alert_number' => $nextAlertNumber,
                        'sent_at' => $now,
                        'notification_type' => 'ready_reminder',
                        'was_delivered' => $result['success'] ?? true,
                        'response_message' => $result['message'] ?? 'Sent successfully',
                    ]);

                    $sentCount++;
                    $this->line("✓ Sent realert #{$nextAlertNumber} for order {$order->folio_number}");

                } catch (\Exception $e) {
                    $nextAlertNumber = $alertsSent + 1;
                    $this->error("✗ Failed to send realert for order {$order->folio_number}: {$e->getMessage()}");

                    // Record failed realert
                    OrderRealert::create([
                        'order_id' => $order->order_id,
                        'alert_number' => $nextAlertNumber,
                        'sent_at' => $now,
                        'notification_type' => 'ready_reminder',
                        'was_delivered' => false,
                        'response_message' => $e->getMessage(),
                    ]);
                }
            } else {
                $skippedCount++;
            }
        }

        $this->info("\nRe-alerts summary:");
        $this->info("✓ Sent: {$sentCount}");
        $this->info("⊘ Skipped: {$skippedCount}");
        $this->info('Done!');

        return Command::SUCCESS;
    }
}
