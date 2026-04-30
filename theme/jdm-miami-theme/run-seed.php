<?php

// 🔐 simple protection (change this key)
$secret = 'jdm123';

if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    http_response_code(403);
    die('Forbidden');
}

// Load WordPress
require_once dirname(__DIR__, 3) . '/wp-load.php';

// Optional: only allow admins if logged in
if (!current_user_can('manage_options')) {
    die('Not authorized');
}

// Run seed
require_once __DIR__ . '/seed_data.php';

echo "<h2>✅ Seeding complete.</h2>";