</main>
<footer class="site-footer">
    <div class="container footer-inner">
        <p><?= e(t('site_tagline')) ?></p>
        <p class="footer-links">
            <a href="/safety.php"><?= e(t('nav_safety')) ?></a> ·
            <a href="/faq.php"><?= e(t('nav_faq')) ?></a> ·
            <a href="/imei-check.php"><?= e(t('nav_imei_check')) ?></a>
        </p>
        <p class="disclaimer">All prices shown are <strong><?= e(t('estimated_price')) ?></strong> estimates only — not guaranteed or final offers. WhatsMyPhonePrice.com is never the buyer; all sales are peer-to-peer between users.</p>
        <p class="copyright">&copy; <?= date('Y') ?> WhatsMyPhonePrice.com</p>
    </div>
</footer>
<?php if (!empty($extraFooterScripts)): foreach ($extraFooterScripts as $src): ?>
<script src="<?= e($src) ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>
