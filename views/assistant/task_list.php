<?php 
/**
 * View: Assistant Task Dashboard
 * @var array $tasks Danh sách task của assistant
 */
include __DIR__ . '/../layouts/header.php'; 
?>

<!-- 
  View: Dashboard hiển thị danh sách các Task dành cho Assistant.
  Tại đây, Assistant có thể xem ngữ cảnh (Context), tên task, độ ưu tiên, hạn chót
  và cập nhật trực tiếp tiến độ công việc thông qua form Dropdown nội tuyến.
-->
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <h4 class="mb-0">My Tasks Dashboard</h4>
        <small>Danh sách công việc được giao</small>
    </div>
    <div class="card-body">
        <?php if (!empty($tasks)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Context (Ngữ cảnh)</th>
                            <th>Task (Công việc)</th>
                            <th>Priority (Độ ưu tiên)</th>
                            <th>Due Date (Hạn chót)</th>
                            <th>Status & Update (Cập nhật)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Vòng lặp duyệt qua tất cả các task được giao cho assistant -->
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <!-- Cột Ngữ cảnh: Hiển thị bộ truyện, chương, trang và người giao -->
                                <td>
                                    <strong><?= htmlspecialchars($task['series_title']) ?></strong><br>
                                    <small class="text-muted">Ch. <?= htmlspecialchars($task['chapter_number']) ?> - Pg. <?= htmlspecialchars($task['page_number']) ?></small><br>
                                    <small class="text-info">By: <?= htmlspecialchars($task['mangaka_name']) ?></small>
                                </td>
                                <!-- Cột Công việc: Tiêu đề và mô tả vắn tắt -->
                                <td>
                                    <strong><?= htmlspecialchars($task['title']) ?></strong>
                                    <?php if (!empty($task['description'])): ?>
                                        <br>
                                        <!-- Cắt ngắn mô tả nếu quá dài (giới hạn 50 ký tự) để giao diện không bị vỡ -->
                                        <small class="text-muted"><?= htmlspecialchars(strlen($task['description']) > 50 ? substr($task['description'], 0, 50).'...' : $task['description']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <!-- Cột Độ ưu tiên: Hiển thị huy hiệu (Badge) màu sắc dựa theo giá trị -->
                                <td>
                                    <?php 
                                    $pColor = 'secondary';
                                    if ($task['priority'] == 'high') $pColor = 'danger';
                                    elseif ($task['priority'] == 'medium') $pColor = 'warning';
                                    else $pColor = 'info';
                                    ?>
                                    <span class="badge bg-<?= $pColor ?>"><?= ucfirst($task['priority']) ?></span>
                                </td>
                                <!-- Cột Hạn chót -->
                                <td>
                                    <?php if ($task['due_date']): ?>
                                        <?= htmlspecialchars(date('d/m/Y H:i', strtotime($task['due_date']))) ?>
                                        <!-- Kiểm tra nếu thời gian hiện tại đã vượt qua hạn chót và task chưa hoàn thành thì báo Quá hạn -->
                                        <?php if (strtotime($task['due_date']) < time() && $task['status'] != 'completed'): ?>
                                            <br><span class="badge bg-danger">Quá hạn</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">None</span>
                                    <?php endif; ?>
                                </td>
                                <!-- Cột Cập nhật trạng thái: Là một form nhỏ chứa Dropdown và nút Save để cập nhật nhanh -->
                                <td>
                                    <form action="/index.php?controller=task&action=update&id=<?= $task['task_id'] ?>" method="POST" class="d-flex align-items-center">
                                        <!-- Dropdown chọn status hiện tại -->
                                        <select name="status" class="form-select form-select-sm me-2" style="width: 130px;">
                                            <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="in_progress" <?= $task['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="completed" <?= $task['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Trạng thái trống (Empty State) khi không có task nào -->
            <div class="alert alert-info mb-0">
                Bạn hiện chưa được giao công việc nào.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
