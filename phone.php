<?php
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/includes/functions.php';

$slug = (string) ($_GET['slug'] ?? '');
$phone = $slug !== '' ? get_phone_by_slug($slug) : null;

if (!$phone) {
    http_response_code(404);
    $pageTitle = 'Phone not found — WhatsMyPhonePrice.com';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="card"><p>We don\'t have that model yet.</p><a class="btn" href="/valuation.php">Browse phones</a></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$name = $phone['brand'] . ' ' . $phone['model'] . ' ' . $phone['variant'];
$pageTitle = $name . ' Price in Pakistan — Resale Value Estimator | WhatsMyPhonePrice.com';
$pageDescription = "How much is a used {$name} worth in Pakistan? Get a free, instant estimated resale price based on condition, PTA status, and age.";

$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $name,
    'brand' => $phone['brand'],
    'offers' => [
        '@type' => 'AggregateOffer',
        'priceCurrency' => 'PKR',
        'lowPrice' => (float) $phone['base_price_pkr'] * 0.3,
        'highPrice' => (float) $phone['base_price_pkr'],
        'offerCount' => 1,
    ],
];
$schemaJsonLd = json_encode($schema);

require_once __DIR__ . '/includes/header.php';
?>

<h1><?= e($name) ?> — Used Phone Resale Value in Pakistan</h1>

<div class="card">
    <p>New price reference (avg. <?= e($phone['price_sources'] ?: 'PriceOye, WhatMobile') ?>): <strong>Rs <?= number_format((float) $phone['base_price_pkr']) ?></strong></p>
    <p class="hint">Released <?= (int) $phone['release_year'] ?>. Prices updated <?= $phone['price_updated_at'] ? date('d M Y', strtotime($phone['price_updated_at'])) : 'periodically' ?>.</p>
    <a class="btn" href="/valuation.php?phone_id=<?= (int) $phone['id'] ?>"><?= e(t('cta_start_valuation')) ?></a>
</div>

<div class="card">
    <h2>How resale value is estimated</h2>
    <p><?= $phone['brand'] === 'Apple' ? 'iPhones depreciate slower than Android phones in Pakistan — roughly 27–33% per year.' : 'Android phones like this one typically depreciate 58–70% per year.' ?>
       Condition (screen, battery, camera), repair history, and especially <strong>PTA approval status</strong> have a major effect on final resale price.</p>
    <a href="/faq.php">Read more in our FAQ</a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
