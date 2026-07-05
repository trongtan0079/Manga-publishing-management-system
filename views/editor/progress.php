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
 * Vai trò: Editor (Biên tập viên) / Các vai trò khác
 * Chức năng: Theo dõi tiến độ hoàn thành chương truyện và các công việc (Task) sắp đến hạn hoặc trễ hạn của từng bộ truyện.
 * 
 * @var array $progressData Danh sách tiến độ và thông tin Task của từng bộ truyện
 */
$pageTitle = 'Tiến độ & Deadline';
$current_page = 'progress';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Tiến độ & Deadline</h2>
        <p class="text-muted text-xs mb-0">Theo dõi tiến độ các bộ truyện, chương và deadline công việc.</p>
    </div>
</div>

<div class="row">
    <?php if (!empty($progressData)): ?>
        <?php foreach ($progressData as $data): ?>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-book me-2 text-primary"></i><?= htmlspecialchars($data['series']['title']) ?>
                            </h5>
                            <?php 
                                $statusClass = 'secondary';
                                if ($data['series']['status'] === 'ongoing') $statusClass = 'success';
                                elseif ($data['series']['status'] === 'planning') $statusClass = 'warning text-dark';
                                elseif ($data['series']['status'] === 'completed') $statusClass = 'info text-dark';
                            ?>
                            <span class="badge bg-<?= $statusClass ?> px-2 py-1"><?= ucfirst(htmlspecialchars($data['series']['status'])) ?></span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted text-xs text-uppercase fw-bold">Tiến độ xuất bản (Chương hoàn thành)</span>
                                <span class="text-dark fw-bold"><?= $data['completed_chapters'] ?> / <?= $data['total_chapters'] ?></span>
                            </div>
                            <?php 
                            $percent = $data['total_chapters'] > 0 ? round(($data['completed_chapters'] / $data['total_chapters']) * 100) : 0; 
                            ?>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3"><i class="far fa-calendar-alt me-2 text-warning"></i>Deadline công việc (Top 5)</h6>
                        <?php if (!empty($data['pending_tasks'])): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($data['pending_tasks'] as $task): ?>
                                    <li class="list-group-item px-0 py-2 border-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="fw-bold text-dark d-block"><?= htmlspecialchars($task['title']) ?></span>
                                                <small class="text-muted">Ch.<?= htmlspecialchars($task['chapter_number'] ?? '') ?></small>
                                            </div>
                                            <div class="text-end">
                                                <?php if ($task['due_date']): ?>
                                                    <span class="badge <?= strtotime($task['due_date']) < time() ? 'bg-danger' : 'bg-light text-dark border' ?>">
                                                        <i class="far fa-clock me-1"></i><?= date('d/m/Y', strtotime($task['due_date'])) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted text-xs">Không có</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted small mb-0">Không có công việc nào đang chờ hoặc đã trễ hạn.</p>
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
                    <p class="text-muted mb-0 small">Hiện không có dự án truyện tranh nào đang trong quá trình sáng tác hoặc hoạt động.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
