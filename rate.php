<?php
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/includes/functions.php';

$db = get_db();
$listingId = (int) ($_GET['listing_id'] ?? $_POST['listing_id'] ?? 0);
$stmt = $db->prepare('SELECT l.id, l.title, l.user_id AS seller_id, u.name AS seller_name FROM listings l JOIN users u ON u.id = l.user_id WHERE l.id = ?');
$stmt->execute([$listingId]);
$listing = $stmt->fetch();

if (!$listing) {
    http_response_code(404);
    $pageTitle = 'Listing not found';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="card"><p>Listing not found.</p></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$submitted = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raterName = trim((string) ($_POST['rater_name'] ?? ''));
    $raterPhone = preg_replace('/[^0-9]/', '', (string) ($_POST['rater_phone'] ?? ''));
    $rating = (int) ($_POST['rating'] ?? 0);
    $comment = trim((string) ($_POST['comment'] ?? ''));

    if ($raterName === '' || $raterPhone === '' || $rating < 1 || $rating > 5) {
        $error = 'Please fill in your name, phone number, and a rating from 1 to 5.';
    } else {
        $userStmt = $db->prepare('SELECT id FROM users WHERE phone_number = ?');
        $userStmt->execute([$raterPhone]);
        $raterUser = $userStmt->fetch();
        if ($raterUser) {
            $raterId = (int) $raterUser['id'];
        } else {
            $db->prepare('INSERT INTO users (name, phone_number) VALUES (?, ?)')->execute([$raterName, $raterPhone]);
            $raterId = (int) $db->lastInsertId();
        }

        if ($raterId === (int) $listing['seller_id']) {
            $error = 'You cannot rate yourself.';
        } else {
            $db->prepare('INSERT INTO ratings (listing_id, rater_user_id, rated_user_id, rating, comment) VALUES (?,?,?,?,?)')
                ->execute([$listingId, $raterId, $listing['seller_id'], $rating, $comment ?: null]);

            $agg = $db->prepare('SELECT AVG(rating) AS avg_rating, COUNT(*) AS cnt FROM ratings WHERE rated_user_id = ?');
            $agg->execute([$listing['seller_id']]);
            $stats = $agg->fetch();
            $db->prepare('UPDATE users SET rating_avg = ?, rating_count = ? WHERE id = ?')
                ->execute([round((float) $stats['avg_rating'], 2), (int) $stats['cnt'], $listing['seller_id']]);

            $submitted = true;
        }
    }
}

$pageTitle = 'Rate ' . $listing['seller_name'] . ' — WhatsMyPhonePrice.com';
require_once __DIR__ . '/includes/header.php';
?>

<h1>Rate this deal</h1>
<p>Listing: <strong><?= e($listing['title']) ?></strong> · Seller: <strong><?= e($listing['seller_name']) ?></strong></p>

<?php if ($submitted): ?>
    <div class="alert alert-info">Thanks — your rating has been recorded.</div>
    <a class="btn" href="/listing.php?id=<?= (int) $listingId ?>">Back to listing</a>
<?php else: ?>
    <?php if ($error): ?><p class="alert alert-danger"><?= e($error) ?></p><?php endif; ?>
    <form class="card" method="post">
        <input type="hidden" name="listing_id" value="<?= (int) $listingId ?>">
        <div class="form-group">
            <label for="rater_name">Your name</label>
            <input type="text" id="rater_name" name="rater_name" maxlength="100" required>
        </div>
        <div class="form-group">
            <label for="rater_phone">Your phone number</label>
            <input type="tel" id="rater_phone" name="rater_phone" pattern="03[0-9]{9}" maxlength="11" required>
        </div>
        <div class="form-group">
            <label for="rating">Rating</label>
            <select id="rating" name="rating" required>
                <option value="">-- select --</option>
                <option value="5">5 — Excellent</option>
                <option value="4">4 — Good</option>
                <option value="3">3 — Okay</option>
                <option value="2">2 — Poor</option>
                <option value="1">1 — Bad experience</option>
            </select>
        </div>
        <div class="form-group">
            <label for="comment">Comment (optional)</label>
            <textarea id="comment" name="comment" rows="3"></textarea>
        </div>
        <button type="submit" class="btn"><?= e(t('submit')) ?></button>
    </form>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
