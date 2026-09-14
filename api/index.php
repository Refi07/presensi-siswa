<?php

// Set lokasi penyimpanan temporer wajib di Vercel (folder /tmp satu-satunya yang bisa ditulis)
$_ENV['APP_STORAGE'] = '/tmp';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';

// Buat folder /tmp jika belum ada
$directories = [
    '/tmp/views',
    '/tmp/sessions',
    '/tmp/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

require __DIR__ . '/../public/index.php';