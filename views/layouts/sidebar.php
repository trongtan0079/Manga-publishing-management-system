<?php
/**
 * Layout: Thanh công cụ/Thanh bên điều hướng (sidebar.php)
 * Chức năng: Hiển thị các liên kết chức năng của hệ thống dựa trên phân quyền vai trò (Role) của người dùng đang đăng nhập.
 * 
 * @var string $role Vai trò của người dùng hiện tại lấy từ session
 */
$role = isset($_SESSION) ? ($_SESSION['role_name'] ?? '') : '';
?>
<div class="sidebar-custom offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header border-bottom border-light border-opacity-25 d-lg-none">
        <h5 class="offcanvas-title text-white fw-bold d-flex align-items-center" id="sidebarLabel">
            <i class="fas fa-book-open me-2 fs-5" style="color: #a5b4fc;"></i>
            Manga<span style="color: #a5b4fc;">PMS</span>
        </h5>
        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
    </div>

    <div class="sidebar-sticky offcanvas-body p-0 d-flex flex-column">
        <ul class="nav flex-column w-100 px-2 mt-3">
            <li class="nav-item">
                <a class="nav-link <?= (isset($current_page) && $current_page == 'dashboard') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=dashboard&action=<?= htmlspecialchars($role) ?>">
                    <i class="fas fa-home"></i> <span>Bảng điều khiển</span>
                </a>
            </li>

            <?php if ($role === 'admin'): ?>
                <li class="nav-item nav-category">Hệ thống</li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'users') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=user&action=index">
                        <i class="fas fa-users"></i> <span>Quản lý Người dùng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'roles') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=user&action=roles">
                        <i class="fas fa-user-tag"></i> <span>Quản lý Vai trò</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'logs') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=dashboard&action=logs">
                        <i class="fas fa-history"></i> <span>Nhật ký hoạt động</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array($role, ['mangaka', 'editor', 'board', 'admin'])): ?>
                <li class="nav-item nav-category">Quản lý Xuất bản</li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'series') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=series&action=index">
                        <i class="fas fa-book"></i>
                        <div class="d-flex flex-column overflow-hidden flex-grow-1" style="min-width: 0;">
                            <span>Dự án Truyện</span>
                            <?= $role !== 'mangaka' ? '<span class="nav-subtitle"><i class="fas fa-lock" style="font-size: 0.55rem; width: auto; margin-right: 4px;"></i>Chỉ xem</span>' : '' ?>
                        </div>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array($role, ['assistant', 'editor'])): ?>
                <li class="nav-item nav-category">Tiến độ & Quy trình</li>
            <?php endif; ?>

            <?php if ($role === 'editor'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'progress') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=dashboard&action=progress">
                        <i class="fas fa-calendar-check"></i> <span>Tiến độ & Deadline</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'dossiers') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=series&action=dossiers">
                        <i class="fas fa-folder-open"></i> <span>Hồ sơ & Bảo vệ Series</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($role === 'assistant'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'tasks') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=task&action=index">
                        <i class="fas fa-tasks"></i> <span>Công việc của tôi</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array($role, ['mangaka', 'assistant', 'editor'])): ?>
                <?php 
                    $subText = 'Danh sách Bản thảo';
                    if ($role === 'assistant') {
                        $subText = 'Sản phẩm đã nộp';
                    } elseif ($role === 'mangaka') {
                        $subText = 'Bản thảo của tôi';
                    }
                ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'submissions') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=submission&action=index">
                        <i class="fas fa-cloud-upload-alt"></i> <span><?= $subText ?></span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array($role, ['mangaka', 'editor'])): ?>
                <?php 
                    $revText = ($role === 'mangaka') ? 'Duyệt bài Trợ lý' : 'Đánh giá & Phê duyệt';
                ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'reviews') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=review&action=index">
                        <i class="fas fa-clipboard-check"></i> <span><?= $revText ?></span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array($role, ['board', 'mangaka', 'editor', 'admin'])): ?>
                <li class="nav-item nav-category">Báo cáo & Thống kê</li>
                <?php if ($role === 'board'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'publish_series') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=series&action=publish">
                        <i class="fas fa-check-circle"></i> <span>Duyệt Series</span>
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'rankings') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=seriesRanking&action=index">
                        <i class="fas fa-chart-line"></i>
                        <div class="d-flex flex-column overflow-hidden flex-grow-1" style="min-width: 0;">
                            <span>Xếp hạng Manga</span>
                            <?= $role !== 'board' ? '<span class="nav-subtitle"><i class="fas fa-lock" style="font-size: 0.55rem; width: auto; margin-right: 4px;"></i>Chỉ xem</span>' : '' ?>
                        </div>
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item nav-category">Cá nhân</li>
            <li class="nav-item">
                <a class="nav-link <?= (isset($current_page) && $current_page == 'notifications') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=notification&action=index">
                    <i class="fas fa-bell"></i> <span>Thông báo</span>
                </a>
            </li>
        </ul>

        <!-- Hồ sơ cá nhân - Nằm trong dòng cuộn để tránh đè chồng chữ ở chiều cao ngắn -->
        <div class="mt-auto mt-4 px-3 pb-3 pt-2 w-100">
            <div class="border-top border-light border-opacity-25 pt-3">
                <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=auth&action=profile" class="d-flex align-items-center text-decoration-none rounded-3 px-2 py-2 sidebar-profile-link <?= (isset($current_page) && $current_page == 'profile') ? 'active' : '' ?>">
                    <img src="<?= getUserAvatarUrl($_SESSION['user_id'], (isset($_SESSION['full_name']) ? $_SESSION['full_name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'U'))) ?>" 
                         alt="Avatar" class="rounded-circle me-2 flex-shrink-0" width="36" height="36" style="object-fit: cover;">
                    <div style="min-width: 0;" class="flex-grow-1">
                        <div class="text-white fw-semibold text-truncate" style="font-size: 0.85rem;"><?= htmlspecialchars(isset($_SESSION) ? ($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User') : 'User') ?></div>
                        <div class="text-white-50 text-truncate" style="font-size: 0.7rem;"><?= ucfirst(htmlspecialchars($role)) ?></div>
                    </div>
                    <i class="fas fa-ellipsis-v text-white-50 ms-2 flex-shrink-0" style="font-size: 0.75rem;"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Wrapper -->
<main class="d-flex flex-column">
    <div class="container-fluid px-lg-5 py-4 flex-grow-1">