// Update info harga sewa saat kendaraan dipilih
function updateHargaSewa() {
    const select = document.getElementById('id_kendaraan');
    const selectedOption = select.options[select.selectedIndex];
    const harga = selectedOption.getAttribute('data-harga');
    const infoHarga = document.getElementById('infoHarga');
    
    if (harga && harga > 0) {
        infoHarga.textContent = 'Harga sewa: Rp ' + parseInt(harga).toLocaleString('id-ID') + ' / hari';
        infoHarga.style.display = 'block';
    } else {
        infoHarga.style.display = 'none';
    }
    
    hitungTotal();
}

// Hitung total harga rental
function hitungTotal() {
    const idKendaraan = document.getElementById('id_kendaraan').value;
    const tglSewa = document.getElementById('tgl_sewa').value;
    const tglKembali = document.getElementById('tgl_kembali').value;
    
    if (!idKendaraan || !tglSewa || !tglKembali) {
        return;
    }
    
    // Validasi tanggal
    const dateSewa = new Date(tglSewa);
    const dateKembali = new Date(tglKembali);
    
    if (dateKembali <= dateSewa) {
        alert('Tanggal kembali harus lebih besar dari tanggal sewa!');
        document.getElementById('tgl_kembali').value = '';
        return;
    }
    
    // Get harga dari option
    const select = document.getElementById('id_kendaraan');
    const selectedOption = select.options[select.selectedIndex];
    const hargaPerHari = parseInt(selectedOption.getAttribute('data-harga')) || 0;
    
    // Hitung jumlah hari
    const diffTime = Math.abs(dateKembali - dateSewa);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    const jumlahHari = diffDays > 0 ? diffDays : 1;
    
    // Hitung total
    const total = hargaPerHari * jumlahHari;
    
    // Update display
    document.getElementById('total_harga').value = total;
    document.getElementById('total_harga_display').value = 'Rp ' + total.toLocaleString('id-ID');
}

// Validasi form sebelum submit
document.addEventListener('DOMContentLoaded', function() {
    const rentalForm = document.getElementById('rentalForm');
    
    if (rentalForm) {
        rentalForm.addEventListener('submit', function(e) {
            const total = document.getElementById('total_harga').value;
            
            if (!total || total == 0) {
                e.preventDefault();
                alert('Total harga belum dihitung! Pastikan semua field terisi dengan benar.');
                return false;
            }
            
            return confirm('Yakin ingin menyimpan transaksi rental ini?');
        });
    }
});