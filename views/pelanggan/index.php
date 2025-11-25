<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan - Rental Kendaraan</title>
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
                    <h1>Data Pelanggan 👥</h1>
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
                <h2>Daftar Member</h2>
                <button class="btn btn-primary" onclick="openModal()">+ Tambah Pelanggan</button>
            </div>

            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php
                if ($_GET['success'] == 'add') echo '✓ Pelanggan berhasil ditambahkan!';
                elseif ($_GET['success'] == 'update') echo '✓ Pelanggan berhasil diupdate!';
                elseif ($_GET['success'] == 'delete') echo '✓ Pelanggan berhasil dihapus!';
                ?>
            </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <div class="search-bar">
                <form method="GET" style="display: flex; gap: 12px; flex: 1;">
                    <input type="hidden" name="page" value="pelanggan">
                    <input type="text" name="search" placeholder="Cari nama, KTP, atau no HP..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    <?php if ($search): ?>
                    <a href="index.php?page=pelanggan" class="btn" style="background: #E5E7EB;">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>No KTP</th>
                            <th>No HP</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pelanggan_list as $p): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($p['nama']); ?></strong></td>
                            <td><?php echo htmlspecialchars($p['no_ktp'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($p['no_hp']); ?></td>
                            <td><?php echo htmlspecialchars($p['email'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars(substr($p['alamat'], 0, 50)); ?>...</td>
                            <td>
                                <a href="index.php?page=pelanggan&edit=<?php echo $p['id_pelanggan']; ?>" class="btn btn-success">Edit</a>
                                <a href="index.php?page=pelanggan&delete=<?php echo $p['id_pelanggan']; ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus data pelanggan ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($pelanggan_list)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Tidak ada data pelanggan</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="index.php?page=pelanggan&p=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
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
            <h2 id="modalTitle"><?php echo $edit_data ? 'Edit Pelanggan' : 'Tambah Pelanggan'; ?></h2>
            <form method="POST" id="pelangganForm">
                <input type="hidden" name="page" value="pelanggan">
                <input type="hidden" name="id_pelanggan" id="id_pelanggan" value="<?php echo $edit_data['id_pelanggan'] ?? ''; ?>">
                
                <div class="form-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" name="nama" id="nama" required value="<?php echo htmlspecialchars($edit_data['nama'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>No KTP *</label>
                    <input type="text" name="no_ktp" id="no_ktp" required maxlength="16" 
                           pattern="[0-9]{16}" title="16 digit angka"
                           value="<?php echo htmlspecialchars($edit_data['no_ktp'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>No HP *</label>
                    <input type="text" name="no_hp" id="no_hp" required 
                           pattern="[0-9]{10,13}" title="10-13 digit angka"
                           value="<?php echo htmlspecialchars($edit_data['no_hp'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($edit_data['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Alamat *</label>
                    <textarea name="alamat" id="alamat" required rows="3"><?php echo htmlspecialchars($edit_data['alamat'] ?? ''); ?></textarea>
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Simpan</button>
                    <button type="button" class="btn" style="flex: 1; background: #E5E7EB;" onclick="closeModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/modal.js"></script>
</body>
</html>