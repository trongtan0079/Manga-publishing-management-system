    </div> <!-- /container-fluid -->

    <!-- Footer -->
    <footer class="py-3 mt-auto bg-white border-top border-light-subtle" style="font-size: 0.825rem; color: var(--slate-500);">
        <div class="container-fluid px-lg-5">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    &copy; <?= date('Y') ?> <strong class="text-dark">Manga<span style="color: #6366f1;">PMS</span></strong>. Toàn bộ bản quyền được bảo lưu.
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span>Phiên bản <strong>1.1.0 Stable</strong></span>
                    <span style="opacity: 0.5;">|</span>
                    <span>Hệ thống quản lý quy trình xuất bản</span>
                </div>
            </div>
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
