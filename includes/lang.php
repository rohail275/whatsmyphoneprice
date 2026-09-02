<?php
// Bilingual support: ?lang=ur switches and remembers via cookie; default English.

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ur'], true)) {
    $_lang = $_GET['lang'];
    setcookie('lang', $_lang, time() + 60 * 60 * 24 * 365, '/');
} else {
    $_lang = $_COOKIE['lang'] ?? 'en';
}
if (!in_array($_lang, ['en', 'ur'], true)) {
    $_lang = 'en';
}

$GLOBALS['CURRENT_LANG'] = $_lang;
$GLOBALS['STRINGS'] = require __DIR__ . '/../lang/' . $_lang . '.php';

function t(string $key): string
{
    return $GLOBALS['STRINGS'][$key] ?? $key;
}

function lang_dir(): string
{
    return $GLOBALS['CURRENT_LANG'] === 'ur' ? 'rtl' : 'ltr';
}
