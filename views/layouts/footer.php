    </div> <!-- /container-fluid -->

    <!-- Footer -->
    <footer class="bg-white px-lg-5 px-4 py-3 border-top text-muted d-flex justify-content-between align-items-center flex-wrap mt-auto" style="font-size: 0.85rem;">
        <div class="mb-2 mb-md-0">
            &copy; <?= date('Y') ?> <strong>Manga<span class="text-primary">PMS</span></strong>. All rights reserved.
        </div>
        <div>
            <span class="text-muted">Phiên bản 1.0.0</span>
        </div>
    </footer>
</main> <!-- /main -->

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var toggleBtn = document.getElementById("desktopSidebarToggle");
        if (toggleBtn) {
            toggleBtn.addEventListener("click", function() {
                document.body.classList.toggle("sidebar-collapsed");
            });
        }
    });
</script>
<?php
// Chuẩn hóa Flash Message: Đảm bảo thông báo chỉ hiển thị 1 lần
unset($_SESSION['success']);
unset($_SESSION['error']);
unset($_SESSION['warning']);
unset($_SESSION['info']);
?>
</body>
</html>
