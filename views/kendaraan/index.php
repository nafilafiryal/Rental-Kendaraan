<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kendaraan - Rental Kendaraan</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        .content-wrapper {
            background: #FFFFFF;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .search-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }
        .search-bar input {
            flex: 1;
            padding: 10px 16px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 14px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #6B4226;
            color: #FFFFFF;
        }
        .btn-primary:hover {
            background-color: #573419;
        }
        .btn-success {
            background-color: #10B981;
            color: #FFFFFF;
            font-size: 12px;
            padding: 6px 12px;
        }
        .btn-danger {
            background-color: #EF4444;
            color: #FFFFFF;
            font-size: 12px;
            padding: 6px 12px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #FFFFFF;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2C1810;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 14px;
        }
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .pagination {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #D1D5DB;
            border-radius: 6px;
            text-decoration: none;
            color: #2C1810;
        }
        .pagination .active {
            background-color: #6B4226;
            color: #FFFFFF;
            border-color: #6B4226;
        }
    </style>
</head>
<body>
    <?php include 'views/layouts/sidebar.php'; ?>

    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <h1>Data Kendaraan</h1>
                <button class="btn btn-primary" onclick="openModal()">+ Tambah Kendaraan</button>
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

    <!-- Modal Form -->
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
                        <option value="disewa" <?php echo (isset($edit_data) && $edit_data['status'] == 'disewa') ? 'selected' : ''; ?>>Disewa</option>
                        <option value="perbaikan" <?php echo (isset($edit_data) && $edit_data['status'] == 'perbaikan') ? 'selected' : ''; ?>>Perbaikan</option>
                    </select>
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