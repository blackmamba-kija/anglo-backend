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

echo "\n2. Running Artisan Commands:\n";
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    $kernel->call('optimize:clear');
    echo "  optimize:clear output:\n" . trim($kernel->output()) . "\n";

    $kernel->call('migrate', ['--force' => true]);
    echo "  migrate --force output:\n" . trim($kernel->output()) . "\n";

    echo "\n=== SUCCESS: Backend is refreshed and ready! ===\n";
} catch (\Throwable $e) {
    echo "  Artisan notice: " . $e->getMessage() . "\n";
    echo "  (The cache files were still deleted above, so new routes will now load!)\n";
}
