    </div> <!-- /container-fluid -->

    <!-- Footer -->
    <footer class="bg-white px-lg-5 px-4 py-3 border-top text-muted d-flex justify-content-between align-items-center flex-wrap mt-auto" style="font-size: 0.85rem;">
        <div class="mb-2 mb-md-0">
            &copy; <?= date('Y') ?> <strong>Manga<span class="text-primary">PMS</span></strong>. All rights reserved.
        </div>
        <div>
            <a href="#" class="text-decoration-none text-muted me-3">Chính sách bảo mật</a>
            <a href="#" class="text-decoration-none text-muted me-3">Điều khoản sử dụng</a>
            <a href="#" class="text-decoration-none text-muted">Trợ giúp</a>
            <span class="ms-3 ms-md-4 text-muted border-start ps-3 border-secondary border-opacity-25">Phiên bản 1.0.0</span>
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
</body>
</html>
