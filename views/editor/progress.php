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
    /* Premium scoped progress dashboard styles */
    .progress-series-card {
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border-color) !important;
        background: #ffffff;
    }
    .progress-series-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md) !important;
        border-color: rgba(79, 70, 229, 0.25) !important;
    }
    .series-cover-mini {
        width: 44px;
        height: 58px;
        object-fit: cover;
        border-radius: var(--radius-sm);
        border: 1px solid var(--slate-200);
        box-shadow: var(--shadow-sm);
    }
    .series-cover-placeholder {
        width: 44px;
        height: 58px;
        background: var(--slate-100);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--slate-400);
        border: 1px solid var(--slate-200);
    }
    .legend-indicator-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 4px rgba(0,0,0,0.15);
    }
    .legend-abbr-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: var(--slate-50);
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.72rem;
        color: var(--slate-700);
        border: 1px solid var(--slate-200);
        transition: all 0.2s ease;
    }
    .legend-abbr-pill:hover {
        background-color: var(--slate-100);
        transform: translateY(-1px);
    }
    .legend-abbr-pill i {
        font-size: 0.8rem;
    }
    .chapter-card-box {
        background-color: var(--slate-50);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius);
        transition: all 0.2s ease;
    }
    .chapter-card-box:hover {
        background-color: #ffffff;
        border-color: rgba(79, 70, 229, 0.2);
        box-shadow: var(--shadow-sm);
    }
    
    /* Page Matrix Cards */
    .page-matrix-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 10px;
        margin-top: 5px;
    }
    .page-item-card {
        background: #ffffff;
        border: 1px solid var(--slate-200);
        border-radius: var(--radius);
        padding: 8px;
        transition: all 0.2s ease-in-out;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        box-shadow: var(--shadow-sm);
        height: 175px;
    }
    .page-item-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
        border-color: var(--primary);
    }
    .page-preview-box {
        position: relative;
        height: 85px;
        border-radius: var(--radius-sm);
        background: var(--slate-100);
        overflow: hidden;
        border: 1px solid var(--slate-200);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--slate-400);
        margin-top: 4px;
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
        font-size: 0.72rem;
        font-weight: 800;
        color: var(--slate-800);
    }
    
    /* Task Status Icons */
    .task-status-dot-container {
        display: flex;
        justify-content: space-between;
        gap: 4px;
        margin-top: 6px;
    }
    .task-status-dot {
        flex: 1;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        cursor: help;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    .task-status-dot:hover {
        transform: translateY(-1.5px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }
    
    /* Soft colors for task statuses */
    .task-status-completed {
        background-color: var(--success-soft) !important;
        color: var(--success) !important;
        border-color: var(--success-border) !important;
    }
    .task-status-in_progress {
        background-color: var(--primary-soft) !important;
        color: var(--primary) !important;
        border-color: rgba(79, 70, 229, 0.2) !important;
    }
    .task-status-pending {
        background-color: var(--warning-soft) !important;
        color: #b45309 !important;
        border-color: var(--warning-border) !important;
    }
    .task-status-null {
        background-color: var(--slate-50) !important;
        color: var(--slate-400) !important;
        border-color: var(--slate-200) !important;
        opacity: 0.65;
    }
    
    /* Zoom Modal Trigger button */
    .btn-zoom-preview {
        position: absolute;
        right: 4px;
        bottom: 4px;
        width: 22px;
        height: 22px;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.9);
        color: var(--slate-700);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        border: 1px solid var(--slate-200);
        transition: all 0.15s ease;
        opacity: 0;
    }
    .page-item-card:hover .btn-zoom-preview {
        opacity: 1;
    }
    .btn-zoom-preview:hover {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Giám sát Tiến độ & Deadline Studio</h2>
        <p class="text-muted text-xs mb-0">Theo dõi tiến độ hoàn thành bản vẽ của nhóm tác giả (studio) theo thời gian thực để đảm bảo kịp deadline giao bản in.</p>
    </div>
</div>

<!-- Chú giải ký hiệu trạng thái vẽ (Legend) -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-3 mb-lg-0 border-lg-end border-light-subtle">
                <h6 class="fw-bold mb-2 text-xs text-uppercase text-muted"><i class="fas fa-info-circle me-1 text-primary"></i>Trạng thái công việc của Trợ lý</h6>
                <div class="d-flex flex-wrap gap-3 text-xs">
                    <div class="d-flex align-items-center"><span class="legend-indicator-dot me-1" style="background-color: var(--success);"></span> Đã hoàn thành</div>
                    <div class="d-flex align-items-center"><span class="legend-indicator-dot me-1" style="background-color: var(--primary);"></span> Trợ lý đang vẽ</div>
                    <div class="d-flex align-items-center"><span class="legend-indicator-dot me-1" style="background-color: var(--warning);"></span> Chờ phân công/xử lý</div>
                    <div class="d-flex align-items-center"><span class="legend-indicator-dot me-1" style="background-color: var(--slate-200); border: 1px solid var(--slate-300);"></span> Không phân công (Tác giả tự vẽ)</div>
                </div>
            </div>
            <div class="col-lg-6 ps-lg-4">
                <h6 class="fw-bold mb-2 text-xs text-uppercase text-muted"><i class="fas fa-tasks me-1 text-secondary"></i>Các công đoạn chính trên mỗi trang</h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="legend-abbr-pill" data-bs-toggle="tooltip" title="Background: Vẽ và dựng bối cảnh/nền của trang"><i class="fa-solid fa-mountain text-danger"></i> BG: Vẽ nền</span>
                    <span class="legend-abbr-pill" data-bs-toggle="tooltip" title="Inking: Đi nét chi tiết cho nhân vật và tiền cảnh"><i class="fa-solid fa-pen-nib text-secondary"></i> INK: Đi nét</span>
                    <span class="legend-abbr-pill" data-bs-toggle="tooltip" title="Coloring: Lên màu/Đổ bóng cho trang truyện"><i class="fa-solid fa-palette text-success"></i> COL: Lên màu</span>
                    <span class="legend-abbr-pill" data-bs-toggle="tooltip" title="Effects: Thêm hiệu ứng âm thanh SFX, thoại đặc biệt"><i class="fa-solid fa-wand-magic-sparkles text-info"></i> FX: Hiệu ứng</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php if (!empty($progressData)): ?>
        <?php foreach ($progressData as $data): ?>
            <div class="col-xl-6 col-md-12 mb-4">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 fw-bold text-dark">
                                <i class="fas fa-book me-2 text-primary"></i><?= htmlspecialchars($data['series']['title']) ?>
                            </h5>
                            <?= $this->getSeriesStatusBadge($data['series']['status']) ?>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- 1. Tiến độ xuất bản tổng thể -->
                        <div class="mb-4 bg-light p-3 rounded" style="border-left: 4px solid #198754;">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted text-xs text-uppercase fw-bold"><i class="fas fa-layer-group me-1"></i>Tiến độ xuất bản tổng thể</span>
                                <span class="text-dark fw-bold"><?= $data['completed_chapters'] ?> / <?= $data['total_chapters'] ?> chương đã duyệt</span>
                            </div>
                            <?php 
                            $percent = $data['total_chapters'] > 0 ? round(($data['completed_chapters'] / $data['total_chapters']) * 100) : 0; 
                            ?>
                            <div class="progress" style="height: 8px; border-radius: 4px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%; border-radius: 4px;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <!-- 2. Giám sát thời gian thực chương đang vẽ (Active Chapter) -->
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-hourglass-half text-danger me-2"></i>Chương đang thực hiện (Active Chapter)</h6>
                        
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
                                $deadlineBadge = 'bg-danger text-white';
                                $deadlineText = 'Trễ ' . $daysLeft . ' ngày';
                            } elseif ($daysLeft <= 3) {
                                $deadlineBadge = 'bg-warning text-dark';
                                $deadlineText = 'Còn ' . $daysLeft . ' ngày';
                            }
                        ?>
                            <div class="border rounded p-3 mb-4 bg-white shadow-xs">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <strong class="text-slate-800 fs-6">Chương <?= htmlspecialchars($actChap['chapter_number']) ?>: <?= htmlspecialchars($actChap['title'] ?? 'Chưa đặt tên') ?></strong>
                                        <span class="badge bg-primary text-xs ms-2"><?= $actChap['status'] === 'drawing' ? 'Đang vẽ' : (in_array($actChap['status'], ['reviewing', 'reviewing_draft', 'reviewing_final']) ? 'Đang chờ duyệt' : 'Bản thảo') ?></span>
                                    </div>
                                    <div>
                                        <span class="text-muted text-xs me-2">Hạn bản in:</span>
                                        <span class="badge <?= $deadlineBadge ?>"><?= $deadlineText ?></span>
                                    </div>
                                </div>

                                <!-- Thanh tiến độ công việc trong chapter -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between text-xs mb-1">
                                        <span class="text-muted">Hoàn thiện nét vẽ studio:</span>
                                        <strong class="text-dark"><?= $actStats['completed_tasks'] ?> / <?= $actStats['total_tasks'] ?> công việc hoàn thành (<?= $actStats['completion_rate'] ?>%)</strong>
                                    </div>
                                    <div class="progress" style="height: 6px; border-radius: 3px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $actStats['completion_rate'] ?>%; border-radius: 3px;" aria-valuenow="<?= $actStats['completion_rate'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <!-- Ma trận tiến độ trang vẽ (Real-time Grid) -->
                                <div class="studio-progress-grid bg-light p-3 rounded" style="max-height: 250px; overflow-y: auto;">
                                    <?php if (!empty($data['active_chapter_pages'])): 
                                        // Helper to get status badge classes
                                        if (!function_exists('getTaskBadge')) {
                                            function getTaskBadge($status) {
                                                if ($status === null) return 'badge bg-light text-muted border'; // không giao
                                                if ($status === 'completed') return 'badge bg-success text-white';
                                                if ($status === 'in_progress') return 'badge bg-primary text-white';
                                                return 'badge bg-warning text-dark'; // pending
                                            }
                                        }
                                    ?>
                                        <div class="row g-2">
                                            <?php foreach ($data['active_chapter_pages'] as $pageData): 
                                                $pg = $pageData['page'];
                                                $tStatus = $pageData['tasks'];
                                            ?>
                                                <div class="col-6 col-sm-4 col-md-3 col-xl-auto" style="flex: 1 1 120px; max-width: 160px;">
                                                    <div class="bg-white border rounded p-2 text-center shadow-xs h-100">
                                                        <span class="fw-bold text-xs text-dark d-block mb-2 border-bottom pb-1">Trang <?= htmlspecialchars($pg['page_number']) ?></span>
                                                        <div class="d-flex flex-wrap justify-content-center gap-1" style="font-size: 0.65rem; line-height: 1;">
                                                            <!-- BG Task -->
                                                            <span class="<?= getTaskBadge($tStatus['background']) ?> px-1 py-1" title="Vẽ nền: <?= $tStatus['background'] ?: 'Không phân công' ?>">BG</span>
                                                            <!-- INK Task -->
                                                            <span class="<?= getTaskBadge($tStatus['inking']) ?> px-1 py-1" title="Đi nét: <?= $tStatus['inking'] ?: 'Không phân công' ?>">INK</span>
                                                            <!-- COL Task -->
                                                            <span class="<?= getTaskBadge($tStatus['coloring']) ?> px-1 py-1" title="Tô màu: <?= $tStatus['coloring'] ?: 'Không phân công' ?>">COL</span>
                                                            <!-- FX Task -->
                                                            <span class="<?= getTaskBadge($tStatus['effects']) ?> px-1 py-1" title="Hiệu ứng: <?= $tStatus['effects'] ?: 'Không phân công' ?>">FX</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-3 text-muted text-xs">
                                            Chương truyện chưa được đăng tải trang vẽ nào lên hệ thống.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="border rounded p-3 mb-4 bg-light text-center py-4">
                                <i class="fas fa-check-circle text-success fa-2x mb-2" style="opacity: 0.7;"></i>
                                <div class="text-dark fw-bold small">Không có chương truyện đang vẽ</div>
                                <p class="text-muted text-xs mb-0">Tất cả các chương đã nộp duyệt thành công hoặc tác giả chưa tạo chương nháp mới.</p>
                            </div>
                        <?php endif; ?>

                        <!-- 3. Top 5 Deadline công việc sắp tới -->
                        <h6 class="fw-bold text-dark mb-3"><i class="far fa-calendar-alt me-2 text-warning"></i>Công việc sắp đến hạn hoặc trễ hạn</h6>
                        <?php if (!empty($data['pending_tasks'])): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.8rem;">
                                    <tbody>
                                        <?php foreach ($data['pending_tasks'] as $task): 
                                            $tDueDate = strtotime($task['due_date']);
                                            $tOverdue = $tDueDate < time();
                                            
                                            $tBadge = 'bg-light text-dark border';
                                            if ($tOverdue) {
                                                $tBadge = 'bg-danger text-white';
                                            }
                                        ?>
                                            <tr>
                                                <td class="ps-0">
                                                    <strong><?= htmlspecialchars($task['title']) ?></strong>
                                                    <span class="text-muted d-block" style="font-size: 0.75rem;">Chapter <?= htmlspecialchars($task['chapter_number']) ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $typeText = 'Khác';
                                                    switch ($task['task_type']) {
                                                        case 'background': $typeText = 'Nền'; break;
                                                        case 'inking': $typeText = 'Nét'; break;
                                                        case 'coloring': $typeText = 'Màu'; break;
                                                        case 'effects': $typeText = 'FX'; break;
                                                    }
                                                    ?>
                                                    <span class="badge bg-light text-secondary border"><?= $typeText ?></span>
                                                </td>
                                                <td class="text-end pe-0">
                                                    <?php if ($task['due_date']): ?>
                                                        <span class="badge <?= $tBadge ?>">
                                                            <i class="far fa-clock me-1"></i><?= date('d/m/Y', $tDueDate) ?>
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
                        <?php else: ?>
                            <p class="text-muted small mb-0">Không có công việc (Task) nào của trợ lý đang trễ hạn.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body py-5 text-center text-muted">
                    <i class="fas fa-folder-open fa-3x mb-3 text-secondary" style="opacity: 0.35;"></i>
                    <h5 class="fw-bold text-dark mb-2">Không có dự án hoạt động</h5>
                    <p class="text-muted mb-0 small">Hiện không có dự án truyện tranh nào được gán chuyên trách cho bạn.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
