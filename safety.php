<?php
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = t('nav_safety') . ' — WhatsMyPhonePrice.com';
$pageDescription = 'Scam-education guide for buying and selling used phones in Pakistan: PTA checks, iCloud/FRP locks, and safe meetup spots.';
require_once __DIR__ . '/includes/header.php';

$meetupSpots = [
    'Karachi' => ['Dolmen Mall Clifton', 'Lucky One Mall', 'Saddar Hafeez Centre (daytime, with a friend)'],
    'Lahore' => ['Packages Mall', 'Emporium Mall', 'Hall Road (daytime, busy area)'],
    'Islamabad' => ['Centaurus Mall', 'Giga Mall', 'Safa Gold Mall'],
    'Rawalpindi' => ['Al-Hafeez Mall', 'Mall of Raja Bazaar area (daytime)'],
    'Faisalabad' => ['Kohinoor Mall', 'D Ground commercial area'],
];
?>

<?php if ($GLOBALS['CURRENT_LANG'] === 'ur'): ?>

<h1>حفاظتی رہنمائی اور فراڈ سے بچاؤ</h1>
<p>پاکستان میں استعمال شدہ فون خریدتے یا بیچتے وقت محفوظ رہنے کے لیے یہ گائیڈ پڑھیں۔</p>

<div class="card">
    <h2>فروخت سے پہلے فون کو ری سیٹ کریں</h2>
    <p>بیچنے سے پہلے اپنا ڈیٹا بیک اپ کریں اور iCloud/Google اکاؤنٹ سے سائن آؤٹ کرکے فون کو فیکٹری ری سیٹ کریں۔ اس سے خریدار کا اعتماد بڑھتا ہے اور تنازعہ نہیں ہوتا۔</p>
</div>

<div class="card">
    <h2>PTA کی حیثیت ضرور چیک کریں</h2>
    <p>خریدنے سے پہلے IMEI نمبر 8484 پر SMS کریں یا <a href="/imei-check.php">PTA/IMEI چیک پیج</a> دیکھیں۔ نان-PTA یا بلاک شدہ فون کی قیمت بہت کم ہوتی ہے۔</p>
</div>

<div class="card">
    <h2>ایڈوانس پیمنٹ فراڈ سے بچیں</h2>
    <ul class="checklist">
        <li>کبھی بھی فون دیکھے بغیر پیشگی رقم نہ بھیجیں۔</li>
        <li>ہمیشہ ذاتی طور پر ملاقات کریں اور فون کو اچھی طرح چیک کریں۔</li>
        <li>عوامی اور محفوظ جگہ پر ملیں — نیچے تجویز کردہ مقامات دیکھیں۔</li>
        <li><a href="/icloud-frp-check.php">iCloud/FRP لاک چیک لسٹ</a> کے مطابق فون کو ری سیٹ کروا کر تصدیق کریں۔</li>
    </ul>
</div>

<div class="card">
    <h2>محفوظ ملاقات کی جگہیں</h2>
    <?php foreach ($meetupSpots as $city => $spots): ?>
        <p><strong><?= e($city) ?>:</strong> <?= e(implode(', ', $spots)) ?></p>
    <?php endforeach; ?>
</div>

<?php else: ?>

<h1>Safety &amp; Scam-Education Guide</h1>
<p>Read this before you buy or sell a used phone in Pakistan.</p>

<div class="card">
    <h2>Sellers: reset your phone before handing it over</h2>
    <p>Back up your data, then sign out of iCloud/Google and factory-reset the device before the meetup. It builds buyer trust and avoids disputes later.</p>
</div>

<div class="card">
    <h2>Always verify PTA status yourself</h2>
    <p>Send the IMEI to 8484 or use the <a href="/imei-check.php">PTA/IMEI check page</a> before agreeing to buy. Non-PTA or blocked phones are worth far less — don't rely on the seller's word.</p>
</div>

<div class="card">
    <h2>Avoid advance-payment scams</h2>
    <ul class="checklist">
        <li>Never send money before seeing and testing the actual phone.</li>
        <li>Always meet in person and inspect the device thoroughly.</li>
        <li>Meet in a busy, public place — see suggested spots below.</li>
        <li>Follow the <a href="/icloud-frp-check.php">iCloud/FRP lock checklist</a> — watch the seller reset the phone in front of you.</li>
        <li>Check the condition card on the listing matches the phone in hand — mismatches are a red flag.</li>
    </ul>
</div>

<div class="card">
    <h2>Suggested safe meetup spots by city</h2>
    <?php foreach ($meetupSpots as $city => $spots): ?>
        <p><strong><?= e($city) ?>:</strong> <?= e(implode(', ', $spots)) ?></p>
    <?php endforeach; ?>
    <p class="hint">Not in this list? Pick a busy mall food court or bank branch in your area — avoid isolated locations.</p>
</div>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
