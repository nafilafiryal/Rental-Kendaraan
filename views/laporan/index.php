<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Rental Kendaraan</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/crud.css">
    <style>
        .laporan-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            border-bottom: 2px solid #E5E7EB;
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 5px;
        }
        .laporan-tabs::-webkit-scrollbar { height: 4px; }
        .laporan-tabs::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 4px; }
        .laporan-tabs a {
            padding: 12px 20px;
            text-decoration: none;
            color: #6B7280;
            font-weight: 500;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .laporan-tabs a:hover { color: #2C1810; }
        .laporan-tabs a.active { color: #6B4226; border-bottom-color: #6B4226; }
        .filter-box { background: #F9FAFB; padding: 20px; border-radius: 8px; margin-bottom: 24px; }
        .filter-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end; }
        .stat-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-box { background: linear-gradient(135deg, #6B4226 0%, #4A2C1A 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-box h4 { font-size: 28px; margin-bottom: 4px; }
        .stat-box p { font-size: 14px; opacity: 0.9; }
    </style>
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
                    <h1>Laporan & Analitik 📊</h1>
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

        <div class="content-wrapper">
            <div class="page-header">
                <h2>Filter Laporan</h2>
                <a href="index.php?page=laporan&action=export&jenis=<?php echo $jenis_laporan; ?>&bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>&tgl_awal=<?php echo $tgl_awal; ?>&tgl_akhir=<?php echo $tgl_akhir; ?>" 
                   class="btn btn-success">
                    📥 Export CSV
                </a>
            </div>

            <?php if (isset($_GET['success']) && $_GET['success'] == 'refresh'): ?>
            <div class="alert alert-success">✓ Materialized View berhasil di-refresh!</div>
            <?php endif; ?>

            <div class="laporan-tabs">
                <a href="index.php?page=laporan&jenis=kendaraan_populer" class="<?php echo $jenis_laporan == 'kendaraan_populer' ? 'active' : ''; ?>">📊 Kendaraan Populer</a>
                <a href="index.php?page=laporan&jenis=pendapatan" class="<?php echo $jenis_laporan == 'pendapatan' ? 'active' : ''; ?>">💰 Pendapatan</a>
                <a href="index.php?page=laporan&jenis=utilisasi" class="<?php echo $jenis_laporan == 'utilisasi' ? 'active' : ''; ?>">🚗 Utilisasi Kendaraan</a>
                <a href="index.php?page=laporan&jenis=pelanggan" class="<?php echo $jenis_laporan == 'pelanggan' ? 'active' : ''; ?>">👥 Pelanggan Aktif</a>
                <a href="index.php?page=laporan&jenis=pengembalian" class="<?php echo $jenis_laporan == 'pengembalian' ? 'active' : ''; ?>">🔄 Pengembalian</a>
                <a href="index.php?page=laporan&jenis=materialized_view" class="<?php echo $jenis_laporan == 'materialized_view' ? 'active' : ''; ?>">⚡ Materialized View</a>
            </div>

            <div class="filter-box">
                <form method="GET">
                    <input type="hidden" name="page" value="laporan">
                    <input type="hidden" name="jenis" value="<?php echo $jenis_laporan; ?>">
                    
                    <div class="filter-row">
    <?php if (in_array($jenis_laporan, ['kendaraan_populer', 'pengembalian', 'utilisasi', 'pelanggan'])): ?>
        <div class="form-group">
            <label>Bulan</label>
            <select name="bulan">
                <option value="">Semua Bulan</option>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?php echo $i; ?>" <?php echo $bulan == $i ? 'selected' : ''; ?>>
                    <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Tahun</label>
            <select name="tahun">
                <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                <option value="<?php echo $i; ?>" <?php echo $tahun == $i ? 'selected' : ''; ?>>
                    <?php echo $i; ?>
                </option>
                <?php endfor; ?>
            </select>
        </div>
    <?php endif; ?>
                </form>
            </div>

            <?php if ($jenis_laporan == 'kendaraan_populer'): ?>
                <h3>Laporan Kendaraan Paling Sering Disewa</h3>
                <?php 
                $col_rental = array_column($data_laporan, 'jumlah_rental');
                $col_pendapatan = array_column($data_laporan, 'total_pendapatan');
                $total_rental = !empty($col_rental) ? array_sum($col_rental) : 0;
                $total_pendapatan = !empty($col_pendapatan) ? array_sum($col_pendapatan) : 0;
                ?>
                <div class="stat-summary">
                    <div class="stat-box"><h4><?php echo count($data_laporan); ?></h4><p>Total Kendaraan</p></div>
                    <div class="stat-box"><h4><?php echo $total_rental; ?></h4><p>Total Transaksi Rental</p></div>
                    <div class="stat-box"><h4>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h4><p>Total Pendapatan</p></div>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Ranking</th><th>No Plat</th><th>Merk</th><th>Tahun</th><th>Tipe</th><th>Jumlah Rental</th><th>Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data_laporan as $index => $d): ?>
                            <tr>
                                <td><strong><?php echo $index + 1; ?></strong></td>
                                <td><?php echo htmlspecialchars($d['no_plat']); ?></td>
                                <td><?php echo htmlspecialchars($d['merk']); ?></td>
                                <td><?php echo $d['tahun']; ?></td>
                                <td><?php echo htmlspecialchars($d['nama_tipe'] ?? '-'); ?></td>
                                <td><strong><?php echo $d['jumlah_rental']; ?>x</strong></td>
                                <td><strong>Rp <?php echo number_format($d['total_pendapatan'] ?? 0, 0, ',', '.'); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($data_laporan)): ?><tr><td colspan="7" style="text-align: center;">Tidak ada data</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($jenis_laporan == 'pendapatan'): ?>
                <h3>Laporan Pendapatan Rental</h3>
                <?php 
                $col_pendapatan = array_column($data_laporan, 'total_pendapatan');
                $col_transaksi = array_column($data_laporan, 'jumlah_transaksi');
                $grand_total = !empty($col_pendapatan) ? array_sum($col_pendapatan) : 0;
                $total_transaksi = !empty($col_transaksi) ? array_sum($col_transaksi) : 0;
                ?>
                <div class="stat-summary">
                    <div class="stat-box"><h4><?php echo $total_transaksi; ?></h4><p>Total Transaksi</p></div>
                    <div class="stat-box"><h4>Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></h4><p>Total Pendapatan</p></div>
                    <div class="stat-box"><h4>Rp <?php echo $total_transaksi > 0 ? number_format($grand_total / $total_transaksi, 0, ',', '.') : 0; ?></h4><p>Rata-rata per Transaksi</p></div>
                </div>
                <div class="table-container">
                    <table>
                        <thead><tr><th>Tanggal</th><th>Jumlah Transaksi</th><th>Total Pendapatan</th></tr></thead>
                        <tbody>
                            <?php foreach ($data_laporan as $d): ?>
                            <tr>
                                <td><strong><?php echo date('d F Y', strtotime($d['tanggal'])); ?></strong></td>
                                <td><?php echo $d['jumlah_transaksi']; ?> transaksi</td>
                                <td><strong>Rp <?php echo number_format($d['total_pendapatan'] ?? 0, 0, ',', '.'); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($data_laporan)): ?><tr><td colspan="3" style="text-align: center;">Tidak ada data</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($jenis_laporan == 'utilisasi'): ?>
                <h3>Laporan Utilisasi Kendaraan</h3>
                <p style="color: #6B7280; margin-bottom: 20px;">Laporan ini menampilkan tingkat utilisasi kendaraan berdasarkan total hari disewa.</p>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No Plat</th><th>Merk</th><th>Tahun</th><th>Status</th><th>Total Rental</th><th>Total Hari Disewa</th><th>Utilisasi (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data_laporan as $d): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($d['no_plat']); ?></strong></td>
                                <td><?php echo htmlspecialchars($d['merk']); ?></td>
                                <td><?php echo $d['tahun']; ?></td>
                                <td>
                                    <span class="badge <?php echo $d['status'] == 'tersedia' ? 'badge-success' : ($d['status'] == 'disewa' ? 'badge-warning' : 'badge-info'); ?>">
                                        <?php echo ucfirst($d['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $d['total_rental']; ?>x</td>
                                <td><?php echo $d['total_hari_disewa']; ?> hari</td>
                                <td><strong><?php echo $d['persentase_utilisasi']; ?>%</strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($jenis_laporan == 'pelanggan'): ?>
                <h3>Laporan Pelanggan Aktif</h3>
                <div class="table-container">
                    <table>
                        <thead><tr><th>Nama</th><th>No HP</th><th>Alamat</th><th>Jumlah Rental</th><th>Total Pengeluaran</th><th>Terakhir Sewa</th></tr></thead>
                        <tbody>
                            <?php foreach ($data_laporan as $d): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($d['nama']); ?></strong></td>
                                <td><?php echo htmlspecialchars($d['no_hp']); ?></td>
                                <td><?php echo htmlspecialchars(substr($d['alamat'], 0, 30)); ?>...</td>
                                <td><?php echo $d['jumlah_rental']; ?>x</td>
                                <td><strong>Rp <?php echo number_format($d['total_pengeluaran'] ?? 0, 0, ',', '.'); ?></strong></td>
                                <td><?php echo date('d/m/Y', strtotime($d['terakhir_sewa'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($jenis_laporan == 'pengembalian'): ?>
                <h3>Laporan Pengembalian dan Denda</h3>
                <?php 
                $col_denda = array_column($data_laporan, 'denda');
                $total_denda = !empty($col_denda) ? array_sum($col_denda) : 0;
                ?>
                <div class="stat-summary">
                    <div class="stat-box"><h4><?php echo count($data_laporan); ?></h4><p>Total Pengembalian</p></div>
                    <div class="stat-box"><h4>Rp <?php echo number_format($total_denda, 0, ',', '.'); ?></h4><p>Total Denda</p></div>
                </div>
                <div class="table-container">
                    <table>
                        <thead><tr><th>Tgl Pengembalian</th><th>Kendaraan</th><th>Pelanggan</th><th>Kondisi</th><th>Denda</th><th>Keterangan</th></tr></thead>
                        <tbody>
                            <?php foreach ($data_laporan as $d): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($d['tgl_pengembalian'])); ?></td>
                                <td><?php echo htmlspecialchars($d['merk']); ?><br><small><?php echo htmlspecialchars($d['no_plat']); ?></small></td>
                                <td><?php echo htmlspecialchars($d['nama_pelanggan']); ?></td>
                                <td><span class="badge <?php echo $d['kondisi'] == 'baik' ? 'badge-success' : 'badge-warning'; ?>"><?php echo ucwords(str_replace('_', ' ', $d['kondisi'])); ?></span></td>
                                <td><strong>Rp <?php echo number_format($d['denda'] ?? 0, 0, ',', '.'); ?></strong></td>
                                <td><?php echo htmlspecialchars($d['keterangan'] ?? '-'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($jenis_laporan == 'materialized_view'): ?>
                <div style="background: #FEF3C7; border: 1px solid #F59E0B; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                    <h4 style="color: #92400E; margin-bottom: 8px;">⚡ Materialized View untuk Performance</h4>
                    <p style="color: #78350F; font-size: 14px; margin-bottom: 12px;">Materialized View menyimpan hasil query yang sudah di-cache untuk performa lebih cepat. Klik tombol refresh untuk update data terbaru.</p>
                    <a href="index.php?page=laporan&jenis=materialized_view&refresh_mv=1" class="btn btn-primary" onclick="return confirm('Refresh materialized view? Ini akan update data cache.')">🔄 Refresh Materialized View</a>
                </div>
                <h3>Data dari Materialized View: mv_kendaraan_populer</h3>
                <div class="table-container">
                    <table>
                        <thead><tr><th>Ranking</th><th>No Plat</th><th>Merk</th><th>Tipe</th><th>Jumlah Rental</th><th>Total Pendapatan</th><th>Rata-rata Hari Sewa</th></tr></thead>
                        <tbody>
                            <?php foreach ($data_laporan as $index => $d): ?>
                            <tr>
                                <td><strong><?php echo $index + 1; ?></strong></td>
                                <td><?php echo htmlspecialchars($d['no_plat']); ?></td>
                                <td><?php echo htmlspecialchars($d['merk']); ?></td>
                                <td><?php echo htmlspecialchars($d['nama_tipe'] ?? '-'); ?></td>
                                <td><?php echo $d['jumlah_rental']; ?>x</td>
                                <td><strong>Rp <?php echo number_format($d['total_pendapatan'] ?? 0, 0, ',', '.'); ?></strong></td>
                                <td><?php echo $d['rata_rata_hari_sewa']; ?> hari</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>