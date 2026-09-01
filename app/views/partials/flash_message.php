<?php
// app/views/partials/flash_message.php

$msg_success = $_SESSION['pesan_sukses'] ?? $_SESSION['success'] ?? null;
$msg_error   = $_SESSION['pesan_error'] ?? $_SESSION['error'] ?? null;
$msg_info    = $_SESSION['pesan_info'] ?? $_SESSION['info'] ?? null;
$msg_warning = $_SESSION['pesan_warning'] ?? $_SESSION['warning'] ?? null;

if ($msg_success) {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: ' . json_encode($msg_success) . ',
                    timer: 2500,
                    showConfirmButton: false,
                    customClass: { popup: "rounded-xl shadow-lg" }
                });
            }
        });
    </script>';
    echo '<div class="alert alert-success alert-dismissible fade show shadow-sm d-none" role="alert" style="border-radius: 12px; border-left: 4px solid #16a34a;">
            <i class="fas fa-check-circle mr-2 fa-lg"></i> <strong>Berhasil!</strong> ' . htmlspecialchars($msg_success) . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>';
    unset($_SESSION['pesan_sukses'], $_SESSION['success']);
}

if ($msg_error) {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    icon: "error",
                    title: "Peringatan / Error",
                    text: ' . json_encode($msg_error) . ',
                    confirmButtonColor: "#dc2626",
                    customClass: { popup: "rounded-xl shadow-lg" }
                });
            }
        });
    </script>';
    echo '<div class="alert alert-danger alert-dismissible fade show shadow-sm d-none" role="alert" style="border-radius: 12px; border-left: 4px solid #dc2626;">
            <i class="fas fa-exclamation-triangle mr-2 fa-lg"></i> <strong>Peringatan / Error:</strong> ' . htmlspecialchars($msg_error) . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>';
    unset($_SESSION['pesan_error'], $_SESSION['error']);
}

if ($msg_info) {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    icon: "info",
                    title: "Informasi",
                    text: ' . json_encode($msg_info) . ',
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: { popup: "rounded-xl shadow-lg" }
                });
            }
        });
    </script>';
    echo '<div class="alert alert-info alert-dismissible fade show shadow-sm d-none" role="alert" style="border-radius: 12px; border-left: 4px solid #0284c7;">
            <i class="fas fa-info-circle mr-2 fa-lg"></i> ' . htmlspecialchars($msg_info) . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>';
    unset($_SESSION['pesan_info'], $_SESSION['info']);
}

if ($msg_warning) {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    icon: "warning",
                    title: "Perhatian",
                    text: ' . json_encode($msg_warning) . ',
                    timer: 3500,
                    showConfirmButton: false,
                    customClass: { popup: "rounded-xl shadow-lg" }
                });
            }
        });
    </script>';
    echo '<div class="alert alert-warning alert-dismissible fade show shadow-sm d-none" role="alert" style="border-radius: 12px; border-left: 4px solid #d97706;">
            <i class="fas fa-exclamation-circle mr-2 fa-lg"></i> ' . htmlspecialchars($msg_warning) . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>';
    unset($_SESSION['pesan_warning'], $_SESSION['warning']);
}
?>