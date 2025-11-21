<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 17h14v-4H5v4zm3-8l2-3h4l2 3m-11 0h14m-14 0v8m14-8v8M7 17v2m10-2v2"/>
                <circle cx="7" cy="19" r="2"/>
                <circle cx="17" cy="19" r="2"/>
            </svg>
        </div>
        <h2>Rental Kendaraan</h2>
    </div>
    
    <nav class="sidebar-nav">
        <a href="index.php?page=dashboard" class="nav-item <?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="index.php?page=kendaraan" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'kendaraan') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h14v-4H5v4zm3-8l2-3h4l2 3"/><circle cx="7" cy="19" r="2"/><circle cx="17" cy="19" r="2"/></svg>
            Kendaraan (CRUD)
        </a>
        <a href="index.php?page=pelanggan" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'pelanggan') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Pelanggan (CRUD)
        </a>
        <a href="index.php?page=rental" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'rental') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/></svg>
            Transaksi Rental
        </a>
        <a href="index.php?page=pengembalian" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'pengembalian') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 14 4 9 9 4"/><path d="M20 20v-7a4 4 0 0 0-4-4H4"/></svg>
            Pengembalian
        </a>
        <a href="index.php?page=laporan" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'laporan') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
            Laporan
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <a href="index.php?page=logout" class="nav-item logout">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/></svg>
            Logout
        </a>
    </div>
</aside>