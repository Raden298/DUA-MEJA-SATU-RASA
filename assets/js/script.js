/* =========================================
   1. SCROLLSPY + CLICK DENGAN OFFSET (MENU PAGE)
   ========================================= */
document.addEventListener("DOMContentLoaded", function () {

    const sections = document.querySelectorAll(".menu-section");
    const navLinks = document.querySelectorAll(".menu-tab-link");

    let isClicking = false;

    if (sections.length > 0 && navLinks.length > 0) {

        // --- fungsi helper: hitung offset (header + tabs) ---
        function getOffset() {
            const header = document.querySelector(".site-header");
            const tabs   = document.querySelector(".menu-tabs");

            let offset = 0;
            if (header) offset += header.offsetHeight;
            if (tabs)   offset += tabs.offsetHeight;

            // kasih jarak kecil biar section turunnya enak
            offset += 20;

            return offset;
        }

        // --- SCROLLSPY: nentuin tab mana yang aktif pas discroll ---
        function onScroll() {
            if (isClicking) return; // lagi animasi karena klik, jangan ganggu

            // kita pakai titik TENGAH layar
            let scrollPos = window.scrollY + (window.innerHeight / 2);
            let currentId = "";

            sections.forEach((section) => {
                if (section.offsetTop - 50 <= scrollPos) {
                    currentId = section.getAttribute("id");
                }
            });

            navLinks.forEach((link) => {
                link.classList.remove("is-active");
                if (link.getAttribute("href") === "#" + currentId) {
                    link.classList.add("is-active");
                }
            });
        }

        window.addEventListener("scroll", onScroll);
        onScroll(); // initial

        // --- KLIK TAB: scroll halus + aktifin tab + lock scrollspy sementara ---
        navLinks.forEach(link => {
            link.addEventListener("click", function (e) {
                const href = this.getAttribute("href");

                if (!href || !href.startsWith("#")) return;

                e.preventDefault(); // jangan pakai default anchor (biar kita yg atur)

                const targetId = href.substring(1);
                const target   = document.getElementById(targetId);
                if (!target) return;

                isClicking = true;

                // set active tab langsung
                navLinks.forEach(l => l.classList.remove("is-active"));
                this.classList.add("is-active");

                // hitung posisi scroll dengan offset header + tabs
                const rect      = target.getBoundingClientRect();
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                const offset    = getOffset();
                const finalTop  = rect.top + scrollTop - offset;

                window.scrollTo({
                    top: finalTop,
                    behavior: "smooth"
                });

                // setelah animasi selesai (0.8s kira-kira), hidupin lagi scrollspy
                setTimeout(() => {
                    isClicking = false;
                    onScroll(); // sync posisi terakhir
                }, 800);
            });
        });
    }
});


/* =========================================
   PAGE TRANSITION FADE IN / FADE OUT
   ========================================= */
document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    body.classList.add("page-transition");

    // Fade IN
    setTimeout(() => {
        body.classList.add("page-loaded");
    }, 10);

    // Fade OUT sebelum pindah halaman
    document.querySelectorAll("a").forEach(a => {
        a.addEventListener("click", function (e) {
            const url = this.href;

            // biar ga fade kalo link anchor (#)
            if (!url || url.includes("#")) return;

            e.preventDefault();

            body.classList.remove("page-loaded"); // fade-out

            setTimeout(() => {
                window.location.href = url;
            }, 350); // sama dengan CSS duration
        });
    });
});
// =========================================
//  RESERVATION – PILIH MEJA
// =========================================
document.addEventListener('DOMContentLoaded', () => {
    const tableBtns = document.querySelectorAll('.res-table-btn');
    const hiddenInput = document.querySelector('#table_id');
    const label = document.querySelector('#selectedTableLabel');

    if (!tableBtns.length || !hiddenInput) return;

    tableBtns.forEach(btn => {
        if (btn.classList.contains('is-taken')) return; // meja penuh, jangan bisa diklik

        btn.addEventListener('click', () => {
            tableBtns.forEach(b => b.classList.remove('is-selected'));
            btn.classList.add('is-selected');

            hiddenInput.value = btn.dataset.id || '';
            if (label) {
                label.textContent = btn.dataset.name || 'Belum dipilih';
            }
        });
    });
});
