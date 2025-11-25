<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Rental - Rental Kendaraan</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/crud.css">
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
                    <h1>Transaksi Rental 📄</h1>
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
                <h2>Daftar Transaksi</h2>
                <button class="btn btn-primary" onclick="openModal()">+ Transaksi Baru</button>
            </div>

            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php
                if ($_GET['success'] == 'add') echo '✓ Transaksi rental berhasil ditambahkan!';
                elseif ($_GET['success'] == 'delete') echo '✓ Transaksi berhasil dihapus!';
                ?>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">⚠️ Gagal memproses transaksi</div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <div class="search-bar">
                <form method="GET" style="display: flex; gap: 12px; flex: 1;">
                    <input type="hidden" name="page" value="rental">
                    <input type="text" name="search" placeholder="Cari no plat atau nama pelanggan..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    <?php if ($search): ?>
                    <a href="index.php?page=rental" class="btn" style="background: #E5E7EB;">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kendaraan</th>
                            <th>Pelanggan</th>
                            <th>Tgl Sewa</th>
                            <th>Tgl Kembali</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rental_list as $r): ?>
                        <tr>
                            <td><strong>#<?php echo $r['id_rental']; ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($r['merk']); ?><br>
                                <small><?php echo htmlspecialchars($r['no_plat']); ?></small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($r['nama_pelanggan']); ?><br>
                                <small><?php echo htmlspecialchars($r['no_hp']); ?></small>
                            </td>
                            <td><?php echo !empty($r['tgl_sewa']) ? date('d/m/Y', strtotime($r['tgl_sewa'])) : '-'; ?></td>
                            <td><?php echo !empty($r['tgl_kembali']) ? date('d/m/Y', strtotime($r['tgl_kembali'])) : '-'; ?></td>
                            <td><strong>Rp <?php echo number_format($r['total_harga'], 0, ',', '.'); ?></strong></td>
                            <td>
                                <span class="badge <?php echo $r['status'] == 'berjalan' ? 'badge-warning' : 'badge-success'; ?>">
                                    <?php echo ucfirst($r['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?page=rental&view=<?php echo $r['id_rental']; ?>" class="btn btn-info">Detail</a>
                                <?php if ($r['status'] == 'berjalan'): ?>
                                <a href="index.php?page=rental&delete=<?php echo $r['id_rental']; ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus transaksi ini?')">Hapus</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($rental_list)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">Tidak ada data transaksi rental</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="index.php?page=rental&p=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                       class="<?php echo $page == $i ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <div id="formModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <h2>Transaksi Rental Baru</h2>
            <form method="POST" id="rentalForm">
                <input type="hidden" name="page" value="rental">
                
                <div class="form-group">
                    <label>Pelanggan *</label>
                    <select name="id_pelanggan" id="id_pelanggan" required>
                        <option value="">Pilih Pelanggan</option>
                        <?php foreach ($pelanggan_list as $p): ?>
                        <option value="<?php echo $p['id_pelanggan']; ?>">
                            <?php echo htmlspecialchars($p['nama']); ?> - <?php echo htmlspecialchars($p['no_hp']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Kendaraan *</label>
                    <select name="id_kendaraan" id="id_kendaraan" required onchange="updateHargaSewa()">
                        <option value="">Pilih Kendaraan</option>
                        <?php foreach ($kendaraan_tersedia as $k): ?>
                        <option value="<?php echo $k['id_kendaraan']; ?>" data-harga="<?php echo $k['harga_sewa'] ?? 0; ?>">
                            <?php echo htmlspecialchars($k['merk']); ?> - <?php echo htmlspecialchars($k['no_plat']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <small id="infoHarga" style="color: #6B7280; display: none;"></small>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Sewa *</label>
                        <input type="date" name="tgl_sewa" id="tgl_sewa" required onchange="hitungTotal()" min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Tanggal Kembali *</label>
                        <input type="date" name="tgl_kembali" id="tgl_kembali" required onchange="hitungTotal()" min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Total Harga</label>
                    <input type="text" id="total_harga_display" readonly style="background: #F3F4F6; font-weight: bold; font-size: 16px;" value="Rp 0">
                    <input type="hidden" name="total_harga" id="total_harga" value="0">
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Simpan Transaksi</button>
                    <button type="button" class="btn" style="flex: 1; background: #E5E7EB;" onclick="closeModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($view_data): ?>
    <div id="detailModal" class="modal active">
        <div class="modal-content">
            <h2>Detail Transaksi Rental #<?php echo $view_data['id_rental']; ?></h2>
            <div style="padding: 20px 0;">
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 8px; font-weight: bold; width: 150px;">Pelanggan:</td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($view_data['nama_pelanggan']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">No HP:</td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($view_data['no_hp']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Alamat:</td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($view_data['alamat']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Kendaraan:</td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($view_data['merk']); ?> - <?php echo htmlspecialchars($view_data['no_plat']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Tanggal Sewa:</td>
                        <td style="padding: 8px;"><?php echo !empty($view_data['tgl_sewa']) ? date('d F Y', strtotime($view_data['tgl_sewa'])) : '-'; ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Tanggal Kembali:</td>
                        <td style="padding: 8px;"><?php echo !empty($view_data['tgl_kembali']) ? date('d F Y', strtotime($view_data['tgl_kembali'])) : '-'; ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Total Harga:</td>
                        <td style="padding: 8px; font-size: 18px; font-weight: bold; color: #6B4226;">
                            Rp <?php echo number_format($view_data['total_harga'], 0, ',', '.'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Status:</td>
                        <td style="padding: 8px;">
                            <span class="badge <?php echo $view_data['status'] == 'berjalan' ? 'badge-warning' : 'badge-success'; ?>">
                                <?php echo ucfirst($view_data['status']); ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <button onclick="window.location='index.php?page=rental'" class="btn btn-primary" style="width: 100%;">Tutup</button>
        </div>
    </div>
    <?php endif; ?>

    <script src="assets/js/modal.js"></script>
    <script src="assets/js/rental.js"></script>
</body>
</html>