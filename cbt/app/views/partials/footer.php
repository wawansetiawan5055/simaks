</div><!-- /.content-wrapper -->
</div>
<!-- /.content-wrapper -->

</div>
<!-- /.wrapper -->

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($extra_scripts))
    echo $extra_scripts; ?>

<script>
    // Global confirm delete helper
    function cbConfirm(url, msg = 'Data akan dihapus permanen!') {
        Swal.fire({
            title: 'Yakin?',
            text: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e94560',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    // Initialize AdminLTE
    $(function () {
        // Initialize layout
        if (typeof AdminLTEOptions !== 'undefined') {
            $('body').AdminLTE();
        }

        // Push menu toggle
        $('[data-widget="pushmenu"]').click(function (e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-collapse');
        });
    });
</script>
</body>

</html>

</html>