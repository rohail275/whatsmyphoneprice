<?php
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/includes/functions.php';

$listingId = isset($_GET['listing_id']) ? (int) $_GET['listing_id'] : null;
$uploaded = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['listing_id'])) {
    $listingId = (int) $_POST['listing_id'];
    if (!empty($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
        $path = save_uploaded_photo($_FILES['screenshot'], 'listings');
        if ($path) {
            $db = get_db();
            $check = $db->prepare('SELECT id FROM listings WHERE id = ?');
            $check->execute([$listingId]);
            if ($check->fetch()) {
                $db->prepare('INSERT INTO listing_photos (listing_id, photo_path, sort_order) VALUES (?, ?, 99)')->execute([$listingId, $path]);
                $uploaded = true;
            }
        }
    }
}

$pageTitle = t('nav_imei_check') . ' — WhatsMyPhonePrice.com';
$pageDescription = 'How to check PTA approval status for a phone in Pakistan using SMS-8484, the DVS app, or dirbs.pta.gov.pk.';
require_once __DIR__ . '/includes/header.php';
?>

<h1><?= e(t('nav_imei_check')) ?></h1>
<p>PTA (non-)approval status is one of the biggest factors in a used phone's value in Pakistan. Always verify it yourself before buying — don't just trust the seller's word.</p>

<div class="card">
    <h2>Option 1 — SMS-8484 (fastest)</h2>
    <p>From any Pakistani SIM, send the phone's 15-digit IMEI as an SMS to <strong>8484</strong>. You'll get a reply confirming PTA-approved / non-compliant / blocked status.</p>
    <p class="hint">Find the IMEI by dialing <code>*#06#</code> on the phone, or in Settings → About Phone.</p>
</div>

<div class="card">
    <h2>Option 2 — DVS Mobile App</h2>
    <p>Download the official <strong>PTA DVS (Device Verification System)</strong> app from the Play Store / App Store and enter the IMEI there.</p>
</div>

<div class="card">
    <h2>Option 3 — DIRBS Website (official, most detailed)</h2>
    <p>Check directly on PTA's own portal:</p>
    <a class="btn" href="https://dirbs.pta.gov.pk/" target="_blank" rel="noopener">Open dirbs.pta.gov.pk</a>
    <p class="hint">We link out to PTA's official site rather than scraping it ourselves — there is no official PTA API, and we don't attempt to bypass their portal.</p>
</div>

<div class="card">
    <h2>Buying or selling? Attach proof to a listing</h2>
    <p>Sellers can upload a screenshot of their SMS-8484 or DVS-app result so buyers can see it on the listing page.</p>
    <?php if ($uploaded): ?>
        <p class="alert alert-info">Screenshot uploaded. <a href="/listing.php?id=<?= (int) $listingId ?>">View listing</a></p>
    <?php else: ?>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="listing_id">Listing ID</label>
            <input type="number" id="listing_id" name="listing_id" value="<?= $listingId ? (int) $listingId : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="screenshot">PTA status screenshot</label>
            <input type="file" id="screenshot" name="screenshot" accept="image/*" required>
        </div>
        <button type="submit" class="btn"><?= e(t('submit')) ?></button>
    </form>
    <?php endif; ?>
</div>

<div class="alert alert-warning">
    <strong>Note:</strong> Non-PTA ("patched") phones can still work on WiFi but face SIM-blocking risk over time. Blocked phones are near-worthless for resale. We label PTA status clearly on every listing — never buy a phone without checking this yourself first.
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
