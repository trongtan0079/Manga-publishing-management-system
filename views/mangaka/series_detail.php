<?php 
/**
 * View: Giao diện chi tiết thông tin bộ truyện (series_detail.php)
 * Vai trò: Mangaka (Họa sĩ chính)
 * Chức năng: Hiển thị chi tiết về một bộ truyện cụ thể bao gồm tên, tác giả, mô tả, trạng thái xuất bản và danh sách các chương truyện đã tạo.
 * 
 * @var array $series Thông tin chi tiết của bộ truyện đang xem
 */
$pageTitle = 'Chi tiết Truyện: ' . htmlspecialchars($series['title']);
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- Thanh điều hướng cơ bản -->
<div class="mb-4 d-flex justify-content-between align-items-center">
    <?php
    $backUrl = BASE_PATH . '/index.php?controller=series&action=index';
    if (isset($_SESSION['role_name'])) {
        if ($_SESSION['role_name'] === 'board') {
            $backUrl = BASE_PATH . '/index.php?controller=series&action=publish';
        } elseif ($_SESSION['role_name'] === 'editor') {
            $backUrl = BASE_PATH . '/index.php?controller=dashboard&action=editor';
        }
    }
    ?>
    <a href="<?= $backUrl ?>" class="btn btn-outline-secondary shadow-sm"><i class="fas fa-arrow-left me-2"></i>Quay lại</a>
    
    <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka'): ?>
    <div class="d-flex gap-2">
        <?php if ($series['status'] === 'planning' && ($series['publish_type'] ?? '') === 'draft'): ?>
        <form action="<?= BASE_PATH ?>/index.php?controller=series&action=submit&id=<?= $series['series_id'] ?>" method="POST" class="m-0" onsubmit="return confirm('Bạn có chắc chắn muốn nộp đề xuất bộ truyện này đến Ban Biên tập?');">
            <button type="submit" class="btn btn-success shadow-sm">
                <i class="fas fa-paper-plane me-2"></i>Nộp Đề Xuất
            </button>
        </form>
        <?php endif; ?>
        <?php if (!in_array($series['status'], ['suspended', 'canceled', 'completed'])): ?>
        <a href="<?= BASE_PATH ?>/index.php?controller=series&action=edit&id=<?= $series['series_id'] ?>" class="btn btn-warning shadow-sm text-dark">
            <i class="fas fa-edit me-2"></i>Sửa Truyện
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<div class="row">
    <!-- Cột trái: Ảnh bìa và Thông tin cơ bản -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <?php if (!empty($series['cover_image'])): 
                $coverUrl = $series['cover_image'];
                $resolvedCover = (strpos($coverUrl, 'http') === 0) ? $coverUrl : BASE_PATH . '/' . ltrim($coverUrl, '/');
            ?>
                <img src="<?= htmlspecialchars($resolvedCover) ?>" class="card-img-top object-fit-cover" alt="Cover Image" style="max-height: 400px;">
            <?php else: ?>
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted" style="height: 300px;">
                    Chưa có ảnh bìa
                </div>
            <?php endif; ?>
            
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($series['title']) ?></h5>
                
                <?php
                $badgeClass = 'bg-secondary';
                $sLabel = $series['status'];
                switch ($series['status']) {
                    case 'planning': 
                        if (($series['publish_type'] ?? '') === 'draft') {
                            $badgeClass = 'bg-secondary'; $sLabel = 'Nháp (Chưa nộp)';
                        } else {
                            $badgeClass = 'bg-info text-dark'; $sLabel = 'Chờ phê duyệt';
                        }
                        break;
                    case 'ongoing': $badgeClass = 'bg-primary'; $sLabel = 'Đang triển khai'; break;
                    case 'completed': $badgeClass = 'bg-success'; $sLabel = 'Hoàn thành'; break;
                    case 'canceled': 
                        $badgeClass = 'bg-danger'; 
                        $sLabel = empty($series['editor_id']) ? 'Từ chối' : 'Đã hủy'; 
                        break;
                    case 'suspended': $badgeClass = 'bg-warning text-dark'; $sLabel = 'Tạm ngưng'; break;
                }
                ?>
                <p class="card-text">
                    <strong>Trạng thái:</strong> 
                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($sLabel) ?></span>
                </p>
                <p class="card-text">
                    <strong>Lịch xuất bản:</strong> 
                    <?php if ($series['status'] === 'planning'): ?>
                        <span class="badge bg-light text-dark border">Chưa quyết định (Chờ duyệt)</span>
                    <?php else: ?>
                        <span class="badge bg-secondary"><?= htmlspecialchars(($series['publish_type'] ?? 'weekly') === 'weekly' ? 'Hàng tuần' : 'Hàng tháng') ?></span>
                    <?php endif; ?>
                </p>
                <p class="card-text">
                    <strong>Ngày tạo:</strong> <br>
                    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($series['created_at']))) ?>
                </p>
                <p class="card-text">
                    <strong>Cập nhật lần cuối:</strong> <br>
                    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($series['updated_at']))) ?>
                </p>
                
                <?php if (!empty($series['proposal_file'])): ?>
                <div class="mt-3 border-top pt-3">
                    <p class="card-text mb-1"><strong>Tài liệu đề xuất:</strong></p>
                    <a href="<?= BASE_PATH . htmlspecialchars($series['proposal_file']) ?>" class="btn btn-sm btn-outline-primary w-100" target="_blank">
                        <i class="fas fa-file-download me-2"></i>Tải bản thảo sơ bộ
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cột phải: Mô tả và Danh sách Chapters -->
    <div class="col-md-8 mb-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Mô tả / Tóm tắt</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($series['description'])): ?>
                    <div class="card-text" style="white-space: pre-wrap;"><?= renderMarkdown($series['description'] ?? '') ?></div>
                <?php else: ?>
                    <p class="text-muted fst-italic">Chưa có mô tả.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($series['dossier_notes'])): ?>
        <div class="card border-danger mb-4 shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Biện hộ & Hồ sơ bảo vệ tác phẩm (Từ Editor phụ trách)</h5>
            </div>
            <div class="card-body">
                <div class="card-text text-dark" style="white-space: pre-wrap; font-size: 0.95rem;"><?= htmlspecialchars($series['dossier_notes']) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php 
        // Chỉ hiển thị danh sách chapter cho tác giả/trợ lý khi truyện đang chờ duyệt. Hội đồng/BTV chỉ thấy khi truyện đã duyệt sang ongoing
        $showChapters = ($series['status'] !== 'planning' || $_SESSION['role_name'] === 'mangaka' || $_SESSION['role_name'] === 'assistant');
        if ($showChapters): 
        ?>
        <!-- Chapter Management -->
        <div class="card border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách Chapter</h5>
                <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && in_array($series['status'], ['planning', 'ongoing'])): ?>
                <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=create&series_id=<?= $series['series_id'] ?>" class="btn btn-sm btn-light">+ Tạo Chapter mới</a>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($chapters)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tên Chapter</th>
                                    <th>Tiến độ Studio</th>
                                    <th>Trạng thái</th>
                                    <th>Cập nhật lần cuối</th>
                                    <th class="text-end">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chapters as $chapter): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($chapter['chapter_number']) ?></td>
                                        <td>
                                            <?= htmlspecialchars($chapter['title'] ?? '') ?>
                                            <?php if (!empty($chapter['is_final'])): ?>
                                                <span class="badge bg-danger text-white text-xs ms-1"><i class="fas fa-flag me-1"></i>Chương cuối</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($chapter['total_tasks'] > 0): 
                                                $percent = round(($chapter['completed_tasks'] / $chapter['total_tasks']) * 100);
                                                $barClass = $percent === 100 ? 'bg-success' : 'bg-primary';
                                            ?>
                                                <div style="min-width: 120px; max-width: 160px;">
                                                    <div class="progress" style="height: 6px; background-color: #e9ecef; border-radius: 3px; margin-bottom: 2px;">
                                                        <div class="progress-bar <?= $barClass ?>" role="progressbar" style="width: <?= $percent ?>%; border-radius: 3px;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <small class="text-muted" style="font-size: 0.72rem; font-weight: 500;"><?= $chapter['completed_tasks'] ?>/<?= $chapter['total_tasks'] ?> việc (<?= $percent ?>%)</small>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-light text-secondary border text-xs" style="font-weight: 500;">Tác giả tự vẽ</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $cBadge = 'bg-secondary';
                                            $cLabel = $chapter['status'];
                                            switch ($chapter['status']) {
                                                case 'drafting': $cBadge = 'bg-secondary'; $cLabel = 'Bản nháp'; break;
                                                case 'drawing': $cBadge = 'bg-primary'; $cLabel = 'Đang vẽ'; break;
                                                case 'reviewing': $cBadge = 'bg-warning text-dark'; $cLabel = 'Đang chờ duyệt'; break;
                                                case 'approved': $cBadge = 'bg-info text-dark'; $cLabel = 'Đã duyệt'; break;
                                                case 'published': $cBadge = 'bg-success'; $cLabel = 'Đã xuất bản'; break;
                                            }
                                            ?>
                                            <span class="badge <?= $cBadge ?>"><?= htmlspecialchars($cLabel) ?></span>
                                        </td>
                                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($chapter['updated_at']))) ?></td>
                                        <td class="text-end">
                                            <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=show&id=<?= $chapter['chapter_id'] ?>" class="btn btn-sm btn-info text-white">Xem</a>
                                            <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !in_array($series['status'], ['suspended', 'canceled', 'completed']) && !in_array($chapter['status'], ['reviewing', 'approved', 'published'])): ?>
                                            <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=edit&id=<?= $chapter['chapter_id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                                            <form action="<?= BASE_PATH ?>/index.php?controller=chapter&action=delete&id=<?= $chapter['chapter_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa chapter này?');">
                                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                            </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <p class="text-muted mb-0">Chưa có chapter nào. Hãy tạo chapter đầu tiên!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
