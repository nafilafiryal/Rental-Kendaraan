# TUGAS AKHIR MATA KULIAH BASIS DATA LANJUT
# Rental Kendaraan 

Aplikasi web berbasis PHP Native dengan PostgreSQL untuk sistem manajemen rental kendaraan.

## 📋 Fitur Utama

### 1. Autentikasi
- Login dengan email dan password
- Session management
- Logout

### 2. Dashboard
- Statistik kendaraan (total, tersedia, disewa)
- Total pelanggan
- Rental berjalan
- Pendapatan bulan ini
- Transaksi rental terbaru
- Kendaraan paling populer

### 3. CRUD Kendaraan
- Create, Read, Update, Delete kendaraan
- Pagination (10 records per halaman)
- Search (no plat, merk)
- Filter berdasarkan status
- Validasi data

### 4. CRUD Pelanggan
- Create, Read, Update, Delete pelanggan
- Pagination
- Search (nama, KTP, no HP)
- Validasi KTP unique
- Validasi no HP dan email

### 5. Transaksi Rental
- Form rental dengan pemilihan kendaraan tersedia
- Pemilihan pelanggan
- Auto-calculate total harga berdasarkan durasi
- Validasi tanggal
- Transaction management (BEGIN/COMMIT)
- Update status kendaraan otomatis

### 6. Pengembalian Kendaraan
- List rental aktif
- Form pengembalian dengan checklist kondisi
- Auto-calculate denda keterlambatan
- Update status kendaraan berdasarkan kondisi
- Riwayat pengembalian

### 7. Laporan (Multiple Reports)
- **Kendaraan Populer**: Ranking kendaraan berdasarkan frekuensi rental
- **Pendapatan**: Laporan pendapatan harian
- **Utilisasi Kendaraan**: Persentase penggunaan kendaraan
- **Pelanggan Aktif**: Pelanggan dengan transaksi terbanyak
- **Pengembalian**: Laporan pengembalian dan denda
- **Materialized View**: Demo performa improvement
