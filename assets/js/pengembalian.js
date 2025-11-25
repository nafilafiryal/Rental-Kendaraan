let currentRental = null;
const modal = document.getElementById('pengembalianModal');

function openPengembalianModal(rentalData) {
    currentRental = rentalData;
    
    // Set ID rental
    document.getElementById('id_rental').value = rentalData.id_rental;
    
    // Set tanggal pengembalian default (hari ini)
    document.getElementById('tgl_pengembalian').value = new Date().toISOString().split('T')[0];
    
    // Tampilkan info rental
    const rentalInfo = document.getElementById('rentalInfo');
    rentalInfo.innerHTML = `
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
                <strong>Kendaraan:</strong><br>
                ${rentalData.merk} - ${rentalData.no_plat}
            </div>
            <div>
                <strong>Pelanggan:</strong><br>
                ${rentalData.nama_pelanggan}
            </div>
            <div>
                <strong>Tgl Sewa:</strong><br>
                ${formatDate(rentalData.tgl_sewa)}
            </div>
            <div>
                <strong>Tgl Kembali:</strong><br>
                ${formatDate(rentalData.tgl_kembali)}
            </div>
        </div>
    `;
    
    // Reset form
    document.getElementById('kondisi').value = 'baik';
    document.getElementById('keterangan').value = '';
    
    // Hitung denda
    hitungDenda();
    
    modal.classList.add('active');
}

function closeModal() {
    modal.classList.remove('active');
    currentRental = null;
}

function hitungDenda() {
    if (!currentRental) return;
    
    const tglPengembalian = document.getElementById('tgl_pengembalian').value;
    if (!tglPengembalian) return;
    
    const tglKembaliRencana = new Date(currentRental.tgl_kembali);
    const tglKembaliAktual = new Date(tglPengembalian);
    
    let denda = 0;
    if (tglKembaliAktual > tglKembaliRencana) {
        const diffTime = Math.abs(tglKembaliAktual - tglKembaliRencana);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        const dendaPerHari = 50000;
        denda = diffDays * dendaPerHari;
    }
    
    document.getElementById('denda').value = denda;
    document.getElementById('denda_display').value = 'Rp ' + denda.toLocaleString('id-ID');
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target == modal) {
        closeModal();
    }
}

// Validasi form
document.getElementById('pengembalianForm').addEventListener('submit', function(e) {
    if (!confirm('Yakin ingin memproses pengembalian kendaraan ini?')) {
        e.preventDefault();
        return false;
    }
});