<?php
require_once __DIR__ . '/../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper escape kalau belum ada
if (!function_exists('esc')) {
    function esc($value)
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

// Ambil filter search sederhana (optional)
$keyword = trim($_GET['q'] ?? '');

// Query utama: JOIN users & tables
$sql = "
    SELECT 
        r.*,
        u.name  AS user_name,
        u.email AS user_email,
        u.phone AS user_phone,
        t.code  AS table_code,
        t.capacity AS table_capacity
    FROM reservations r
    LEFT JOIN users  u ON r.user_id  = u.id
    LEFT JOIN tables t ON r.table_id = t.id
";

$params = [];
if ($keyword !== '') {
    // filter sederhana: by reservation_code / user_name / table_code
    $sql .= " WHERE 
        r.reservation_code LIKE ? 
        OR u.name LIKE ?
        OR t.code LIKE ?
    ";
    $like = '%' . $keyword . '%';
    $params = [$like, $like, $like];
}

$sql .= " ORDER BY r.reservation_date DESC, r.reservation_time DESC";

$stmt = $conn->prepare($sql);

if ($stmt && !empty($params)) {
    $stmt->bind_param("sss", ...$params);
}

$reservations = [];

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Reservasi - Admin | Dua Meja Satu Rasa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Admin layout basic */
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #0f172a;
            margin: 0;
            color: #e5e7eb;
        }

        .admin-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-header {
            padding: 16px 24px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.3);
            display: flex;
            align-items: center;
            justify-content: space-between;
            backdrop-filter: blur(10px);
            background: rgba(15, 23, 42, 0.9);
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .admin-header-title {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .admin-header-title h1 {
            font-size: 1.3rem;
            margin: 0;
        }

        .admin-header-title span {
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .admin-header-nav {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-ghost,
        .btn-primary,
        .btn-pill {
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.3);
            padding: 6px 14px;
            font-size: 0.8rem;
            background: transparent;
            color: #e5e7eb;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-ghost:hover {
            background: rgba(148, 163, 184, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border-color: transparent;
            color: #022c22;
            font-weight: 600;
        }

        .btn-primary:hover {
            filter: brightness(1.05);
        }

        .admin-main {
            padding: 20px 24px 32px;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        .admin-card {
            background: radial-gradient(circle at top left, rgba(148, 163, 184, 0.2), transparent 55%),
                        rgba(15, 23, 42, 0.9);
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.8);
            padding: 18px 18px 12px;
        }

        .admin-card-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 16px;
        }

        .admin-card-header-left h2 {
            margin: 0;
            font-size: 1.1rem;
        }

        .admin-card-header-left p {
            margin: 2px 0 0;
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .admin-filters {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .admin-search-wrap {
            position: relative;
        }

        .admin-search-input {
            background: rgba(15, 23, 42, 0.9);
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.6);
            padding: 6px 28px 6px 10px;
            font-size: 0.8rem;
            color: #e5e7eb;
            outline: none;
        }

        .admin-search-input::placeholder {
            color: #6b7280;
        }

        .admin-search-icon {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .admin-table-wrap {
            overflow-x: auto;
            border-radius: 14px;
            border: 1px solid rgba(55, 65, 81, 0.8);
            background: rgba(15, 23, 42, 0.95);
        }

        table.admin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.78rem;
        }

        .admin-table thead {
            background: linear-gradient(to right, rgba(30, 64, 175, 0.7), rgba(22, 163, 74, 0.7));
        }

        .admin-table th,
        .admin-table td {
            padding: 8px 10px;
            text-align: left;
            white-space: nowrap;
        }

        .admin-table th {
            font-weight: 600;
            color: #e5e7eb;
            border-bottom: 1px solid rgba(15, 23, 42, 0.9);
        }

        .admin-table tbody tr {
            border-top: 1px solid rgba(31, 41, 55, 0.9);
            transition: background 0.15s ease, transform 0.05s ease;
        }

        .admin-table tbody tr:nth-child(even) {
            background: rgba(15, 23, 42, 0.9);
        }

        .admin-table tbody tr:nth-child(odd) {
            background: rgba(15, 23, 42, 0.7);
        }

        .admin-table tbody tr:hover {
            background: rgba(30, 64, 175, 0.35);
            transform: translateY(-1px);
        }

        .admin-table td small {
            display: block;
            font-size: 0.7rem;
            color: #9ca3af;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 999px;
            margin-right: 5px;
        }

        .badge-status-pending {
            background: rgba(55, 65, 81, 0.8);
            color: #e5e7eb;
        }

        .badge-status-confirmed {
            background: rgba(22, 163, 74, 0.25);
            color: #bbf7d0;
        }

        .badge-status-cancelled {
            background: rgba(220, 38, 38, 0.18);
            color: #fecaca;
        }

        .badge-pay-pending {
            background: rgba(202, 138, 4, 0.23);
            color: #facc15;
        }

        .badge-pay-success {
            background: rgba(22, 163, 74, 0.25);
            color: #bbf7d0;
        }

        .badge-pay-failed {
            background: rgba(220, 38, 38, 0.18);
            color: #fecaca;
        }

        .badge-table {
            background: rgba(59, 130, 246, 0.2);
            color: #bfdbfe;
        }

        .admin-row-actions {
            display: flex;
            gap: 4px;
        }

        .btn-icon {
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.4);
            padding: 3px 8px;
            font-size: 0.7rem;
            background: rgba(15, 23, 42, 0.9);
            color: #e5e7eb;
            cursor: pointer;
        }

        .btn-icon:hover {
            background: rgba(55, 65, 81, 0.9);
        }

        .btn-icon-primary {
            border-color: rgba(22, 163, 74, 0.7);
        }

        .btn-icon-danger {
            border-color: rgba(220, 38, 38, 0.7);
        }

        .admin-empty {
            padding: 24px 12px;
            text-align: center;
            font-size: 0.85rem;
            color: #9ca3af;
        }

        .admin-footer {
            padding: 10px 2px 0;
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            color: #6b7280;
        }

        .pill-muted {
            border-radius: 999px;
            border: 1px solid rgba(55, 65, 81, 0.9);
            padding: 3px 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .pill-muted span {
            font-size: 0.7rem;
        }

        @media (max-width: 768px) {
            .admin-main {
                padding: 16px;
            }

            .admin-card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .admin-filters {
                width: 100%;
                justify-content: space-between;
            }

            .admin-search-wrap {
                flex: 1;
            }

            .admin-search-input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <header class="admin-header">
        <div class="admin-header-title">
            <h1>Kelola Reservasi</h1>
            <span>Monitor meja terisi, status pembayaran, dan konfirmasi tamu.</span>
        </div>
        <div class="admin-header-nav">
            <a href="index.php" class="btn-ghost">Dashboard</a>
            <!-- Nanti kalau sudah ada logout:
            <a href="../auth/logout.php" class="btn-ghost">Logout</a>
            -->
        </div>
    </header>

    <main class="admin-main">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-header-left">
                    <h2>Daftar Reservasi</h2>
                    <p>
                        <?= count($reservations) > 0
                            ? 'Total ' . count($reservations) . ' reservasi tercatat.'
                            : 'Belum ada reservasi yang masuk.' ?>
                    </p>
                </div>
                <div class="admin-filters">
                    <form method="get" class="admin-search-wrap">
                        <input
                            type="text"
                            name="q"
                            value="<?= esc($keyword) ?>"
                            class="admin-search-input"
                            placeholder="Cari kode, nama tamu, atau meja..."
                        >
                        <span class="admin-search-icon">🔍</span>
                    </form>
                    <button type="button" class="btn-pill" onclick="location.reload();">
                        ⟳ Refresh
                    </button>
                </div>
            </div>

            <?php if (empty($reservations)): ?>
                <div class="admin-empty">
                    Belum ada data reservasi. Arahkan pengunjung untuk melakukan pemesanan melalui halaman
                    <strong>Reservasi</strong> di website.
                </div>
            <?php else: ?>
                <div class="admin-table-wrap">
                    <table class="admin-table" id="reservation-table">
                        <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tamu &amp; Kontak</th>
                            <th>Meja</th>
                            <th>Waktu</th>
                            <th>Tamu</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($reservations as $r): ?>
                            <?php
                            $pay = $r['payment_status'] ?? 'pending';
                            $status = $r['status'] ?? 'pending';

                            // Badge payment
                            $payClass = 'badge-pay-pending';
                            $payLabel = 'Pending';
                            if ($pay === 'success') {
                                $payClass = 'badge-pay-success';
                                $payLabel = 'Berhasil';
                            } elseif ($pay === 'failed') {
                                $payClass = 'badge-pay-failed';
                                $payLabel = 'Gagal';
                            }

                            // Badge status
                            $statusClass = 'badge-status-pending';
                            $statusLabel = 'Menunggu';
                            if ($status === 'confirmed') {
                                $statusClass = 'badge-status-confirmed';
                                $statusLabel = 'Terkonfirmasi';
                            } elseif ($status === 'cancelled') {
                                $statusClass = 'badge-status-cancelled';
                                $statusLabel = 'Dibatalkan';
                            }

                            $date = $r['reservation_date'] ?? '';
                            $time = $r['reservation_time'] ?? '';
                            $created = $r['created_at'] ?? '';
                            ?>
                            <tr>
                                <td>
                                    <strong><?= esc($r['reservation_code']) ?></strong>
                                    <small>
                                        Dibuat: <?= esc($created) ?>
                                    </small>
                                </td>
                                <td>
                                    <?= esc($r['user_name'] ?? 'Guest #' . $r['user_id']) ?>
                                    <small>
                                        <?= esc($r['user_email'] ?? '-') ?><?php
                                        if (!empty($r['user_phone'])) {
                                            echo ' · ' . esc($r['user_phone']);
                                        }
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge badge-table">
                                        <span class="badge-dot" style="background:#60a5fa;"></span>
                                        <?= esc($r['table_code'] ?? ('ID ' . $r['table_id'])) ?>
                                    </span>
                                    <small>
                                        Kapasitas: <?= (int)($r['table_capacity'] ?? 0) ?> orang
                                    </small>
                                </td>
                                <td>
                                    <?= esc($date) ?>
                                    <small><?= esc(substr((string)$time, 0, 5)) ?> WIB</small>
                                </td>
                                <td>
                                    <?= (int)($r['guests'] ?? 0) ?> orang
                                    <?php if (!empty($r['notes'])): ?>
                                        <small>Catatan: <?= esc($r['notes']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $payClass ?>">
                                        <span class="badge-dot"
                                              style="background:<?= $pay === 'success'
                                                  ? '#4ade80'
                                                  : ($pay === 'failed' ? '#f97373' : '#eab308') ?>;"></span>
                                        <?= esc($payLabel) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $statusClass ?>">
                                        <span class="badge-dot"
                                              style="background:<?= $status === 'confirmed'
                                                  ? '#4ade80'
                                                  : ($status === 'cancelled' ? '#f97373' : '#9ca3af') ?>;"></span>
                                        <?= esc($statusLabel) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="admin-row-actions">
                                        <button type="button" class="btn-icon" title="Detail (belum aktif)">
                                            Detail
                                        </button>
                                        <button type="button" class="btn-icon btn-icon-primary" title="Konfirmasi (belum aktif)" disabled>
                                            ✓
                                        </button>
                                        <button type="button" class="btn-icon btn-icon-danger" title="Batalkan (belum aktif)" disabled>
                                            ✕
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="admin-footer">
                <div>
                    <span class="pill-muted">
                        <span>💡</span>
                        <span>Fitur aksi (konfirmasi / batal) belum di-wire ke backend. Bisa ditambahkan belakangan.</span>
                    </span>
                </div>
                <div>
                    <span>Admin • Dua Meja Satu Rasa</span>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // Optional: client-side filter (kalau mau tanpa reload)
    const searchInput = document.querySelector('.admin-search-input');
    const table = document.getElementById('reservation-table');

    if (searchInput && table) {
        searchInput.addEventListener('input', function () {
            const value = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(value) ? '' : 'none';
            });
        });
    }
</script>
</body>
</html>
