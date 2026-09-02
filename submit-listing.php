<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /valuation.php');
    exit;
}

$valuationId = (int) ($_POST['valuation_id'] ?? 0);
$db = get_db();
$stmt = $db->prepare('SELECT id FROM valuations WHERE id = ?');
$stmt->execute([$valuationId]);
if (!$stmt->fetch()) {
    http_response_code(400);
    die('Invalid valuation.');
}

$sellerPhone = preg_replace('/[^0-9]/', '', (string) ($_POST['seller_phone'] ?? ''));
$sellerName = trim((string) ($_POST['seller_name'] ?? ''));
$city = trim((string) ($_POST['city'] ?? ''));

if ($sellerPhone === '' || $sellerName === '' || $city === '') {
    http_response_code(400);
    die('Missing required fields.');
}

// find or create the seller user record by phone number
$userStmt = $db->prepare('SELECT id FROM users WHERE phone_number = ?');
$userStmt->execute([$sellerPhone]);
$user = $userStmt->fetch();

if ($user) {
    $userId = (int) $user['id'];
    $db->prepare('UPDATE users SET name = ?, city = ? WHERE id = ?')->execute([$sellerName, $city, $userId]);
} else {
    $db->prepare('INSERT INTO users (name, phone_number, city) VALUES (?, ?, ?)')->execute([$sellerName, $sellerPhone, $city]);
    $userId = (int) $db->lastInsertId();
}

$title = trim((string) ($_POST['title'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));
$askingPrice = (float) ($_POST['asking_price_pkr'] ?? 0);
$area = trim((string) ($_POST['area'] ?? ''));

$listingStmt = $db->prepare('INSERT INTO listings (valuation_id, user_id, title, description, asking_price_pkr, city, area) VALUES (?,?,?,?,?,?,?)');
$listingStmt->execute([$valuationId, $userId, $title, $description ?: null, $askingPrice, $city, $area ?: null]);
$listingId = (int) $db->lastInsertId();

if (!empty($_FILES['listing_photos']) && is_array($_FILES['listing_photos']['name'])) {
    $count = count($_FILES['listing_photos']['name']);
    $order = 0;
    for ($i = 0; $i < $count; $i++) {
        if ($_FILES['listing_photos']['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }
        $file = [
            'name' => $_FILES['listing_photos']['name'][$i],
            'type' => $_FILES['listing_photos']['type'][$i],
            'tmp_name' => $_FILES['listing_photos']['tmp_name'][$i],
            'error' => $_FILES['listing_photos']['error'][$i],
            'size' => $_FILES['listing_photos']['size'][$i],
        ];
        $path = save_uploaded_photo($file, 'listings');
        if ($path) {
            $db->prepare('INSERT INTO listing_photos (listing_id, photo_path, sort_order) VALUES (?, ?, ?)')->execute([$listingId, $path, $order++]);
        }
    }
}

// kick off phone verification
$otp = generate_otp();
$db->prepare('UPDATE users SET otp_code = ?, otp_expires_at = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE id = ?')->execute([$otp, $userId]);
send_otp_sms($sellerPhone, $otp);

header('Location: /listing.php?id=' . $listingId . '&new=1&verify_user=' . $userId);
exit;
