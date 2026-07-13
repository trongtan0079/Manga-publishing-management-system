<?php 
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
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
<style>
    /* Premium style system for Series Detail */
    .series-cover-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        border-radius: 16px !important;
        overflow: hidden;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.02), 0 2px 8px -1px rgba(15, 23, 42, 0.02) !important;
        position: sticky;
        top: 24px;
        transition: all 0.3s ease;
    }
    .series-cover-card:hover {
        box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.05), 0 4px 12px -2px rgba(15, 23, 42, 0.03) !important;
    }
    .series-cover-img {
        aspect-ratio: 2 / 3;
        height: auto;
        object-fit: cover;
        width: 100%;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        transition: transform 0.5s ease;
    }
    .series-cover-card:hover .series-cover-img {
        transform: scale(1.04);
    }
    .detail-card {
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.02), 0 2px 8px -1px rgba(15, 23, 42, 0.02) !important;
        overflow: hidden;
        background: #ffffff;
    }
    .clickable-row {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .clickable-row:hover {
        background-color: #f8fafc !important;
    }
    .gradient-progress-bar {
        background: linear-gradient(90deg, #4f46e5 0%, #818cf8 100%) !important;
    }
    .action-btn-pill {
        border-radius: 20px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        padding: 5px 12px !important;
        transition: all 0.2s ease;
    }
    .metadata-item {
        transition: all 0.2s ease;
    }
    .metadata-item:hover {
        background-color: #f1f5f9 !important;
        transform: translateX(2px);
    }
</style>

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
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
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
    <a href="<?= $backUrl ?>" class="btn btn-outline-secondary px-3.5 py-2 shadow-xs d-inline-flex align-items-center" style="border-radius: 10px; font-weight: 600; font-size: 0.85rem;"><i class="fa-solid fa-arrow-left me-2"></i>Quay lại</a>
    
    <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka'): ?>
    <div class="d-flex gap-2">
        <?php if ($series['status'] === 'planning' && ($series['publish_type'] ?? '') === 'draft'): ?>
        <form action="<?= BASE_PATH ?>/index.php?controller=series&action=submit&id=<?= $series['series_id'] ?>" method="POST" class="m-0" onsubmit="return confirm('Bạn có chắc chắn muốn nộp đề xuất bộ truyện này đến Ban Biên tập?');">
            <button type="submit" class="btn btn-success px-3.5 py-2 shadow-xs d-inline-flex align-items-center" style="border-radius: 10px; font-weight: 600; font-size: 0.85rem;">
                <i class="fa-solid fa-paper-plane me-2"></i>Nộp Đề Xuất
            </button>
        </form>
        <?php endif; ?>
        <?php if (!$this->isSeriesLocked($series)): ?>
        <a href="<?= BASE_PATH ?>/index.php?controller=series&action=edit&id=<?= $series['series_id'] ?>" class="btn btn-warning px-3.5 py-2 shadow-xs d-inline-flex align-items-center text-slate-800" style="border-radius: 10px; font-weight: 600; font-size: 0.85rem; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: 0; color: #ffffff !important; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.15) !important;">
            <i class="fa-solid fa-pen-to-square me-2"></i>Sửa Truyện
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Tiêu đề trang Premium -->
<div class="mb-4">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <h1 class="fw-extrabold text-slate-900 mb-0" style="font-size: 2.2rem; letter-spacing: -0.03em;"><?= htmlspecialchars($series['title']) ?></h1>
        <div style="transform: scale(0.9); transform-origin: left center;">
            <?= $this->getSeriesStatusBadge($series) ?>
        </div>
    </div>
    <p class="text-slate-500 text-sm mt-1 mb-0">Thông tin chi tiết và tiến độ triển khai tác phẩm</p>
</div>

<div class="row">
    <!-- Cột trái: Ảnh bìa và Thông tin cơ bản -->
    <div class="col-md-3 mb-4">
        <div class="card series-cover-card">
            <?php if (!empty($series['cover_image'])): 
                $coverUrl = $series['cover_image'];
                $resolvedCover = (strpos($coverUrl, 'http') === 0) ? $coverUrl : BASE_PATH . '/' . ltrim($coverUrl, '/');
            ?>
                <div class="overflow-hidden" style="position: relative;">
                    <img src="<?= htmlspecialchars($resolvedCover) ?>" class="series-cover-img" alt="Cover Image">
                </div>
            <?php else: ?>
                <div class="bg-light d-flex flex-column align-items-center justify-content-center text-muted border-bottom" style="height: 250px;">
                    <i class="fa-solid fa-image fa-3x mb-3 text-slate-300"></i>
                    <span class="text-xs fw-semibold uppercase tracking-wider text-slate-400">Chưa có ảnh bìa</span>
                </div>
            <?php endif; ?>
            
            <div class="card-body p-4">
                <ul class="list-unstyled d-flex flex-column gap-3.5 text-xs mb-0">
                    <li class="metadata-item d-flex align-items-center justify-content-between p-2.5 rounded bg-slate-50 border border-light-subtle">
                        <span class="text-slate-500 fw-medium"><i class="fa-regular fa-calendar-check me-1.5 text-success"></i>Lịch xuất bản:</span>
                        <span>
                            <?php if ($series['status'] === 'planning'): ?>
                                <span class="badge bg-light text-dark border">Chờ duyệt</span>
                            <?php else: ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-bold px-2 py-0.5" style="border-radius: 4px;"><?= htmlspecialchars(($series['publish_type'] ?? 'weekly') === 'weekly' ? 'Hàng tuần' : 'Hàng tháng') ?></span>
                            <?php endif; ?>
                        </span>
                    </li>
                    <li class="metadata-item d-flex align-items-center justify-content-between p-2.5 rounded bg-slate-50 border border-light-subtle">
                        <span class="text-slate-500 fw-medium"><i class="fa-regular fa-clock me-1.5 text-info"></i>Ngày tạo:</span>
                        <span class="text-slate-700 fw-semibold"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($series['created_at']))) ?></span>
                    </li>
                    <li class="metadata-item d-flex align-items-center justify-content-between p-2.5 rounded bg-slate-50 border border-light-subtle">
                        <span class="text-slate-500 fw-medium"><i class="fa-solid fa-arrows-rotate me-1.5 text-warning"></i>Cập nhật:</span>
                        <span class="text-slate-700 fw-semibold"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($series['updated_at']))) ?></span>
                    </li>
                </ul>
                
                <?php if (!empty($series['proposal_file'])): ?>
                <div class="mt-4 border-top pt-3">
                    <p class="card-text mb-2 text-xs text-slate-500 fw-semibold"><i class="fa-solid fa-file-pdf me-1.5 text-danger"></i> Tài liệu đề xuất:</p>
                    <a href="<?= BASE_PATH . htmlspecialchars($series['proposal_file']) ?>" class="btn btn-sm btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-1.5" style="border-radius: 10px; font-weight: 600; font-size: 0.78rem;" target="_blank">
                        <i class="fa-solid fa-cloud-arrow-down"></i>Tải bản thảo sơ bộ
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cột phải: Mô tả và Danh sách Chapters -->
    <div class="col-md-9 mb-4">
        <div class="card detail-card mb-4">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h5 class="mb-0 fw-extrabold text-slate-800" style="font-size: 1rem;"><i class="fa-solid fa-file-lines me-2 text-primary"></i>Mô tả / Tóm tắt</h5>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($series['description'])): ?>
                    <div class="card-text text-slate-700 font-medium" style="white-space: pre-wrap; font-size: 0.85rem; line-height: 1.6;"><?= renderMarkdown($series['description'] ?? '') ?></div>
                <?php else: ?>
                    <p class="text-muted fst-italic text-xs mb-0">Chưa có mô tả chi tiết cho bộ truyện này.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($series['dossier_notes'])): ?>
        <div class="card detail-card border-danger mb-4 shadow-sm">
            <div class="card-header bg-danger text-white py-3">
                <h5 class="mb-0 text-white fw-extrabold" style="font-size: 1rem;"><i class="fa-solid fa-shield-halved me-2"></i>Biện hộ & Hồ sơ bảo vệ tác phẩm (BTV phụ trách)</h5>
            </div>
            <div class="card-body p-4">
                <div class="card-text text-slate-700 font-medium" style="white-space: pre-wrap; font-size: 0.85rem; line-height: 1.6;"><?= htmlspecialchars($series['dossier_notes']) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php 
        // Chỉ hiển thị danh sách chapter cho tác giả/trợ lý khi truyện đang chờ duyệt. Hội đồng/BTV chỉ thấy khi truyện đã duyệt sang ongoing
        $showChapters = ($series['status'] !== 'planning' || $_SESSION['role_name'] === 'mangaka' || $_SESSION['role_name'] === 'assistant');
        if ($showChapters): 
        ?>
        <!-- Chapter Management -->
        <div class="card detail-card">
            <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 fw-extrabold text-slate-800" style="font-size: 1rem;"><i class="fa-solid fa-list-ol me-2 text-primary"></i>Danh sách Chapter</h5>
                <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && $series['status'] === 'ongoing'): ?>
                <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=create&series_id=<?= $series['series_id'] ?>" class="btn btn-sm btn-primary action-btn-pill d-inline-flex align-items-center gap-1.5" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); border: 0; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15); color: #ffffff !important;"><i class="fa-solid fa-circle-plus"></i>Tạo Chapter mới</a>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($chapters)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                            <thead class="table-light text-slate-500 font-extrabold text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Tên Chapter</th>
                                    <th>Tiến độ Studio</th>
                                    <th>Trạng thái</th>
                                    <th>Cập nhật lần cuối</th>
                                    <th class="text-end pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chapters as $chapter): ?>
                                    <tr class="clickable-row" data-href="<?= BASE_PATH ?>/index.php?controller=chapter&action=show&id=<?= $chapter['chapter_id'] ?>">
                                        <td class="ps-4 fw-extrabold text-slate-400">#<?= htmlspecialchars($chapter['chapter_number']) ?></td>
                                        <td>
                                            <strong class="text-slate-800"><?= htmlspecialchars($chapter['title'] ?? '') ?></strong>
                                            <?php if (!empty($chapter['is_final'])): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle text-xs ms-1.5" style="border-radius: 4px; font-size: 0.6rem !important;"><i class="fa-solid fa-flag me-1"></i>Chương cuối</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($chapter['total_tasks'] > 0): 
                                                $percent = round(($chapter['completed_tasks'] / $chapter['total_tasks']) * 100);
                                            ?>
                                                <div style="min-width: 120px; max-width: 160px;">
                                                    <div class="progress" style="height: 5px; background-color: var(--slate-100); border-radius: 3px; margin-bottom: 3px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);">
                                                        <div class="progress-bar gradient-progress-bar" role="progressbar" style="width: <?= $percent ?>%; border-radius: 3px;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <small class="text-slate-500 font-bold" style="font-size: 0.68rem;"><?= $chapter['completed_tasks'] ?>/<?= $chapter['total_tasks'] ?> việc (<?= $percent ?>%)</small>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-light text-secondary border text-xs" style="font-weight: 600; border-radius: 4px; font-size: 0.6rem !important;">Tác giả tự vẽ</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= $this->getStatusBadge($chapter['status']) ?>
                                        </td>
                                        <td class="text-slate-600 font-semibold"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($chapter['updated_at']))) ?></td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-1.5">
                                                <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=show&id=<?= $chapter['chapter_id'] ?>" class="btn btn-sm btn-outline-primary action-btn-pill">Xem</a>
                                                <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$this->isSeriesLocked($series) && !$this->isChapterLocked($chapter)): ?>
                                                    <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=edit&id=<?= $chapter['chapter_id'] ?>" class="btn btn-sm btn-outline-warning action-btn-pill">Sửa</a>
                                                    <form action="<?= BASE_PATH ?>/index.php?controller=chapter&action=delete&id=<?= $chapter['chapter_id'] ?>" method="POST" class="d-inline m-0" onsubmit="return confirm('Bạn có chắc chắn muốn xóa chapter này?');">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger action-btn-pill">Xóa</button>
                                                    </form>
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
                        <p class="text-muted mb-0">Chưa có chapter nào. Hãy tạo chapter đầu tiên!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const clickableRows = document.querySelectorAll(".clickable-row");
    clickableRows.forEach(row => {
        row.addEventListener("click", function(e) {
            // Đảm bảo không kích hoạt chuyển trang nếu click trúng nút, dropdown hoặc link con
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('select') || e.target.closest('form') || e.target.closest('input')) {
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

<?php include __DIR__ . '/../layouts/footer.php'; ?>
