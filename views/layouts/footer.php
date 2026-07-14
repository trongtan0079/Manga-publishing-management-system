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
        // We will show a slider/carousel inside the modal for all unread notifications (limit to 5)
        $unreadPopups = array_slice($unreadNotificationsForPopup, 0, 5);
        $totalPopups = count($unreadPopups);
        ?>
        <!-- Modal thông báo nổi bật ở giữa màn hình (dạng Slideshow cho nhiều thông báo) -->
        <div class="modal fade notif-modal" id="latestNotificationPopupModal" tabindex="-1" aria-labelledby="latestNotificationPopupLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header notif-modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-extrabold d-flex align-items-center gap-2" id="latestNotificationPopupLabel" style="font-size: 1.05rem; color: #0f172a;">
                            <span class="rounded-circle bg-primary-subtle p-2 d-inline-flex align-items-center justify-content-center text-primary" style="width: 32px; height: 32px; background-color: rgba(99, 102, 241, 0.1) !important;">
                                <i class="fas fa-bell" style="font-size: 0.9rem;"></i>
                            </span>
                            Thông báo mới nhất
                            <?php if ($totalPopups > 1): ?>
                                <span class="badge bg-primary text-white border-0 rounded-pill ms-2" style="font-size: 0.7rem; padding: 0.35em 0.8em;" id="popupIndexIndicator">1/<?= $totalPopups ?></span>
                            <?php endif; ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.8rem;"></button>
                    </div>
                    
                    <div id="notificationCarousel" class="carousel slide" data-bs-interval="false">
                        <div class="carousel-inner">
                            <?php foreach ($unreadPopups as $idx => $notif): 
                                $details = Notification::getTypeDetails($notif['type']);
                                $redirectUrl = (defined('BASE_PATH') ? BASE_PATH : '') . '/index.php?controller=notification&action=readAndRedirect&id=' . $notif['notification_id'];
                            ?>
                                <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                                    <div class="modal-body p-4 text-center">
                                        <div class="notif-icon-container" style="background: <?= $details['bg_gradient'] ?>;">
                                            <i class="fas <?= $details['icon'] ?> fs-2 text-white"></i>
                                            <div class="notif-icon-glow" style="color: <?= $details['color'] ?>;"></div>
                                        </div>
                                        <h6 class="notif-modal-title"><?= htmlspecialchars($details['label']) ?></h6>
                                        <p class="notif-message-text text-secondary mb-4"><?= htmlspecialchars($notif['message']) ?></p>
                                        
                                        <div class="d-flex gap-2.5 justify-content-center">
                                            <button type="button" class="btn notif-btn-secondary" data-bs-dismiss="modal">Bỏ qua</button>
                                            <a href="<?= $redirectUrl ?>" class="btn notif-btn-primary" style="background: <?= $details['bg_gradient'] ?>;">
                                                <i class="fas fa-arrow-right-to-bracket me-1.5"></i>Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if ($totalPopups > 1): ?>
                            <!-- Carousel Navigation Controls -->
                            <div class="d-flex justify-content-between align-items-center px-4 pb-4">
                                <button class="btn btn-link notif-nav-btn text-decoration-none p-0" type="button" data-bs-target="#notificationCarousel" data-bs-slide="prev">
                                    <i class="fas fa-chevron-left me-1"></i> Trước
                                </button>
                                <button class="btn btn-link notif-nav-btn text-decoration-none p-0" type="button" data-bs-target="#notificationCarousel" data-bs-slide="next">
                                    Sau <i class="fas fa-chevron-right ms-1"></i>
                                </button>
                            </div>
                        <?php endif; ?>
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
                
                var carouselEl = document.getElementById('notificationCarousel');
                if (carouselEl) {
                    carouselEl.addEventListener('slide.bs.carousel', function (event) {
                        var nextIndex = event.to + 1;
                        var indicator = document.getElementById('popupIndexIndicator');
                        if (indicator) {
                            indicator.innerText = nextIndex + '/' + <?= $totalPopups ?>;
                        }
                    });
                }
            });
        </script>
        <?php
    }
}
?>
</body>
</html>
