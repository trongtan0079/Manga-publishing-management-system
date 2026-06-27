<?php
/**
 * @var array $submission
 */
$pageTitle = 'Chi tiết bản thảo nộp';
$current_page = 'submissions';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';

$role = $_SESSION['role_name'] ?? '';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Chi tiết Bản thảo nộp</h2>
        <p class="text-muted text-xs mb-0">Xem thông tin tệp gửi lên, ghi chú, người thực hiện và trạng thái phê duyệt.</p>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=index" class="btn btn-outline-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
    </a>
</div>

<div class="row">
    <!-- Cột bên trái: Hiển thị Preview File / File info -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-file-image me-2 text-primary"></i>Xem trước sản phẩm</h5>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center p-4 bg-light" style="min-height: 400px;">
                <?php 
                $fileUrl = BASE_PATH . '/' . $submission['file_url'];
                $ext = strtolower(pathinfo($submission['file_url'], PATHINFO_EXTENSION));
                $isImage = in_array($ext, ['png', 'jpg', 'jpeg']);
                ?>

                <?php if ($isImage): ?>
                    <div class="img-preview-container text-center bg-white p-2 rounded border shadow-sm w-100" style="max-height: 500px; overflow: auto;">
                        <img src="<?= htmlspecialchars((string)($fileUrl ?? '')) ?>" alt="Bản thảo" class="img-fluid rounded" style="max-height: 480px; object-fit: contain;">
                    </div>
                <?php elseif ($ext === 'pdf'): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-file-pdf text-danger fa-5x mb-3 animate__animated animate__pulse animate__infinite"></i>
                        <h5>Tài liệu PDF hoàn chỉnh</h5>
                        <p class="text-muted">Bạn có thể tải về hoặc xem trực tiếp bằng trình đọc PDF của trình duyệt.</p>
                        <a href="<?= htmlspecialchars((string)($fileUrl ?? '')) ?>" target="_blank" class="btn btn-danger px-4 mt-2">
                            <i class="fas fa-external-link-alt me-2"></i>Mở trong Tab Mới
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-file-archive text-warning fa-5x mb-3"></i>
                        <h5>Tệp tin nén (ZIP)</h5>
                        <p class="text-muted">Tệp tin này chứa nhiều trang vẽ hoặc tài liệu được nén lại.</p>
                        <a href="<?= htmlspecialchars((string)($fileUrl ?? '')) ?>" download class="btn btn-warning text-dark px-4 mt-2 fw-bold">
                            <i class="fas fa-download me-2"></i>Tải Tệp ZIP
                        </a>
                    </div>
                <?php endif; ?>

                <div class="mt-4 pt-3 border-top w-100 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Tên file vật lý:</small>
                        <strong class="text-dark text-xs"><?= htmlspecialchars(basename($submission['file_url'] ?? '')) ?></strong>
                    </div>
                    <div>
                        <a href="<?= htmlspecialchars((string)($fileUrl ?? '')) ?>" download class="btn btn-outline-dark btn-sm">
                            <i class="fas fa-download me-1"></i>Tải xuống bản gốc
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột bên phải: Metadata (thông tin người gửi, trạng thái, ghi chú) / Hành động -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Thông tin chi tiết</h5>
            </div>
            <div class="card-body p-4">
                
                <div class="mb-3">
                    <small class="text-muted d-block text-uppercase text-xs fw-bold mb-1">Người thực hiện</small>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-weight: bold;">
                            <?= strtoupper(substr($submission['sender_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <span class="d-block fw-bold text-dark"><?= htmlspecialchars((string)($submission['sender_name'] ?? 'Không rõ')) ?></span>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <div class="row">
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block text-uppercase text-xs fw-bold mb-1">Loại nộp bài</small>
                        <?php if (!empty($submission['task_id'])): ?>
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">Task Drawing</span>
                        <?php else: ?>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Full Chapter</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block text-uppercase text-xs fw-bold mb-1">Trạng thái hiện tại</small>
                        <?php 
                        $statusClass = 'bg-secondary';
                        $statusLabel = 'Pending';
                        if ($submission['status'] === 'reviewed') {
                            $statusClass = 'bg-info';
                            $statusLabel = 'Reviewed';
                        } elseif ($submission['status'] === 'approved') {
                            $statusClass = 'bg-success';
                            $statusLabel = 'Approved';
                        } elseif ($submission['status'] === 'rejected') {
                            $statusClass = 'bg-danger';
                            $statusLabel = 'Rejected';
                        }
                        ?>
                        <span class="badge <?= $statusClass ?> px-2 py-1"><?= $statusLabel ?></span>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block text-uppercase text-xs fw-bold mb-1">Series tác phẩm</small>
                    <span class="text-dark fw-semibold"><?= htmlspecialchars((string)($submission['series_title'] ?? 'Không xác định')) ?></span>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block text-uppercase text-xs fw-bold mb-1">Chương hoặc Task cụ thể</small>
                    <span class="text-dark">
                        <?php if (!empty($submission['task_id'])): ?>
                            <i class="fas fa-tasks text-muted me-1"></i><?= htmlspecialchars((string)($submission['task_title'] ?? '')) ?>
                        <?php else: ?>
                            <i class="fas fa-layer-group text-muted me-1"></i>Chương <?= htmlspecialchars((string)($submission['chapter_number'] ?? '')) ?> - <?= htmlspecialchars((string)($submission['chapter_title'] ?? 'Chưa đặt tên')) ?>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block text-uppercase text-xs fw-bold mb-1">Ngày nộp</small>
                    <span class="text-dark"><i class="far fa-calendar-alt text-muted me-1"></i><?= htmlspecialchars(date('d/m/Y H:i:s', strtotime($submission['submitted_at'] ?? 'now'))) ?></span>
                </div>

                <hr class="my-3">

                <div class="mb-3">
                    <small class="text-muted d-block text-uppercase text-xs fw-bold mb-1">Ghi chú kèm theo</small>
                    <div class="bg-light p-3 rounded text-dark text-sm border-start border-primary border-3" style="white-space: pre-line;">
                        <?= !empty($submission['notes']) ? htmlspecialchars((string)($submission['notes'] ?? '')) : '<em>Không có ghi chú nào đi kèm.</em>' ?>
                    </div>
                </div>

            </div>
        </div>

        <!-- Khung hành động (Chuyển sang review hoặc Xóa bản thảo) -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-cogs me-2 text-primary"></i>Hành động khả dụng</h5>
            </div>
            <div class="card-body p-4 text-center">
                <?php if ($role === 'editor'): ?>
                    <p class="text-muted text-xs mb-3">Chuyển sang module đánh giá để ghi nhận xét, chấm điểm hoặc phê duyệt chương truyện này.</p>
                    <a href="<?= BASE_PATH ?>/index.php?controller=review&action=create&submission_id=<?= $submission['submission_id'] ?>" class="btn btn-success w-100 py-2.5 shadow-sm fw-bold">
                        <i class="fas fa-clipboard-check me-2"></i>Chuyển sang Review (Đánh giá)
                    </a>
                <?php elseif (($role === 'assistant' || $role === 'mangaka') && $submission['status'] === 'pending' && $submission['user_id'] == $_SESSION['user_id']): ?>
                    <p class="text-muted text-xs mb-3">Bạn có thể xóa bản thảo đã nộp nếu nó chưa được đánh giá bởi biên tập viên.</p>
                    <form action="<?= BASE_PATH ?>/index.php?controller=submission&action=delete&id=<?= $submission['submission_id'] ?>" method="POST" class="d-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản thảo này không?');">
                        <button type="submit" class="btn btn-danger w-100 py-2.5 shadow-sm fw-bold">
                            <i class="fas fa-trash-alt me-2"></i>Xóa bản thảo nộp này
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-muted mb-0">Bản thảo này hiện ở trạng thái <strong><?= htmlspecialchars(strtoupper($submission['status'] ?? '')) ?></strong> và không còn thay đổi được.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
