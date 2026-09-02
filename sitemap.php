<?php
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/xml; charset=utf-8');

$staticPages = ['/index.php', '/valuation.php', '/listings.php', '/imei-check.php', '/icloud-frp-check.php', '/safety.php', '/faq.php'];
$phones = get_db()->query('SELECT slug, updated_at FROM phones WHERE is_active = 1')->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($staticPages as $path) {
    echo '  <url><loc>' . htmlspecialchars(SITE_URL . $path, ENT_QUOTES, 'UTF-8') . '</loc></url>' . "\n";
}
foreach ($phones as $phone) {
    echo '  <url><loc>' . htmlspecialchars(SITE_URL . '/phone/' . $phone['slug'], ENT_QUOTES, 'UTF-8') . '</loc>'
        . '<lastmod>' . date('Y-m-d', strtotime($phone['updated_at'])) . '</lastmod></url>' . "\n";
}

echo '</urlset>';
