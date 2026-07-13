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
<!-- Quill JS Library -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var toggleBtn = document.getElementById("desktopSidebarToggle");
        if (toggleBtn) {
            toggleBtn.addEventListener("click", function() {
                document.body.classList.toggle("sidebar-collapsed");
            });
        }
    });

    // Hàm hỗ trợ chèn cú pháp in đậm, in nghiêng, gạch ngang cho textarea
    function insertFormatting(id, syntax) {
        const textarea = document.getElementById(id);
        if (!textarea) return;
        
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        let selectedText = text.substring(start, end);
        
        let placeholderUsed = false;
        if (selectedText.length === 0) {
            placeholderUsed = true;
            if (syntax === '**') selectedText = 'chữ in đậm';
            else if (syntax === '*') selectedText = 'chữ in nghiêng';
            else if (syntax === '~~') selectedText = 'chữ gạch ngang';
            else selectedText = 'văn bản';
        }
        
        const replacement = syntax + selectedText + syntax;
        
        textarea.value = text.substring(0, start) + replacement + text.substring(end);
        textarea.focus();
        
        if (placeholderUsed) {
            // Bôi đen chữ placeholder để người dùng gõ đè lên được ngay
            textarea.setSelectionRange(start + syntax.length, start + syntax.length + selectedText.length);
        } else {
            // Đưa con trỏ chuột về sau cụm từ định dạng
            textarea.setSelectionRange(start + replacement.length, start + replacement.length);
        }
        
        // Trigger input event
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
    }

    // Hàm hỗ trợ chèn danh sách gạch đầu dòng
    function insertList(id) {
        const textarea = document.getElementById(id);
        if (!textarea) return;
        
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        let selectedText = text.substring(start, end);
        
        let replacement = '';
        if (selectedText.length > 0) {
            replacement = selectedText.split('\n').map(line => line.startsWith('- ') ? line : '- ' + line).join('\n');
        } else {
            selectedText = 'dòng danh sách';
            replacement = '\n- ' + selectedText;
        }
        
        textarea.value = text.substring(0, start) + replacement + text.substring(end);
        textarea.focus();
        
        if (selectedText === 'dòng danh sách') {
            textarea.setSelectionRange(start + 3, start + 3 + selectedText.length);
        } else {
            textarea.setSelectionRange(start + replacement.length, start + replacement.length);
        }
        
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
    }
</script>
<?php
// Chuẩn hóa Flash Message: Đảm bảo thông báo chỉ hiển thị 1 lần
if (isset($_SESSION)) {
    unset($_SESSION['success']);
    unset($_SESSION['error']);
    unset($_SESSION['warning']);
    unset($_SESSION['info']);
}
?>
<?php
// Kiểm tra nếu có session đăng nhập và cờ just_logged_in đang được đặt
if (isset($_SESSION['user_id']) && !empty($_SESSION['just_logged_in'])) {
    // Tạm thời tắt cờ để chỉ hiển thị đúng 1 lần duy nhất sau khi đăng nhập thành công
    $_SESSION['just_logged_in'] = false;
    
    // Khởi tạo model Notification để lấy thông báo chưa đọc
    require_once __DIR__ . '/../../models/Notification.php';
    $notificationModelForPopup = new Notification();
    $unreadNotificationsForPopup = $notificationModelForPopup->findUnreadByUserId($_SESSION['user_id']);
    
    if (!empty($unreadNotificationsForPopup)) {
        // Lấy thông báo mới nhất chưa đọc
        $latestUnreadPopup = $unreadNotificationsForPopup[0];
        
        // Tùy chỉnh icon và nhãn loại thông báo
        $popupIcon = 'fa-bell';
        $popupColor = 'text-primary';
        $popupLabel = 'Thông báo mới';
        switch($latestUnreadPopup['type']) {
            case 'task_assigned': $popupIcon = 'fa-tasks'; $popupColor = 'text-warning'; $popupLabel = 'Công việc mới được giao'; break;
            case 'submission_submitted':
            case 'chapter_submitted': $popupIcon = 'fa-file-upload'; $popupColor = 'text-info'; $popupLabel = 'Bản thảo mới'; break;
            case 'review_created': $popupIcon = 'fa-comment-dots'; $popupColor = 'text-primary'; $popupLabel = 'Ý kiến nhận xét mới'; break;
            case 'submission_approved': $popupIcon = 'fa-check-circle'; $popupColor = 'text-success'; $popupLabel = 'Phê duyệt bản thảo'; break;
            case 'submission_rejected': $popupIcon = 'fa-times-circle'; $popupColor = 'text-danger'; $popupLabel = 'Từ chối bản thảo'; break;
            case 'ranking_published': $popupIcon = 'fa-trophy'; $popupColor = 'text-warning'; $popupLabel = 'Bảng xếp hạng mới'; break;
            case 'series_warning': $popupIcon = 'fa-exclamation-triangle'; $popupColor = 'text-danger'; $popupLabel = 'Cảnh báo bộ truyện'; break;
            case 'series_completed': $popupIcon = 'fa-flag-checkered'; $popupColor = 'text-success'; $popupLabel = 'Hoàn thành bộ truyện'; break;
            case 'series_submitted': $popupIcon = 'fa-folder-plus'; $popupColor = 'text-primary'; $popupLabel = 'Đề xuất truyện mới'; break;
        }
        
        $redirectUrl = (defined('BASE_PATH') ? BASE_PATH : '') . '/index.php?controller=notification&action=readAndRedirect&id=' . $latestUnreadPopup['notification_id'];
        ?>
        <!-- Modal thông báo nổi bật ở giữa màn hình -->
        <div class="modal fade" id="latestNotificationPopupModal" tabindex="-1" aria-labelledby="latestNotificationPopupLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-extrabold text-slate-800 d-flex align-items-center gap-2" id="latestNotificationPopupLabel" style="font-size: 1.1rem;">
                            <span class="rounded-circle bg-primary-subtle p-2 d-inline-flex align-items-center justify-content-center text-primary" style="width: 32px; height: 32px;">
                                <i class="fas fa-bell"></i>
                            </span>
                            Thông báo mới nhất
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm border bg-light" style="width: 72px; height: 72px;">
                            <i class="fas <?= $popupIcon ?> <?= $popupColor ?> fs-1"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2"><?= htmlspecialchars($popupLabel) ?></h6>
                        <p class="text-secondary mb-4 px-2" style="font-size: 0.92rem; line-height: 1.5;"><?= htmlspecialchars($latestUnreadPopup['message']) ?></p>
                        
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2.5" data-bs-dismiss="modal" style="border-radius: 12px; font-weight: 600; font-size: 0.85rem;">Bỏ qua</button>
                            <a href="<?= $redirectUrl ?>" class="btn btn-primary px-4 py-2.5 shadow-sm" style="border-radius: 12px; font-weight: 600; font-size: 0.85rem;">
                                <i class="fas fa-external-link-alt me-1.5"></i>Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('latestNotificationPopupModal'), {
                    keyboard: false
                });
                myModal.show();
            });
        </script>
        <?php
    }
}
?>
</body>
</html>
