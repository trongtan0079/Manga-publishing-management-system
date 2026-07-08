<?php
/**
 * Layout: Thanh điều hướng phía trên (navbar.php)
 * Chức năng: Hiển thị thương hiệu/logo, nút chuyển đổi menu (sidebar togglers) và thông tin tài khoản người dùng cùng dropdown thông báo.
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
<nav class="navbar navbar-expand-lg bg-white fixed-top">
    <div class="container-fluid px-3 px-lg-0 ps-lg-2 pe-lg-4">
        <!-- Nút bật tắt menu cho mobile -->
        <button class="btn btn-light d-lg-none me-3 shadow-sm border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Nút bật tắt thanh bên cho Desktop (Canh giữa tuyệt đối bằng position absolute ở vị trí 40px) -->
        <div class="d-none d-lg-flex align-items-center justify-content-center h-100" style="position: absolute; left: 0; top: 0; width: 80px; z-index: 1050;">
            <button id="desktopSidebarToggle" class="btn btn-link text-dark p-0 text-decoration-none shadow-none sidebar-toggler-btn">
                <i class="fas fa-bars fs-5"></i>
            </button>
        </div>

        <a class="navbar-brand d-flex align-items-center" style="margin-left: 68px !important;" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=dashboard&action=<?= (isset($_SESSION) && isset($_SESSION['role_name'])) ? htmlspecialchars($_SESSION['role_name']) : '' ?>">
            <i class="fas fa-book-open me-2 fs-3" style="color: var(--primary);"></i>
            <span class="fw-bold" style="color: var(--slate-800); font-size: 1.45rem; letter-spacing: -0.5px;">Manga<span style="color: var(--primary);">PMS</span></span>
        </a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-ellipsis-v text-dark"></i>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav ms-auto mb-2 mb-md-0 align-items-center">


                <li class="nav-item dropdown me-3">
                    <a class="nav-link position-relative d-flex align-items-center" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <i class="fas fa-bell fs-5"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span class="position-absolute badge text-white fw-bold d-inline-flex align-items-center justify-content-center" 
                                  style="top: 2px; right: -2px; font-size: 0.625rem; background-color: #ef4444 !important; color: #ffffff !important; border: 2px solid #ffffff !important; min-width: 18px; height: 18px; border-radius: 50rem !important; padding: 0 4px; line-height: 1; z-index: 10;">
                                <?= $unreadCount > 99 ? '99+' : $unreadCount ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3 p-0" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <li class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light sticky-top rounded-top">
                            <span class="fw-bold text-dark">Thông báo</span>
                            <?php if ($unreadCount > 0): ?>
                                <span class="badge bg-primary rounded-pill"><?= $unreadCount ?> mới</span>
                            <?php endif; ?>
                        </li>
                        <?php if (!empty($latestNotifications)): ?>
                            <?php foreach ($latestNotifications as $notif): ?>
                                <li>
                                    <a class="dropdown-item py-3 border-bottom <?= !$notif['is_read'] ? 'bg-light' : '' ?> text-wrap" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=notification&action=readAndRedirect&id=<?= $notif['notification_id'] ?>">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                                                <?php if (!$notif['is_read']): ?>
                                                    <span class="d-inline-block bg-primary rounded-circle me-1" style="width: 8px; height: 8px;"></span>
                                                <?php endif; ?>
                                                Thông báo
                                            </span>
                                            <small class="text-muted" style="font-size: 0.7rem;"><?= date('d/m H:i', strtotime($notif['created_at'])) ?></small>
                                        </div>
                                        <p class="mb-0 text-muted" style="font-size: 0.85rem; line-height: 1.4;"><?= htmlspecialchars($notif['message']) ?></p>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li class="sticky-bottom bg-white rounded-bottom">
                                <a class="dropdown-item text-center py-2 text-primary fw-bold" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=notification&action=index">Xem tất cả thông báo</a>
                            </li>
                        <?php else: ?>
                            <li class="p-4 text-center text-muted">
                                <i class="fas fa-bell-slash fs-4 mb-2 opacity-50"></i>
                                <p class="mb-0 small">Không có thông báo mới</p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <div class="vr mx-2 d-none d-lg-block" style="height: 30px; align-self: center;"></div>
                <li class="nav-item dropdown ms-2">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode((isset($_SESSION) && isset($_SESSION['full_name'])) ? $_SESSION['full_name'] : ((isset($_SESSION) && isset($_SESSION['username'])) ? $_SESSION['username'] : 'G')); ?>&background=6366f1&color=fff" alt="User" class="rounded-circle me-2" width="32" height="32">
                        <div class="d-none d-md-block text-start lh-1 me-1">
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars((isset($_SESSION) && isset($_SESSION['full_name'])) ? $_SESSION['full_name'] : ((isset($_SESSION) && isset($_SESSION['username'])) ? $_SESSION['username'] : 'Khách')); ?></div>
                            <small class="text-muted" style="font-size: 0.75rem;"><?php echo (isset($_SESSION) && isset($_SESSION['role_name'])) ? ucfirst(htmlspecialchars($_SESSION['role_name'])) : ''; ?></small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item py-2" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=auth&action=profile"><i class="fas fa-user-circle text-primary me-2"></i> Hồ sơ cá nhân</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger fw-bold" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=auth&action=logout"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
