<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$userId = (int) ($_POST['user_id'] ?? 0);
$otp = trim((string) ($_POST['otp_code'] ?? ''));
$redirect = (string) ($_POST['redirect'] ?? '/index.php');
if (strpos($redirect, '/') !== 0 || strpos($redirect, '//') === 0) {
    $redirect = '/index.php'; // only allow same-site relative redirects
}

$db = get_db();
$stmt = $db->prepare('SELECT id FROM users WHERE id = ? AND otp_code = ? AND otp_expires_at >= NOW()');
$stmt->execute([$userId, $otp]);

$separator = strpos($redirect, '?') === false ? '?' : '&';

if ($stmt->fetch()) {
    $db->prepare('UPDATE users SET phone_verified = 1, otp_code = NULL, otp_expires_at = NULL WHERE id = ?')->execute([$userId]);
    header('Location: ' . $redirect . $separator . 'verified=1');
} else {
    header('Location: ' . $redirect . $separator . 'verify_user=' . $userId . '&verify_error=1');
}
exit;
