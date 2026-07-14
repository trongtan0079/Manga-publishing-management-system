<?php
/**
 * Component View: Danh sách thông báo thu gọn hiển thị trên Dashboard (dashboard_notifications.php)
 * Vai trò: Tất cả người dùng sau khi đăng nhập
 * Chức năng: Hiển thị nhanh các thông báo chưa đọc và danh sách thông báo mới nhất kèm các tác vụ đánh dấu đã đọc.
 */
$unreadCount = 0;
$latestNotifications = [];

$vars = get_defined_vars();
if (array_key_exists('this', $vars) && is_object($vars['this'])) {
    $unreadCount = $vars['this']->unreadCount ?? 0;
    $latestNotifications = $vars['this']->latestNotifications ?? [];
} elseif (isset($GLOBALS['controller']) && is_object($GLOBALS['controller'])) {
    $unreadCount = $GLOBALS['controller']->unreadCount ?? 0;
    $latestNotifications = $GLOBALS['controller']->latestNotifications ?? [];
}
?>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold"><i class="fas fa-bell text-primary me-2"></i>Thông báo mới (<?= $unreadCount ?>)</h6>
        <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=notification&action=index" class="text-decoration-none text-sm">Xem tất cả</a>
    </div>
    <div class="card-body">
        <?php if (!empty($latestNotifications)): ?>
            <div class="notif-list-container">
                <?php foreach ($latestNotifications as $notif): 
                    $details = Notification::getTypeDetails($notif['type']);
                ?>
                    <div class="notif-list-item d-flex gap-3 align-items-center py-2.5 px-3 <?= !$notif['is_read'] ? 'unread' : '' ?>" style="border-radius: 12px; margin-bottom: 8px; border: 1px solid #f1f5f9; transition: all 0.2s ease;">
                        <div class="d-flex align-items-center">
                            <?php if (!$notif['is_read']): ?>
                                <span class="notif-badge-indicator me-2" style="width: 8px; height: 8px;" title="Chưa đọc"></span>
                            <?php else: ?>
                                <span class="me-2" style="width: 8px; height: 8px; display: inline-block;"></span>
                            <?php endif; ?>
                            
                            <div class="notif-list-icon shadow-sm d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 10px; background: <?= $details['bg_gradient'] ?>;">
                                <i class="fas <?= $details['icon'] ?> text-white" style="font-size: 0.85rem;"></i>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center w-100 ms-1">
                            <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=notification&action=readAndRedirect&id=<?= $notif['notification_id'] ?>" class="text-decoration-none text-dark hover-primary-text">
                                <h6 class="mb-1 text-sm fw-semibold text-slate-800" style="font-size: 0.88rem; line-height: 1.4;"><?= htmlspecialchars($notif['message']) ?></h6>
                            </a>
                            <small class="text-muted" style="font-size: 0.72rem; font-weight: 500;"><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></small>
                        </div>
                        <?php if (!$notif['is_read']): ?>
                            <div class="d-flex align-items-center ms-auto">
                                <form action="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=notification&action=markAsRead&id=<?= $notif['notification_id'] ?>" method="POST" class="m-0">
                                    <button type="submit" class="btn btn-sm btn-link text-decoration-none p-1" title="Đánh dấu đã đọc">
                                        <i class="fas fa-check text-success" style="font-size: 0.85rem;"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <style>
            .hover-primary-text {
                transition: color 0.15s ease-in-out;
            }
            .hover-primary-text:hover {
                color: var(--primary, #6366f1) !important;
            }
            </style>
        <?php else: ?>
            <div class="text-center py-4">
                <div class="text-muted mb-2"><i class="fas fa-bell-slash fs-1 text-light"></i></div>
                <p class="text-muted mb-0">Bạn không có thông báo nào.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
