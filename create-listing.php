<?php
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/includes/functions.php';

$valuationId = (int) ($_GET['valuation_id'] ?? 0);
$stmt = get_db()->prepare('SELECT v.*, p.brand, p.model, p.variant FROM valuations v JOIN phones p ON p.id = v.phone_id WHERE v.id = ?');
$stmt->execute([$valuationId]);
$valuation = $stmt->fetch();

if (!$valuation) {
    http_response_code(404);
    $pageTitle = 'Valuation not found';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="card"><p>Start a valuation first before posting a listing.</p><a class="btn" href="/valuation.php">Get a valuation</a></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$name = $valuation['brand'] . ' ' . $valuation['model'] . ' (' . $valuation['variant'] . ')';
$pageTitle = t('cta_post_for_sale') . ' — ' . $name;
$pageDescription = 'List your ' . $name . ' for sale directly to buyers in Pakistan.';
require_once __DIR__ . '/includes/header.php';
?>

<h1><?= e(t('cta_post_for_sale')) ?></h1>

<div class="card condition-card">
    <h2 style="margin-top:0"><?= e($name) ?></h2>
    <p><?= e(t('estimated_price')) ?>: <strong>Rs <?= number_format((float) $valuation['estimated_price_pkr']) ?></strong></p>
    <p class="hint">This condition card is locked from your valuation and will be shown to buyers exactly as declared.</p>
</div>

<form class="card" action="/submit-listing.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="valuation_id" value="<?= (int) $valuation['id'] ?>">

    <div class="form-group">
        <label for="title">Listing title</label>
        <input type="text" id="title" name="title" maxlength="150" value="<?= e($name . ' — Good Condition') ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4" placeholder="Any extra details for buyers..."></textarea>
    </div>
    <div class="form-group">
        <label for="asking_price_pkr">Asking price (PKR)</label>
        <input type="number" id="asking_price_pkr" name="asking_price_pkr" min="0" step="500" value="<?= (int) $valuation['estimated_price_pkr'] ?>" required>
    </div>
    <div class="form-group">
        <label for="city">City</label>
        <input type="text" id="city" name="city" maxlength="100" required>
    </div>
    <div class="form-group">
        <label for="area">Area (optional)</label>
        <input type="text" id="area" name="area" maxlength="100">
    </div>

    <h2>Your Contact Info</h2>
    <div class="form-group">
        <label for="seller_name">Your name</label>
        <input type="text" id="seller_name" name="seller_name" maxlength="100" required>
    </div>
    <div class="form-group">
        <label for="seller_phone">Phone number (03XXXXXXXXX)</label>
        <input type="tel" id="seller_phone" name="seller_phone" pattern="03[0-9]{9}" maxlength="11" required>
        <p class="hint">Used for buyers to contact you and for phone verification (Verified Seller badge).</p>
    </div>

    <div class="form-group">
        <label for="listing_photos">Photos of your actual device</label>
        <input type="file" id="listing_photos" name="listing_photos[]" accept="image/*" multiple>
    </div>

    <button type="submit" class="btn"><?= e(t('submit')) ?></button>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
