<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kendaraan - Rental Kendaraan</title>
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
                    <h1>Data Kendaraan 🚗</h1>
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
                <h2>Daftar Unit</h2> <button class="btn btn-primary" onclick="openModal()">+ Tambah Kendaraan</button>
            </div>

            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php
                if ($_GET['success'] == 'add') echo '✓ Kendaraan berhasil ditambahkan!';
                elseif ($_GET['success'] == 'update') echo '✓ Kendaraan berhasil diupdate!';
                elseif ($_GET['success'] == 'delete') echo '✓ Kendaraan berhasil dihapus!';
                ?>
            </div>
            <?php endif; ?>

            <div class="search-bar">
                <form method="GET" style="display: flex; gap: 12px; flex: 1;">
                    <input type="hidden" name="page" value="kendaraan">
                    <input type="text" name="search" placeholder="Cari no plat atau merk..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    <?php if ($search): ?>
                    <a href="index.php?page=kendaraan" class="btn" style="background: #E5E7EB;">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No Plat</th>
                            <th>Merk</th>
                            <th>Tahun</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kendaraan_list as $k): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($k['no_plat']); ?></strong></td>
                            <td><?php echo htmlspecialchars($k['merk']); ?></td>
                            <td><?php echo htmlspecialchars($k['tahun']); ?></td>
                            <td><?php echo htmlspecialchars($k['nama_tipe'] ?? '-'); ?></td>
                            <td>
                                <span class="badge <?php 
                                    echo $k['status'] == 'tersedia' ? 'badge-success' : 
                                        ($k['status'] == 'disewa' ? 'badge-warning' : 'badge-info'); 
                                ?>">
                                    <?php echo ucfirst($k['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?page=kendaraan&edit=<?php echo $k['id_kendaraan']; ?>" class="btn btn-success">Edit</a>
                                <a href="index.php?page=kendaraan&delete=<?php echo $k['id_kendaraan']; ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($kendaraan_list)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Tidak ada data kendaraan</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="index.php?page=kendaraan&p=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                       class="<?php echo $page == $i ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <div id="formModal" class="modal <?php echo $edit_data ? 'active' : ''; ?>">
        <div class="modal-content">
            <h2 id="modalTitle"><?php echo $edit_data ? 'Edit Kendaraan' : 'Tambah Kendaraan'; ?></h2>
            <form method="POST" id="kendaraanForm">
                <input type="hidden" name="page" value="kendaraan">
                <input type="hidden" name="id_kendaraan" id="id_kendaraan" value="<?php echo $edit_data['id_kendaraan'] ?? ''; ?>">
                
                <div class="form-group">
                    <label>No Plat *</label>
                    <input type="text" name="no_plat" id="no_plat" required value="<?php echo htmlspecialchars($edit_data['no_plat'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Merk *</label>
                    <input type="text" name="merk" id="merk" required value="<?php echo htmlspecialchars($edit_data['merk'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Tahun *</label>
                    <input type="number" name="tahun" id="tahun" required value="<?php echo htmlspecialchars($edit_data['tahun'] ?? date('Y')); ?>">
                </div>
                
                <div class="form-group">
                    <label>Tipe Kendaraan *</label>
                    <select name="id_tipe" id="id_tipe" required>
                        <option value="">Pilih Tipe</option>
                        <?php foreach ($tipe_list as $t): ?>
                        <option value="<?php echo $t['id_tipe']; ?>" 
                                <?php echo (isset($edit_data) && $edit_data['id_tipe'] == $t['id_tipe']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($t['nama_tipe']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" id="status" required>
                        <option value="tersedia" <?php echo (isset($edit_data) && $edit_data['status'] == 'tersedia') ? 'selected' : ''; ?>>Tersedia</option>
                        <option value="perbaikan" <?php echo (isset($edit_data) && $edit_data['status'] == 'perbaikan') ? 'selected' : ''; ?>>Perbaikan (Maintenance)</option>
                        
                        <?php if (isset($edit_data) && $edit_data['status'] == 'disewa'): ?>
                            <option value="disewa" selected>Disewa (Sedang Rental)</option>
                        <?php endif; ?>
                    </select>
                    <small style="color: #6B7280; font-size: 12px; display: block; margin-top: 5px;">
                        ℹ️ Status <b>"Disewa"</b> hanya otomatis aktif melalui menu <b>Transaksi Rental</b>.
                    </small>
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Simpan</button>
                    <button type="button" class="btn" style="flex: 1; background: #E5E7EB;" onclick="closeModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('formModal');
        
        function openModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Kendaraan';
            document.getElementById('kendaraanForm').reset();
            document.getElementById('id_kendaraan').value = '';
            document.getElementById('status').value = 'tersedia';
            modal.classList.add('active');
        }
        
        function closeModal() {
            modal.classList.remove('active');
            if (window.location.search.includes('edit=')) {
                window.location.href = 'index.php?page=kendaraan';
            }
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>