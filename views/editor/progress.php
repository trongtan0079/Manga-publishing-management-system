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

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Giám sát Tiến độ & Deadline Studio</h2>
        <p class="text-muted text-xs mb-0">Theo dõi tiến độ hoàn thành bản vẽ của nhóm tác giả (studio) theo thời gian thực để đảm bảo kịp deadline giao bản in.</p>
    </div>
</div>

<!-- Chú giải ký hiệu trạng thái vẽ (Legend) -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body py-3">
        <h6 class="fw-bold mb-2 text-xs text-uppercase text-muted"><i class="fas fa-info-circle me-1"></i>Chú giải ký hiệu trạng thái vẽ của các trang</h6>
        <div class="d-flex flex-wrap gap-3 text-xs">
            <div class="d-flex align-items-center"><span class="badge bg-success me-1" style="width:12px; height:12px; display:inline-block; padding:0;"></span> Đã hoàn thành</div>
            <div class="d-flex align-items-center"><span class="badge bg-primary me-1" style="width:12px; height:12px; display:inline-block; padding:0;"></span> Trợ lý đang vẽ</div>
            <div class="d-flex align-items-center"><span class="badge bg-warning text-dark me-1" style="width:12px; height:12px; display:inline-block; padding:0;"></span> Chờ phân công/xử lý</div>
            <div class="d-flex align-items-center"><span class="badge bg-light border text-muted me-1" style="width:12px; height:12px; display:inline-block; padding:0;"></span> Không phân công (Tác giả tự vẽ)</div>
            <div class="ms-auto fw-bold text-dark"><span class="text-danger">BG</span>: Vẽ nền | <span class="text-secondary">INK</span>: Đi nét | <span class="text-success">COL</span>: Lên màu | <span class="text-info">FX</span>: Hiệu ứng SFX</div>
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
                            <?php 
                                $statusClass = 'secondary';
                                $statusText = $data['series']['status'];
                                if ($data['series']['status'] === 'ongoing') { $statusClass = 'success'; $statusText = 'Đang phát hành'; }
                                elseif ($data['series']['status'] === 'planning') { $statusClass = 'warning text-dark'; $statusText = 'Kế hoạch'; }
                                elseif ($data['series']['status'] === 'completed') { $statusClass = 'info text-dark'; $statusText = 'Hoàn thành'; }
                                elseif ($data['series']['status'] === 'suspended') { $statusClass = 'dark'; $statusText = 'Tạm ngưng'; }
                                elseif ($data['series']['status'] === 'canceled') { $statusClass = 'danger'; $statusText = 'Đã hủy'; }
                            ?>
                            <span class="badge bg-<?= $statusClass ?> px-2 py-1"><?= htmlspecialchars($statusText) ?></span>
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
                                        <span class="badge bg-primary text-xs ms-2"><?= $actChap['status'] === 'drawing' ? 'Đang vẽ' : ($actChap['status'] === 'reviewing' ? 'Đang chờ duyệt' : 'Bản thảo') ?></span>
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
