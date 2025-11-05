<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:clean-expired
                            {--dry-run : Run without actually deleting orders}
                            {--business= : Clean orders for specific business ID}
                            {--days= : Override retention days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired orders based on business plan retention days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗑️  Starting order cleanup process...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $specificBusinessId = $this->option('business');
        $overrideDays = $this->option('days');

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No orders will be deleted');
            $this->newLine();
        }

        // Get businesses to process
        $businesses = $this->getBusinessesToProcess($specificBusinessId);

        if ($businesses->isEmpty()) {
            $this->error('No businesses found to process');
            return Command::FAILURE;
        }

        $totalDeleted = 0;
        $totalBusinesses = $businesses->count();

        $progressBar = $this->output->createProgressBar($totalBusinesses);
        $progressBar->start();

        foreach ($businesses as $business) {
            $retentionDays = $overrideDays ?? $business->plan->retention_days;
            $cutoffDate = Carbon::now()->subDays($retentionDays);

            $query = Order::forBusiness($business->business_id)
                ->where('created_at', '<', $cutoffDate)
                ->whereIn('status', ['delivered', 'cancelled']);

            $count = $query->count();

            if ($count > 0) {
                if (!$dryRun) {
                    $deleted = $query->delete();
                    $totalDeleted += $deleted;
                } else {
                    $totalDeleted += $count;
                }

                $this->newLine();
                $this->line("  📦 {$business->business_name} (ID: {$business->business_id})");
                $this->line("     Retention: {$retentionDays} days | Cutoff: {$cutoffDate->format('Y-m-d')}");
                $this->line("     Orders " . ($dryRun ? 'to delete' : 'deleted') . ": {$count}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('✅ Cleanup process completed!');
        $this->newLine();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Businesses processed', $totalBusinesses],
                ['Orders ' . ($dryRun ? 'to delete' : 'deleted'), $totalDeleted],
                ['Mode', $dryRun ? 'DRY RUN' : 'LIVE'],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->comment('💡 Run without --dry-run flag to actually delete orders');
        }

        return Command::SUCCESS;
    }

    /**
     * Get businesses to process based on options
     *
     * @param int|null $specificBusinessId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getBusinessesToProcess(?int $specificBusinessId)
    {
        $query = Business::with('plan')->where('is_active', true);

        if ($specificBusinessId) {
            $query->where('business_id', $specificBusinessId);
        }

        return $query->get();
    }
}
