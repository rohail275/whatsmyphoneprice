<?php
// Groq API config (used for the plain-language price explainer — see
// README "AI Usage — Explicit Scope"). Real key never goes in this file
// (it's committed to git); create config/groq.local.php instead, same
// git-ignored pattern as config/db.local.php.

$localConfig = __DIR__ . '/groq.local.php';
if (is_file($localConfig)) {
    require $localConfig;
} else {
    define('GROQ_API_KEY', '');
}

// Fast, cheap Groq-hosted model — plenty for a 2-3 sentence explanation.
define('GROQ_MODEL', 'llama-3.1-8b-instant');
