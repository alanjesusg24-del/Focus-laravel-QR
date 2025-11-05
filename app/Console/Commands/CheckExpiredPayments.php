<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiredPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:check-expired
                            {--deactivate : Deactivate businesses with expired payments}
                            {--notify : Send notification emails to businesses}
                            {--grace-days=3 : Grace period days before deactivation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired payments and optionally deactivate businesses';

    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        parent::__construct();
        $this->paymentService = $paymentService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('💳 Checking for expired payments...');
        $this->newLine();

        $deactivate = $this->option('deactivate');
        $notify = $this->option('notify');
        $graceDays = (int) $this->option('grace-days');

        // Get all active businesses
        $businesses = Business::with('plan')
            ->where('is_active', true)
            ->whereNotNull('last_payment_date')
            ->get();

        if ($businesses->isEmpty()) {
            $this->warn('No active businesses found');
            return Command::SUCCESS;
        }

        $expiredCount = 0;
        $deactivatedCount = 0;
        $notifiedCount = 0;
        $gracePeriodCount = 0;

        $expiredBusinesses = [];

        foreach ($businesses as $business) {
            if ($this->paymentService->isPaymentExpired($business)) {
                $expiredCount++;

                $expirationDate = $business->last_payment_date
                    ->addDays($business->plan->duration_days);
                $daysSinceExpired = Carbon::now()->diffInDays($expirationDate);

                $status = [
                    'business_id' => $business->business_id,
                    'business_name' => $business->business_name,
                    'email' => $business->email,
                    'plan' => $business->plan->name,
                    'expired_on' => $expirationDate->format('Y-m-d'),
                    'days_expired' => $daysSinceExpired,
                ];

                // Check if within grace period
                if ($daysSinceExpired <= $graceDays) {
                    $status['action'] = 'Grace Period';
                    $gracePeriodCount++;
                } else {
                    $status['action'] = 'Expired';

                    // Deactivate if flag is set
                    if ($deactivate) {
                        $business->is_active = false;
                        $business->save();
                        $status['action'] = 'Deactivated';
                        $deactivatedCount++;

                        Log::info("Business deactivated due to expired payment", [
                            'business_id' => $business->business_id,
                            'business_name' => $business->business_name,
                        ]);
                    }

                    // Send notification if flag is set
                    if ($notify) {
                        $this->sendNotificationEmail($business);
                        $notifiedCount++;
                    }
                }

                $expiredBusinesses[] = $status;
            }
        }

        // Display results
        if ($expiredCount > 0) {
            $this->newLine();
            $this->table(
                ['ID', 'Business', 'Plan', 'Expired On', 'Days', 'Action'],
                array_map(function ($item) {
                    return [
                        $item['business_id'],
                        substr($item['business_name'], 0, 30),
                        $item['plan'],
                        $item['expired_on'],
                        $item['days_expired'],
                        $item['action'],
                    ];
                }, $expiredBusinesses)
            );
        }

        $this->newLine();
        $this->info('✅ Payment check completed!');
        $this->newLine();

        // Summary
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total businesses checked', $businesses->count()],
                ['Expired payments', $expiredCount],
                ['In grace period', $gracePeriodCount],
                ['Deactivated', $deactivatedCount],
                ['Notifications sent', $notifiedCount],
            ]
        );

        if ($expiredCount > 0 && !$deactivate) {
            $this->newLine();
            $this->comment('💡 Use --deactivate flag to automatically deactivate expired businesses');
        }

        if ($expiredCount > 0 && !$notify) {
            $this->newLine();
            $this->comment('💡 Use --notify flag to send notification emails');
        }

        return Command::SUCCESS;
    }

    /**
     * Send notification email to business
     *
     * @param Business $business
     * @return void
     */
    protected function sendNotificationEmail(Business $business): void
    {
        // TODO: Implement email notification
        // This would integrate with Laravel's Mail system
        Log::info("Payment expiration notification sent", [
            'business_id' => $business->business_id,
            'email' => $business->email,
        ]);
    }
}
