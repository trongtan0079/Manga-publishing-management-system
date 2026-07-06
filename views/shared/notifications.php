<?php 
$pageTitle = 'Tất cả thông báo';
$current_page = 'notifications';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';

$unreadCount = 0;
$vars = get_defined_vars();
if (array_key_exists('this', $vars) && is_object($vars['this'])) {
    $unreadCount = $vars['this']->unreadCount ?? 0;
} elseif (isset($GLOBALS['controller']) && is_object($GLOBALS['controller'])) {
    $unreadCount = $GLOBALS['controller']->unreadCount ?? 0;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Tất cả thông báo</h2>
        <p class="text-muted text-xs mb-0">Quản lý và xem lịch sử thông báo của bạn.</p>
    </div>
    <?php if ($unreadCount > 0): ?>
        <form action="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=notification&action=markAllAsRead" method="POST" class="m-0">
            <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fas fa-check-double me-2"></i>Đánh dấu tất cả đã đọc</button>
        </form>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <?php if (!empty($notifications)): ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $notif): ?>
                    <div class="list-group-item d-flex gap-3 py-4 <?= !$notif['is_read'] ? 'bg-light' : '' ?>">
                        <div class="d-flex align-items-center">
                            <?php if (!$notif['is_read']): ?>
                                <span class="badge bg-primary p-1 border border-light rounded-circle me-3" style="width: 12px; height: 12px;"><span class="visually-hidden">New alerts</span></span>
                            <?php else: ?>
                                <span class="p-1 me-3" style="width: 12px; height: 12px;"></span>
                            <?php endif; ?>
                            
                            <?php 
                                // Tùy chỉnh icon và màu sắc hiển thị dựa trên loại thông báo (type)
                                $icon = 'fa-bell';
                                $color = 'text-secondary';
                                $typeLabel = 'Thông báo';
                                switch($notif['type']) {
                                    case 'task_assigned': $icon = 'fa-tasks'; $color = 'text-warning'; $typeLabel = 'Task mới'; break;
                                    case 'submission_submitted':
                                    case 'chapter_submitted': $icon = 'fa-file-upload'; $color = 'text-info'; $typeLabel = 'Bản thảo'; break;
                                    case 'review_created': $icon = 'fa-comment-dots'; $color = 'text-primary'; $typeLabel = 'Nhận xét'; break;
                                    case 'submission_approved': $icon = 'fa-check-circle'; $color = 'text-success'; $typeLabel = 'Phê duyệt'; break;
                                    case 'submission_rejected': $icon = 'fa-times-circle'; $color = 'text-danger'; $typeLabel = 'Từ chối'; break;
                                    case 'ranking_published': $icon = 'fa-trophy'; $color = 'text-warning'; $typeLabel = 'Xếp hạng'; break;
                                    case 'series_warning': $icon = 'fa-exclamation-triangle'; $color = 'text-danger'; $typeLabel = 'Cảnh báo'; break;
                                    case 'series_completed': $icon = 'fa-flag-checkered'; $color = 'text-success'; $typeLabel = 'Bộ truyện'; break;
                                    case 'series_submitted': $icon = 'fa-folder-plus'; $color = 'text-primary'; $typeLabel = 'Đề xuất mới'; break;
                                }
                            ?>
                            <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center border" style="width: 48px; height: 48px;">
                                <i class="fas <?= $icon ?> <?= $color ?> fs-4"></i>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center w-100 ms-2">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                <strong class="mb-1 text-dark"><?= $typeLabel ?></strong>
                                <small class="text-muted" style="font-size: 0.8rem;"><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></small>
                            </div>
                            <p class="mb-1 text-dark fw-normal"><?= htmlspecialchars($notif['message']) ?></p>
                        </div>
                        <div class="d-flex align-items-center ms-3">
                            <?php if (!$notif['is_read']): ?>
                                <form action="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=notification&action=markAsRead&id=<?= $notif['notification_id'] ?>" method="POST" class="m-0">
                                    <button type="submit" class="btn btn-sm btn-light border shadow-sm" title="Đánh dấu đã đọc">
                                        <i class="fas fa-check text-success"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fas fa-check me-1"></i> Đã đọc</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-notification-7359560-6024628.png" alt="No notifications" class="img-fluid mb-3" style="max-height: 150px; opacity: 0.7;">
                <h5 class="text-muted mb-0">Chưa có thông báo nào</h5>
                <p class="text-muted text-sm mt-2">Khi có hoạt động mới, thông báo sẽ hiển thị tại đây.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
