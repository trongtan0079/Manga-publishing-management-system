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

<div class="mb-3">
    <a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=assistant" class="btn btn-sm btn-outline-secondary py-1.5 px-3 d-inline-flex align-items-center" style="border-radius: 8px; font-size: 0.78rem; font-weight: 500; transition: all 0.2s;">
        <i class="fas fa-arrow-left me-1.5"></i> Quay lại Dashboard
    </a>
</div>

<?php 
// Phân loại các task thành active và completed
$activeTasks = [];
$completedTasks = [];
if (!empty($tasks)) {
    foreach ($tasks as $task) {
        if ($task['status'] === 'completed') {
            $completedTasks[] = $task;
        } else {
            $activeTasks[] = $task;
        }
    }
}

// Xử lý bộ lọc từ URL
$statusFilter = $_GET['status'] ?? '';
$activeTabClass = 'active';
$completedTabClass = '';
if ($statusFilter === 'completed') {
    $activeTabClass = '';
    $completedTabClass = 'active';
}

// Hàm render bảng công việc dùng chung
if (!function_exists('renderTaskTable')) {
    function renderTaskTable($taskList, $isActive = true) {
        if (!empty($taskList)): ?>
            <div class="table-responsive" style="overflow: visible;">
                <table class="table premium-table align-middle mb-0">
                    <thead>
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
                        <?php foreach ($taskList as $task): ?>
                            <tr class="clickable-row" data-href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= $task['page_id'] ?>&highlight_region=<?= !empty($task['grouped_region_ids']) ? htmlspecialchars($task['grouped_region_ids']) : $task['page_region_id'] ?>">
                                <td class="ps-4" style="width: 200px;">
                                    <a href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= $task['page_id'] ?>&highlight_region=<?= !empty($task['grouped_region_ids']) ? htmlspecialchars($task['grouped_region_ids']) : $task['page_region_id'] ?>" class="text-decoration-none text-dark hover-primary-text d-block" title="Xem chi tiết phân trang &amp; phân vùng">
                                        <div class="fw-bold text-slate-800" style="font-size: 0.9rem; line-height: 1.25;"><?= htmlspecialchars($task['series_title']) ?></div>
                                        <small class="text-slate-500 font-medium">Ch. <?= htmlspecialchars($task['chapter_number']) ?> - Tr. <?= htmlspecialchars($task['page_number']) ?></small>
                                        <?php if (!empty($task['grouped_region_ids'])): ?>
                                            <?php
                                            $gidArr = array_filter(array_map('trim', explode(',', $task['grouped_region_ids'])));
                                            $gidList = implode(', ', array_map(fn($id) => '#'.$id, $gidArr));
                                            ?>
                                            <div class="mt-1">
                                                <span class="region-ai-badge" style="background: rgba(99,102,241,0.1); color: #4338ca; border: 1px solid rgba(99,102,241,0.3);"><i class="fas fa-layer-group me-1" style="font-size: 9px;"></i>Nhóm vùng: <?= $gidList ?></span>
                                            </div>
                                        <?php elseif (!empty($task['page_region_id'])): ?>
                                            <div class="mt-1">
                                                <span class="region-ai-badge"><i class="fas fa-crop me-1" style="font-size: 9px;"></i>Phân vùng #<?= $task['page_region_id'] ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                    <small class="text-slate-400 d-block mt-1"><i class="fas fa-user-edit me-1"></i><?= htmlspecialchars($task['mangaka_name']) ?></small>
                                </td>
                                <td style="width: 140px;">
                                    <?php if (!empty($task['image_url'])): ?>
                                        <a href="<?= BASE_PATH ?><?= htmlspecialchars($task['image_url']) ?>" download class="btn btn-sm btn-light border-slate-200 text-slate-700 w-100 mb-1.5 py-1.5 d-flex align-items-center justify-content-center" style="border-radius: 8px; font-weight: 500; font-size: 0.78rem; transition: all 0.2s;" title="Tải trang gốc">
                                            <i class="fas fa-download me-1 text-slate-500"></i> Tải Trang
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($task['resource_url'])): ?>
                                        <a href="<?= htmlspecialchars($task['resource_url']) ?>" target="_blank" class="btn btn-sm btn-indigo text-white w-100 py-1.5 d-flex align-items-center justify-content-center" style="background-color: #6366f1; border-radius: 8px; font-weight: 500; font-size: 0.78rem; transition: all 0.2s;" title="Liên kết tài nguyên hỗ trợ">
                                            <i class="fas fa-external-link-alt me-1"></i> Tài nguyên
                                        </a>
                                    <?php endif; ?>

                                    <?php if (empty($task['image_url']) && empty($task['resource_url'])): ?>
                                        <span class="badge bg-slate-100 text-slate-400 border py-1.5 px-2" style="font-size: 0.72rem; border-radius: 6px;">Không có file</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $typeKey = $task['task_type'] ?? 'other';
                                    $typeLabel = htmlspecialchars($typeKey);
                                    $typeClass = 'task-type-bg-other';
                                    
                                    if (strpos($task['title'], '(Nhóm:') !== false) {
                                        $typeLabel = 'Tổ hợp (Group)';
                                        $typeClass = 'bg-primary text-white border border-primary';
                                    } elseif ($typeKey === 'background') { 
                                        $typeLabel = 'Vẽ nền (Background)'; 
                                        $typeClass = 'task-type-bg-background'; 
                                    } elseif ($typeKey === 'inking') { 
                                        $typeLabel = 'Đi nét (Inking)'; 
                                        $typeClass = 'task-type-bg-inking'; 
                                    } elseif ($typeKey === 'coloring') { 
                                        $typeLabel = 'Lên màu (Coloring)'; 
                                        $typeClass = 'task-type-bg-coloring'; 
                                    } elseif ($typeKey === 'effects') { 
                                        $typeLabel = 'Hiệu ứng (Effects)'; 
                                        $typeClass = 'task-type-bg-effects'; 
                                    }
                                    ?>
                                    <span class="badge <?= $typeClass ?> mb-1" style="font-size: 0.7rem; font-weight: 600; padding: 3px 8px; border-radius: 12px;"><?= $typeLabel ?></span>
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <a href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= $task['page_id'] ?>&highlight_region=<?= !empty($task['grouped_region_ids']) ? htmlspecialchars($task['grouped_region_ids']) : $task['page_region_id'] ?>" class="text-decoration-none text-slate-800 hover-primary-text fw-bold fs-6" title="Xem chi tiết trang">
                                            <?= htmlspecialchars($task['title']) ?>
                                        </a>
                                        <?php if (!empty($task['description'])): ?>
                                            <button class="btn btn-link btn-xs p-0 text-decoration-none text-indigo-500 ms-1 d-inline-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#task-desc-<?= $task['task_id'] ?><?= $isActive ? '-active' : '-completed' ?>" aria-expanded="false" aria-controls="task-desc-<?= $task['task_id'] ?><?= $isActive ? '-active' : '-completed' ?>" title="Xem chi tiết mô tả" style="font-size: 0.72rem; font-weight: 600; box-shadow: none;">
                                                <i class="fas fa-info-circle me-1"></i>Chi tiết
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($task['description'])): ?>
                                        <div class="collapse mt-2" id="task-desc-<?= $task['task_id'] ?><?= $isActive ? '-active' : '-completed' ?>">
                                            <div class="card card-body bg-slate-50 p-2.5 border-slate-100 text-slate-600 shadow-none text-start" style="font-size: 0.8rem; max-width: 400px; max-height: 250px; overflow-y: auto; line-height: 1.5; border-radius: 8px;">
                                                <?= renderMarkdown($task['description']) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="width: 120px;">
                                     <?php 
                                     $pClass = 'priority-normal';
                                     $pLabel = 'Thường';
                                     if ($task['priority'] == 'high') {
                                         $pClass = 'priority-high';
                                         $pLabel = 'Cao';
                                     } elseif ($task['priority'] == 'medium') {
                                         $pClass = 'priority-medium';
                                         $pLabel = 'Trung bình';
                                     } elseif ($task['priority'] == 'low') {
                                         $pClass = 'priority-low';
                                         $pLabel = 'Thấp';
                                     }
                                     ?>
                                     <span class="badge <?= $pClass ?> px-2.5 py-1" style="font-size: 0.72rem; border-radius: 20px; font-weight: 600;"><?= $pLabel ?></span>
                                 </td>
                                 <td style="width: 150px;">
                                     <?php if ($task['due_date']): 
                                         $isOverdue = (strtotime($task['due_date']) < time() && $task['status'] != 'completed');
                                     ?>
                                         <div class="d-flex flex-column align-items-start gap-1">
                                             <span class="text-slate-700" style="font-size: 0.82rem; font-weight: 500;">
                                                 <i class="far fa-calendar-alt text-slate-400 me-1"></i><?= htmlspecialchars(date('d/m/Y H:i', strtotime($task['due_date']))) ?>
                                             </span>
                                             <?php if ($isOverdue): ?>
                                                 <span class="badge bg-red-100 text-red-700 border border-red-200" style="font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Quá hạn</span>
                                             <?php endif; ?>
                                         </div>
                                     <?php else: ?>
                                         <span class="text-slate-400" style="font-size: 0.8rem;">Không có</span>
                                     <?php endif; ?>
                                 </td>
                                 <td class="text-end pe-4" style="width: 250px;">
                                     <div class="d-flex align-items-center justify-content-end gap-2">
                                          <!-- Dropdown cập nhật trạng thái hoặc Badge hoàn thành -->
                                          <?php if ($task['status'] == 'completed'): ?>
                                              <span class="badge bg-success-subtle text-success border border-success-subtle py-1.5 px-3 d-inline-flex align-items-center" style="border-radius: 8px; font-size: 0.75rem; font-weight: 600;"><i class="fas fa-check-circle me-1.5 text-success"></i>Đã hoàn thành</span>
                                          <?php elseif ($task['status'] == 'submitted'): ?>
                                              <span class="badge bg-info-subtle text-info border border-info-subtle py-1.5 px-3 d-inline-flex align-items-center" style="border-radius: 8px; font-size: 0.75rem; font-weight: 600;"><i class="fas fa-spinner fa-spin me-1.5 text-info"></i>Đang chờ duyệt</span>
                                          <?php else: ?>
                                              <form action="<?= BASE_PATH ?>/index.php?controller=task&action=update&id=<?= $task['task_id'] ?>" method="POST" class="m-0">
                                                  <?= Csrf::field() ?>
                                                  <select name="status" class="form-select form-select-sm py-1.5 px-2 <?= $task['status'] == 'rejected' ? 'border-danger text-danger fw-bold bg-danger-subtle' : '' ?>" style="width: 135px; border-radius: 8px; font-size: 0.78rem; border-color: #cbd5e1; cursor: pointer;" onchange="this.form.submit()" title="Trạng thái">
                                                      <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Chờ thực hiện</option>
                                                      <option value="in_progress" <?= $task['status'] == 'in_progress' ? 'selected' : '' ?>>Đang làm</option>
                                                      <?php if ($task['status'] == 'rejected'): ?>
                                                      <option value="rejected" selected disabled>Yêu cầu sửa</option>
                                                      <?php endif; ?>
                                                  </select>
                                              </form>
                                          <?php endif; ?>

                                          <!-- Nút xem chi tiết -->
                                          <a href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= $task['page_id'] ?>&highlight_region=<?= !empty($task['grouped_region_ids']) ? htmlspecialchars($task['grouped_region_ids']) : $task['page_region_id'] ?>" class="btn btn-sm btn-light text-slate-600 border border-slate-200 d-inline-flex align-items-center justify-content-center" style="border-radius: 8px; width: 32px; height: 32px; transition: all 0.15s;" title="Xem chi tiết trang">
                                              <i class="fas fa-eye" style="font-size: 0.78rem;"></i>
                                          </a>

                                          <!-- Nút nộp bài -->
                                          <?php if ($task['status'] != 'completed'): ?>
                                              <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=create&task_id=<?= $task['task_id'] ?>" class="btn btn-sm btn-success py-1.5 px-3 d-inline-flex align-items-center" style="border-radius: 8px; font-size: 0.75rem; font-weight: 500; background-color: #10b981; border-color: #10b981; transition: all 0.2s;">
                                                  <i class="fas fa-paper-plane me-1.5" style="font-size: 0.75rem;"></i>Nộp bài
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
            <div class="text-center py-5 bg-white rounded-3 shadow-sm border border-slate-100">
                <div class="mb-3 text-muted">
                    <i class="fas fa-tasks fa-3x text-slate-300"></i>
                </div>
                <p class="text-slate-500 fw-semibold mb-1"><?= $isActive ? 'Không có công việc đang làm' : 'Chưa có công việc nào hoàn thành' ?></p>
                <p class="text-slate-400 text-xs mb-0"><?= $isActive ? 'Bạn hiện không có công việc nào cần thực hiện.' : 'Lịch sử công việc đã hoàn thành của bạn sẽ xuất hiện tại đây.' ?></p>
            </div>
        <?php endif;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Công việc của tôi</h2>
        <p class="text-muted text-xs mb-0">Danh sách công việc được giao.</p>
    </div>
    <?php if (!empty($statusFilter)): ?>
        <a href="<?= BASE_PATH ?>/index.php?controller=task&action=index" class="btn btn-sm btn-outline-secondary py-1.5 px-3" style="border-radius: 8px; font-size: 0.78rem;">
            <i class="fas fa-times me-1"></i>Xóa bộ lọc
        </a>
    <?php endif; ?>
