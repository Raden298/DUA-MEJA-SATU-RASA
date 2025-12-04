<?php
// header = koneksi + session + layout
require_once __DIR__ . '/../includes/header.php';

// ====== WAJIB LOGIN ======
if (!isset($_SESSION['user_id'])) {
    // kalau belum login, lempar ke login + info mau ke reservation
    header('Location: /auth/login.php?next=reservation');
    exit;
}

$userId = (int) $_SESSION['user_id'];

// ====== DATE & TIME (UNTUK CEK MEJA KOSONG/PENUH) ======
$date = $_GET['date'] ?? date('Y-m-d');
$time = $_GET['time'] ?? '19:00'; // default jam 7 malam

// sanitize dikit
$date = preg_replace('/[^0-9\-]/', '', $date);
$time = preg_replace('/[^0-9:]/', '', $time);

// ====== AMBIL DATA MEJA ======
$tables        = [];
$takenTableIds = [];

// struktur tabel lo: id | code | capacity | is_active
$sql = "
    SELECT id, code, capacity
    FROM tables
    WHERE is_active = 1
    ORDER BY id
";
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $tables[$row['id']] = $row;
    }
}

// ====== MEJA YANG SUDAH DIBOOKING DI TANGGAL + JAM INI ======
$stmt = $conn->prepare("
    SELECT table_id
    FROM reservations
    WHERE reservation_date = ?
      AND reservation_time = ?
      AND status IN ('pending','confirmed')
");
$stmt->bind_param('ss', $date, $time);
$stmt->execute();
$r = $stmt->get_result();
while ($row = $r->fetch_assoc()) {
    $takenTableIds[] = (int) $row['table_id'];
}

// ====== HANDLE FORM SUBMIT (SIMPAN RESERVASI) ======
$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reserveDate = $_POST['reservation_date'] ?? $date;
    $reserveTime = $_POST['reservation_time'] ?? $time;
    $guests      = (int) ($_POST['guests'] ?? 0);
    $tableId     = (int) ($_POST['table_id'] ?? 0);

    if (!$tableId || !isset($tables[$tableId])) {
        $errors[] = 'Silakan pilih meja terlebih dahulu.';
    }
    if (!$reserveDate) {
        $errors[] = 'Tanggal reservasi wajib diisi.';
    }
    if (!$reserveTime) {
        $errors[] = 'Jam reservasi wajib diisi.';
    }
    if ($guests <= 0) {
        $errors[] = 'Jumlah tamu minimal 1 orang.';
    }

    // cek lagi apakah meja masih kosong di tanggal/jam itu
    if (empty($errors)) {
        $check = $conn->prepare("
            SELECT COUNT(*) AS cnt
            FROM reservations
            WHERE table_id         = ?
              AND reservation_date = ?
              AND reservation_time = ?
              AND status IN ('pending','confirmed')
        ");
        $check->bind_param('iss', $tableId, $reserveDate, $reserveTime);
        $check->execute();
        $cntRow = $check->get_result()->fetch_assoc();
        $cnt    = $cntRow['cnt'] ?? 0;

        if ($cnt > 0) {
            $errors[] = 'Maaf, meja ini baru saja diambil tamu lain. Silakan pilih meja lain.';
        }
    }

    // insert kalau aman
    if (empty($errors)) {
        $ins = $conn->prepare("
            INSERT INTO reservations (
                user_id, table_id, reservation_date, reservation_time, guests, status
            ) VALUES (
                ?, ?, ?, ?, ?, 'pending'
            )
        ");
        $ins->bind_param('iissi', $userId, $tableId, $reserveDate, $reserveTime, $guests);

        if ($ins->execute()) {
            $success = 'Reservasi berhasil dibuat. Silakan lanjutkan pembayaran.';
        } else {
            $errors[] = 'Gagal menyimpan reservasi. Coba lagi beberapa saat.';
        }
    }

    // update tanggal/jam yg lagi diliat
    $date = $reserveDate;
    $time = $reserveTime;

    // refresh list meja yang ke-book buat kombinasi tanggal/jam yg baru
    $takenTableIds = [];
    $stmt = $conn->prepare("
        SELECT table_id
        FROM reservations
        WHERE reservation_date = ?
          AND reservation_time = ?
          AND status IN ('pending','confirmed')
    ");
    $stmt->bind_param('ss', $date, $time);
    $stmt->execute();
    $r = $stmt->get_result();
    while ($row = $r->fetch_assoc()) {
        $takenTableIds[] = (int) $row['table_id'];
    }
}
?>

<main class="reservation-main">
    <section class="reservation-layout">

        <!-- KARTU FORM RESERVASI -->
        <article class="reservation-card">
            <header class="reservation-card-header">
                <p class="reservation-kicker">Step 1 • Reservation</p>
                <h1 class="reservation-title">Pilih Jadwal &amp; Meja</h1>
                <p class="reservation-subtitle">
                    Tentukan tanggal, Jam, dan Jumlah Tamu. Meja yang tersedia akan Berwarna Hijau,
                    sedangkan yang sudah terisi Berwarna Merah.
                </p>
            </header>

            <?php if (!empty($errors)): ?>
                <div class="reservation-alert reservation-alert-error">
                    <?php foreach ($errors as $e): ?>
                        <p><?= esc($e) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="reservation-alert reservation-alert-success">
                    <p><?= esc($success) ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="reservation-form">
                <div class="reservation-field">
                    <label for="reservation_date">Tanggal</label>
                    <input
                        type="date"
                        id="reservation_date"
                        name="reservation_date"
                        value="<?= esc($date) ?>"
                        required
                    >
                </div>

                <div class="reservation-field">
                    <label for="reservation_time">Jam</label>
                    <input
                        type="time"
                        id="reservation_time"
                        name="reservation_time"
                        value="<?= esc($time) ?>"
                        required
                    >
                </div>

                <div class="reservation-field">
                    <label for="guests">Jumlah Tamu</label>
                    <input
                        type="number"
                        id="guests"
                        name="guests"
                        min="1"
                        max="20"
                        value="<?= isset($_POST['guests']) ? (int)$_POST['guests'] : 2 ?>"
                        required
                    >
                </div>

                <div class="reservation-selected">
                    <span>Meja Terpilih:</span>
                    <strong id="selectedTableLabel">
                        <?php
                        if (isset($_POST['table_id']) &&
                            $_POST['table_id'] &&
                            isset($tables[(int)$_POST['table_id']])
                        ) {
                            echo esc($tables[(int)$_POST['table_id']]['code']);
                        } else {
                            echo 'Belum dipilih';
                        }
                        ?>
                    </strong>
                </div>

                <input
                    type="hidden"
                    name="table_id"
                    id="table_id"
                    value="<?= isset($_POST['table_id']) ? (int)$_POST['table_id'] : '' ?>"
                >

                <button type="submit" class="reservation-submit-btn">
                    Reservasi Sekarang
                </button>
            </form>

            <div class="reservation-legend">
                <span class="legend-item">
                    <span class="legend-dot legend-available"></span> Tersedia
                </span>
                <span class="legend-item">
                    <span class="legend-dot legend-taken"></span> Terisi
                </span>
                <span class="legend-item">
                    <span class="legend-dot legend-selected"></span> Meja Pilihan Anda
                </span>
            </div>
        </article>

        <!-- LAYOUT MEJA (STYLE XXI) -->
        <article class="reservation-room">
            <h2 class="room-title">Layout Meja</h2>
            <p class="room-subtitle">
                Pilih meja dengan menekan salah satu kotak di bawah.
                Hijau menandakan meja kosong, merah menandakan sudah terisi.
            </p>

            <div class="room-screen">Kitchen</div>

            <div class="room-grid">
                <?php if (empty($tables)): ?>
                    <p style="color:#9ca3af;">Belum ada data meja di sistem.</p>
                <?php else: ?>
                    <?php
                    $selectedId = isset($_POST['table_id']) ? (int)$_POST['table_id'] : 0;
                    foreach ($tables as $t):
                        $id   = (int) $t['id'];
                        $name = $t['code'];          // PAKE code, BUKAN name
                        $cap  = (int) $t['capacity'];
                        $isTaken = in_array($id, $takenTableIds, true);
                        ?>
                        <button
                            type="button"
                            class="res-table-btn
                                   <?= $isTaken ? 'is-taken' : 'is-available' ?>
                                   <?= ($selectedId === $id) ? 'is-selected' : '' ?>"
                            data-id="<?= $id ?>"
                            data-name="<?= esc($name) ?>"
                            <?= $isTaken ? 'disabled' : '' ?>
                        >
                            <span class="res-table-name"><?= esc($name) ?></span>
                            <span class="res-table-cap"><?= $cap ?> pax</span>
                        </button>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </article>

    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons      = document.querySelectorAll('.res-table-btn');
    const hiddenInput  = document.getElementById('table_id');
    const label        = document.getElementById('selectedTableLabel');

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            if (btn.classList.contains('is-taken')) return;

            buttons.forEach(b => b.classList.remove('is-selected'));
            btn.classList.add('is-selected');

            const id   = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');

            hiddenInput.value = id;
            if (label) label.textContent = name;
        });
    });
});
</script>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
