<?php 
/**
 * Quản lý Vai trò (Chỉ đọc) - Hiển thị thông tin và số lượng người dùng của từng vai trò trong hệ thống.
 * @var array $rolesWithCount Danh sách các vai trò kèm theo số lượng thành viên tương ứng
 */

if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}

$pageTitle = 'Quản lý Vai trò';
$current_page = 'roles';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';

// Cấu hình metadata cho các vai trò để render động qua JS
$rolesMetadata = [];
$roleDescriptions = [
    'admin'     => 'Quản trị viên hệ thống có đặc quyền cao nhất để vận hành, cấu hình hạ tầng, quản lý phân quyền và giám sát toàn bộ hoạt động của hệ thống.',
    'mangaka'   => 'Họa sĩ chính / Tác giả sáng tác cốt truyện và nét vẽ chính. Nguồn tạo ra các bộ truyện, quản lý tiến độ vẽ và điều phối nhóm trợ lý của mình.',
    'assistant' => 'Trợ lý họa sĩ hỗ trợ hoàn thiện chi tiết bản vẽ theo sự phân công trực tiếp của Họa sĩ chính, giúp đẩy nhanh tiến trình sản xuất trang bản thảo.',
    'editor'    => 'Biên tập viên phụ trách kiểm soát chất lượng nghệ thuật, kịch bản, chấm điểm chuyên môn và ra quyết định phê duyệt/từ chối bản thảo chương truyện.',
    'board'     => 'Ban giám đốc / Hội đồng đánh giá chiến lược. Giám sát tổng thể hoạt động xuất bản, tổ chức các kỳ đánh giá xếp hạng và đưa ra quyết định thương mại hóa.',
];

$roleIcons = [
    'admin'     => 'fa-user-shield',
    'mangaka'   => 'fa-paint-brush',
    'assistant' => 'fa-hands-helping',
    'editor'    => 'fa-pen-fancy',
    'board'     => 'fa-gavel',
];

$roleColors = [
    'admin'     => '#ef4444',
    'mangaka'   => '#4f46e5',
    'assistant' => '#0ea5e9',
    'editor'    => '#f59e0b',
    'board'     => '#10b981',
];

$roleRgb = [
    'admin'     => '239, 68, 68',
    'mangaka'   => '79, 70, 229',
    'assistant' => '14, 165, 233',
    'editor'    => '245, 158, 11',
    'board'     => '16, 185, 129',
];

$roleBadges = [
    'admin'     => 'bg-danger-subtle text-danger border-danger-subtle',
    'mangaka'   => 'bg-primary-subtle text-primary border-primary-subtle',
    'assistant' => 'bg-info-subtle text-info border-info-subtle',
    'editor'    => 'bg-warning-subtle text-warning border-warning-subtle',
    'board'     => 'bg-success-subtle text-success border-success-subtle',
];

