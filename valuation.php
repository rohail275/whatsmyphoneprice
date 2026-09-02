<?php
require_once __DIR__ . '/includes/lang.php';
$pageTitle = t('nav_valuation') . ' — WhatsMyPhonePrice.com';
$pageDescription = 'Answer a few questions about your used phone to get an instant estimated resale price in PKR.';
require_once __DIR__ . '/includes/header.php';

$phones = get_all_phones();
$preselectedId = isset($_GET['phone_id']) ? (int) $_GET['phone_id'] : null;
$currentYear = CURRENT_YEAR;
?>
<h1><?= e(t('cta_start_valuation')) ?></h1>
<p class="hint">Free, instant, no obligation. We never force you to sell to us.</p>

<form class="card" action="/calculate.php" method="post" enctype="multipart/form-data">

    <div class="form-group">
        <label for="phone_id"><?= e(t('form_brand')) ?> / <?= e(t('form_model')) ?> / <?= e(t('form_variant')) ?></label>
        <select id="phone_id" name="phone_id" required>
            <option value="">-- Select your phone --</option>
            <?php foreach ($phones as $p): ?>
                <option value="<?= (int) $p['id'] ?>" <?= $preselectedId === (int) $p['id'] ? 'selected' : '' ?>>
                    <?= e($p['brand'] . ' ' . $p['model'] . ' (' . $p['variant'] . ')') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="purchase_year"><?= e(t('form_purchase_year')) ?></label>
        <select id="purchase_year" name="purchase_year" required>
            <?php for ($y = $currentYear; $y >= $currentYear - 10; $y--): ?>
                <option value="<?= $y ?>"><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="color"><?= e(t('form_color')) ?></label>
        <input type="text" id="color" name="color" maxlength="50">
    </div>

    <h2><?= e(t('form_screen_condition')) ?></h2>
    <div class="form-group radio-row">
        <label><input type="radio" name="screen_condition" value="none" checked> <?= e(t('form_screen_none')) ?></label>
        <label><input type="radio" name="screen_condition" value="minor"> <?= e(t('form_screen_minor')) ?></label>
        <label><input type="radio" name="screen_condition" value="cracked"> <?= e(t('form_screen_cracked')) ?></label>
    </div>

    <div class="form-group">
        <label><?= e(t('form_touch_working')) ?></label>
        <div class="radio-row">
            <label><input type="radio" name="touch_working" value="1" checked> <?= e(t('yes')) ?></label>
            <label><input type="radio" name="touch_working" value="0"> <?= e(t('no')) ?></label>
        </div>
        <input type="text" name="touch_issue_notes" placeholder="Note any dead pixels or touch issues (optional)">
    </div>

    <div class="form-group">
        <label><?= e(t('form_camera_front')) ?></label>
        <div class="radio-row">
            <label><input type="radio" name="front_camera_working" value="1" checked> <?= e(t('yes')) ?></label>
            <label><input type="radio" name="front_camera_working" value="0"> <?= e(t('no')) ?></label>
        </div>
    </div>
    <div class="form-group">
        <label><?= e(t('form_camera_back')) ?></label>
        <div class="radio-row">
            <label><input type="radio" name="back_camera_working" value="1" checked> <?= e(t('yes')) ?></label>
            <label><input type="radio" name="back_camera_working" value="0"> <?= e(t('no')) ?></label>
        </div>
    </div>

    <h2>Battery</h2>
    <div class="form-group">
        <label for="battery_health_pct"><?= e(t('form_battery_health')) ?></label>
        <input type="number" id="battery_health_pct" name="battery_health_pct" min="0" max="100" placeholder="e.g. 87">
        <p class="hint">Many Android phones don't expose this easily — leave blank and answer below instead.</p>
    </div>
    <div id="battery-symptom-fields">
        <div class="form-group">
            <label><?= e(t('form_battery_full_day')) ?></label>
            <div class="radio-row">
                <label><input type="radio" name="battery_full_day" value="1" checked> <?= e(t('yes')) ?></label>
                <label><input type="radio" name="battery_full_day" value="0"> <?= e(t('no')) ?></label>
            </div>
        </div>
        <div class="form-group">
            <label><?= e(t('form_battery_drains_fast')) ?></label>
            <div class="radio-row">
                <label><input type="radio" name="battery_drains_fast" value="1"> <?= e(t('yes')) ?></label>
                <label><input type="radio" name="battery_drains_fast" value="0" checked> <?= e(t('no')) ?></label>
            </div>
        </div>
        <div class="form-group">
            <label><?= e(t('form_battery_shutoff')) ?></label>
            <div class="radio-row">
                <label><input type="radio" name="battery_random_shutoff" value="1"> <?= e(t('yes')) ?></label>
                <label><input type="radio" name="battery_random_shutoff" value="0" checked> <?= e(t('no')) ?></label>
            </div>
        </div>
    </div>

    <h2>Condition & History</h2>
    <div class="form-group">
        <label><?= e(t('form_water_damage')) ?></label>
        <div class="radio-row">
            <label><input type="radio" name="water_damage" value="1"> <?= e(t('yes')) ?></label>
            <label><input type="radio" name="water_damage" value="0" checked> <?= e(t('no')) ?></label>
        </div>
    </div>
    <div class="form-group">
        <label for="repair_history"><?= e(t('form_repair_history')) ?></label>
        <select id="repair_history" name="repair_history">
            <option value="none"><?= e(t('form_repair_none')) ?></option>
            <option value="original_parts"><?= e(t('form_repair_original')) ?></option>
            <option value="non_original_parts"><?= e(t('form_repair_non_original')) ?></option>
        </select>
    </div>

    <div class="form-group">
        <label><?= e(t('form_box_included')) ?></label>
        <div class="radio-row">
            <label><input type="radio" name="box_included" value="1"> <?= e(t('yes')) ?></label>
            <label><input type="radio" name="box_included" value="0" checked> <?= e(t('no')) ?></label>
        </div>
    </div>
    <div class="form-group">
        <label><?= e(t('form_charger_included')) ?></label>
        <div class="radio-row">
            <label><input type="radio" name="charger_included" value="1"> <?= e(t('yes')) ?></label>
            <label><input type="radio" name="charger_included" value="0" checked> <?= e(t('no')) ?></label>
        </div>
    </div>
    <div class="form-group">
        <label><?= e(t('form_headphones_included')) ?></label>
        <div class="radio-row">
            <label><input type="radio" name="headphones_included" value="1"> <?= e(t('yes')) ?></label>
            <label><input type="radio" name="headphones_included" value="0" checked> <?= e(t('no')) ?></label>
        </div>
    </div>

    <h2>PTA & Network</h2>
    <div class="form-group">
        <label for="pta_status"><?= e(t('form_pta_status')) ?></label>
        <select id="pta_status" name="pta_status" required>
            <option value="approved"><?= e(t('form_pta_approved')) ?></option>
            <option value="non_pta"><?= e(t('form_pta_non_pta')) ?></option>
            <option value="blocked"><?= e(t('form_pta_blocked')) ?></option>
        </select>
        <p id="pta-warning" class="hint" style="display:none">Non-PTA/blocked status significantly reduces resale value. <a href="/imei-check.php">Check your status</a>.</p>
    </div>
    <div class="form-group">
        <label><?= e(t('form_network_lock')) ?></label>
        <div class="radio-row">
            <label><input type="radio" name="network_lock" value="unlocked" checked> <?= e(t('form_unlocked')) ?></label>
            <label><input type="radio" name="network_lock" value="locked"> <?= e(t('form_locked')) ?></label>
        </div>
    </div>
    <div class="form-group">
        <label for="imei"><?= e(t('form_imei')) ?></label>
        <input type="text" id="imei" name="imei" maxlength="20" pattern="[0-9]{14,16}" placeholder="15-digit IMEI">
    </div>
    <div class="form-group">
        <label><?= e(t('form_bill_available')) ?></label>
        <div class="radio-row">
            <label><input type="radio" name="bill_available" value="1"> <?= e(t('yes')) ?></label>
            <label><input type="radio" name="bill_available" value="0" checked> <?= e(t('no')) ?></label>
        </div>
    </div>

    <h2>Photos</h2>
    <div class="form-group">
        <label for="photos"><?= e(t('form_photos')) ?></label>
        <input type="file" id="photos" name="photos[]" accept="image/*" multiple>
        <p class="hint">Real photos of your device — used for buyer review only, never for automatic price grading.</p>
    </div>

    <button type="submit" class="btn"><?= e(t('submit')) ?></button>
</form>

<?php
$extraFooterScripts = ['/assets/js/main.js'];
require_once __DIR__ . '/includes/footer.php';
