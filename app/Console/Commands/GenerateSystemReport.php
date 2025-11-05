<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateSystemReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:report
                            {--period=30 : Report period in days}
                            {--export= : Export to file (csv, json, txt)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate comprehensive system usage and statistics report';

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
        $this->info('📊 Generating System Report...');
        $this->newLine();

        $period = (int) $this->option('period');
        $export = $this->option('export');
        $startDate = Carbon::now()->subDays($period);

        // Collect statistics
        $report = [
            'generated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'period_days' => $period,
            'period_start' => $startDate->format('Y-m-d'),
            'period_end' => Carbon::now()->format('Y-m-d'),
            'businesses' => $this->getBusinessStatistics($startDate),
            'orders' => $this->getOrderStatistics($startDate),
            'payments' => $this->getPaymentStatistics($startDate),
            'system' => $this->getSystemStatistics(),
        ];

        // Display report
        $this->displayReport($report);

        // Export if requested
        if ($export) {
            $this->exportReport($report, $export);
        }

        return Command::SUCCESS;
    }

    /**
     * Get business statistics
     */
    protected function getBusinessStatistics(Carbon $startDate): array
    {
        $total = Business::count();
        $active = Business::where('is_active', true)->count();
        $inactive = $total - $active;
        $newInPeriod = Business::where('registration_date', '>=', $startDate)->count();

        $byPlan = Business::with('plan')
            ->get()
            ->groupBy('plan.name')
            ->map(fn($group) => $group->count())
            ->toArray();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'new_in_period' => $newInPeriod,
            'by_plan' => $byPlan,
        ];
    }

    /**
     * Get order statistics
     */
    protected function getOrderStatistics(Carbon $startDate): array
    {
        $total = Order::count();
        $inPeriod = Order::where('created_at', '>=', $startDate)->count();

        $byStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $avgDeliveryTime = Order::whereNotNull('delivered_at')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, delivered_at)) as avg_minutes')
            ->value('avg_minutes');

        return [
            'total' => $total,
            'in_period' => $inPeriod,
            'by_status' => $byStatus,
            'avg_delivery_time_minutes' => round($avgDeliveryTime ?? 0, 2),
        ];
    }

    /**
     * Get payment statistics
     */
    protected function getPaymentStatistics(Carbon $startDate): array
    {
        $total = Payment::count();
        $inPeriod = Payment::where('payment_date', '>=', $startDate)->count();

        $revenue = Payment::where('status', 'completed')
            ->sum('amount');

        $revenueInPeriod = Payment::where('status', 'completed')
            ->where('payment_date', '>=', $startDate)
            ->sum('amount');

        $byStatus = Payment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'total' => $total,
            'in_period' => $inPeriod,
            'total_revenue' => round($revenue, 2),
            'period_revenue' => round($revenueInPeriod, 2),
            'by_status' => $byStatus,
        ];
    }

    /**
     * Get system statistics
     */
    protected function getSystemStatistics(): array
    {
        return [
            'database_size_mb' => $this->getDatabaseSize(),
            'active_sessions' => 0, // TODO: Implement session tracking
            'uptime_days' => 0, // TODO: Implement uptime tracking
        ];
    }

    /**
     * Get database size in MB
     */
    protected function getDatabaseSize(): float
    {
        try {
            $database = config('database.connections.mysql.database');
            $result = DB::select("
                SELECT
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables
                WHERE table_schema = ?
            ", [$database]);

            return $result[0]->size_mb ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Display report in console
     */
    protected function displayReport(array $report): void
    {
        $this->info("📅 Report Period: {$report['period_start']} to {$report['period_end']} ({$report['period_days']} days)");
        $this->newLine();

        // Businesses
        $this->line('👥 <fg=cyan>BUSINESSES</>');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Businesses', $report['businesses']['total']],
                ['Active', $report['businesses']['active']],
                ['Inactive', $report['businesses']['inactive']],
                ['New in Period', $report['businesses']['new_in_period']],
            ]
        );

        if (!empty($report['businesses']['by_plan'])) {
            $this->line('Plans Distribution:');
            foreach ($report['businesses']['by_plan'] as $plan => $count) {
                $this->line("  • {$plan}: {$count}");
            }
            $this->newLine();
        }

        // Orders
        $this->line('📦 <fg=cyan>ORDERS</>');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Orders', $report['orders']['total']],
                ['Orders in Period', $report['orders']['in_period']],
                ['Avg Delivery Time', $report['orders']['avg_delivery_time_minutes'] . ' min'],
            ]
        );

        if (!empty($report['orders']['by_status'])) {
            $this->line('Orders by Status:');
            foreach ($report['orders']['by_status'] as $status => $count) {
                $this->line("  • " . ucfirst($status) . ": {$count}");
            }
            $this->newLine();
        }

        // Payments
        $this->line('💰 <fg=cyan>PAYMENTS</>');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Payments', $report['payments']['total']],
                ['Payments in Period', $report['payments']['in_period']],
                ['Total Revenue', '$' . number_format($report['payments']['total_revenue'], 2) . ' MXN'],
                ['Period Revenue', '$' . number_format($report['payments']['period_revenue'], 2) . ' MXN'],
            ]
        );

        if (!empty($report['payments']['by_status'])) {
            $this->line('Payments by Status:');
            foreach ($report['payments']['by_status'] as $status => $count) {
                $this->line("  • " . ucfirst($status) . ": {$count}");
            }
            $this->newLine();
        }

        // System
        $this->line('⚙️  <fg=cyan>SYSTEM</>');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Database Size', $report['system']['database_size_mb'] . ' MB'],
            ]
        );

        $this->newLine();
        $this->info('✅ Report generation completed!');
    }

    /**
     * Export report to file
     */
    protected function exportReport(array $report, string $format): void
    {
        $filename = 'system_report_' . Carbon::now()->format('Y-m-d_His') . '.' . $format;
        $path = storage_path('app/reports/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        switch ($format) {
            case 'json':
                file_put_contents($path, json_encode($report, JSON_PRETTY_PRINT));
                break;

            case 'csv':
                $this->exportToCsv($report, $path);
                break;

            case 'txt':
                $this->exportToTxt($report, $path);
                break;

            default:
                $this->error("Unsupported export format: {$format}");
                return;
        }

        $this->newLine();
        $this->info("📄 Report exported to: {$path}");
    }

    /**
     * Export report to CSV
     */
    protected function exportToCsv(array $report, string $path): void
    {
        $csv = fopen($path, 'w');

        fputcsv($csv, ['Order QR System - Report']);
        fputcsv($csv, ['Generated', $report['generated_at']]);
        fputcsv($csv, ['Period', "{$report['period_start']} to {$report['period_end']}"]);
        fputcsv($csv, []);

        fputcsv($csv, ['BUSINESSES']);
        foreach ($report['businesses'] as $key => $value) {
            if (!is_array($value)) {
                fputcsv($csv, [ucfirst(str_replace('_', ' ', $key)), $value]);
            }
        }
        fputcsv($csv, []);

        fputcsv($csv, ['ORDERS']);
        foreach ($report['orders'] as $key => $value) {
            if (!is_array($value)) {
                fputcsv($csv, [ucfirst(str_replace('_', ' ', $key)), $value]);
            }
        }
        fputcsv($csv, []);

        fputcsv($csv, ['PAYMENTS']);
        foreach ($report['payments'] as $key => $value) {
            if (!is_array($value)) {
                fputcsv($csv, [ucfirst(str_replace('_', ' ', $key)), $value]);
            }
        }

        fclose($csv);
    }

    /**
     * Export report to TXT
     */
    protected function exportToTxt(array $report, string $path): void
    {
        $content = "ORDER QR SYSTEM - SYSTEM REPORT\n";
        $content .= str_repeat('=', 50) . "\n\n";
        $content .= "Generated: {$report['generated_at']}\n";
        $content .= "Period: {$report['period_start']} to {$report['period_end']}\n\n";

        $content .= "BUSINESSES\n" . str_repeat('-', 50) . "\n";
        foreach ($report['businesses'] as $key => $value) {
            if (!is_array($value)) {
                $content .= ucfirst(str_replace('_', ' ', $key)) . ": {$value}\n";
            }
        }

        $content .= "\nORDERS\n" . str_repeat('-', 50) . "\n";
        foreach ($report['orders'] as $key => $value) {
            if (!is_array($value)) {
                $content .= ucfirst(str_replace('_', ' ', $key)) . ": {$value}\n";
            }
        }

        $content .= "\nPAYMENTS\n" . str_repeat('-', 50) . "\n";
        foreach ($report['payments'] as $key => $value) {
            if (!is_array($value)) {
                $content .= ucfirst(str_replace('_', ' ', $key)) . ": {$value}\n";
            }
        }

        file_put_contents($path, $content);
    }
}
