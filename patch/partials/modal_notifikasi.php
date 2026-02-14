<?php
// 1. Kita siapkan variabel untuk modal dari Session
//    Kita baca pesan dari Session, lalu langsung hapus.
$modal_show = false;
$modal_type = '';
$modal_title = '';
$modal_message = '';

if (isset($_SESSION['pesan_sukses'])) {
    $modal_show = true;
    $modal_type = 'success';
    $modal_title = 'Sukses!';
    $modal_message = $_SESSION['pesan_sukses'];
    unset($_SESSION['pesan_sukses']);
} elseif (isset($_SESSION['pesan_error'])) {
    $modal_show = true;
    $modal_type = 'danger'; // Tipe 'danger' untuk eror
    $modal_title = 'Gagal!';
    $modal_message = $_SESSION['pesan_error'];
    unset($_SESSION['pesan_error']);
} elseif (isset($_SESSION['pesan_info'])) { // [REVISI] Ditambahkan
    $modal_show = true;
    $modal_type = 'info'; // Tipe 'info' untuk biru
    $modal_title = 'Informasi';
    $modal_message = $_SESSION['pesan_info'];
    unset($_SESSION['pesan_info']);
}
?>

<!-- 2. HTML untuk Modal Pop-up Notifikasi -->
<!-- Kita gunakan Modal bawaan Bootstrap/AdminLTE -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- Header Modal: Warna berubah sesuai tipe (sukses/eror/info) -->
            <div class="modal-header <?php 
                // [REVISI] Diubah untuk menangani 3 tipe (bg-info untuk biru)
                echo ($modal_type == 'success') ? 'bg-success' : (($modal_type == 'info') ? 'bg-info' : 'bg-danger'); 
                ?> text-white">
                <h5 class="modal-title" id="modalLabel">
                    <!-- Ikon dinamis -->
                    <i class="icon fas <?php 
                        // [REVISI] Diubah untuk menangani 3 ikon (fa-info-circle untuk info)
                        echo ($modal_type == 'success') ? 'fa-check' : (($modal_type == 'info') ? 'fa-info-circle' : 'fa-ban'); 
                        ?> mr-2"></i>
                    <?php echo htmlspecialchars($modal_title); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <!-- Body Modal: Menampilkan pesan -->
            <div class="modal-body">
                <p class="text-lg text-center"><?php echo htmlspecialchars($modal_message); ?></p>
            </div>
            <!-- Footer Modal -->
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn <?php 
                    // [REVISI] Diubah untuk menangani 3 tombol (btn-info untuk biru)
                    echo ($modal_type == 'success') ? 'btn-success' : (($modal_type == 'info') ? 'btn-info' : 'btn-danger'); 
                    ?>" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- 3. JavaScript untuk memanggil modal -->
<!-- Script ini akan dipanggil oleh footer.php -->
<script>
$(function () {
  // Pastikan fungsi bsCustomFileInput.init(); tetap ada jika dipindah ke footer
  // bsCustomFileInput.init(); 

  // Tambahkan script untuk memanggil modal
  // Jika variabel $modal_show dari PHP adalah true, tampilkan modal-nya
  <?php if ($modal_show): ?>
    $('#notificationModal').modal('show');
  <?php endif; ?>
});
</script>