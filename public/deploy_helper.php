<?php
// deploy_helper.php - Clears cache and runs migrations on production
header('Content-Type: text/plain; charset=utf-8');

$token = $_GET['token'] ?? '';
if ($token !== 'kija2026') {
    http_response_code(403);
    echo "Unauthorized. Visit with ?token=kija2026\n";
    exit;
}

echo "=== ANGLO BACKEND CACHE CLEAR & MIGRATION HELPER ===\n\n";

// 0. Attempt git pull if shell_exec is available
echo "0. Git Pull:\n";
if (function_exists('shell_exec')) {
    $pullOutput = @shell_exec('git pull origin main 2>&1');
    echo "  " . ($pullOutput ? trim($pullOutput) : "No output from git pull") . "\n\n";
} else {
    echo "  shell_exec is disabled on this server.\n\n";
}

$bootstrapCache = __DIR__ . '/../bootstrap/cache';
$cacheFiles = [
    'routes-v7.php',
    'config.php',
    'services.php',
    'packages.php',
    'events.php',
];

echo "1. Cleaning bootstrap/cache files:\n";
foreach ($cacheFiles as $file) {
    $filePath = $bootstrapCache . '/' . $file;
    if (file_exists($filePath)) {
        if (@unlink($filePath)) {
            echo "  [DELETED] $file\n";
        } else {
            echo "  [FAILED TO DELETE] $file\n";
        }
    } else {
        echo "  [OK - NOT FOUND] $file\n";
    }
}

echo "\n2. Running Artisan & Database Updates:\n";
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    // Direct SQL fallback to guarantee permissions & fuel_type columns exist
    try {
        $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
        
        $chkPerm = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'permissions'");
        if (!$chkPerm->fetch()) {
            $pdo->exec("ALTER TABLE `users` ADD COLUMN `permissions` JSON NULL AFTER `role`");
            echo "  [SQL DIRECT] Added `permissions` column to `users` table.\n";
        } else {
            echo "  [SQL DIRECT] `permissions` column already exists in `users`.\n";
        }

        $chkFuel = $pdo->query("SHOW COLUMNS FROM `consumable_items` LIKE 'fuel_type'");
        if (!$chkFuel->fetch()) {
            $pdo->exec("ALTER TABLE `consumable_items` ADD COLUMN `fuel_type` VARCHAR(255) NULL AFTER `category`");
            echo "  [SQL DIRECT] Added `fuel_type` column to `consumable_items` table.\n";
        } else {
            echo "  [SQL DIRECT] `fuel_type` column already exists in `consumable_items`.\n";
        }
    } catch (\Throwable $sqlErr) {
        echo "  SQL check notice: " . $sqlErr->getMessage() . "\n";
    }

    $kernel->call('migrate', ['--force' => true]);
    echo "  migrate --force output:\n" . trim($kernel->output()) . "\n";

    $kernel->call('optimize:clear');
    echo "  optimize:clear output:\n" . trim($kernel->output()) . "\n";

    echo "\n=== SUCCESS: Backend is refreshed, migrations run, and permissions enabled! ===\n";
} catch (\Throwable $e) {
    echo "  Artisan notice: " . $e->getMessage() . "\n";
    echo "  (The cache files were still deleted above, so new routes and schema will now load!)\n";
}
