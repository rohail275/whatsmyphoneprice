<?php
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/includes/functions.php';

$id = (int) ($_GET['id'] ?? 0);
$db = get_db();
$stmt = $db->prepare('SELECT
        l.id, l.title, l.description, l.asking_price_pkr, l.city, l.area, l.status,
        v.screen_condition, v.touch_working, v.touch_issue_notes, v.front_camera_working, v.back_camera_working,
        v.battery_health_pct, v.water_damage, v.repair_history, v.box_included, v.charger_included,
        v.pta_status, v.network_lock, v.bill_available, v.estimated_price_pkr,
        p.brand, p.model, p.variant, p.slug,
        u.name AS seller_name, u.phone_number AS seller_phone, u.phone_verified,
        u.rating_avg, u.rating_count
    FROM listings l
    JOIN valuations v ON v.id = l.valuation_id
    JOIN phones p ON p.id = v.phone_id
    JOIN users u ON u.id = l.user_id
    WHERE l.id = ?');
$stmt->execute([$id]);
$listing = $stmt->fetch();

if (!$listing) {
    http_response_code(404);
    $pageTitle = 'Listing not found';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="card"><p>This listing doesn\'t exist or was removed.</p><a class="btn" href="/listings.php">Browse listings</a></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$photoStmt = $db->prepare('SELECT photo_path FROM listing_photos WHERE listing_id = ? ORDER BY sort_order');
$photoStmt->execute([$id]);
$photos = $photoStmt->fetchAll();

$name = $listing['brand'] . ' ' . $listing['model'] . ' (' . $listing['variant'] . ')';
$pageTitle = e($listing['title']) . ' — ' . $name . ' | WhatsMyPhonePrice.com';
$pageDescription = 'Used ' . $name . ' for sale in ' . $listing['city'] . ', Pakistan.';
$ptaBadgeClass = ['approved' => 'badge-pta', 'non_pta' => 'badge-non-pta', 'blocked' => 'badge-blocked'][$listing['pta_status']] ?? 'badge-pta';
$repairLabelKey = ['none' => 'form_repair_none', 'original_parts' => 'form_repair_original', 'non_original_parts' => 'form_repair_non_original'][$listing['repair_history']] ?? 'form_repair_none';

require_once __DIR__ . '/includes/header.php';
?>

<?php if (!empty($_GET['new'])): ?>
<div class="alert alert-info">Your listing is live! Share the link with buyers, or wait for them to find it here.</div>
<?php endif; ?>

<?php if (!empty($_GET['verified'])): ?>
<div class="alert alert-info">Phone verified — you now have the Verified Seller badge.</div>
<?php endif; ?>

<?php if (!empty($_GET['verify_user']) && !$listing['phone_verified']): ?>
<div class="card">
    <h2>Verify your phone number</h2>
    <p>Get the "Verified Seller" badge — buyers trust verified listings more. We sent a 6-digit code by SMS.</p>
    <?php if (!empty($_GET['verify_error'])): ?>
        <p class="alert alert-danger">That code is incorrect or expired. Please try again.</p>
    <?php endif; ?>
    <form action="/verify-otp.php" method="post">
        <input type="hidden" name="user_id" value="<?= (int) $_GET['verify_user'] ?>">
        <input type="hidden" name="redirect" value="/listing.php?id=<?= (int) $id ?>">
        <div class="form-group">
            <label for="otp_code">Verification code</label>
            <input type="text" id="otp_code" name="otp_code" maxlength="6" pattern="[0-9]{6}" required>
        </div>
        <button type="submit" class="btn">Verify</button>
    </form>
</div>
<?php endif; ?>

<h1><?= e($listing['title']) ?></h1>
<p><?= e($name) ?> · <?= e($listing['city']) ?><?= $listing['area'] ? ', ' . e($listing['area']) : '' ?></p>

<?php if ($photos): ?>
<div class="phone-grid">
    <?php foreach ($photos as $photo): ?>
        <img src="/<?= e($photo['photo_path']) ?>" alt="<?= e($name) ?> photo" style="width:100%;border-radius:8px">
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="price-display">
    <div class="label">Asking Price</div>
    <div class="amount">Rs <?= number_format((float) $listing['asking_price_pkr']) ?></div>
</div>
<p class="hint" style="text-align:center;margin-top:6px">System <?= e(t('estimated_price')) ?>: Rs <?= number_format((float) $listing['estimated_price_pkr']) ?></p>

<div class="card condition-card">
    <h2 style="margin-top:0">Condition Card <span class="hint">(locked from original valuation)</span></h2>
    <dl>
        <dt>Screen</dt><dd><?= e(t('form_screen_' . $listing['screen_condition'])) ?></dd>
        <dt>Touch/Display</dt><dd><?= $listing['touch_working'] ? e(t('yes')) : e(t('no')) ?></dd>
        <dt>Front Camera</dt><dd><?= $listing['front_camera_working'] ? e(t('yes')) : e(t('no')) ?></dd>
        <dt>Back Camera</dt><dd><?= $listing['back_camera_working'] ? e(t('yes')) : e(t('no')) ?></dd>
        <dt>Battery Health</dt><dd><?= $listing['battery_health_pct'] !== null ? (int) $listing['battery_health_pct'] . '%' : 'Not specified' ?></dd>
        <dt>Water Damage</dt><dd><?= $listing['water_damage'] ? e(t('yes')) : e(t('no')) ?></dd>
        <dt>Repair History</dt><dd><?= e(t($repairLabelKey)) ?></dd>
        <dt>PTA Status</dt><dd><span class="badge <?= $ptaBadgeClass ?>"><?= e(t('form_pta_' . $listing['pta_status'])) ?></span></dd>
        <dt>Network Lock</dt><dd><?= e(t('form_' . $listing['network_lock'])) ?></dd>
        <dt>Bill Available</dt><dd><?= $listing['bill_available'] ? e(t('yes')) : e(t('no')) ?></dd>
        <dt>Box / Charger</dt><dd><?= $listing['box_included'] ? 'Box ' : '' ?><?= $listing['charger_included'] ? 'Charger' : '' ?><?= (!$listing['box_included'] && !$listing['charger_included']) ? 'Neither' : '' ?></dd>
    </dl>
    <?php if ($listing['touch_issue_notes']): ?>
        <p class="hint">Seller note: <?= e($listing['touch_issue_notes']) ?></p>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Seller</h2>
    <p>
        <?= e($listing['seller_name']) ?>
        <?php if ($listing['phone_verified']): ?><span class="badge badge-verified">Verified Seller</span><?php endif; ?>
        <?php if ($listing['rating_count'] > 0): ?>
            <br><span class="hint"><?= number_format((float) $listing['rating_avg'], 1) ?> ★ (<?= (int) $listing['rating_count'] ?> reviews)</span>
        <?php endif; ?>
    </p>
    <p>Contact: <?= e($listing['seller_phone']) ?></p>
    <p class="hint">Completed a deal with this seller? <a href="/rate.php?listing_id=<?= (int) $id ?>">Leave a rating</a>.</p>
</div>

<div class="alert alert-warning">
    <strong>Before you pay:</strong> verify PTA/IMEI status yourself, ask the seller to factory-reset in front of you to check for iCloud/FRP lock, and meet in a safe public place. See our <a href="/safety.php">Safe Deal Checklist</a>.
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
