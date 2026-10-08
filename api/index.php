<?php

// Vercel serverless environment operates with a read-only filesystem except for /tmp.
// Ensure all necessary storage, cache, and view directories exist inside /tmp:
$storageDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/testing',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/storage/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Ensure application key exists so Laravel does not fail with MissingAppKeyException
if (!getenv('APP_KEY') && empty($_SERVER['APP_KEY']) && empty($_ENV['APP_KEY'])) {
    $fallbackKey = 'base64:/Kcg3RB4SF42k+7HS+2QbEeUx4m1oow79juF6m3ydvg=';
    putenv("APP_KEY={$fallbackKey}");
    $_ENV['APP_KEY'] = $fallbackKey;
    $_SERVER['APP_KEY'] = $fallbackKey;
}

// Ensure SQLite database file exists in /tmp with seed data
$tmpDb = '/tmp/database.sqlite';
if (!file_exists($tmpDb) || filesize($tmpDb) === 0) {
    $seedDb = __DIR__ . '/../database/seed_database.sqlite';
    $localDb = __DIR__ . '/../database/database.sqlite';
    if (file_exists($seedDb) && filesize($seedDb) > 0) {
        @copy($seedDb, $tmpDb);
    } elseif (file_exists($localDb) && filesize($localDb) > 0) {
        @copy($localDb, $tmpDb);
    } else {
        @touch($tmpDb);
    }
}

// Fallback to SQLite in /tmp if no external database is configured
if (!getenv('DB_CONNECTION') && empty($_SERVER['DB_CONNECTION'])) {
    putenv('DB_CONNECTION=sqlite');
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_CONNECTION'] = 'sqlite';
}
if ((getenv('DB_CONNECTION') === 'sqlite' || $_SERVER['DB_CONNECTION'] === 'sqlite') && (!getenv('DB_DATABASE') || getenv('DB_DATABASE') === 'database/database.sqlite')) {
    putenv('DB_DATABASE=/tmp/database.sqlite');
    $_ENV['DB_DATABASE'] = '/tmp/database.sqlite';
    $_SERVER['DB_DATABASE'] = '/tmp/database.sqlite';
}

// Ensure HTTPS protocol detection for Vercel reverse proxy
if (getenv('VERCEL') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

// Fallback ASSET_URL to root-relative path to prevent protocol-mismatched assets
if (!getenv('ASSET_URL') && empty($_SERVER['ASSET_URL'])) {
    putenv('ASSET_URL=/');
    $_ENV['ASSET_URL'] = '/';
    $_SERVER['ASSET_URL'] = '/';
}

// Set SCRIPT_NAME to /index.php so Laravel properly routes root and sub-paths
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Forward execution to Laravel's main front controller
require __DIR__ . '/../public/index.php';
