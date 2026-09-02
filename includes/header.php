<?php
require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? "WhatsMyPhonePrice.com — Used Phone Valuation Pakistan";
$pageDescription = $pageDescription ?? "Get a free, instant estimated resale value for your used phone in Pakistan, then optionally list it for sale directly to real buyers.";
?>
<!DOCTYPE html>
<html lang="<?= e($GLOBALS['CURRENT_LANG']) ?>" dir="<?= lang_dir() ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDescription) ?>">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
<?php if (!empty($schemaJsonLd)): ?>
<script type="application/ld+json"><?= $schemaJsonLd ?></script>
<?php endif; ?>
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="logo" href="/index.php">What'sMyPhonePrice</a>
        <nav class="main-nav">
            <a href="/index.php"><?= e(t('nav_home')) ?></a>
            <a href="/valuation.php"><?= e(t('nav_valuation')) ?></a>
            <a href="/listings.php"><?= e(t('nav_listings')) ?></a>
            <a href="/imei-check.php"><?= e(t('nav_imei_check')) ?></a>
            <a href="/safety.php"><?= e(t('nav_safety')) ?></a>
            <a href="/faq.php"><?= e(t('nav_faq')) ?></a>
        </nav>
        <div class="lang-switch">
            <a href="?lang=en" class="<?= $GLOBALS['CURRENT_LANG'] === 'en' ? 'active' : '' ?>">EN</a>
            <a href="?lang=ur" class="<?= $GLOBALS['CURRENT_LANG'] === 'ur' ? 'active' : '' ?>">اردو</a>
        </div>
    </div>
</header>
<main class="container">