$roleTimeline = [
    'admin' => [
        ['label' => 'Cài đặt & Cấu hình Hệ thống', 'desc' => 'Thiết lập ban đầu các biến môi trường, cơ sở dữ liệu và cấu hình hệ thống.'],
        ['label' => 'Quản lý Tài khoản & Phân quyền', 'desc' => 'Cấp phát tài khoản, điều chỉnh trạng thái hoạt động (khóa/mở khóa) của người dùng.'],
        ['label' => 'Giám sát Hoạt động qua Logs', 'desc' => 'Truy vết toàn bộ các hành động mang tính thay đổi dữ liệu nhạy cảm thông qua Logs hệ thống.'],
        ['label' => 'Duy trì Vận hành Kỹ thuật', 'desc' => 'Đảm bảo hoạt động ổn định và hỗ trợ kỹ thuật cho các tài khoản nghiệp vụ khác.']
    ],
    'mangaka' => [
        ['label' => 'Sáng tác Ý tưởng & Đăng ký Series', 'desc' => 'Đăng ký thông tin bộ truyện mới (Tên truyện, Tóm tắt, Ảnh bìa) gửi Biên tập viên.'],
        ['label' => 'Tạo Chương mới & Thiết kế Phân vùng', 'desc' => 'Khởi tạo chương truyện mới, định nghĩa danh sách các trang và tạo phân vùng công việc.'],
        ['label' => 'Giao việc cho Trợ lý (Tasks Assignment)', 'desc' => 'Phân công các phân vùng bản vẽ cụ thể cho các Trợ lý thuộc nhóm.'],
        ['label' => 'Kiểm duyệt Nét vẽ Trợ lý & Gửi Bản thảo', 'desc' => 'Duyệt hoặc yêu cầu trợ lý vẽ lại, sau đó hoàn thiện tổng thể chương truyện và nộp bản thảo.']
    ],
    'assistant' => [
        ['label' => 'Nhận Công việc vẽ được giao', 'desc' => 'Theo dõi danh sách công việc (Tasks) được Họa sĩ chính chỉ định kèm thời hạn.'],
        ['label' => 'Thực hiện Vẽ phân vùng được giao', 'desc' => 'Tiến hành vẽ hoàn thiện chi tiết khu vực tranh được phân công theo chỉ dẫn.'],
        ['label' => 'Đăng tải Bản vẽ Hoàn thành', 'desc' => 'Upload trực tiếp tệp hình ảnh sản phẩm lên hệ thống để Họa sĩ chính kiểm duyệt.'],
        ['label' => 'Sửa đổi theo Yêu cầu', 'desc' => 'Chỉnh sửa lại nét vẽ nếu Họa sĩ chính từ chối duyệt, hoặc hoàn thành nếu được chấp thuận.']
    ],
    'editor' => [
        ['label' => 'Giám sát Tiến độ Vẽ của Tác giả', 'desc' => 'Theo dõi trạng thái hoàn thành bản thảo của các nhóm tác giả được quản lý.'],
        ['label' => 'Tiếp nhận Bản thảo Chương truyện', 'desc' => 'Nhận thông báo khi tác giả hoàn thành và nộp bản thảo chương mới.'],
        ['label' => 'Đánh giá Kịch bản & Nghệ thuật', 'desc' => 'Thực hiện chấm điểm, để lại các bình luận góp ý chi tiết cho từng trang vẽ.'],
        ['label' => 'Quyết định Duyệt hoặc Từ chối', 'desc' => 'Phê duyệt để đưa vào danh sách xuất bản hoặc từ chối để yêu cầu tác giả vẽ lại chương.']
    ],
    'board' => [
        ['label' => 'Giám sát Tiến độ Hệ thống', 'desc' => 'Xem bảng thống kê hiệu suất hoạt động, số lượng bản vẽ và chương truyện hoàn thành.'],
        ['label' => 'Tổ chức Kỳ Đánh giá & Xếp hạng', 'desc' => 'Thiết lập các kỳ xếp hạng truyện định kỳ, chấm điểm chất lượng và phân loại tác phẩm.'],
        ['label' => 'Xây dựng Chiến lược Xuất bản', 'desc' => 'Phê duyệt các chiến dịch ra mắt truyện mới, phân bổ biên tập viên quản lý.'],
        ['label' => 'Quyết định Thương mại hóa & Đình bản', 'desc' => 'Quyết định cuối cùng về việc cho phép xuất bản thương mại hoặc đình chỉ tác phẩm kém chất lượng.']
    ]
];

$rolePermissions = [
    'admin' => [
        ['name' => 'Quản lý Tài khoản Người dùng', 'val' => 'Toàn quyền CRUD'],
        ['name' => 'Cấu hình Quyền hạn (RBAC)', 'val' => 'Xem cấu hình'],
        ['name' => 'Truy cập Nhật ký System Logs', 'val' => 'Đọc & Tìm kiếm chi tiết'],
        ['name' => 'Quản lý Quy trình Nghiệp vụ', 'val' => 'Hỗ trợ kỹ thuật']
    ],
    'mangaka' => [
        ['name' => 'Quản lý Dự án (Series)', 'val' => 'Sở hữu & Chỉnh sửa'],
        ['name' => 'Đăng tải Bản thảo (Submissions)', 'val' => 'Khởi tạo & Gửi'],
        ['name' => 'Phân chia & Giao việc (Tasks)', 'val' => 'Toàn quyền quản lý nhóm'],
        ['name' => 'Kiểm duyệt nét vẽ trợ lý', 'val' => 'Phê duyệt/Từ chối bản vẽ']
    ],
    'assistant' => [
        ['name' => 'Tiếp nhận nhiệm vụ vẽ', 'val' => 'Chủ trì thực hiện'],
        ['name' => 'Tải lên sản phẩm vẽ trang', 'val' => 'Đăng tải hình ảnh'],
        ['name' => 'Quản lý Nhật ký công việc', 'val' => 'Cập nhật tiến độ'],
        ['name' => 'Truy cập thông tin bộ truyện', 'val' => 'Chỉ xem']
    ],
    'editor' => [
        ['name' => 'Tiếp nhận Bản thảo kiểm duyệt', 'val' => 'Nhận thông báo tự động'],
        ['name' => 'Đánh giá & Nhận xét bản thảo', 'val' => 'Để lại ý kiến & Điểm số'],
        ['name' => 'Phê duyệt chương truyện', 'val' => 'Duyệt / Từ chối (Yêu cầu vẽ lại)'],
        ['name' => 'Quản lý các bộ truyện được giao', 'val' => 'Giám sát tiến độ']
    ],
    'board' => [
        ['name' => 'Xem báo cáo toàn hệ thống', 'val' => 'Bảng điều khiển & Thống kê'],
        ['name' => 'Xếp hạng & Chấm điểm truyện', 'val' => 'Đánh giá định kỳ các Series'],
        ['name' => 'Cập nhật trạng thái Series', 'val' => 'Duyệt xuất bản / Đình bản'],
        ['name' => 'Phân quyền phân công Biên tập', 'val' => 'Xem & Chỉ đạo']
    ]
];

