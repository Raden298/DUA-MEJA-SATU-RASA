<?php
// includes/footer.php
?>
<footer class="site-footer">
    <div class="footer-inner">
        <p>&copy; <?= date('Y') ?> Dua Meja Satu Rasa. All rights reserved.</p>
    </div>
</footer><script>
(function () {
    const body = document.body;
    const THEME_KEY = 'dmr_theme';

    // set awal dari localStorage
    const saved = localStorage.getItem(THEME_KEY);
    if (saved === 'light' || saved === 'dark') {
        body.classList.remove('theme-light', 'theme-dark');
        body.classList.add('theme-' + saved);
    }

    // handle klik tombol
    document.querySelectorAll('.theme-btn[data-theme]').forEach(btn => {
        btn.addEventListener('click', () => {
            const theme = btn.dataset.theme; // "light" atau "dark"
            body.classList.remove('theme-light', 'theme-dark');
            body.classList.add('theme-' + theme);
            localStorage.setItem(THEME_KEY, theme);
        });
    });
})();
</script>

</body>
</html>
