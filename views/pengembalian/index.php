<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Kendaraan - Rental Kendaraan</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/crud.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Fix style input flatpickr */
        .flatpickr-input {
            background-color: #fff !important;
        }
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
                    <h1>Pengembalian</h1>
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
                <h2>Proses Pengembalian</h2>
            </div>

            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">✓ Pengembalian berhasil diproses!</div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <h3 style="margin: 24px 0 16px 0; color: #2C1810;">Rental Aktif</h3>
            
            <div class="search-bar">
                <form method="GET" style="display: flex; gap: 12px; flex: 1;">
                    <input type="hidden" name="page" value="pengembalian">
                    <input type="text" name="search" placeholder="Cari no plat atau nama pelanggan..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    <?php if ($search): ?>
                    <a href="index.php?page=pengembalian" class="btn" style="background: #E5E7EB;">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID Rental</th>
                            <th>Kendaraan</th>
                            <th>Pelanggan</th>
                            <th>Tgl Sewa</th>
                            <th>Tgl Kembali</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rental_aktif)): ?>
                            <?php foreach ($rental_aktif as $r): ?>
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
                                    <button onclick="openPengembalianModal(<?php echo htmlspecialchars(json_encode($r)); ?>)" class="btn btn-primary">
                                        Proses Pengembalian
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">Tidak ada rental aktif saat ini</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <h3 style="margin: 40px 0 16px 0; color: #2C1810;">Riwayat Pengembalian</h3>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tgl Sewa</th>
                            <th>Tenggat (Rencana)</th>
                            <th>Tgl Kembali (Realisasi)</th>
                            <th>Kendaraan</th>
                            <th>Pelanggan</th>
                            <th>Kondisi</th>
                            <th>Denda</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($riwayat_pengembalian)): ?>
                            <?php foreach ($riwayat_pengembalian as $p): ?>
                            <tr>
                                <td><?php echo !empty($p['tgl_sewa']) ? date('d/m/y', strtotime($p['tgl_sewa'])) : '-'; ?></td>
                                <td><?php echo !empty($p['tgl_rencana']) ? date('d/m/y', strtotime($p['tgl_rencana'])) : '-'; ?></td>
                                <td>
                                    <?php 
                                        $tgl_real = !empty($p['tgl_pengembalian']) ? date('d/m/y', strtotime($p['tgl_pengembalian'])) : '-';
                                        $is_late = ($p['denda'] > 0);
                                        echo $is_late ? "<span style='color:red; font-weight:bold;'>$tgl_real</span>" : $tgl_real;
                                    ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($p['merk']); ?><br>
                                    <small><?php echo htmlspecialchars($p['no_plat']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($p['nama_pelanggan']); ?></td>
                                <td>
                                    <span class="badge <?php 
                                        echo $p['kondisi'] == 'baik' ? 'badge-success' : 
                                             ($p['kondisi'] == 'rusak_ringan' ? 'badge-warning' : 'badge-danger'); 
                                    ?>">
                                        <?php echo ucwords(str_replace('_', ' ', $p['kondisi'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($p['denda'] > 0): ?>
                                        <strong style="color: #DC2626;">Rp <?php echo number_format($p['denda'], 0, ',', '.'); ?></strong>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($p['keterangan'] ?? '-'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">Belum ada riwayat pengembalian</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="index.php?page=pengembalian&p=<?php echo $i; ?>" 
                       class="<?php echo $page == $i ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <div id="pengembalianModal" class="modal">
        <div class="modal-content">
            <h2>Proses Pengembalian</h2>
            
            <div id="rentalInfo" style="padding: 16px; background: #F3F4F6; border-radius: 8px; margin-bottom: 20px;">
                </div>
            
            <form method="POST" id="pengembalianForm">
                <input type="hidden" name="id_rental" id="id_rental">
                
                <div class="form-group">
                    <label>Tanggal Pengembalian (Realisasi) *</label>
                    <input type="text" class="datepicker" name="tgl_pengembalian" id="tgl_pengembalian" required placeholder="Pilih tanggal kembali...">
                </div>
                
                <div class="form-group">
                    <label>Kondisi Kendaraan *</label>
                    <select name="kondisi" id="kondisi" required>
                        <option value="baik">Baik</option>
                        <option value="rusak_ringan">Rusak Ringan</option>
                        <option value="rusak">Rusak Berat</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Denda Keterlambatan</label>
                    <input type="text" id="denda_display" readonly style="background: #FEE2E2; font-weight: bold; color: #991B1B;">
                    <input type="hidden" name="denda" id="denda" value="0">
                    <small>Denda Rp 50.000/hari (Dihitung otomatis oleh Database)</small>
                </div>
                
                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" placeholder="Catatan kondisi kendaraan atau hal lainnya..."></textarea>
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Proses Pengembalian</button>
                    <button type="button" class="btn" style="flex: 1; background: #E5E7EB;" onclick="closeModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/modal.js"></script>
    <script src="assets/js/pengembalian.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

    <script>
        // Inisialisasi Flatpickr
        const fp = flatpickr(".datepicker", {
            altInput: true,
            altFormat: "j F Y", // Format Tampilan: 27 November 2025
            dateFormat: "Y-m-d", // Format Database
            locale: "id",
            defaultDate: "today", // Default hari ini
            onChange: function(selectedDates, dateStr, instance) {
                // Panggil fungsi hitung denda saat tanggal berubah
                hitungDenda();
            }
        });

        // Override fungsi openPengembalianModal agar support Flatpickr
        // Kita simpan referensi fungsi asli (jika ada di pengembalian.js) atau buat baru
        const originalOpenModal = window.openPengembalianModal;

        window.openPengembalianModal = function(rentalData) {
            // Panggil logika asli dulu (set ID rental, info rental, dll)
            // Pastikan Anda menyalin logika pengisian info rental di sini jika pengembalian.js tidak di-load dengan benar
            // Tapi asumsinya pengembalian.js sudah ada dan berfungsi.
            
            // Set ID rental
            document.getElementById('id_rental').value = rentalData.id_rental;
            currentRental = rentalData; // Variable global dari pengembalian.js

            // Tampilkan Info Rental
            const rentalInfo = document.getElementById('rentalInfo');
            rentalInfo.innerHTML = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div><strong>Kendaraan:</strong><br>${rentalData.merk} - ${rentalData.no_plat}</div>
                    <div><strong>Pelanggan:</strong><br>${rentalData.nama_pelanggan}</div>
                    <div><strong>Tgl Sewa:</strong><br>${formatDate(rentalData.tgl_sewa)}</div>
                    <div><strong>Tenggat:</strong><br>${formatDate(rentalData.tgl_kembali)}</div>
                </div>
            `;

            // Reset Form
            document.getElementById('kondisi').value = 'baik';
            document.getElementById('keterangan').value = '';

            // Update Flatpickr ke Hari Ini
            fp.setDate(new Date());
            
            // Hitung Denda Awal
            hitungDenda();

            // Tampilkan Modal
            document.getElementById('pengembalianModal').classList.add('active');
        }

        // Helper format tanggal untuk info
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }
    </script>
</body>
</html>