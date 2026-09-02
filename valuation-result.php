<?php
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/includes/functions.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = get_db()->prepare('SELECT v.*, p.brand, p.model, p.variant, p.slug FROM valuations v JOIN phones p ON p.id = v.phone_id WHERE v.id = ?');
$stmt->execute([$id]);
$valuation = $stmt->fetch();

if (!$valuation) {
    http_response_code(404);
    $pageTitle = 'Valuation not found';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="card"><p>We couldn\'t find that valuation.</p><a class="btn" href="/valuation.php">Start a new valuation</a></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$breakdown = json_decode($valuation['price_breakdown_json'] ?? '[]', true) ?: [];
$ptaBadgeClass = ['approved' => 'badge-pta', 'non_pta' => 'badge-non-pta', 'blocked' => 'badge-blocked'][$valuation['pta_status']] ?? 'badge-pta';

$pageTitle = $valuation['brand'] . ' ' . $valuation['model'] . ' — ' . t('estimated_price') . ' | WhatsMyPhonePrice.com';
$pageDescription = 'Estimated resale value for a used ' . $valuation['brand'] . ' ' . $valuation['model'] . ' in Pakistan.';
require_once __DIR__ . '/includes/header.php';
?>

<h1><?= e($valuation['brand'] . ' ' . $valuation['model'] . ' (' . $valuation['variant'] . ')') ?></h1>

<div class="price-display">
    <div class="label"><?= e(t('estimated_price')) ?></div>
    <div class="amount">Rs <?= number_format((float) $valuation['estimated_price_pkr']) ?></div>
</div>
<p class="hint" style="text-align:center;margin-top:8px"><?= e(t('estimated_price_note')) ?></p>

<div class="card">
    <h2>Price Breakdown</h2>
    <table class="breakdown-table">
        <?php foreach ($breakdown as $row): ?>
            <tr>
                <td><?= e($row['label']) ?></td>
                <td class="<?= $row['amount'] < 0 ? 'negative' : '' ?>">
                    <?= $row['amount'] >= 0 ? '+' : '' ?>Rs <?= number_format($row['amount']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="card">
    <h2>Condition Declared</h2>
    <span class="badge <?= $ptaBadgeClass ?>"><?= e(t('form_pta_' . $valuation['pta_status'])) ?></span>
    <?php if ($valuation['pta_status'] !== 'approved'): ?>
        <p class="alert alert-warning">Non-PTA or blocked phones carry real ongoing network-block risk. <a href="/imei-check.php">Learn how to verify PTA status</a>.</p>
    <?php endif; ?>
</div>

<div class="card" style="text-align:center">
    <p>Want to sell it? Post this exact valuation as a listing — condition details lock in automatically so buyers see exactly what you declared.</p>
    <a class="btn btn-secondary" href="/create-listing.php?valuation_id=<?= (int) $valuation['id'] ?>"><?= e(t('cta_post_for_sale')) ?></a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