</div>

<ul class="nav nav-tabs border-0 mb-4 gap-2" id="taskTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $activeTabClass === 'active' ? 'active' : '' ?> px-4 py-2.5 d-flex align-items-center" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-pane" type="button" role="tab" aria-controls="active-pane" aria-selected="<?= $activeTabClass === 'active' ? 'true' : 'false' ?>">
            <i class="fas fa-spinner me-2 text-warning"></i> Đang thực hiện
            <span class="badge bg-warning text-dark ms-2" style="font-size: 0.72rem; border-radius: 6px;"><?= count($activeTasks) ?></span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $completedTabClass === 'active' ? 'active' : '' ?> px-4 py-2.5 d-flex align-items-center" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-pane" type="button" role="tab" aria-controls="completed-pane" aria-selected="<?= $completedTabClass === 'active' ? 'true' : 'false' ?>">
            <i class="fas fa-check-circle me-2 text-success"></i> Lịch sử hoàn thành
            <span class="badge bg-success text-white ms-2" style="font-size: 0.72rem; border-radius: 6px;"><?= count($completedTasks) ?></span>
        </button>
    </li>
</ul>

<div class="tab-content" id="taskTabContent">
    <div class="tab-pane fade <?= $activeTabClass === 'active' ? 'show active' : '' ?>" id="active-pane" role="tabpanel" aria-labelledby="active-tab">
        <div class="card border-0 bg-transparent mb-4">
            <div class="card-body p-0">
                <?php renderTaskTable($activeTasks, true); ?>
            </div>
        </div>
    </div>
    <div class="tab-pane fade <?= $completedTabClass === 'active' ? 'show active' : '' ?>" id="completed-pane" role="tabpanel" aria-labelledby="completed-tab">
        <div class="card border-0 bg-transparent mb-4">
            <div class="card-body p-0">
                <?php renderTaskTable($completedTasks, false); ?>
            </div>
        </div>
    </div>
