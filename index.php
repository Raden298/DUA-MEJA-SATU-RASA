<?php
require_once __DIR__ . '/includes/header.php';


// Ambil data signature menu
$signatureMenus = [];
$sql = "
    SELECT id, name, description_id, description_en, price
    FROM menu_items 
    WHERE is_signature = 1 AND is_active = 1
";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $signatureMenus[] = $row;
    }
}

$lang = $_SESSION['lang'] ?? 'id';
?>

<main class="home-main">
    <section class="fusion-hero fusion-hero--index">
    <!-- BACKGROUND IMAGE -->
    <div class="fusion-hero-bg">
        <img src="assets/img/Background Fushion.jpg" alt="Background Fusion">
    </div>

    <!-- OVERLAY GELAP / TERANG -->
    <div class="fusion-hero-overlay"></div>

    <!-- ISI HERO -->
    <div class="fusion-hero-content">
        <div class="hero--inside-fusion">
            <div class="hero-content">
                <p class="hero-kicker">Fusion Rasa Indonesia &amp; Italia</p>
                <h1 class="hero-title">Dua Budaya, Satu Meja, Satu Rasa</h1>
                <p class="hero-subtitle">
                    Harmoni cita rasa Nusantara dan keanggunan Italia dalam setiap piring.
                </p>

                <div class="hero-actions">
                    <a href="pages/reservation.php" class="btn-primary">Make a Reservation</a>
                    <a href="pages/menu.php" class="btn-secondary">View Full Menu</a>
                </div>

                <div class="hero-note">
                    <span class="dot"></span>
                    <span>Berlaku Minimum Transaksi Rp 1.000.000 Per Meja</span>
                </div>
            </div>

            <div class="hero-visual">
                <?php
                $highlightTitle = 'Sambal Rendang Pasta';
                $highlightDesc  = 'Pasta Al Dente dengan Saus Rendang Creamy dan sentuhan Sambal Terasi.';
                $highlightImg   = 'Sambal Rendang Pasta.jpg';
                ?>
                <div class="hero-card">
                    <p class="hero-card-label">Tonight’s Highlight</p>
                    <div class="hero-highlight-thumb">
                        <img src="assets/img/<?= rawurlencode($highlightImg) ?>" alt="<?= esc($highlightTitle) ?>">
                    </div>
                    <p class="hero-card-name"><?= esc($highlightTitle) ?></p>
                    <p class="hero-card-text"><?= esc($highlightDesc) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>


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
                <p style="text-align:center; color:#ccc;">Menu signature sedang disiapkan.</p>
            <?php else: ?>
                <div class="signature-grid">
                    <?php foreach ($signatureMenus as $item): ?>
                        <?php
                        $title = $item['name'];
                        $desc  = $lang === 'en'
                            ? ($item['description_en'] ?: $item['description_id'])
                            : ($item['description_id'] ?: $item['description_en']);

                        $imgFile = $title . '.jpg';
                        ?>
                        
                        <article class="signature-card" style="text-align: center;">
                            <div class="signature-thumb">
                                <img src="assets/img/<?= rawurlencode($imgFile) ?>" 
                                     alt="<?= esc($title) ?>">
                            </div>

                            <div class="signature-body">
                                <h3 class="signature-title" 
                                    style="font-family: 'Georgia', serif; font-size: 20px; margin-bottom: 8px;">
                                    <?= esc($title) ?>
                                </h3>
                                
                                <p class="signature-desc" 
                                   style="font-family: 'Georgia', serif; 
                                          font-size: 15px; 
                                          line-height: 1.8; 
                                          margin-top: 10px; 
                                          color: #cbd5e1;">
                                    <?= esc($desc) ?>
                                </p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

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
?>