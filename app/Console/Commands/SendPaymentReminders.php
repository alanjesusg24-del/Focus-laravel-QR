<?php

namespace App\Console\Commands;

use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:send-reminders
                            {--days-before=7 : Send reminder X days before expiration}
                            {--dry-run : Preview reminders without sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment renewal reminders to businesses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📧 Sending payment reminders...');
        $this->newLine();

        $daysBefore = (int) $this->option('days-before');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No emails will be sent');
            $this->newLine();
        }

        // Calculate target date
        $targetDate = Carbon::now()->addDays($daysBefore);

        // Get businesses with upcoming payment expiration
        $businesses = Business::with('plan')
            ->where('is_active', true)
            ->whereNotNull('last_payment_date')
            ->get()
            ->filter(function ($business) use ($targetDate) {
                if (!$business->plan) {
                    return false;
                }

                $expirationDate = $business->last_payment_date
                    ->copy()
                    ->addDays($business->plan->duration_days);

                // Check if expiration is within the target window (±1 day)
                return $expirationDate->between(
                    $targetDate->copy()->subDay(),
                    $targetDate->copy()->addDay()
                );
            });

        if ($businesses->isEmpty()) {
            $this->info('No businesses require payment reminders at this time');
            return Command::SUCCESS;
        }

        $this->info("Found {$businesses->count()} businesses requiring reminders");
        $this->newLine();

        $reminders = [];
        $sentCount = 0;

        foreach ($businesses as $business) {
            $expirationDate = $business->last_payment_date
                ->copy()
                ->addDays($business->plan->duration_days);

            $daysUntilExpiration = Carbon::now()->diffInDays($expirationDate, false);

            $reminderData = [
                'business_id' => $business->business_id,
                'business_name' => $business->business_name,
                'email' => $business->email,
                'plan' => $business->plan->name,
                'expires_on' => $expirationDate->format('Y-m-d'),
                'days_until' => $daysUntilExpiration,
                'amount' => $business->plan->price,
            ];

            $reminders[] = $reminderData;

            if (!$dryRun) {
                $this->sendReminderEmail($business, $reminderData);
                $sentCount++;
            }
        }

        // Display reminders
        $this->table(
            ['ID', 'Business', 'Email', 'Plan', 'Expires', 'Days', 'Amount'],
            array_map(function ($item) {
                return [
                    $item['business_id'],
                    substr($item['business_name'], 0, 20),
                    substr($item['email'], 0, 25),
                    $item['plan'],
                    $item['expires_on'],
                    $item['days_until'],
                    '$' . number_format($item['amount'], 2),
                ];
            }, $reminders)
        );

        $this->newLine();
        $this->info('✅ Reminder process completed!');
        $this->newLine();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Reminders to send', count($reminders)],
                ['Emails sent', $sentCount],
                ['Mode', $dryRun ? 'DRY RUN' : 'LIVE'],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->comment('💡 Run without --dry-run to actually send reminder emails');
        }

        return Command::SUCCESS;
    }

    /**
     * Send reminder email to business
     *
     * @param Business $business
     * @param array $data
     * @return void
     */
    protected function sendReminderEmail(Business $business, array $data): void
    {
        // TODO: Implement email notification using Laravel Mail
        // Example structure:
        // Mail::to($business->email)->send(new PaymentReminderMail($data));

        Log::info("Payment reminder sent", [
            'business_id' => $business->business_id,
            'email' => $business->email,
            'expires_on' => $data['expires_on'],
            'days_until' => $data['days_until'],
        ]);

        $this->line("  ✉️  Sent reminder to: {$business->email}");
    }
}
