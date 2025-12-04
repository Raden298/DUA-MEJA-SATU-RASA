<?php
require_once __DIR__ . '/../includes/header.php';

$lang = $_SESSION['lang'] ?? 'id';

// --- FUNGSI PENGAMAN KHUSUS FILE INI ---
function safe($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * 1. AMBIL SIGNATURE MENU
 */
$signatureMenus = [];
$sqlSig = "
    SELECT id, name, description_id, description_en, price, image_path, category_id
    FROM menu_items
    WHERE is_signature = 1 AND is_active = 1
    ORDER BY name ASC
";
$resSig = $conn->query($sqlSig);
if ($resSig) {
    while ($row = $resSig->fetch_assoc()) {
        $signatureMenus[] = $row;
    }
}

/**
 * 2. AMBIL SEMUA MENU PER KATEGORI
 */
$itemsByCat = [];
$categories = [];

$sqlAll = "
    SELECT mi.id,
           mi.name,
           mi.description_id,
           mi.description_en,
           mi.price,
           mi.image_path,
           mi.category_id,
           mc.name_id,
           mc.name_en
    FROM menu_items mi
    JOIN menu_categories mc ON mi.category_id = mc.id
    WHERE mi.is_active = 1
    ORDER BY mc.id ASC, mi.name ASC
";
$resAll = $conn->query($sqlAll);

if ($resAll) {
    while ($row = $resAll->fetch_assoc()) {
        $cid = (int)$row['category_id'];
        $catName = ($lang === 'en') ? $row['name_en'] : $row['name_id'];

        if (!isset($categories[$cid])) {
            $categories[$cid] = $catName;
        }
        $itemsByCat[$cid][] = $row;
    }
}

// 3. SIAPKAN TAB NAVIGASI
$tabs = [];

// Tab 1: Signature
if (!empty($signatureMenus)) {
    $tabs[] = ['id' => 'menu-signature', 'label' => 'Signature'];
}

// Tab Sisa: Kategori Database (Skip ID 1 karena Signature)
foreach ($categories as $cid => $cname) {
    if ($cid == 1) continue; 
    $tabs[] = ['id' => 'menu-cat-' . $cid, 'label' => $cname];
}
?>

<main class="menu-main">

    <section class="menu-hero">
        <div class="menu-hero-inner">
            <p class="menu-kicker">Menu Selezione</p>
            <h1 class="menu-title">Dua Meja Satu Rasa</h1>
            
            <p class="menu-subtitle" style="font-family: 'Georgia', 'Times New Roman', serif; font-size: 15px; font-style: italic; opacity: 0.9;">
                Harga belum termasuk pajak &amp; layanan. Setiap hidangan diracik untuk menghadirkan
                harmoni antara cita rasa Indonesia dan teknik Italia.
            </p>
        </div>
    </section>

    <nav class="menu-tabs">
        <?php foreach ($tabs as $index => $tab): ?>
            <a href="#<?= safe($tab['id']) ?>"
               class="menu-tab-link <?= $index === 0 ? 'is-active' : '' ?>">
                <?= safe($tab['label']) ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="menu-content">

        <?php if (!empty($signatureMenus)): ?>
            <section id="menu-signature" class="menu-section">
                <div class="menu-section-header">
                    <div class="menu-section-divider"></div>
                    <span class="menu-section-kicker">Signature</span>
                    <div class="menu-section-divider"></div>
                </div>

                <div class="menu-section-grid">
                    <?php foreach ($signatureMenus as $item): ?>
                        <?php
                        $title = $item['name'];
                        $desc  = $lang === 'en'
                            ? ($item['description_en'] ?: $item['description_id'])
                            : ($item['description_id'] ?: $item['description_en']);

                        // Logika Gambar Pintar
                        $filename = !empty($item['image_path']) ? $item['image_path'] : $title . '.jpg';
                        $physicalPath = __DIR__ . '/../assets/img/' . $filename;
                        
                        if (file_exists($physicalPath)) {
                            $imgSrc = "../assets/img/" . rawurlencode($filename);
                        } else {
                            $imgSrc = "https://placehold.co/600x400/0f172a/FFF?text=" . rawurlencode($title);
                        }
                        ?>

                        <article class="menu-item-card">
                            <div class="menu-item-image">
                                <img src="<?= safe($imgSrc) ?>" alt="<?= safe($title) ?>">
                            </div>
                            <div class="menu-item-body">
                                <div class="menu-item-header-line">
                                    <div class="menu-item-text">
                                        <h3 class="menu-item-title"><?= safe($title) ?></h3>
                                        <?php if (!empty($desc)): ?>
                                            <p class="menu-item-desc"><?= safe($desc) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="menu-item-price-block">
                                        <span class="menu-item-price">
                                            Rp <?= number_format($item['price'], 0, ',', '.') ?>
                                        </span>
                                        <form method="post" action="/cart_action.php">
                                            <input type="hidden" name="menu_id" value="<?= (int)$item['id'] ?>">
                                            <input type="hidden" name="action" value="add">
                                            <!-- balik lagi ke tab Signature -->
                                            <input type="hidden" name="redirect" value="/pages/menu.php#menu-signature">
                                            <button type="submit" class="menu-add-btn">Tambahkan</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="menu-item-dots-line"></div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php foreach ($categories as $cid => $cname): ?>
            <?php if ($cid == 1) continue; ?>
            
            <section id="menu-cat-<?= (int)$cid ?>" class="menu-section">
                <div class="menu-section-header">
                    <div class="menu-section-divider"></div>
                    <span class="menu-section-kicker"><?= safe(strtoupper($cname)) ?></span>
                    <div class="menu-section-divider"></div>
                </div>

                <?php if (empty($itemsByCat[$cid])): ?>
                    <p class="menu-empty-text">Belum ada menu untuk kategori ini.</p>
                <?php else: ?>
                    <div class="menu-section-grid">
                        <?php foreach ($itemsByCat[$cid] as $item): ?>
                            <?php
                            $title = $item['name'];
                            $desc  = $lang === 'en'
                                ? ($item['description_en'] ?: $item['description_id'])
                                : ($item['description_id'] ?: $item['description_en']);

                            $filename = !empty($item['image_path']) ? $item['image_path'] : $title . '.jpg';
                            $physicalPath = __DIR__ . '/../assets/img/' . $filename;
                            
                            if (file_exists($physicalPath)) {
                                $imgSrc = "../assets/img/" . rawurlencode($filename);
                            } else {
                                $imgSrc = "https://placehold.co/600x400/0f172a/FFF?text=" . rawurlencode($title);
                            }
                            ?>

                            <article class="menu-item-card">
                                <div class="menu-item-image">
                                    <img src="<?= safe($imgSrc) ?>" alt="<?= safe($title) ?>">
                                </div>
                                <div class="menu-item-body">
                                    <div class="menu-item-header-line">
                                        <div class="menu-item-text">
                                            <h3 class="menu-item-title"><?= safe($title) ?></h3>
                                            <?php if (!empty($desc)): ?>
                                                <p class="menu-item-desc"><?= safe($desc) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="menu-item-price-block">
                                            <span class="menu-item-price">
                                                Rp <?= number_format($item['price'], 0, ',', '.') ?>
                                            </span>
                                            <form method="post" action="/cart_action.php">
                                                <input type="hidden" name="menu_id" value="<?= (int)$item['id'] ?>">
                                                <input type="hidden" name="action" value="add">
                                                <!-- balik ke kategori yang sama -->
                                                <input type="hidden" name="redirect" value="/pages/menu.php#menu-cat-<?= (int)$cid ?>">
                                                <button type="submit" class="menu-add-btn">Tambahkan</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="menu-item-dots-line"></div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>

    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
