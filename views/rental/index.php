<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Rental - Rental Kendaraan</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/crud.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Badge Booking warna biru */
        .badge-primary { background-color: #DBEAFE; color: #1E40AF; border: 1px solid #93C5FD; }
        
        /* Style Toggle */
        .sopir-toggle-container { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #6B4226; }
        input:checked + .slider:before { transform: translateX(20px); }
        .toggle-label { display: flex; align-items: center; justify-content: space-between; cursor: pointer; }
        .toggle-text { font-weight: 500; color: #374151; font-size: 14px; display: flex; align-items: center; gap: 8px; }
        #div_sopir { margin-top: 15px; padding-top: 15px; border-top: 1px solid #E5E7EB; animation: slideDown 0.3s ease-out; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .flatpickr-input { background-color: #fff !important; }
    </style>
</head>
<body>
    <?php include 'views/layouts/sidebar.php'; ?>

    <main class="main-content">
        <header class="header">
            <div class="header-left" style="display: flex; align-items: center; gap: 15px;">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
                <div><h1>Transaksi Rental 📄</h1></div>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <span class="user-role">Administrator</span>
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)); ?></div>
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
                elseif ($_GET['success'] == 'activate') echo '✓ Booking berhasil diaktifkan (Berjalan)!'; 
                ?>
            </div>
            <?php endif; ?>

            <div class="search-bar">
                <form method="GET" style="display: flex; gap: 12px; flex: 1;">
                    <input type="hidden" name="page" value="rental">
                    <input type="text" name="search" placeholder="Cari..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kendaraan</th>
                            <th>Pelanggan</th>
                            <th>Sopir</th>
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
                            <td><?php echo htmlspecialchars($r['merk']); ?><br><small><?php echo htmlspecialchars($r['no_plat']); ?></small></td>
                            <td><?php echo htmlspecialchars($r['nama_pelanggan']); ?></td>
                            <td>
                                <?php if (!empty($r['nama_sopir'])): ?>
                                    <span style="color: #059669; font-weight: 500;">👨‍✈️ <?php echo htmlspecialchars($r['nama_sopir']); ?></span>
                                <?php else: ?>
                                    <span style="color: #6B7280;">Lepas Kunci</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo !empty($r['tgl_sewa']) ? date('d F Y', strtotime($r['tgl_sewa'])) : '-'; ?></td>
                            <td><?php echo !empty($r['tgl_kembali']) ? date('d F Y', strtotime($r['tgl_kembali'])) : '-'; ?></td>
                            <td><strong>Rp <?php echo number_format($r['total_harga'], 0, ',', '.'); ?></strong></td>
                            <td>
                                <span class="badge <?php 
                                    echo $r['status'] == 'booking' ? 'badge-primary' : 
                                        ($r['status'] == 'berjalan' ? 'badge-warning' : 'badge-success'); 
                                ?>">
                                    <?php echo ucfirst($r['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <a href="index.php?page=rental&view=<?php echo $r['id_rental']; ?>" class="btn btn-info" style="padding: 6px 10px;">👁️</a>
                                    
                                    <?php if ($r['status'] == 'booking'): ?>
                                        <a href="index.php?page=rental&activate=<?php echo $r['id_rental']; ?>" class="btn btn-success" style="padding: 6px 10px;" onclick="return confirm('Pelanggan sudah mengambil unit? Aktifkan rental ini sekarang?')" title="Ambil Unit">🚗 Ambil</a>
                                        <a href="index.php?page=rental&delete=<?php echo $r['id_rental']; ?>" class="btn btn-danger" style="padding: 6px 10px;" onclick="return confirm('Batalkan booking ini?')">❌</a>
                                    
                                    <?php elseif ($r['status'] == 'berjalan'): ?>
                                        <a href="index.php?page=rental&delete=<?php echo $r['id_rental']; ?>" class="btn btn-danger" style="padding: 6px 10px;" onclick="return confirm('Hapus transaksi berjalan?')">❌</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="formModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <h2>Transaksi Rental Baru</h2>
            <form method="POST" id="rentalForm">
                <input type="hidden" name="page" value="rental">
                
                <div class="form-group">
                    <label>Pelanggan *</label>
                    <select name="id_pelanggan" required>
                        <option value="">Pilih Pelanggan</option>
                        <?php foreach ($pelanggan_list as $p): ?>
                        <option value="<?php echo $p['id_pelanggan']; ?>"><?php echo htmlspecialchars($p['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Kendaraan *</label>
                    <select name="id_kendaraan" id="id_kendaraan" required onchange="hitungTotal()">
                        <option value="">Pilih Kendaraan</option>
                        <?php foreach ($kendaraan_tersedia as $k): ?>
                        <option value="<?php echo $k['id_kendaraan']; ?>" data-harga="<?php echo $k['harga_sewa'] ?? 300000; ?>">
                            <?php echo htmlspecialchars($k['merk']); ?> - <?php echo htmlspecialchars($k['no_plat']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sopir-toggle-container">
                    <label class="toggle-label" for="pakai_sopir">
                        <span class="toggle-text">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #6B4226;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            Sewa dengan Sopir?
                        </span>
                        <label class="toggle-switch">
                            <input type="checkbox" id="pakai_sopir" name="pakai_sopir" value="1" onchange="toggleSopir()">
                            <span class="slider"></span>
                        </label>
                    </label>
                    
                    <div id="div_sopir" style="display: none;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="id_sopir">Pilih Sopir *</label>
                            <select name="id_sopir" id="id_sopir" onchange="hitungTotal()" style="background: white;">
                                <option value="">-- Pilih Sopir Tersedia --</option>
                                <?php foreach ($sopir_list as $s): ?>
                                <option value="<?php echo $s['id_sopir']; ?>" data-tarif="<?php echo $s['tarif_harian']; ?>">
                                    <?php echo htmlspecialchars($s['nama']); ?> (Rp <?php echo number_format($s['tarif_harian'], 0, ',', '.'); ?>/hari)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Sewa *</label>
                        <input type="text" class="datepicker" name="tgl_sewa" id="tgl_sewa" required placeholder="Pilih tanggal...">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Kembali *</label>
                        <input type="text" class="datepicker" name="tgl_kembali" id="tgl_kembali" required placeholder="Pilih tanggal...">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Total Harga</label>
                    <input type="text" id="total_harga_display" readonly style="background: #F3F4F6; font-weight: bold; font-size: 18px; color: #6B4226;" value="Rp 0">
                    <input type="hidden" name="total_harga" id="total_harga" value="0">
                    <small id="rincian_harga" style="display: block; margin-top: 5px; color: #6B7280; font-style: italic;"></small>
                </div>
                
                <div style="display: flex; gap: 12px;">
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
                    <tr><td style="padding: 8px;">Pelanggan:</td><td style="padding: 8px;"><strong><?php echo htmlspecialchars($view_data['nama_pelanggan']); ?></strong></td></tr>
                    <tr><td style="padding: 8px;">Kendaraan:</td><td style="padding: 8px;"><?php echo htmlspecialchars($view_data['merk']); ?> - <?php echo htmlspecialchars($view_data['no_plat']); ?></td></tr>
                    <tr><td style="padding: 8px;">Sopir:</td><td style="padding: 8px;"><?php echo !empty($view_data['nama_sopir']) ? htmlspecialchars($view_data['nama_sopir']) : 'Lepas Kunci'; ?></td></tr>
                    <tr><td style="padding: 8px;">Tanggal Sewa:</td><td style="padding: 8px;"><?php echo !empty($view_data['tgl_sewa']) ? date('d F Y', strtotime($view_data['tgl_sewa'])) : '-'; ?></td></tr>
                    <tr><td style="padding: 8px;">Tanggal Kembali:</td><td style="padding: 8px;"><?php echo !empty($view_data['tgl_kembali']) ? date('d F Y', strtotime($view_data['tgl_kembali'])) : '-'; ?></td></tr>
                    <tr><td style="padding: 8px;">Total Harga:</td><td style="padding: 8px; font-size: 18px; color: #6B4226;"><strong>Rp <?php echo number_format($view_data['total_harga'], 0, ',', '.'); ?></strong></td></tr>
                    <tr><td style="padding: 8px;">Status:</td><td style="padding: 8px;"><?php echo ucfirst($view_data['status']); ?></td></tr>
                </table>
            </div>
            <button onclick="window.location='index.php?page=rental'" class="btn btn-primary" style="width: 100%;">Tutup</button>
        </div>
    </div>
    <?php endif; ?>

    <script src="assets/js/modal.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

    <script>
        flatpickr(".datepicker", {
            altInput: true, altFormat: "j F Y", dateFormat: "Y-m-d", locale: "id", minDate: "today",
            onChange: function() { hitungTotal(); }
        });

        const modal = document.getElementById('formModal');
        function openModal() {
            document.getElementById('rentalForm').reset();
            document.getElementById('div_sopir').style.display = 'none';
            document.getElementById('pakai_sopir').checked = false;
            document.getElementById('total_harga').value = 0;
            document.getElementById('total_harga_display').value = 'Rp 0';
            document.getElementById('rincian_harga').innerText = '';
            modal.classList.add('active');
        }
        function closeModal() { modal.classList.remove('active'); }
        window.onclick = function(event) { if (event.target == modal) closeModal(); }

        function toggleSopir() {
            const checkbox = document.getElementById('pakai_sopir');
            const divSopir = document.getElementById('div_sopir');
            const selectSopir = document.getElementById('id_sopir');
            if (checkbox.checked) {
                divSopir.style.display = 'block'; selectSopir.setAttribute('required', 'required');
            } else {
                divSopir.style.display = 'none'; selectSopir.value = ""; selectSopir.removeAttribute('required');
            }
            hitungTotal();
        }

        function hitungTotal() {
            const selectMobil = document.getElementById('id_kendaraan');
            const selectSopir = document.getElementById('id_sopir');
            const tglSewa = document.getElementById('tgl_sewa').value;
            const tglKembali = document.getElementById('tgl_kembali').value;
            const checkboxSopir = document.getElementById('pakai_sopir');

            let hargaMobil = 0;
            if (selectMobil.selectedIndex >= 0) hargaMobil = parseInt(selectMobil.options[selectMobil.selectedIndex].getAttribute('data-harga')) || 0;

            let hargaSopir = 0;
            if (checkboxSopir.checked && selectSopir.selectedIndex >= 0) {
                const option = selectSopir.options[selectSopir.selectedIndex];
                if(option.value) hargaSopir = parseInt(option.getAttribute('data-tarif')) || 0;
            }

            let jumlahHari = 0;
            if (tglSewa && tglKembali) {
                const date1 = new Date(tglSewa); const date2 = new Date(tglKembali);
                if (date2 >= date1) {
                    const diffTime = Math.abs(date2 - date1);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                    jumlahHari = diffDays > 0 ? diffDays : 1; 
                }
            }

            const totalPerHari = hargaMobil + hargaSopir;
            const grandTotal = totalPerHari * jumlahHari;

            document.getElementById('total_harga').value = grandTotal;
            document.getElementById('total_harga_display').value = 'Rp ' + grandTotal.toLocaleString('id-ID');
            
            if (jumlahHari > 0 && (hargaMobil > 0 || hargaSopir > 0)) {
                let rincian = `Mobil: Rp ${hargaMobil.toLocaleString()}`;
                if (hargaSopir > 0) rincian += ` + Sopir: Rp ${hargaSopir.toLocaleString()}`;
                rincian += ` (x ${jumlahHari} hari)`;
                document.getElementById('rincian_harga').innerText = rincian;
            } else {
                document.getElementById('rincian_harga').innerText = '';
            }
        }
    </script>
</body>
</html>