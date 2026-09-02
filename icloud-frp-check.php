<?php
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'iCloud & FRP Lock Checklist — WhatsMyPhonePrice.com';
$pageDescription = 'How to check an iPhone is not iCloud-locked, and an Android is not FRP-locked, before you pay — a common scam in Pakistan.';
require_once __DIR__ . '/includes/header.php';
?>

<h1>iCloud &amp; FRP Lock Checklist</h1>
<p>An iCloud-locked iPhone or an Android with Factory Reset Protection (FRP) enabled can become an expensive brick the moment the previous owner signs out of their account remotely. This is one of the most common phone scams in Pakistan — always check <strong>in person, before paying</strong>.</p>

<div class="card">
    <h2>For iPhones — iCloud Lock</h2>
    <ul class="checklist">
        <li>Ask the seller to fully sign out of iCloud in front of you: Settings → [their name] → Sign Out.</li>
        <li>Ask them to erase the phone: Settings → General → Transfer or Reset iPhone → Erase All Content and Settings.</li>
        <li>Watch the phone reboot to the initial "Hello" setup screen — this proves it is not tied to their Apple ID.</li>
        <li>Do NOT accept "I'll remove it after you pay" — always verify before money changes hands.</li>
        <li>You can also check Settings → General → About → look for "Find My iPhone" status, or use Apple's official <a href="https://checkcoverage.apple.com/" target="_blank" rel="noopener">Check Coverage</a> tool with the serial number.</li>
    </ul>
</div>

<div class="card">
    <h2>For Android — Factory Reset Protection (FRP)</h2>
    <ul class="checklist">
        <li>Ask the seller to remove their Google account: Settings → Accounts → Google → Remove Account.</li>
        <li>Ask them to factory reset the device in front of you: Settings → System → Reset options → Erase all data.</li>
        <li>After reboot, complete setup yourself without needing the previous owner's Google password — if it asks for their old account, FRP is still active. Do not buy.</li>
    </ul>
</div>

<div class="alert alert-danger">
    <strong>Never pay before this check is done in person.</strong> A phone that looks fine on but is still linked to someone else's account can be remotely locked at any time after the sale.
</div>

<p><a href="/safety.php">See the full Safe Deal Checklist</a> · <a href="/imei-check.php"><?= e(t('nav_imei_check')) ?></a></p>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
