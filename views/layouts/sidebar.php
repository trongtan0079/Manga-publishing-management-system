<div class="sidebar-custom offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header border-bottom border-secondary d-lg-none">
        <h5 class="offcanvas-title text-white" id="sidebarLabel"><i class="fas fa-book-open text-primary me-2"></i>Manga PMS</h5>
        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
    </div>
    <div class="sidebar-sticky offcanvas-body flex-column p-0">
        <ul class="nav flex-column w-100 px-2 mt-3">
            <li class="nav-item">
                <a class="nav-link <?= (isset($current_page) && $current_page == 'dashboard') ? 'active' : '' ?>" href="#">
                    <i class="fas fa-home"></i> Bảng điều khiển
                </a>
            </li>
            
            <div class="nav-category">Quản lý Xuất bản</div>
            
            <li class="nav-item">
                <a class="nav-link <?= (isset($current_page) && $current_page == 'series') ? 'active' : '' ?>" href="#">
                    <i class="fas fa-book"></i> Dự án Truyện (Series)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (isset($current_page) && $current_page == 'chapters') ? 'active' : '' ?>" href="#">
                    <i class="fas fa-layer-group"></i> Quản lý Chương
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (isset($current_page) && $current_page == 'pages') ? 'active' : '' ?>" href="#">
                    <i class="fas fa-file-image"></i> Hình ảnh (Pages)
                </a>
            </li>
            
            <div class="nav-category">Tiến độ & Quy trình</div>
            
            <li class="nav-item">
                <a class="nav-link <?= (isset($current_page) && $current_page == 'tasks') ? 'active' : '' ?>" href="#">
                    <i class="fas fa-tasks"></i> Phân công Công việc
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (isset($current_page) && $current_page == 'submissions') ? 'active' : '' ?>" href="#">
                    <i class="fas fa-cloud-upload-alt"></i> Bản thảo đã nộp
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (isset($current_page) && $current_page == 'reviews') ? 'active' : '' ?>" href="#">
                    <i class="fas fa-comment-dots"></i> Phê duyệt & Đánh giá
                </a>
            </li>
            
            <div class="nav-category">Báo cáo & Thống kê</div>
            
            <li class="nav-item">
                <a class="nav-link <?= (isset($current_page) && $current_page == 'rankings') ? 'active' : '' ?>" href="#">
                    <i class="fas fa-chart-line"></i> Xếp hạng Manga
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (isset($current_page) && $current_page == 'notifications') ? 'active' : '' ?>" href="#">
                    <i class="fas fa-bell"></i> Lịch sử Thông báo
                </a>
            </li>
        </ul>
        
        <div class="mt-auto p-3 w-100">
            <div class="bg-dark bg-opacity-50 rounded-3 p-3 text-center">
                <p class="text-white mb-2" style="font-size: 0.8rem;">Cần hỗ trợ?</p>
                <button class="btn btn-sm btn-outline-light w-100 border-0" style="background: rgba(255,255,255,0.1);"><i class="fas fa-headset me-2"></i>Liên hệ IT</button>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Wrapper -->
<main>
    <div class="container-fluid px-lg-5 py-4">
