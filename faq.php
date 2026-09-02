<?php
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/includes/functions.php';

$faqs = [
    [
        'q' => 'How much is my phone worth in Pakistan?',
        'a' => 'It depends on the model, age, condition, and — critically — PTA approval status. Use our free valuation tool to get an instant estimate based on the current market price for your exact model plus deductions for condition, age, and PTA status.',
    ],
    [
        'q' => 'What is the PTA approved vs non-PTA price difference?',
        'a' => 'Non-PTA ("patched") phones can carry ongoing network-block risk and sell for significantly less than PTA-approved phones — often 40-60% less depending on the model. A blocked phone has almost no resale value since it cannot use a local SIM at all.',
    ],
    [
        'q' => 'Is this a trade-in service? Will you buy my phone?',
        'a' => 'No. WhatsMyPhonePrice.com never buys your phone. We give you a free estimated value, and if you choose, help you list it for real individual buyers to contact directly — you control the final sale.',
    ],
    [
        'q' => 'Is the estimated price guaranteed?',
        'a' => 'No — it is an Estimated Price based on the condition details you provide. The actual price you get depends on in-person inspection and negotiation with a real buyer.',
    ],
    [
        'q' => 'How do I check if a phone is PTA approved before buying?',
        'a' => 'Send the phone\'s IMEI via SMS to 8484, use the official PTA DVS app, or check dirbs.pta.gov.pk directly. See our PTA/IMEI Check page for step-by-step instructions.',
    ],
    [
        'q' => 'How do I avoid getting scammed when buying a used phone?',
        'a' => 'Never pay in advance, always meet in a safe public place, verify PTA status yourself, and make sure the seller can factory-reset the phone in front of you to prove it isn\'t iCloud- or FRP-locked. See our full Safety & Scam-Education Guide.',
    ],
    [
        'q' => 'Why do iPhones hold their value better than Android phones?',
        'a' => 'iPhones typically depreciate around 27-33% per year in Pakistan\'s resale market, while most Android phones depreciate 58-70% per year, due to stronger long-term demand and software support for iPhones.',
    ],
];

$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(static function ($f) {
        return [
            '@type' => 'Question',
            'name' => $f['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
        ];
    }, $faqs),
];
$schemaJsonLd = json_encode($schema);

$pageTitle = 'FAQ — Used Phone Valuation in Pakistan | WhatsMyPhonePrice.com';
$pageDescription = 'Answers to common questions about used phone valuation, PTA approval status, and safe peer-to-peer selling in Pakistan.';
require_once __DIR__ . '/includes/header.php';
?>

<h1>Frequently Asked Questions</h1>

<?php foreach ($faqs as $f): ?>
    <div class="card">
        <h2><?= e($f['q']) ?></h2>
        <p><?= e($f['a']) ?></p>
    </div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
