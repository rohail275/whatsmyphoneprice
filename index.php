<?php
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'WhatsMyPhonePrice.com — Free Used Phone Valuation in Pakistan';
$pageDescription = "Get a free, instant estimated resale value for your used phone in Pakistan. No forced trade-in — optionally list it for sale directly to real buyers.";

$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => 'WhatsMyPhonePrice.com',
    'url' => SITE_URL,
];
$schemaJsonLd = json_encode($schema);

require_once __DIR__ . '/includes/header.php';

$popularPhones = array_slice(get_all_phones(), 0, 8);
?>

<section class="card" style="text-align:center">
    <h1><?= e(t('site_tagline')) ?></h1>
    <p>Free · Instant · No obligation to sell to us. Ever.</p>
    <a class="btn" href="/valuation.php"><?= e(t('cta_start_valuation')) ?></a>
</section>

<section>
    <h2>Popular Phones</h2>
    <div class="phone-grid">
        <?php foreach ($popularPhones as $p): ?>
            <a class="card phone-card" href="/phone/<?= e($p['slug']) ?>">
                <strong><?= e($p['brand'] . ' ' . $p['model']) ?></strong>
                <p class="hint"><?= e($p['variant']) ?></p>
                <p class="price">Rs <?= number_format((float) $p['base_price_pkr']) ?> new</p>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="card">
    <h2>Why WhatsMyPhonePrice.com?</h2>
    <ul class="checklist">
        <li>Real market-based pricing, aggregated from multiple sources — not a lowball trade-in offer.</li>
        <li>PTA status weighted heavily — the biggest factor in Pakistani resale value.</li>
        <li>Optional peer-to-peer listing — you deal directly with the buyer, we're never the middleman.</li>
        <li>Condition-locked listings, IMEI/PTA checks, and iCloud/FRP scam warnings built in.</li>
    </ul>
</section>

<section class="card" style="text-align:center">
    <p>Ready to sell? Browse what other people in Pakistan are already listing.</p>
    <a class="btn btn-secondary" href="/listings.php"><?= e(t('nav_listings')) ?></a>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
