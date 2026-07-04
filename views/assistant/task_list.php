<?php 
/**
 * View: Assistant Task Dashboard
 * @var array $tasks Danh sách task của assistant
 */
$pageTitle = 'Danh sách Công việc';
$current_page = 'tasks';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">My Tasks Dashboard</h2>
        <p class="text-muted text-xs mb-0">Danh sách công việc được giao.</p>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white text-dark py-3 border-bottom border-light">
        <h5 class="card-title mb-0"><i class="fas fa-list me-2 text-primary"></i>Danh sách Công việc</h5>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($tasks)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Context (Ngữ cảnh)</th>
                            <th>Tài nguyên</th>
                            <th>Task (Công việc)</th>
                            <th>Priority</th>
                            <th>Due Date</th>
                            <th class="text-end pe-4">Status & Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td class="ps-4">
                                    <strong><?= htmlspecialchars($task['series_title']) ?></strong><br>
                                    <small class="text-muted">Ch. <?= htmlspecialchars($task['chapter_number']) ?> - Tr. <?= htmlspecialchars($task['page_number']) ?></small><br>
                                    <?php if (!empty($task['page_region_id'])): ?>
                                        <span class="badge bg-light text-dark border border-secondary mt-1">Phân vùng #<?= $task['page_region_id'] ?> (AI)</span><br>
                                    <?php endif; ?>
                                    <small class="text-info">By: <?= htmlspecialchars($task['mangaka_name']) ?></small>
                                </td>
                                <td>
                                    <?php if (!empty($task['image_url'])): ?>
                                        <a href="<?= BASE_PATH ?><?= htmlspecialchars($task['image_url']) ?>" download class="btn btn-sm btn-outline-dark mb-1 d-block" title="Tải trang gốc">
                                            <i class="fas fa-download me-1"></i> Tải Trang
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($task['resource_url'])): ?>
                                        <a href="<?= htmlspecialchars($task['resource_url']) ?>" target="_blank" class="btn btn-sm btn-info text-white d-block" title="Liên kết tài nguyên hỗ trợ">
                                            <i class="fas fa-external-link-alt me-1"></i> Tài nguyên
                                        </a>
                                    <?php endif; ?>

                                    <?php if (empty($task['image_url']) && empty($task['resource_url'])): ?>
                                        <span class="text-muted text-xs">Không có file</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $typeLabel = 'Khác';
                                    $typeBadge = 'bg-secondary';
                                    switch ($task['task_type'] ?? 'other') {
                                        case 'background': $typeLabel = 'Vẽ nền (Background)'; $typeBadge = 'bg-dark'; break;
                                        case 'inking': $typeLabel = 'Đi nét (Inking)'; $typeBadge = 'bg-secondary'; break;
                                        case 'coloring': $typeLabel = 'Lên màu (Coloring)'; $typeBadge = 'bg-success'; break;
                                        case 'effects': $typeLabel = 'Hiệu ứng (Effects)'; $typeBadge = 'bg-info text-dark'; break;
                                        case 'other': $typeLabel = 'Khác (Other)'; $typeBadge = 'bg-secondary'; break;
                                    }
                                    ?>
                                    <span class="badge <?= $typeBadge ?> mb-1"><?= $typeLabel ?></span><br>
                                    <strong><?= htmlspecialchars($task['title']) ?></strong>
                                    <?php if (!empty($task['description'])): ?>
                                        <br>
                                        <small class="text-muted" title="<?= htmlspecialchars($task['description']) ?>">
                                            <?= htmlspecialchars(mb_strlen($task['description']) > 50 ? mb_substr($task['description'], 0, 50).'...' : $task['description']) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $pColor = 'secondary';
                                    if ($task['priority'] == 'high') $pColor = 'danger';
                                    elseif ($task['priority'] == 'medium') $pColor = 'warning';
                                    else $pColor = 'info';
                                    ?>
                                    <span class="badge bg-<?= $pColor ?>"><?= ucfirst($task['priority']) ?></span>
                                </td>
                                <td>
                                    <?php if ($task['due_date']): ?>
                                        <?= htmlspecialchars(date('d/m/Y H:i', strtotime($task['due_date']))) ?>
                                        <?php if (strtotime($task['due_date']) < time() && $task['status'] != 'completed'): ?>
                                            <br><span class="badge bg-danger">Quá hạn</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">None</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <form action="<?= BASE_PATH ?>/index.php?controller=task&action=update&id=<?= $task['task_id'] ?>" method="POST" class="d-flex align-items-center gap-2 m-0">
                                            <select name="status" class="form-select form-select-sm" style="width: 120px;" title="Trạng thái">
                                                <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="in_progress" <?= $task['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                                <?php if ($task['status'] == 'completed'): ?>
                                                    <option value="completed" selected disabled>Completed</option>
                                                <?php endif; ?>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                        </form>
                                        <?php if ($task['status'] !== 'completed'): ?>
                                            <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=create&task_id=<?= $task['task_id'] ?>" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-paper-plane me-1"></i>Nộp bài
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-3 text-muted">
                    <i class="fas fa-tasks fa-3x"></i>
                </div>
                <p class="text-muted mb-0">Bạn hiện chưa được giao công việc nào.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
