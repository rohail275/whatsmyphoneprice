<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /valuation.php');
    exit;
}

$phoneId = (int) ($_POST['phone_id'] ?? 0);
$phone = get_phone_by_id($phoneId);
$purchaseYear = (int) ($_POST['purchase_year'] ?? 0);

if (!$phone || $purchaseYear < 2000 || $purchaseYear > CURRENT_YEAR) {
    http_response_code(400);
    die('Invalid submission — please go back and select a valid phone and purchase year.');
}

$allowedPta = ['approved', 'non_pta', 'blocked'];
$ptaStatus = in_array($_POST['pta_status'] ?? '', $allowedPta, true) ? $_POST['pta_status'] : 'approved';

$allowedScreen = ['none', 'minor', 'cracked'];
$screenCondition = in_array($_POST['screen_condition'] ?? '', $allowedScreen, true) ? $_POST['screen_condition'] : 'none';

$allowedRepair = ['none', 'original_parts', 'non_original_parts'];
$repairHistory = in_array($_POST['repair_history'] ?? '', $allowedRepair, true) ? $_POST['repair_history'] : 'none';

$input = [
    'purchase_year' => $purchaseYear,
    'screen_condition' => $screenCondition,
    'touch_working' => !empty($_POST['touch_working']),
    'touch_issue_notes' => trim((string) ($_POST['touch_issue_notes'] ?? '')),
    'front_camera_working' => !empty($_POST['front_camera_working']),
    'back_camera_working' => !empty($_POST['back_camera_working']),
    'battery_health_pct' => ($_POST['battery_health_pct'] ?? '') !== '' ? max(0, min(100, (int) $_POST['battery_health_pct'])) : null,
    'battery_full_day' => isset($_POST['battery_full_day']) ? (bool) $_POST['battery_full_day'] : null,
    'battery_drains_fast' => !empty($_POST['battery_drains_fast']),
    'battery_random_shutoff' => !empty($_POST['battery_random_shutoff']),
    'water_damage' => !empty($_POST['water_damage']),
    'repair_history' => $repairHistory,
    'box_included' => !empty($_POST['box_included']),
    'charger_included' => !empty($_POST['charger_included']),
    'headphones_included' => isset($_POST['headphones_included']) ? (bool) $_POST['headphones_included'] : null,
    'pta_status' => $ptaStatus,
    'network_lock' => ($_POST['network_lock'] ?? 'unlocked') === 'locked' ? 'locked' : 'unlocked',
    'imei' => preg_replace('/[^0-9]/', '', (string) ($_POST['imei'] ?? '')) ?: null,
    'bill_available' => !empty($_POST['bill_available']),
    'color' => trim((string) ($_POST['color'] ?? '')),
];

$result = calculate_valuation($phone, $input);

$phoneName = $phone['brand'] . ' ' . $phone['model'] . ' ' . $phone['variant'];
$aiExplanation = generate_price_explanation($phoneName, $result['breakdown'], $result['estimated_price']);

$db = get_db();
$stmt = $db->prepare('INSERT INTO valuations (
    phone_id, screen_condition, touch_working, touch_issue_notes,
    front_camera_working, back_camera_working, battery_health_pct,
    purchase_year, battery_full_day, battery_drains_fast, battery_random_shutoff,
    water_damage, repair_history, box_included, charger_included, headphones_included,
    pta_status, network_lock, imei, bill_available, color,
    estimated_price_pkr, price_breakdown_json, ai_explanation
) VALUES (?,?,?,?, ?,?,?, ?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?)');

$stmt->execute([
    $phone['id'], $input['screen_condition'], (int) $input['touch_working'], $input['touch_issue_notes'] ?: null,
    (int) $input['front_camera_working'], (int) $input['back_camera_working'], $input['battery_health_pct'],
    $input['purchase_year'], $input['battery_full_day'] === null ? null : (int) $input['battery_full_day'], (int) $input['battery_drains_fast'], (int) $input['battery_random_shutoff'],
    (int) $input['water_damage'], $input['repair_history'], (int) $input['box_included'], (int) $input['charger_included'], $input['headphones_included'] === null ? null : (int) $input['headphones_included'],
    $input['pta_status'], $input['network_lock'], $input['imei'], (int) $input['bill_available'], $input['color'] ?: null,
    $result['estimated_price'], json_encode($result['breakdown']), $aiExplanation,
]);

$valuationId = (int) $db->lastInsertId();

if (!empty($_FILES['photos']) && is_array($_FILES['photos']['name'])) {
    $count = count($_FILES['photos']['name']);
    for ($i = 0; $i < $count; $i++) {
        if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }
        $file = [
            'name' => $_FILES['photos']['name'][$i],
            'type' => $_FILES['photos']['type'][$i],
            'tmp_name' => $_FILES['photos']['tmp_name'][$i],
            'error' => $_FILES['photos']['error'][$i],
            'size' => $_FILES['photos']['size'][$i],
        ];
        $path = save_uploaded_photo($file, 'valuations');
        if ($path) {
            $photoStmt = $db->prepare('INSERT INTO valuation_photos (valuation_id, photo_path) VALUES (?, ?)');
            $photoStmt->execute([$valuationId, $path]);
        }
    }
}

header('Location: /valuation-result.php?id=' . $valuationId);
exit;
