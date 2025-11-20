<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Cleaning up user_id column from orders table...\n\n";

try {
    // Drop foreign key
    DB::statement('ALTER TABLE orders DROP FOREIGN KEY IF EXISTS orders_user_id_foreign');
    echo "✓ Foreign key dropped (if existed)\n";
} catch (Exception $e) {
    echo "⚠ Foreign key: " . $e->getMessage() . "\n";
}

try {
    // Drop index
    DB::statement('ALTER TABLE orders DROP INDEX IF EXISTS orders_user_id_index');
    echo "✓ Index dropped (if existed)\n";
} catch (Exception $e) {
    echo "⚠ Index: " . $e->getMessage() . "\n";
}

try {
    // Drop column
    if (Schema::hasColumn('orders', 'user_id')) {
        Schema::table('orders', function ($table) {
            $table->dropColumn('user_id');
        });
        echo "✓ Column user_id dropped\n";
    } else {
        echo "⚠ Column user_id doesn't exist\n";
    }
} catch (Exception $e) {
    echo "✗ Error dropping column: " . $e->getMessage() . "\n";
}

echo "\nCleanup complete!\n";
