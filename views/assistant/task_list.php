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
        <h2 class="h3 mb-1">Công việc của tôi</h2>
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
                            <th class="ps-4">Ngữ cảnh truyện</th>
                            <th>Tài nguyên</th>
                            <th>Nhiệm vụ phân công</th>
                            <th>Độ ưu tiên</th>
                            <th>Hạn chót</th>
                            <th class="text-end pe-4">Trạng thái & Cập nhật</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td class="ps-4">
                                    <a href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= $task['page_id'] ?>&highlight_region=<?= $task['page_region_id'] ?>" class="text-decoration-none text-dark hover-primary-text" title="Xem chi tiết phân trang & phân vùng">
                                        <strong><?= htmlspecialchars($task['series_title']) ?></strong><br>
                                        <small class="text-muted">Ch. <?= htmlspecialchars($task['chapter_number']) ?> - Tr. <?= htmlspecialchars($task['page_number']) ?></small><br>
                                    </a>
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
                                    <?php if (!empty($task['description'])): 
                                        $plainDesc = strip_tags($task['description']);
                                    ?>
                                        <br>
                                        <small class="text-muted" title="<?= htmlspecialchars($plainDesc) ?>">
                                            <?= htmlspecialchars(mb_strlen($plainDesc) > 50 ? mb_substr($plainDesc, 0, 50).'...' : $plainDesc) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                     <?php 
                                     $pColor = 'secondary';
                                     $pLabel = 'Thường';
                                     if ($task['priority'] == 'high') {
                                         $pColor = 'danger';
                                         $pLabel = 'Cao';
                                     } elseif ($task['priority'] == 'medium') {
                                         $pColor = 'warning';
                                         $pLabel = 'Trung bình';
                                     } else {
                                         $pColor = 'info';
                                         $pLabel = 'Thấp';
                                     }
                                     ?>
                                     <span class="badge bg-<?= $pColor ?>"><?= $pLabel ?></span>
                                 </td>
                                 <td>
                                     <?php if ($task['due_date']): ?>
                                         <?= htmlspecialchars(date('d/m/Y H:i', strtotime($task['due_date']))) ?>
                                         <?php if (strtotime($task['due_date']) < time() && $task['status'] != 'completed'): ?>
                                             <br><span class="badge bg-danger">Quá hạn</span>
                                         <?php endif; ?>
                                     <?php else: ?>
                                         <span class="text-muted">Không có</span>
                                     <?php endif; ?>
                                 </td>
                                 <td class="text-end pe-4">
                                     <div class="d-flex align-items-center justify-content-end gap-2">
                                          <a href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= $task['page_id'] ?>&highlight_region=<?= $task['page_region_id'] ?>" class="btn btn-sm btn-outline-info" style="border-radius: 6px;" title="Xem chi tiết trang">
                                              <i class="fas fa-eye"></i>
                                          </a>
                                          <form action="<?= BASE_PATH ?>/index.php?controller=task&action=update&id=<?= $task['task_id'] ?>" method="POST" class="d-flex align-items-center gap-2 m-0">
                                              <select name="status" class="form-select form-select-sm" style="width: 130px; border-radius: 6px;" title="Trạng thái">
                                                  <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Chờ thực hiện</option>
                                                  <option value="in_progress" <?= $task['status'] == 'in_progress' ? 'selected' : '' ?>>Đang làm</option>
                                                  <?php if ($task['status'] == 'completed'): ?>
                                                      <option value="completed" selected disabled>Đã hoàn thành</option>
                                                  <?php endif; ?>
                                              </select>
                                              <button type="submit" class="btn btn-sm btn-primary" style="border-radius: 6px;">Lưu</button>
                                          </form>
                                          <?php if ($task['status'] !== 'completed'): ?>
                                              <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=create&task_id=<?= $task['task_id'] ?>" class="btn btn-sm btn-outline-success" style="border-radius: 6px;">
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const filterStatus = urlParams.get('status');
    if (filterStatus) {
        const rows = document.querySelectorAll("tbody tr");
        let foundCount = 0;
        rows.forEach(row => {
            const selectEl = row.querySelector("td select[name='status']");
            if (selectEl) {
                const statusVal = selectEl.value;
                if (statusVal === filterStatus) {
                    row.style.display = "";
                    foundCount++;
                } else {
                    row.style.display = "none";
                }
            }
        });
        
        const cardHeader = document.querySelector(".card-header h5");
        if (cardHeader) {
            const clearBtn = document.createElement("a");
            clearBtn.href = window.location.pathname + "?controller=task&action=index";
            clearBtn.className = "btn btn-sm btn-outline-secondary ms-3 py-1 px-2";
            clearBtn.style.fontSize = "0.75rem";
            clearBtn.innerHTML = "<i class='fas fa-times me-1'></i>Xóa bộ lọc";
            cardHeader.appendChild(clearBtn);
        }
    }
});
</script>

<style>
.hover-primary-text {
    transition: color 0.15s ease-in-out;
}
.hover-primary-text:hover, .hover-primary-text:hover small, .hover-primary-text:hover strong {
    color: var(--primary, #6366f1) !important;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
