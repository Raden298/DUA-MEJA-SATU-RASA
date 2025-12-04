<?php
// includes/footer.php
?>
<footer class="site-footer">
    <div class="footer-inner">
        <p>&copy; <?= date('Y') ?> Dua Meja Satu Rasa. All rights reserved.</p>
    </div>
</footer>

<script>
(function () {
    const body = document.body;
    const THEME_KEY = 'dmr_theme';

    const saved = localStorage.getItem(THEME_KEY);
    if (saved === 'light' || saved === 'dark') {
        body.classList.remove('theme-light', 'theme-dark');
        body.classList.add('theme-' + saved);
    }

    document.querySelectorAll('.theme-btn[data-theme]').forEach(btn => {
        btn.addEventListener('click', () => {
            const theme = btn.dataset.theme;
            body.classList.remove('theme-light', 'theme-dark');
            body.classList.add('theme-' + theme);
            localStorage.setItem(THEME_KEY, theme);
        });
    });
})();
</script>

<script src="/assets/js/script.js"></script>
<script>
document.querySelectorAll(".res-table-btn").forEach(btn => {
    btn.addEventListener("click", function() {
        let id = this.dataset.id;
        let name = this.dataset.name;

        document.getElementById("table_id").value = id;
        document.getElementById("selectedTableLabel").innerText = name;

        document.querySelectorAll(".res-table-btn").forEach(b => b.classList.remove("is-selected"));
        this.classList.add("is-selected");
    });
});
</script>

</body>
</html>