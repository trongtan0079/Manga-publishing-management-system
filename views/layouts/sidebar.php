<?php
$role = $_SESSION['role_name'] ?? '';
?>
<div class="sidebar-custom offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header border-bottom border-light border-opacity-25 d-lg-none">
        <h5 class="offcanvas-title text-white fw-bold d-flex align-items-center" id="sidebarLabel">
            <div class="bg-white text-primary rounded p-1 me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                <i class="fas fa-book-open fs-6"></i>
            </div>
            Manga<span class="text-white-50">PMS</span>
        </h5>
        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
    </div>

    <div class="sidebar-sticky offcanvas-body p-0">
        <ul class="nav flex-column w-100 px-2 mt-3">
            <li class="nav-item">
                <a class="nav-link <?= (isset($current_page) && $current_page == 'dashboard') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=auth&action=login">
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
            <?php endif; ?>

            <?php if (in_array($role, ['admin', 'mangaka', 'editor'])): ?>
                <li class="nav-item nav-category">Quản lý Xuất bản</li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'series') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=series&action=index">
                        <i class="fas fa-book"></i> <span>Dự án Truyện</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($role === 'mangaka'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'chapters') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=chapter&action=index">
                        <i class="fas fa-layer-group"></i> <span>Quản lý Chương</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array($role, ['mangaka', 'assistant', 'editor'])): ?>
                <li class="nav-item nav-category">Tiến độ & Quy trình</li>
            <?php endif; ?>

            <?php if (in_array($role, ['mangaka', 'assistant'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'tasks') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=task&action=index">
                        <i class="fas fa-tasks"></i> <span>Phân công Công việc</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array($role, ['mangaka', 'assistant', 'editor'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'submissions') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=submission&action=index">
                        <i class="fas fa-cloud-upload-alt"></i> <span>Bản thảo & Phê duyệt</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array($role, ['admin', 'board', 'mangaka', 'editor'])): ?>
                <li class="nav-item nav-category">Báo cáo & Thống kê</li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'rankings') ? 'active' : '' ?>" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=seriesranking&action=index">
                        <i class="fas fa-chart-line"></i>
                        <div class="d-flex flex-column overflow-hidden flex-grow-1" style="min-width: 0;">
                            <span>Xếp hạng Manga</span>
                            <?= in_array($role, ['admin', 'editor']) ? '<span class="nav-subtitle"><i class="fas fa-lock" style="font-size: 0.55rem; width: auto; margin-right: 4px;"></i>Chỉ xem</span>' : '' ?>
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

    </div>
</div>

<!-- Main Content Wrapper -->
<main class="d-flex flex-column">
    <div class="container-fluid px-lg-5 py-4 flex-grow-1">