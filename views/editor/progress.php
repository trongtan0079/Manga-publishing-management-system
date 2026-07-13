<?php
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
/**
 * View: Giao diện theo dõi tiến độ & deadline xuất bản các bộ truyện (progress.php)
 * Vai trò: Editor (Biên tập viên)
 * Chức năng: Theo dõi tiến độ hoàn thành chương truyện và các công việc (Task) sắp đến hạn hoặc trễ hạn của từng bộ truyện theo thời gian thực.
 * 
 * @var array $progressData Danh sách tiến độ và thông tin Task của từng bộ truyện
 */
$pageTitle = 'Tiến độ & Deadline Studio';
$current_page = 'progress';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    /* Elite SaaS styling for progress tracking */
    .progress-series-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        border-radius: 20px !important;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.02), 0 2px 8px -1px rgba(15, 23, 42, 0.02) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
    }
    .progress-series-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .progress-series-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 10px 10px -5px rgba(15, 23, 42, 0.04) !important;
        border-color: rgba(99, 102, 241, 0.3) !important;
    }
    .progress-series-card:hover::before {
        opacity: 1;
    }
    .series-cover-mini {
        width: 52px;
        height: 70px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        transition: transform 0.3s ease;
    }
    .progress-series-card:hover .series-cover-mini {
        transform: scale(1.08) rotate(1deg);
    }
    .series-cover-placeholder {
        width: 52px;
        height: 70px;
        background: linear-gradient(135deg, var(--slate-100) 0%, var(--slate-200) 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--slate-400);
        border: 1px dashed var(--slate-300);
    }
    
    /* Pulsating statuses */
    .status-dot-pulse {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
        position: relative;
    }
    .status-dot-pulse::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        animation: pulse-ring 2s infinite ease-in-out;
        opacity: 0.7;
    }
    .status-dot-pulse.bg-success::after { background-color: var(--success); }
    .status-dot-pulse.bg-primary::after { background-color: var(--primary); }
    .status-dot-pulse.bg-warning::after { background-color: var(--warning); }
    .status-dot-pulse.bg-secondary::after { background-color: var(--secondary); }
    
    @keyframes pulse-ring {
        0% { transform: scale(0.95); opacity: 0.8; }
        100% { transform: scale(2.8); opacity: 0; }
    }
    
    /* Interactive legends */
    .legend-container-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 16px;
        padding: 14px 20px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.01);
    }
    .legend-title {
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--slate-400);
        margin-bottom: 8px;
    }
    .legend-indicator-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    
    /* Progress and Chapter Card Box */
    .chapter-card-box {
        background: #ffffff;
        border: 1px solid var(--slate-100) !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.01), 0 2px 4px -1px rgba(15, 23, 42, 0.01) !important;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .chapter-card-box::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--primary);
    }
    .chapter-card-box:hover {
        border-color: rgba(99, 102, 241, 0.2) !important;
        box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.04) !important;
    }
    
    /* Page Matrix Cards */
    .page-matrix-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 12px;
        margin-top: 5px;
    }
    .page-item-card {
        background: #ffffff;
        border: 1px solid var(--slate-200);
        border-radius: 14px;
        padding: 12px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        height: 200px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01);
    }
    .page-item-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -8px rgba(15, 23, 42, 0.15);
        border-color: var(--primary);
    }
    .page-preview-box {
        position: relative;
        height: 100px;
        border-radius: 10px;
        background: var(--slate-50);
        overflow: hidden;
        border: 1px solid var(--slate-100);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--slate-400);
        margin-top: 6px;
    }
    .page-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .page-item-card:hover .page-preview-box img {
        transform: scale(1.08);
    }
    .page-number-tag {
        font-size: 0.78rem;
        font-weight: 800;
        color: var(--slate-800);
    }
    
    /* Custom Sleek Badges for Page Cards */
    .page-item-card .badge {
        font-size: 0.6rem !important;
        font-weight: 700;
        padding: 3px 6px !important;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    
    /* Custom Badges for Card Header */
    .progress-series-card .card-header .badge {
        font-size: 0.68rem !important;
        font-weight: 700;
        padding: 4px 8px !important;
        border-radius: 6px;
        letter-spacing: -0.01em;
    }
    
    /* Elegant Task Status Badges instead of simple blocks */
    .task-status-dot-container {
        display: flex;
        justify-content: space-between;
        gap: 4px;
        margin-top: 10px;
    }
    .task-status-dot {
        flex: 1;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 0.62rem;
        font-weight: 800;
        cursor: help;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        text-transform: uppercase;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .task-status-dot:hover {
        transform: translateY(-1px);
    }
    
    /* Modern Colors for Task States (glowing style) */
    .task-status-completed {
        background-color: rgba(16, 185, 129, 0.06) !important;
        color: #059669 !important;
        border-color: rgba(16, 185, 129, 0.2) !important;
    }
    .task-status-completed:hover {
        background-color: rgba(16, 185, 129, 0.12) !important;
        box-shadow: 0 0 8px rgba(16, 185, 129, 0.25);
    }
    .task-status-in_progress {
        background-color: rgba(59, 130, 246, 0.06) !important;
        color: #2563eb !important;
        border-color: rgba(59, 130, 246, 0.2) !important;
        animation: pulse-border 2s infinite ease-in-out;
    }
    .task-status-in_progress:hover {
        background-color: rgba(59, 130, 246, 0.12) !important;
        box-shadow: 0 0 8px rgba(59, 130, 246, 0.25);
    }
    .task-status-pending {
        background-color: rgba(245, 158, 11, 0.06) !important;
        color: #d97706 !important;
        border-color: rgba(245, 158, 11, 0.2) !important;
    }
    .task-status-pending:hover {
        background-color: rgba(245, 158, 11, 0.12) !important;
        box-shadow: 0 0 8px rgba(245, 158, 11, 0.25);
    }
    .task-status-null {
        background-color: var(--slate-50) !important;
        color: var(--slate-400) !important;
        border-color: var(--slate-200) !important;
        opacity: 0.6;
    }
    
    @keyframes pulse-border {
        0% { border-color: rgba(59, 130, 246, 0.2); }
        50% { border-color: rgba(59, 130, 246, 0.6); }
        100% { border-color: rgba(59, 130, 246, 0.2); }
    }
    
    /* Zoom Preview Button */
    .btn-zoom-preview {
        position: absolute;
        right: 6px;
        bottom: 6px;
        width: 26px;
        height: 26px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.95);
        color: var(--slate-700);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        border: 1px solid var(--slate-200);
        transition: all 0.2s ease;
        opacity: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .page-item-card:hover .btn-zoom-preview {
        opacity: 1;
    }
    .btn-zoom-preview:hover {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
        transform: scale(1.1);
    }

    /* Premium Empty State */
    .premium-empty-state {
        background: #ffffff;
        border: 1px dashed var(--slate-200);
        border-radius: 16px;
        padding: 40px 24px;
        text-align: center;
        position: relative;
        overflow: hidden;
        transition: border-color 0.3s ease;
    }
    .premium-empty-state:hover {
        border-color: var(--primary-soft);
    }
    .empty-state-icon-wrapper {
        width: 64px;
        height: 64px;
        background: rgba(79, 70, 229, 0.05);
        color: var(--primary);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 16px;
        position: relative;
    }
    .empty-state-icon-wrapper::after {
        content: '';
        position: absolute;
        top: -4px; left: -4px; right: -4px; bottom: -4px;
        border: 2px solid rgba(79, 70, 229, 0.1);
        border-radius: 50%;
        animation: pulse-ring 2s infinite ease-in-out;
    }
    
    /* Clean Overall Progress Block */
    .overall-progress-box {
        background: linear-gradient(135deg, var(--slate-50) 0%, #ffffff 100%);
        border: 1px solid var(--slate-200) !important;
        border-radius: 12px;
        padding: 16px !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01);
    }
    .gradient-progress-bar {
        background: linear-gradient(90deg, #10b981 0%, #34d399 100%) !important;
    }
    .gradient-task-progress-bar {
        background: linear-gradient(90deg, var(--primary) 0%, #818cf8 100%) !important;
    }
</style>

<?php
// Helper render status dot cho từng công việc vẽ
if (!function_exists('renderTaskStatusDot')) {
    function renderTaskStatusDot($type, $status) {
        $icon = '';
        $typeLabel = '';
        switch ($type) {
            case 'background':
                $icon = 'fa-mountain';
                $typeLabel = 'BG: Vẽ nền';
                break;
            case 'inking':
                $icon = 'fa-pen-nib';
                $typeLabel = 'INK: Đi nét';
                break;
            case 'coloring':
                $icon = 'fa-palette';
                $typeLabel = 'COL: Lên màu';
                break;
            case 'effects':
                $icon = 'fa-wand-magic-sparkles';
                $typeLabel = 'FX: Hiệu ứng SFX';
                break;
        }
        
        $statusClass = 'task-status-null';
        $statusLabel = 'Tác giả tự vẽ (Không phân công)';
        
        if ($status === 'completed') {
            $statusClass = 'task-status-completed';
            $statusLabel = 'Đã hoàn thành';
        } elseif ($status === 'in_progress') {
            $statusClass = 'task-status-in_progress';
            $statusLabel = 'Trợ lý đang vẽ';
        } elseif ($status === 'pending' || $status === 'submitted' || $status === 'rejected') {
            $statusClass = 'task-status-pending';
            if ($status === 'submitted') {
                $statusLabel = 'Đã nộp, chờ duyệt';
            } elseif ($status === 'rejected') {
                $statusLabel = 'Bản vẽ lỗi, vẽ lại';
            } else {
                $statusLabel = 'Chờ phân công/xử lý';
            }
        }
        
        $abbr = '';
        switch ($type) {
            case 'background': $abbr = 'BG'; break;
            case 'inking': $abbr = 'INK'; break;
            case 'coloring': $abbr = 'COL'; break;
            case 'effects': $abbr = 'FX'; break;
        }
        
        return '<span class="task-status-dot ' . $statusClass . '" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $typeLabel . ': ' . $statusLabel . '">'
             . '<i class="fa-solid ' . $icon . ' me-1" style="font-size: 0.65rem;"></i>' . $abbr
             . '</span>';
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-4">
    <div>
        <h2 class="h3 mb-1 fw-extrabold tracking-tight" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><i class="fa-solid fa-chart-line text-primary me-2"></i>Giám sát Tiến độ & Deadline Studio</h2>
        <p class="text-muted text-xs mb-0">Theo dõi tiến độ hoàn thành bản vẽ của nhóm tác giả (studio) theo thời gian thực để đảm bảo kịp deadline giao bản in.</p>
    </div>
</div>

<!-- Premium Legend Card -->
<div class="legend-container-card d-flex align-items-center gap-4 flex-wrap text-xs mb-4">
    <div class="pe-4 border-end border-slate-200" style="flex: 1; min-width: 300px;">
        <div class="legend-title"><i class="fa-solid fa-circle-nodes text-primary me-1"></i> Trạng thái vẽ của trợ lý</div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <span class="d-flex align-items-center gap-1.5 text-slate-700 fw-semibold"><span class="status-dot-pulse bg-success"></span> Đã hoàn thành</span>
            <span class="d-flex align-items-center gap-1.5 text-slate-700 fw-semibold"><span class="status-dot-pulse bg-primary"></span> Trợ lý đang vẽ</span>
            <span class="d-flex align-items-center gap-1.5 text-slate-700 fw-semibold"><span class="status-dot-pulse bg-warning"></span> Chờ phân công/xử lý</span>
            <span class="d-flex align-items-center gap-1.5 text-muted fw-semibold"><span class="status-dot-pulse bg-secondary"></span> Tác giả tự vẽ</span>
        </div>
    </div>
    <div style="flex: 1; min-width: 300px;">
        <div class="legend-title"><i class="fa-solid fa-layer-group text-primary me-1"></i> Công đoạn chính trên mỗi trang</div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 text-uppercase"><i class="fa-solid fa-mountain me-1"></i>BG: Vẽ nền</span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 text-uppercase"><i class="fa-solid fa-pen-nib me-1"></i>INK: Đi nét</span>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 text-uppercase"><i class="fa-solid fa-palette me-1"></i>COL: Lên màu</span>
            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 text-uppercase"><i class="fa-solid fa-wand-magic-sparkles me-1"></i>FX: Hiệu ứng</span>
        </div>
    </div>
</div>

<div class="row">
    <?php if (!empty($progressData)): ?>
        <?php foreach ($progressData as $data): ?>
            <div class="col-xl-6 col-md-12 mb-4">
                <div class="card shadow-sm border-0 rounded-3 h-100 progress-series-card">
                    <!-- Header bộ truyện được cải tiến -->
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <div class="d-flex align-items-center gap-3 w-100">
                            <?php if (!empty($data['series']['cover_image'])): ?>
                                <img src="<?= BASE_PATH . $data['series']['cover_image'] ?>" class="series-cover-mini" alt="<?= htmlspecialchars($data['series']['title']) ?>">
                            <?php else: ?>
                                <div class="series-cover-placeholder">
                                    <i class="fa-solid fa-book text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1 fw-extrabold text-slate-800 text-truncate" style="max-width: 250px;">
                                    <?= htmlspecialchars($data['series']['title']) ?>
                                </h5>
                                <div class="text-xs text-muted">
                                    <i class="fa-regular fa-folder-open me-1"></i> ID bộ truyện: #<?= $data['series']['series_id'] ?>
                                </div>
                            </div>
                            <div>
                                <?= $this->getSeriesStatusBadge($data['series']['status']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php 
                        $percent = $data['total_chapters'] > 0 ? round(($data['completed_chapters'] / $data['total_chapters']) * 100) : 0; 
                        ?>
                        <!-- 1. Tiến độ xuất bản tổng thể -->
                        <div class="mb-4 p-3 overall-progress-box">
                            <div class="d-flex justify-content-between mb-2 align-items-center">
                                <span class="text-slate-600 text-xs text-uppercase fw-bold"><i class="fa-solid fa-square-poll-horizontal me-1 text-success"></i> Tiến độ xuất bản tổng thể</span>
                                <span class="text-slate-700 fw-extrabold text-xs bg-white px-2.5 py-1 rounded-2 border border-light-subtle shadow-xs">
                                    <?= $data['completed_chapters'] ?> / <?= $data['total_chapters'] ?> chương (<?= $percent ?>%)
                                </span>
                            </div>
                            <div class="progress mt-2" style="height: 6px; border-radius: 4px; background-color: var(--slate-200); box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                                <div class="progress-bar gradient-progress-bar" role="progressbar" style="width: <?= $percent ?>%; border-radius: 4px;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <!-- 2. Giám sát thời gian thực chương đang vẽ (Active Chapter) -->
                        <h6 class="fw-bold text-slate-800 mb-3"><i class="fas fa-hourglass-half text-danger me-2"></i>Chương đang thực hiện (Active Chapter)</h6>
                        
                        <?php if (!empty($data['active_chapter'])): 
                            $actChap = $data['active_chapter'];
                            $actStats = $data['active_chapter_stats'];
                            
                            // Tính toán thời hạn nộp bản in
                            $dueDate = strtotime($actChap['due_date']);
                            $isOverdue = $dueDate < time();
                            $daysLeft = ceil(abs($dueDate - time()) / (60 * 60 * 24));
                            
                            $deadlineBadge = 'bg-light text-dark border';
                            $deadlineText = $daysLeft . ' ngày nữa';
                            if ($isOverdue) {
                                $deadlineBadge = 'bg-danger text-white border-0';
                                $deadlineText = 'Trễ ' . $daysLeft . ' ngày';
                            } elseif ($daysLeft <= 3) {
                                $deadlineBadge = 'bg-warning text-dark border-0';
                                $deadlineText = 'Còn ' . $daysLeft . ' ngày';
                            }
                        ?>
                            <div class="border rounded-4 p-4 mb-4 bg-white shadow-xs chapter-card-box">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                                    <div>
                                        <span class="text-muted text-xs d-block mb-1 text-uppercase fw-bold"><i class="fa-solid fa-folder-open me-1"></i> Đang biên tập</span>
                                        <strong class="text-slate-900 fs-6">Chương <?= htmlspecialchars($actChap['chapter_number']) ?>: <?= htmlspecialchars($actChap['title'] ?? 'Chưa đặt tên') ?></strong>
                                        <span class="ms-2"><?= $this->getStatusBadge($actChap['status']) ?></span>
                                    </div>
                                    <div class="bg-light p-2 px-3 rounded-3 border border-light-subtle d-flex align-items-center gap-2">
                                        <span class="text-slate-500 text-xs"><i class="fa-regular fa-calendar me-1"></i> Hạn bản in:</span>
                                        <span class="badge <?= $deadlineBadge ?> rounded-pill px-2.5 py-1 text-xs fw-extrabold shadow-sm"><i class="fa-regular fa-clock me-1"></i><?= $deadlineText ?></span>
                                    </div>
                                </div>

                                <!-- Thanh tiến độ công việc trong chapter -->
                                <div class="mb-4">
                                    <?php if ($actStats['total_tasks'] > 0): ?>
                                        <div class="d-flex justify-content-between text-xs mb-1.5 align-items-center">
                                            <span class="text-slate-600 fw-bold"><i class="fa-solid fa-list-check me-1 text-primary"></i> Tiến độ vẽ (Trợ lý):</span>
                                            <span class="text-slate-800 fw-extrabold bg-light p-1 px-2 rounded"><?= $actStats['completed_tasks'] ?> / <?= $actStats['total_tasks'] ?> công việc (<?= $actStats['completion_rate'] ?>%)</span>
                                        </div>
                                        <div class="progress" style="height: 6px; border-radius: 4px; background-color: var(--slate-100); box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                                            <div class="progress-bar gradient-task-progress-bar" role="progressbar" style="width: <?= $actStats['completion_rate'] ?>%; border-radius: 4px;" aria-valuenow="<?= $actStats['completion_rate'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex align-items-center bg-light p-3 rounded-3 border border-light-subtle text-muted text-xs">
                                            <i class="fa-solid fa-user-pen text-primary me-2 fa-lg"></i>
                                            <span class="fw-semibold">Chương này do tác giả tự vẽ và hoàn thiện (Không phân công trợ lý).</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Ma trận tiến độ trang vẽ (Real-time Grid dạng thẻ ảnh xem trước) -->
                                <div class="studio-progress-grid-wrapper p-3 rounded-4 border border-light-subtle shadow-inner" style="background-color: var(--slate-100); max-height: 420px; overflow-y: auto;">
                                    <?php if (!empty($data['active_chapter_pages'])): ?>
                                        <div class="page-matrix-container">
                                            <?php foreach ($data['active_chapter_pages'] as $pageData): 
                                                $pg = $pageData['page'];
                                                $tStatus = $pageData['tasks'];
                                                $resolvedImgUrl = $this->resolvePageImageUrl($pg['image_url']);
                                            ?>
                                                <div class="page-item-card">
                                                    <div>
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="page-number-tag">Trang <?= htmlspecialchars($pg['page_number']) ?></span>
                                                            <div style="transform: scale(0.85); transform-origin: right center;">
                                                                <?= $this->getPageStatusBadge($pg['status'], $actChap['status']) ?>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="page-preview-box">
                                                            <?php if (!empty($resolvedImgUrl) && $resolvedImgUrl !== 'no_genko'): ?>
                                                                <img src="<?= htmlspecialchars($resolvedImgUrl) ?>" alt="Bản vẽ Trang <?= htmlspecialchars($pg['page_number']) ?>">
                                                                <button type="button" class="btn-zoom-preview" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#imagePreviewModal" 
                                                                        data-img-url="<?= htmlspecialchars($resolvedImgUrl) ?>"
                                                                        data-page-num="<?= htmlspecialchars($pg['page_number']) ?>"
                                                                        title="Xem ảnh lớn">
                                                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                                </button>
                                                            <?php else: ?>
                                                                <div class="text-center">
                                                                    <i class="fa-regular fa-image fa-lg mb-1 d-block opacity-40"></i>
                                                                    <span style="font-size: 0.65rem;">Chưa tải lên</span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="task-status-dot-container">
                                                        <?= renderTaskStatusDot('background', $tStatus['background']) ?>
                                                        <?= renderTaskStatusDot('inking', $tStatus['inking']) ?>
                                                        <?= renderTaskStatusDot('coloring', $tStatus['coloring']) ?>
                                                        <?= renderTaskStatusDot('effects', $tStatus['effects']) ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4 text-muted text-xs bg-white rounded border border-light-subtle">
                                            <i class="fa-regular fa-folder-open fa-2x mb-2 d-block opacity-30 text-secondary"></i>
                                            Chương truyện chưa được tải trang vẽ nào lên hệ thống.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- Styled Premium Empty State -->
                            <div class="premium-empty-state mb-4 shadow-sm">
                                <div class="empty-state-icon-wrapper text-success bg-success-subtle bg-opacity-25">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                </div>
                                <div class="text-slate-800 fw-extrabold text-xs mb-1">Không có chương đang vẽ (No Active Chapter)</div>
                                <p class="text-muted text-xs mb-0 px-3">Tất cả các chương đã nộp duyệt thành công hoặc tác giả chưa khởi tạo chương nháp mới.</p>
                            </div>
                        <?php endif; ?>

                        <!-- 3. Top 5 Deadline công việc sắp tới -->
                        <h6 class="fw-bold text-slate-800 mb-3"><i class="fa-regular fa-calendar-check me-2 text-warning"></i>Công việc sắp đến hạn hoặc trễ hạn</h6>
                        <?php if (!empty($data['pending_tasks'])): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.8rem;">
                                    <tbody>
                                        <?php foreach ($data['pending_tasks'] as $task): 
                                            $tDueDate = strtotime($task['due_date']);
                                            $tOverdue = $tDueDate < time();
                                            $tDaysLeft = ceil(abs($tDueDate - time()) / (60 * 60 * 24));
                                            
                                            $tBadge = 'bg-light text-secondary border';
                                            if ($tOverdue) {
                                                $tBadge = 'bg-danger-subtle text-danger border border-danger-subtle';
                                            } elseif ($tDaysLeft <= 3) {
                                                $tBadge = 'bg-warning-subtle text-warning border border-warning-subtle';
                                            }
                                        ?>
                                            <tr style="transition: background-color 0.2s ease;">
                                                <td class="ps-0 py-2">
                                                    <strong class="text-slate-800"><?= htmlspecialchars($task['title']) ?></strong>
                                                    <span class="text-muted d-block text-xs" style="font-size: 0.72rem;">Chương <?= htmlspecialchars($task['chapter_number']) ?></span>
                                                </td>
                                                <td class="py-2">
                                                    <?php
                                                    $typeBadgeClass = 'bg-light text-secondary border';
                                                    $typeText = 'Khác';
                                                    switch ($task['task_type']) {
                                                        case 'background': 
                                                            $typeBadgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                                            $typeText = 'BG: Nền'; 
                                                            break;
                                                        case 'inking': 
                                                            $typeBadgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                                                            $typeText = 'INK: Nét'; 
                                                            break;
                                                        case 'coloring': 
                                                            $typeBadgeClass = 'bg-success-subtle text-success border border-success-subtle';
                                                            $typeText = 'COL: Màu'; 
                                                            break;
                                                        case 'effects': 
                                                            $typeBadgeClass = 'bg-info-subtle text-info border border-info-subtle';
                                                            $typeText = 'FX: SFX'; 
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge <?= $typeBadgeClass ?> text-xs font-semibold px-2 py-0.5"><?= $typeText ?></span>
                                                </td>
                                                <td class="text-end pe-0 py-2">
                                                    <?php if ($task['due_date']): ?>
                                                        <span class="badge <?= $tBadge ?> px-2 py-1 text-xs">
                                                            <i class="fa-regular fa-clock me-1"></i><?= date('d/m/Y', $tDueDate) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex align-items-center bg-success-subtle bg-opacity-10 border border-success-subtle p-3 rounded-3 text-success text-xs">
                                <i class="fa-solid fa-circle-check me-2 fa-lg"></i>
                                <span class="fw-bold">Tất cả công việc (Tasks) của trợ lý đều đang đúng hạn!</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="premium-empty-state py-5 shadow-sm">
                <div class="empty-state-icon-wrapper text-primary bg-primary-subtle bg-opacity-25 mb-3">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <h5 class="fw-extrabold text-slate-800 mb-2 text-sm">Không có dự án hoạt động</h5>
                <p class="text-muted mb-0 small px-3">Hiện tại chưa có dự án truyện tranh nào được phân công biên tập chuyên trách cho tài khoản của bạn.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal xem ảnh lớn -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 overflow-hidden shadow-lg">
            <div class="modal-header py-3 border-bottom">
                <h5 class="modal-title fw-bold" id="imagePreviewModalLabel">Chi tiết Bản vẽ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center bg-dark d-flex align-items-center justify-content-center" style="min-height: 400px; max-height: 80vh;">
                <img id="modalPreviewImage" src="" class="img-fluid" style="max-height: 80vh; object-fit: contain;" alt="Xem trước bản vẽ">
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Khởi tạo tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Xử lý phóng to ảnh trong modal
        var zoomButtons = document.querySelectorAll(".btn-zoom-preview");
        var modalImage = document.getElementById("modalPreviewImage");
        var modalTitle = document.getElementById("imagePreviewModalLabel");
        
        zoomButtons.forEach(function(btn) {
            btn.addEventListener("click", function(e) {
                e.stopPropagation(); // Ngăn chặn sự kiện click lan ra ngoài thẻ card
                var imgUrl = this.getAttribute("data-img-url");
                var pageNum = this.getAttribute("data-page-num");
                if (modalImage && imgUrl) {
                    modalImage.src = imgUrl;
                    if (modalTitle) {
                        modalTitle.textContent = "Bản vẽ Trang " + pageNum;
                    }
                }
            });
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
