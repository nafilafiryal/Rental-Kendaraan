<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Rental Kendaraan</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <?php include 'views/layouts/sidebar.php'; ?>

    <main class="main-content">
        <header class="header">
            <div class="header-left" style="display: flex; align-items: center; gap: 15px;">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <div>
                    <h1>Dashboard Admin 🚗</h1>
                    <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['nama'] ?? 'Admin'); ?>!</p>
                </div>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <span class="user-role">Administrator</span>
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h14v-4H5v4zm3-8l2-3h4l2 3"/><circle cx="7" cy="19" r="2"/><circle cx="17" cy="19" r="2"/></svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($data['total_kendaraan'], 0, ',', '.'); ?></h3>
                    <p>Total Kendaraan</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($data['kendaraan_tersedia'], 0, ',', '.'); ?></h3>
                    <p>Kendaraan Tersedia</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($data['rental_berjalan'], 0, ',', '.'); ?></h3>
                    <p>Rental Berjalan</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="stat-content">
                    <h3>Rp <?php echo number_format($data['pendapatan_bulan_ini'], 0, ',', '.'); ?></h3>
                    <p>Pendapatan Bulan Ini</p>
                </div>
            </div>
        </section>

        <div class="content-grid">
            <section class="card">
                <div class="card-header">
                    <h2>Transaksi Rental Terbaru</h2>
                    <a href="index.php?page=rental" class="btn-link">Lihat Semua</a>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Kendaraan</th>
                                <th>Pelanggan</th>
                                <th>Tanggal Sewa</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($data['rental_terbaru']) > 0): ?>
                                <?php foreach ($data['rental_terbaru'] as $rental): 
                                    $badge_class = $rental['status'] == 'berjalan' ? 'badge-warning' : 'badge-info';
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($rental['merk']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($rental['no_plat']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($rental['nama_pelanggan']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($rental['tgl_sewa'])); ?></td>
                                    <td>Rp <?php echo number_format($rental['total_harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo ucfirst($rental['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center;">Belum ada transaksi rental</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="card">
                <div class="card-header">
                    <h2>Kendaraan Paling Populer</h2>
                </div>
                <div class="popular-list">
                    <?php if (count($data['kendaraan_populer']) > 0): ?>
                        <?php foreach ($data['kendaraan_populer'] as $index => $kendaraan): ?>
                        <div class="popular-item">
                            <div class="popular-rank"><?php echo $index + 1; ?></div>
                            <div class="popular-info">
                                <h4><?php echo htmlspecialchars($kendaraan['merk']); ?></h4>
                                <p><?php echo htmlspecialchars($kendaraan['no_plat']); ?> (Tahun: <?php echo htmlspecialchars($kendaraan['tahun']); ?>)</p>
                            </div>
                            <div class="popular-stats">
                                <span class="rental-count"><?php echo $kendaraan['jumlah_rental']; ?>x disewa</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #6B7280;">Belum ada data kendaraan</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
</body>
</html>