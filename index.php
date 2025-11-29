<?php
require_once __DIR__ . '/includes/header.php';

// ambil 3 menu signature dari DB (kalau fungsi ini dipakai di tempat lain, biarin aja)
$signatures = getSignatureMenus($conn);

// bahasa
$lang = $_SESSION['lang'] ?? 'id';

// ambil ulang signature menu untuk section di bawah
$signatureMenus = [];
$sql = "
    SELECT id, name, description_id, description_en, price
    FROM menu_items 
    WHERE is_signature = 1 AND is_active = 1
";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $signatureMenus[] = $row;
}
?>
<main class="home-main">

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-content">
            <p class="hero-kicker">
                Fusion Rasa Indonesia &amp; Italia
            </p>
            <h1 class="hero-title">
                Dua Budaya, Satu Meja, Satu Rasa
            </h1>
            <p class="hero-subtitle">
                Harmoni cita rasa Nusantara dan keanggunan Italia dalam setiap piring.
            </p>

            <div class="hero-actions">
                <a href="pages/reservation.php" class="btn-primary">
                    Make a Reservation
                </a>
                <a href="pages/menu.php" class="btn-secondary">
                    View Full Menu
                </a>
            </div>

            <div class="hero-note">
                <span class="dot"></span>
                <span>Berlaku Minimum Transaksi Rp 1.000.000 Per Meja</span>
            </div>
        </div>

        <div class="hero-visual">
    <?php
    // data highlight lo, manual aja di sini
    $highlightTitle = 'Sambal Rendang Pasta';
    $highlightDesc  = 'Pasta Al Dente dengan Saus Rendang Creamy dan sentuhan Sambal Terasi.';
    $highlightImg   = 'Sambal Rendang Pasta.jpg'; // NAMA FILE YANG SUDAH ADA DI FOLDER assets/img
    ?>
    <div class="hero-card">
        <p class="hero-card-label">Tonight’s Highlight</p>

        <div class="hero-highlight-thumb">
            <img src="assets/img/<?= esc($highlightImg) ?>"
                 alt="<?= esc($highlightTitle) ?>">
        </div>

        <p class="hero-card-name"><?= esc($highlightTitle) ?></p>
        <p class="hero-card-text"><?= esc($highlightDesc) ?></p>
    </div>
</div>

    </section>

    <!-- SIGNATURE MENU SECTION (CUMA 1X) -->
    <section class="signature-section">
        <div class="container-narrow">
            <div class="section-header">
                <div class="section-header-main">
                    <p class="section-kicker">Chef’s Signature</p>
                    <h2>Signature Menu</h2>
                </div>
                <p class="section-header-desc">
                    Tiga hidangan pilihan chef yang merangkum perjalanan rasa
                    antara dapur Nusantara dan teknik Italia.
                </p>
            </div>

            <?php if (empty($signatureMenus)): ?>
                <p>Menu signature sedang disiapkan. Silakan cek kembali nanti.</p>
            <?php else: ?>
                <div class="signature-grid">
                    <?php foreach ($signatureMenus as $item): ?>
                        <?php
                        $title = $item['name'];
                        $desc  = $lang === 'en'
                            ? ($item['description_en'] ?: $item['description_id'])
                            : ($item['description_id'] ?: $item['description_en']);

                        // OPSI C: nama file gambar = judul + ".jpg"
                        $imgFile = $title . '.jpg';
                        ?>
                        
                        <article class="signature-card">
                            <div class="signature-thumb">
                                <img src="assets/img/<?= esc($imgFile) ?>" 
                                     alt="<?= esc($title) ?>">
                            </div>

                            <div class="signature-body">
                                <h3 class="signature-title"><?= esc($title) ?></h3>
                                <p class="signature-desc"><?= esc($desc) ?></p>
                                <span class="signature-price">
                                    Rp <?= number_format($item['price'], 0, ',', '.') ?>
                                </span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <!-- FILOSOFI SECTION -->
<section class="philosophy-section">
    <div class="philosophy-container">

        <div class="philosophy-icon">
            <span>🍃</span>
        </div>

        <h2 class="philosophy-title">Filosofi Dua Meja</h2>

        <div class="philosophy-divider"></div>

        <p class="philosophy-text">
            "Dua Meja" melambangkan pertemuan dua budaya besar di atas piring Anda.
            Meja pertama membawa kehangatan dan kekayaan rempah Indonesia.
            Meja kedua membawa keanggunan dan teknik presisi Italia.
        </p>

        <p class="philosophy-text">
            Di restoran ini, kami tidak sekadar mencampur, tapi menyatukan jiwa.
            Di sini, rendang menemukan jodohnya dalam pasta, dan gelato menemukan
            rumahnya dalam klepon. Ini adalah "Satu Rasa" yang tak terlupakan.
        </p>

    </div>
</section>


</main>

<?php
require_once __DIR__ . '/includes/footer.php';
