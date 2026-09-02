<?php
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/includes/functions.php';

$db = get_db();
$city = trim((string) ($_GET['city'] ?? ''));
$brand = trim((string) ($_GET['brand'] ?? ''));

$sql = 'SELECT l.id, l.title, l.asking_price_pkr, l.city, l.area, l.created_at,
               p.brand, p.model, p.variant, v.pta_status
        FROM listings l
        JOIN valuations v ON v.id = l.valuation_id
        JOIN phones p ON p.id = v.phone_id
        WHERE l.status = ?';
$params = ['active'];
if ($city !== '') {
    $sql .= ' AND l.city = ?';
    $params[] = $city;
}
if ($brand !== '') {
    $sql .= ' AND p.brand = ?';
    $params[] = $brand;
}
$sql .= ' ORDER BY l.created_at DESC LIMIT 60';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$listings = $stmt->fetchAll();

$cities = $db->query("SELECT DISTINCT city FROM listings WHERE status = 'active' ORDER BY city")->fetchAll(PDO::FETCH_COLUMN);
$brands = $db->query('SELECT DISTINCT brand FROM phones ORDER BY brand')->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = t('nav_listings') . ' — WhatsMyPhonePrice.com';
$pageDescription = 'Browse used phones for sale directly from real sellers in Pakistan, with condition and PTA status declared upfront.';
require_once __DIR__ . '/includes/header.php';
?>

<h1><?= e(t('nav_listings')) ?></h1>

<form class="filter-bar" method="get">
    <select name="city" onchange="this.form.submit()">
        <option value="">All cities</option>
        <?php foreach ($cities as $c): ?>
            <option value="<?= e($c) ?>" <?= $city === $c ? 'selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="brand" onchange="this.form.submit()">
        <option value="">All brands</option>
        <?php foreach ($brands as $b): ?>
            <option value="<?= e($b) ?>" <?= $brand === $b ? 'selected' : '' ?>><?= e($b) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<?php if (!$listings): ?>
    <div class="card"><p>No listings yet<?= $city ? ' in ' . e($city) : '' ?>. <a href="/valuation.php"><?= e(t('cta_start_valuation')) ?></a> and be the first to post one.</p></div>
<?php else: ?>
    <div class="listing-grid">
        <?php foreach ($listings as $l): ?>
            <a class="card phone-card" href="/listing.php?id=<?= (int) $l['id'] ?>">
                <strong><?= e($l['title']) ?></strong>
                <p class="hint"><?= e($l['brand'] . ' ' . $l['model'] . ' (' . $l['variant'] . ')') ?></p>
                <p class="price">Rs <?= number_format((float) $l['asking_price_pkr']) ?></p>
                <p class="hint"><?= e($l['city']) ?><?= $l['area'] ? ', ' . e($l['area']) : '' ?></p>
                <?php if ($l['pta_status'] !== 'approved'): ?>
                    <span class="badge <?= $l['pta_status'] === 'blocked' ? 'badge-blocked' : 'badge-non-pta' ?>"><?= e(t('form_pta_' . $l['pta_status'])) ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
