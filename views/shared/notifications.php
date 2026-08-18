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
            <?= Csrf::field() ?>
            <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fas fa-check-double me-2"></i>Đánh dấu tất cả đã đọc</button>
        </form>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4 bg-light-subtle">
        <?php if (!empty($notifications)): ?>
            <div class="notif-list-container">
                <?php foreach ($notifications as $notif): 
                    $details = Notification::getTypeDetails($notif['type']);
                ?>
                    <div class="notif-list-item d-flex gap-3 align-items-center <?= !$notif['is_read'] ? 'unread' : '' ?>">
                        <div class="d-flex align-items-center">
                            <?php if (!$notif['is_read']): ?>
                                <span class="notif-badge-indicator me-3" title="Chưa đọc"></span>
                            <?php else: ?>
                                <span class="me-3" style="width: 10px; height: 10px; display: inline-block;"></span>
                            <?php endif; ?>
                            
                            <div class="notif-list-icon" style="background: <?= $details['bg_gradient'] ?>;">
                                <i class="fas <?= $details['icon'] ?> fs-5 text-white"></i>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center w-100 ms-2">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=notification&action=readAndRedirect&id=<?= $notif['notification_id'] ?>" class="text-decoration-none text-dark hover-primary-text">
                                    <strong class="mb-0 text-slate-800" style="font-size: 0.95rem; font-weight: 700;"><?= htmlspecialchars($details['label']) ?></strong>
                                    <?php if (isset($notif['username']) && isset($notif['full_name'])): ?>
                                        <span class="badge bg-secondary ms-2" style="font-size: 0.7rem; font-weight: normal; vertical-align: middle;">
                                            Gửi tới: <?= htmlspecialchars($notif['full_name'] . ' (@' . $notif['username'] . ')') ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                                <small class="text-muted" style="font-size: 0.78rem; font-weight: 500;"><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></small>
                            </div>
                            <p class="mb-0 fw-normal">
                                <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=notification&action=readAndRedirect&id=<?= $notif['notification_id'] ?>" class="text-decoration-none text-secondary hover-primary-text" style="font-size: 0.88rem; font-weight: 500; color: #64748b !important;">
                                    <?= htmlspecialchars($notif['message']) ?>
                                </a>
                            </p>
                        </div>
                        <div class="d-flex align-items-center ms-3">
                            <?php if (!$notif['is_read']): ?>
                                <form action="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=notification&action=markAsRead&id=<?= $notif['notification_id'] ?>" method="POST" class="m-0">
                                    <?= Csrf::field() ?>
                                    <button type="submit" class="btn btn-sm btn-light border shadow-sm px-2.5 py-1.5" style="border-radius: 8px;" title="Đánh dấu đã đọc">
                                        <i class="fas fa-check text-success"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5" style="border-radius: 8px; font-weight: 600;"><i class="fas fa-check me-1"></i> Đã đọc</span>
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

<style>
.hover-primary-text {
    transition: color 0.15s ease-in-out;
}
.hover-primary-text:hover {
    color: var(--primary, #6366f1) !important;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