// Tạo mảng dữ liệu JSON gửi sang Javascript
foreach ($rolesWithCount as $r) {
    $key = strtolower($r['role_name']);
    $rolesMetadata[$key] = [
        'role_id'     => $r['role_id'],
        'role_name'   => ucfirst($r['role_name']),
        'user_count'  => (int)$r['user_count'],
        'color'       => $roleColors[$key] ?? '#64748b',
        'rgb'         => $roleRgb[$key] ?? '100, 116, 139',
        'icon'        => $roleIcons[$key] ?? 'fa-user',
        'badge'       => $roleBadges[$key] ?? 'bg-secondary-subtle text-secondary',
        'description' => $roleDescriptions[$key] ?? ($r['description'] ?? ''),
        'timeline'    => $roleTimeline[$key] ?? [],
        'permissions' => $rolePermissions[$key] ?? []
    ];
}
?>

<style>
    /* Premium Interactive Control Center */
    .role-console-wrapper {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }
    
    .role-selector-sidebar {
        border-right: 1px solid var(--border-color);
        background: #fafbfe;
        padding: 1.5rem;
    }
    
    .role-btn {
        width: 100%;
        border: 1px solid transparent !important;
        border-radius: 14px !important;
        padding: 1rem 1.15rem !important;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: left;
        margin-bottom: 0.75rem;
    }
    
    .role-btn:hover {
        background: #ffffff !important;
        border-color: var(--border-color) !important;
        transform: translateX(4px);
    }
    
    .role-btn.active {
        background: #ffffff !important;
        border-color: var(--border-color) !important;
        box-shadow: 0 8px 16px -4px rgba(79, 70, 229, 0.08) !important;
    }
    
    .role-btn-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        transition: all 0.3s ease;
    }
    
    .role-btn.active .role-btn-icon {
        transform: scale(1.1);
    }
    
    .role-btn-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--slate-700);
        margin-bottom: 2px;
        transition: color 0.3s ease;
    }
    
    .role-btn.active .role-btn-title {
        color: var(--slate-900);
    }
    
    .role-btn-count {
        font-size: 0.75rem;
        font-weight: 700;
        background: var(--slate-100);
        border: 1px solid var(--slate-200);
        color: var(--slate-600);
        padding: 0.35rem 0.65rem;
        border-radius: 20px;
    }
    
    /* Details Panel */
    .role-details-panel {
        padding: 2.25rem;
        position: relative;
        background: #ffffff;
    }
    
    .role-glow-accent {
        position: absolute;
        top: -150px;
        right: -150px;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.12;
        pointer-events: none;
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 0;
    }
    
    .role-header-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        box-shadow: 0 8px 20px -6px rgba(0,0,0,0.1);
    }
    
    /* Stepper Timeline */
    .role-stepper {
        position: relative;
        padding-left: 1.8rem;
    }
    
    .role-stepper::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 6px;
        bottom: 6px;
        width: 2px;
        background: var(--slate-100);
    }
    
    .step-node {
        position: relative;
        padding-bottom: 1.5rem;
    }
    
    .step-node:last-child {
        padding-bottom: 0;
    }
    
    .step-node::before {
        content: '';
        position: absolute;
        left: -26px;
        top: 4px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #ffffff;
        border: 3.5px solid var(--slate-300);
        z-index: 1;
        transition: all 0.3s ease;
    }
    
    .step-node-title {
        font-weight: 700;
        font-size: 0.875rem;
        color: var(--slate-800);
        margin-bottom: 2px;
    }
    
    .step-node-desc {
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.4;
    }
    
    /* Permission Table Card */
    .permission-item {
        background: var(--slate-50);
        border: 1px solid var(--slate-200);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    
    .permission-item:hover {
        background: #ffffff;
        border-color: var(--primary-soft);
        box-shadow: var(--shadow-sm);
    }
</style>

<!-- Tiêu đề và Breadcrumb -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1 text-dark fw-bold">Bảng điều khiển vai trò</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=admin" class="text-decoration-none">Bảng điều khiển</a></li>
                <li class="breadcrumb-item active" aria-current="page">Vai trò & Phân quyền</li>
            </ol>
        </nav>
    </div>
    <span class="badge bg-light text-muted border px-3 py-2"><i class="fas fa-shield-alt me-1 text-primary"></i>Phân quyền RBAC</span>
</div>

<!-- Interactive Console -->
<div class="role-console-wrapper mb-4">
    <div class="row g-0">
        <!-- Selector Menu bên trái -->
        <div class="col-lg-4 role-selector-sidebar">
            <h6 class="text-xs text-uppercase fw-bold text-muted mb-4 px-2" style="letter-spacing: 0.08em; font-size: 0.7rem;">Danh sách vai trò</h6>
            <div class="d-flex flex-column" id="roleButtonList">
                <?php $idx = 0; foreach ($rolesWithCount as $r): ?>
                    <?php 
                    $roleKey = strtolower($r['role_name']);
                    $roleColor = $roleColors[$roleKey] ?? '#64748b';
                    $roleIcon = $roleIcons[$roleKey] ?? 'fa-user';
                    ?>
                    <button class="role-btn <?= $idx === 0 ? 'active' : '' ?>" data-role="<?= $roleKey ?>">
                        <div class="d-flex align-items-center gap-3">
                            <div class="role-btn-icon" style="background: rgba(<?= $roleRgb[$roleKey] ?>, 0.07); color: <?= $roleColor ?>;">
                                <i class="fas <?= $roleIcon ?>"></i>
                            </div>
                            <div>
                                <div class="role-btn-title"><?= htmlspecialchars(ucfirst($r['role_name'])) ?></div>
                                <small class="text-muted" style="font-size: 0.75rem;">Mã: <?= htmlspecialchars($roleKey) ?></small>
                            </div>
                        </div>
                        <span class="role-btn-count"><?= (int)$r['user_count'] ?></span>
                    </button>
                <?php $idx++; endforeach; ?>
            </div>
        </div>
        
        <!-- Bảng điều khiển chi tiết bên phải -->
        <div class="col-lg-8 role-details-panel">
            <div class="role-glow-accent" id="panelGlowAccent"></div>
            
            <div id="panelContent" style="transition: opacity 0.2s ease-in-out; opacity: 1; z-index: 1; position: relative;">
                <!-- Nội dung được cập nhật động bằng Javascript -->
            </div>
        </div>
    </div>
</div>

<script>
    // Nhận dữ liệu vai trò từ PHP chuyển sang JSON
    const rolesData = <?= json_encode($rolesMetadata) ?>;
    
    document.addEventListener("DOMContentLoaded", function() {
        const buttons = document.querySelectorAll(".role-btn");
        const panelContent = document.getElementById("panelContent");
        const panelGlow = document.getElementById("panelGlowAccent");
        
        // Hàm cập nhật giao diện chi tiết vai trò
        function renderRoleDetails(roleKey) {
            const data = rolesData[roleKey];
            if (!data) return;
            
            // Cập nhật background glow
            panelGlow.style.background = `radial-gradient(circle, ${data.color} 0%, rgba(255,255,255,0) 70%)`;
            
            // Xây dựng Timeline các bước thực thi
            let timelineHtml = '';
            data.timeline.forEach(item => {
                timelineHtml += `
                    <div class="step-node">
                        <div class="step-node-title">${item.label}</div>
                        <div class="step-node-desc">${item.desc}</div>
                    </div>
                `;
            });
            
            // Xây dựng danh sách quyền hạn dọc không bị co cụm chữ
            let permissionsHtml = '';
            data.permissions.forEach(p => {
                permissionsHtml += `
                    <div class="col-12 mb-2.5">
                        <div class="permission-item">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: rgba(${data.rgb}, 0.08); color: ${data.color}; flex-shrink: 0;">
                                    <i class="fas fa-check" style="font-size: 0.825rem;"></i>
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 0.85rem;">${p.name}</span>
                            </div>
                            <span class="badge bg-white text-secondary border px-2.5 py-1.5 fw-semibold" style="font-size: 0.725rem; border-radius: 6px;">${p.val}</span>
                        </div>
                    </div>
                `;
            });
            
            // Template chi tiết đầy đủ
            const contentTemplate = `
                <!-- Header chi tiết -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 pb-4 mb-4 border-bottom border-light-subtle">
                    <div class="d-flex align-items-center gap-3">
                        <div class="role-header-icon" style="background: rgba(${data.rgb}, 0.08); color: ${data.color};">
                            <i class="fas ${data.icon}"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold text-dark mb-1">${data.role_name}</h3>
                            <p class="text-muted text-sm mb-0">Hạng vai trò ID: #${data.role_id} • Trạng thái: <span class="badge ${data.badge} ms-1" style="font-size: 0.68rem; padding: 0.25em 0.5em;">Active</span></p>
                        </div>
                    </div>
                    <div>
                        <a href="<?= BASE_PATH ?>/index.php?controller=user&action=index&search=${encodeURIComponent(data.role_name.toLowerCase())}&from=roles" class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm" style="border-radius: var(--radius); font-size: 0.85rem; background: linear-gradient(135deg, ${data.color} 0%, #1e1b4b 100%); border: none;">
                            <i class="fas fa-users"></i> Xem ${data.user_count} thành viên
                        </a>
                    </div>
                </div>
                
                <!-- Mô tả vai trò -->
                <div class="mb-4">
                    <h6 class="text-xs text-uppercase fw-bold text-muted mb-2" style="letter-spacing: 0.05em; font-size: 0.725rem;">Mô tả chung:</h6>
                    <p class="text-dark fw-normal mb-0" style="font-size: 0.9rem; line-height: 1.6;">${data.description}</p>
                </div>
                
                <div class="row g-4 pt-2">
                    <!-- Quyền hạn hệ thống -->
                    <div class="col-lg-7">
                        <h6 class="text-xs text-uppercase fw-bold text-muted mb-3" style="letter-spacing: 0.05em; font-size: 0.725rem;">Quyền hạn trên hệ thống:</h6>
                        <div class="row g-2">
                            ${permissionsHtml}
                        </div>
                    </div>
                    
                    <!-- Luồng công việc/Quy trình sáng tác -->
                    <div class="col-lg-5">
                        <h6 class="text-xs text-uppercase fw-bold text-muted mb-3" style="letter-spacing: 0.05em; font-size: 0.725rem;">Các bước trong quy trình:</h6>
                        <div class="role-stepper" id="stepperContainer">
                            ${timelineHtml}
                        </div>
                    </div>
                </div>
            `;
            
            // Cập nhật hiệu ứng fade in mượt mà
            panelContent.style.opacity = 0;
            setTimeout(() => {
                panelContent.innerHTML = contentTemplate;
                panelContent.style.opacity = 1;
                
                // Cập nhật màu sắc động của timeline nodes
                const stepNodes = document.querySelectorAll(".step-node");
                stepNodes.forEach(node => {
                    node.style.setProperty('--slate-300', data.color);
                });
            }, 150);
        }
        
        // Lắng nghe sự kiện click trên menu vai trò
        buttons.forEach(btn => {
            btn.addEventListener("click", function() {
                // Xóa trạng thái hoạt động cũ
                buttons.forEach(b => b.classList.remove("active"));
                // Thêm trạng thái hoạt động mới
                this.classList.add("active");
                
                const role = this.getAttribute("data-role");
                renderRoleDetails(role);
            });
        });
        
        // Khởi tạo hiển thị vai trò đầu tiên khi tải trang
        const firstRole = buttons[0]?.getAttribute("data-role");
        if (firstRole) {
            renderRoleDetails(firstRole);
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
