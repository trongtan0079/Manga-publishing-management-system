<nav class="navbar navbar-expand-lg bg-white fixed-top">
    <div class="container-fluid px-4">
        <!-- Nút bật tắt menu cho mobile -->
        <button class="btn btn-light d-lg-none me-3 shadow-sm border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
            <i class="fas fa-bars"></i>
        </button>
        
        <a class="navbar-brand d-flex align-items-center" href="#">
            <div class="bg-primary text-white rounded p-2 me-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i class="fas fa-book-open fs-5"></i>
            </div>
            <span>Manga<span style="color: var(--primary-color)">PMS</span></span>
        </a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-ellipsis-v text-dark"></i>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <form class="d-none d-md-flex ms-4" style="width: 300px;">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control bg-light border-0 shadow-none" placeholder="Tìm kiếm truyện, chương...">
                </div>
            </form>
            
            <ul class="navbar-nav ms-auto mb-2 mb-md-0 align-items-center">
                <li class="nav-item me-3">
                    <a class="nav-link position-relative" href="#">
                        <i class="fas fa-bell fs-5"></i>
                        <span class="position-absolute top-25 start-75 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    </a>
                </li>
                <div class="vr mx-2 d-none d-lg-block" style="height: 30px; align-self: center;"></div>
                <li class="nav-item dropdown ms-2">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(isset($_SESSION['username']) ? $_SESSION['username'] : 'G'); ?>&background=6366f1&color=fff" alt="User" class="rounded-circle me-2" width="32" height="32">
                        <div class="d-none d-md-block text-start lh-1 me-1">
                            <div class="fw-bold text-dark"><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Khách'; ?></div>
                            <small class="text-muted" style="font-size: 0.75rem;"><?php echo isset($_SESSION['role_name']) ? ucfirst(htmlspecialchars($_SESSION['role_name'])) : ''; ?></small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item py-2" href="#"><i class="fas fa-user-circle text-primary me-2"></i> Hồ sơ cá nhân</a></li>
                        <li><a class="dropdown-item py-2" href="#"><i class="fas fa-cog text-secondary me-2"></i> Cài đặt</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger fw-bold" href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=auth&action=logout"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
