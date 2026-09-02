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
<p class="hint" style="margin-bottom:16px">Free, instant, no obligation. We never force you to sell to us.</p>

<nav class="step-nav">
    <a href="#sec-phone"><span class="num">1</span> Phone</a>
    <a href="#sec-condition"><span class="num">2</span> Condition</a>
    <a href="#sec-battery"><span class="num">3</span> Battery</a>
    <a href="#sec-history"><span class="num">4</span> History</a>
    <a href="#sec-pta"><span class="num">5</span> PTA</a>
    <a href="#sec-photos"><span class="num">6</span> Photos</a>
</nav>

<form action="/calculate.php" method="post" enctype="multipart/form-data">

    <section id="sec-phone" class="section-card">
        <h2><span class="section-icon">📱</span> Your Phone</h2>
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
        <div class="form-grid">
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
                <input type="text" id="color" name="color" maxlength="50" placeholder="e.g. Midnight Black">
            </div>
        </div>
    </section>

    <section id="sec-condition" class="section-card">
        <h2><span class="section-icon">🔍</span> Screen &amp; Condition</h2>
        <div class="form-group">
            <label><?= e(t('form_screen_condition')) ?></label>
            <div class="toggle-group">
                <?php toggle_group('screen_condition', ['none' => t('form_screen_none'), 'minor' => t('form_screen_minor'), 'cracked' => t('form_screen_cracked')], 'none'); ?>
            </div>
        </div>
        <div class="form-group">
            <label><?= e(t('form_touch_working')) ?></label>
            <div class="toggle-group">
                <?php toggle_group('touch_working', ['1' => t('yes'), '0' => t('no')], '1'); ?>
            </div>
            <input type="text" name="touch_issue_notes" placeholder="Note any dead pixels or touch issues (optional)" style="margin-top:8px">
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label><?= e(t('form_camera_front')) ?></label>
                <div class="toggle-group">
                    <?php toggle_group('front_camera_working', ['1' => t('yes'), '0' => t('no')], '1'); ?>
                </div>
            </div>
            <div class="form-group">
                <label><?= e(t('form_camera_back')) ?></label>
                <div class="toggle-group">
                    <?php toggle_group('back_camera_working', ['1' => t('yes'), '0' => t('no')], '1'); ?>
                </div>
            </div>
        </div>
    </section>

    <section id="sec-battery" class="section-card">
        <h2><span class="section-icon">🔋</span> Battery</h2>
        <div class="form-group">
            <label for="battery_health_pct"><?= e(t('form_battery_health')) ?></label>
            <input type="number" id="battery_health_pct" name="battery_health_pct" min="0" max="100" placeholder="e.g. 87">
            <p class="hint">Many Android phones don't expose this easily — leave blank and answer below instead.</p>
        </div>
        <div id="battery-symptom-fields" class="form-grid">
            <div class="form-group">
                <label><?= e(t('form_battery_full_day')) ?></label>
                <div class="toggle-group">
                    <?php toggle_group('battery_full_day', ['1' => t('yes'), '0' => t('no')], '1'); ?>
                </div>
            </div>
            <div class="form-group">
                <label><?= e(t('form_battery_drains_fast')) ?></label>
                <div class="toggle-group">
                    <?php toggle_group('battery_drains_fast', ['1' => t('yes'), '0' => t('no')], '0'); ?>
                </div>
            </div>
            <div class="form-group">
                <label><?= e(t('form_battery_shutoff')) ?></label>
                <div class="toggle-group">
                    <?php toggle_group('battery_random_shutoff', ['1' => t('yes'), '0' => t('no')], '0'); ?>
                </div>
            </div>
        </div>
    </section>

    <section id="sec-history" class="section-card">
        <h2><span class="section-icon">🛠️</span> History &amp; Accessories</h2>
        <div class="form-grid">
            <div class="form-group">
                <label><?= e(t('form_water_damage')) ?></label>
                <div class="toggle-group">
                    <?php toggle_group('water_damage', ['1' => t('yes'), '0' => t('no')], '0'); ?>
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
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label><?= e(t('form_box_included')) ?></label>
                <div class="toggle-group">
                    <?php toggle_group('box_included', ['1' => t('yes'), '0' => t('no')], '0'); ?>
                </div>
            </div>
            <div class="form-group">
                <label><?= e(t('form_charger_included')) ?></label>
                <div class="toggle-group">
                    <?php toggle_group('charger_included', ['1' => t('yes'), '0' => t('no')], '0'); ?>
                </div>
            </div>
            <div class="form-group">
                <label><?= e(t('form_headphones_included')) ?></label>
                <div class="toggle-group">
                    <?php toggle_group('headphones_included', ['1' => t('yes'), '0' => t('no')], '0'); ?>
                </div>
            </div>
        </div>
    </section>

    <section id="sec-pta" class="section-card">
        <h2><span class="section-icon">📶</span> PTA &amp; Network</h2>
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
            <div class="toggle-group">
                <?php toggle_group('network_lock', ['unlocked' => t('form_unlocked'), 'locked' => t('form_locked')], 'unlocked'); ?>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="imei"><?= e(t('form_imei')) ?></label>
                <input type="text" id="imei" name="imei" maxlength="20" pattern="[0-9]{14,16}" placeholder="15-digit IMEI">
            </div>
            <div class="form-group">
                <label><?= e(t('form_bill_available')) ?></label>
                <div class="toggle-group">
                    <?php toggle_group('bill_available', ['1' => t('yes'), '0' => t('no')], '0'); ?>
                </div>
            </div>
        </div>
    </section>

    <section id="sec-photos" class="section-card">
        <h2><span class="section-icon">📷</span> Photos</h2>
        <div class="form-group">
            <label for="photos"><?= e(t('form_photos')) ?></label>
            <input type="file" id="photos" name="photos[]" accept="image/*" multiple>
            <p class="hint">Real photos of your device — used for buyer review only, never for automatic price grading.</p>
        </div>
    </section>

    <button type="submit" class="btn btn-block"><?= e(t('submit')) ?></button>
</form>

<?php
$extraFooterScripts = ['/assets/js/main.js'];
require_once __DIR__ . '/includes/footer.php';