</div>

<style>
.hover-primary-text {
    transition: color 0.15s ease-in-out;
}
.hover-primary-text:hover, .hover-primary-text:hover small, .hover-primary-text:hover strong {
    color: var(--primary, #4f46e5) !important;
}

/* Premium separate rows table layout */
.premium-table {
    border-collapse: separate !important;
    border-spacing: 0 12px !important;
    background-color: transparent !important;
}
.premium-table tr {
    background-color: #ffffff;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
    border-radius: 12px;
    transition: all 0.2s ease-in-out;
}
.premium-table tr:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
    background-color: #fafafa !important;
}
.premium-table td {
    border: none !important;
    padding: 16px 20px !important;
}
.premium-table td:first-child {
    border-top-left-radius: 12px;
    border-bottom-left-radius: 12px;
}
.premium-table td:last-child {
    border-top-right-radius: 12px;
    border-bottom-right-radius: 12px;
}
.premium-table thead tr {
    background-color: transparent !important;
    box-shadow: none !important;
}
.premium-table thead tr:hover {
    transform: none !important;
    box-shadow: none !important;
    background-color: transparent !important;
}
.premium-table thead th {
    border: none !important;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.72rem;
    letter-spacing: 0.05em;
    padding: 8px 20px !important;
}

/* Soft colored badges */
.priority-high { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
.priority-medium { background-color: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
.priority-low { background-color: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
.priority-normal { background-color: #f8fafc; color: #475569; border: 1px solid #f1f5f9; }

.task-type-bg-background { background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
.task-type-bg-inking { background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.task-type-bg-coloring { background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
.task-type-bg-effects { background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.task-type-bg-other { background-color: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

.region-ai-badge { 
    background-color: #f5f3ff; 
    color: #6d28d9; 
    border: 1px solid #ddd6fe; 
    font-size: 0.7rem; 
    font-weight: 600; 
    padding: 3px 8px; 
    border-radius: 20px;
    display: inline-block;
}

/* Custom Tabs styling */
#taskTabs .nav-link {
    color: #64748b;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
}
#taskTabs .nav-link:hover {
    color: #4f46e5;
    background-color: #f1f5f9;
    border-color: #cbd5e1;
}
#taskTabs .nav-link.active {
    color: #ffffff;
    background-color: #4f46e5;
    border-color: #4f46e5;
}

.clickable-row {
    cursor: pointer;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const clickableRows = document.querySelectorAll(".clickable-row");
    clickableRows.forEach(row => {
        row.addEventListener("click", function(e) {
            // Đảm bảo không kích hoạt chuyển trang nếu click trúng nút, dropdown hoặc link con
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('select') || e.target.closest('form')) {
                return;
            }
            const href = this.getAttribute("data-href");
            if (href) {
                window.location.href = href;
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
