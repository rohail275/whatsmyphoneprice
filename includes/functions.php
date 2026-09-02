<?php
require_once __DIR__ . '/../config/db.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function get_phone_by_id(int $id): ?array
{
    $stmt = get_db()->prepare('SELECT * FROM phones WHERE id = ? AND is_active = 1');
    $stmt->execute([$id]);
    $phone = $stmt->fetch();
    return $phone ?: null;
}

function get_phone_by_slug(string $slug): ?array
{
    $stmt = get_db()->prepare('SELECT * FROM phones WHERE slug = ? AND is_active = 1');
    $stmt->execute([$slug]);
    $phone = $stmt->fetch();
    return $phone ?: null;
}

function get_all_phones(): array
{
    return get_db()->query('SELECT * FROM phones WHERE is_active = 1 ORDER BY brand, model')->fetchAll();
}

/**
 * Core valuation algorithm — implements README "Valuation Algorithm".
 * Deductions are applied as percentages of the value remaining after
 * depreciation, tracked step-by-step so the breakdown can be shown to
 * the user (README: "Label the result clearly as Estimated Price").
 *
 * @return array{estimated_price: float, breakdown: array<int, array{label: string, amount: float}>}
 */
function calculate_valuation(array $phone, array $input): array
{
    $breakdown = [];
    $basePrice = (float) $phone['base_price_pkr'];
    $breakdown[] = ['label' => 'Base price (' . $phone['brand'] . ' ' . $phone['model'] . ' ' . $phone['variant'] . ')', 'amount' => $basePrice];

    // 1. Age-based depreciation — brand-specific curve.
    // iPhones hold value much better than Android in the Pakistani resale market.
    $age = max(0, CURRENT_YEAR - (int) $input['purchase_year']);
    $annualDepreciationRate = ($phone['brand'] === 'Apple') ? 0.30 : 0.64;
    $afterDepreciation = $basePrice * pow(1 - $annualDepreciationRate, $age);
    $breakdown[] = ['label' => "Age depreciation ({$age} yr(s) @ " . round($annualDepreciationRate * 100) . '%/yr)', 'amount' => $afterDepreciation - $basePrice];

    $value = $afterDepreciation;

    // 2. Condition deductions (screen/body damage, camera, non-original repairs, water damage, missing accessories).
    $screenDeductionPct = ['none' => 0.0, 'minor' => 0.05, 'cracked' => 0.20][$input['screen_condition']] ?? 0.0;
    $value = apply_deduction($value, $screenDeductionPct, 'Screen condition', $breakdown);

    if (empty($input['touch_working'])) {
        $value = apply_deduction($value, 0.15, 'Touch/display issue', $breakdown);
    }
    if (empty($input['front_camera_working'])) {
        $value = apply_deduction($value, 0.07, 'Front camera not working', $breakdown);
    }
    if (empty($input['back_camera_working'])) {
        $value = apply_deduction($value, 0.07, 'Back camera not working', $breakdown);
    }

    // Battery: use battery health % if given, else fall back to symptom questions.
    if (!empty($input['battery_health_pct'])) {
        $pct = (int) $input['battery_health_pct'];
        $batteryDeductionPct = $pct >= 90 ? 0.0 : ($pct >= 80 ? 0.03 : ($pct >= 70 ? 0.08 : 0.15));
        $value = apply_deduction($value, $batteryDeductionPct, 'Battery health', $breakdown);
    } else {
        $symptomPct = 0.0;
        if (isset($input['battery_full_day']) && !$input['battery_full_day']) {
            $symptomPct += 0.05;
        }
        if (!empty($input['battery_drains_fast'])) {
            $symptomPct += 0.05;
        }
        if (!empty($input['battery_random_shutoff'])) {
            $symptomPct += 0.10;
        }
        if ($symptomPct > 0) {
            $value = apply_deduction($value, min($symptomPct, 0.15), 'Battery symptoms reported', $breakdown);
        }
    }

    if (!empty($input['water_damage'])) {
        $value = apply_deduction($value, 0.25, 'Water damage history', $breakdown);
    }

    if (($input['repair_history'] ?? 'none') === 'non_original_parts') {
        $value = apply_deduction($value, 0.10, 'Non-original repair parts', $breakdown);
    }

    // Box/charger/headphones — small deductions only, per README.
    if (empty($input['box_included'])) {
        $value = apply_deduction($value, 0.02, 'No box', $breakdown);
    }
    if (empty($input['charger_included'])) {
        $value = apply_deduction($value, 0.02, 'No charger', $breakdown);
    }
    if (isset($input['headphones_included']) && !$input['headphones_included']) {
        $value = apply_deduction($value, 0.01, 'No headphones', $breakdown);
    }

    // 3. PTA status adjustment — large deduction, not small.
    if ($input['pta_status'] === 'non_pta') {
        $value = apply_deduction($value, 0.55, 'Non-PTA / patched phone', $breakdown);
    } elseif ($input['pta_status'] === 'blocked') {
        $blockedFloor = 3000.0;
        $breakdown[] = ['label' => 'Blocked IMEI — near-zero resale value', 'amount' => $blockedFloor - $value];
        $value = $blockedFloor;
    }

    if (($input['network_lock'] ?? 'unlocked') === 'locked') {
        $value = apply_deduction($value, 0.05, 'Carrier-locked', $breakdown);
    }

    $value = max($value, 1000.0);
    $value = round($value / 500) * 500; // round to nearest 500 PKR for a clean quote

    return ['estimated_price' => $value, 'breakdown' => $breakdown];
}

function apply_deduction(float $value, float $pct, string $label, array &$breakdown): float
{
    if ($pct <= 0) {
        return $value;
    }
    $amount = -1 * ($value * $pct);
    $breakdown[] = ['label' => $label, 'amount' => $amount];
    return $value + $amount;
}

function generate_otp(): string
{
    return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Stub SMS sender — no gateway is wired up yet. Logs the OTP so the site
 * is testable end-to-end; swap this out for a real Pakistani SMS API
 * (e.g. a local aggregator) before going live.
 */
function send_otp_sms(string $phoneNumber, string $otp): void
{
    error_log("OTP for {$phoneNumber}: {$otp}");
}

function save_uploaded_photo(array $file, string $subdir): ?string
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        return null;
    }
    $dir = __DIR__ . '/../uploads/' . $subdir . '/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    move_uploaded_file($file['tmp_name'], $dir . $filename);
    return 'uploads/' . $subdir . '/' . $filename;
}
