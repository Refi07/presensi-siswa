<?php

// Arahkan folder temporer Laravel ke /tmp (wajib di Vercel)
putenv('VIEW_COMPILED_PATH=/tmp');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');

// Buat direktori temporer jika belum ada
$directories = [
    '/tmp/posts',
    '/tmp/sessions',
    '/tmp/views',
    '/tmp/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

require __DIR__ . '/../public/index.php';