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

// Ensure SQLite database file exists in /tmp if sqlite is used
if (getenv('DB_CONNECTION') === 'sqlite' || (!getenv('DB_CONNECTION') && getenv('DB_DATABASE') === '/tmp/database.sqlite')) {
    if (!file_exists('/tmp/database.sqlite')) {
        $localDb = __DIR__ . '/../database/database.sqlite';
        if (file_exists($localDb) && filesize($localDb) > 0) {
            @copy($localDb, '/tmp/database.sqlite');
        } else {
            @touch('/tmp/database.sqlite');
        }
    }
}

// Set SCRIPT_NAME to /index.php so Laravel properly routes root and sub-paths
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Forward execution to Laravel's main front controller
require __DIR__ . '/../public/index.php';
