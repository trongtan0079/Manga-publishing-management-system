<?php 
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
/**
 * View: Giao diện nhập dữ liệu bình chọn của độc giả và tự động xếp hạng (ranking_create.php)
 * Vai trò: Board (Hội đồng/Ban giám đốc)
 * Chức năng: Cho phép Hội đồng nhập nhanh số phiếu bình chọn của độc giả cho tất cả bộ truyện đang hoạt động và tự động tính toán thứ hạng.
 * 
 * @var array $seriesList Danh sách các bộ truyện đang hoạt động
 */
require_once __DIR__ . '/../../core/Auth.php';
$role = $_SESSION['role_name'];
$pageTitle = 'Nhập Số Phiếu Bình Chọn & Xếp Hạng';
$current_page = 'rankings';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1 text-dark fw-bold"><i class="fas fa-vote-yea me-2 text-primary"></i>Nhập Phiếu Bình Chọn Độc Giả</h2>
        <p class="text-muted text-xs mb-0">Hệ thống sẽ tự động sắp xếp thứ tự và tính điểm số quy chuẩn (0 - 100) dựa trên số phiếu bạn nhập.</p>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=index" class="btn btn-secondary btn-sm shadow-sm px-3" style="border-radius: 8px;">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px;">
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <form action="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=store" method="POST">
            <?= Csrf::field() ?>
            <!-- Khung chọn chu kỳ -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4 bg-light rounded-3">
                    <div class="row align-items-center">
                        <div class="col-lg-6 mb-3 mb-lg-0">
                            <label for="period_start_date" class="form-label fw-bold text-slate-700"><i class="far fa-calendar-alt me-1 text-primary"></i>Chọn Kỳ Đánh Giá (Ngày Bắt Đầu) <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="period_start_date" name="period_start_date" required style="border-radius: 8px;">
                            <div class="form-text text-muted" style="font-size: 0.75rem;">Ví dụ: Chọn ngày đầu tiên của tuần hoặc của tháng để ghi nhận chu kỳ phát hành này.</div>
                        </div>
                        <div class="col-lg-6">
                            <div class="p-3 border rounded-3 bg-white" style="border-style: dashed !important;">
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;"><i class="fas fa-magic me-1 text-success"></i>Cơ chế tự động hóa:</h6>
                                <p class="text-muted mb-0" style="font-size: 0.78rem; line-height: 1.4;">
                                    Sau khi bạn gửi số phiếu, hệ thống sẽ tự động tìm Manga có số phiếu cao nhất để đặt mốc **100 điểm**. Các tác phẩm còn lại sẽ nhận điểm quy chuẩn tỷ lệ thuận với số phiếu và tự động gán thứ hạng `#1`, `#2`, `#3`...
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng nhập phiếu bầu -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 0.95rem;"><i class="fas fa-list-ol me-2 text-primary"></i>Danh sách bộ truyện đang hoạt động</h5>
                    <span class="badge bg-primary px-2.5 py-1.5" style="border-radius: 6px; font-size: 0.75rem;"><?= count($seriesList) ?> Series</span>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($seriesList)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-slate-700">
                                    <tr>
                                        <th class="ps-4" style="width: 80px;">Mã</th>
                                        <th style="min-width: 250px;">Tên Bộ Truyện (Manga)</th>
                                        <th>Họa sĩ (Mangaka)</th>
                                        <th>Biên tập phụ trách</th>
                                        <th>Trạng thái hiện tại</th>
                                        <th>Chương đã xuất bản</th>
                                        <th class="text-end pe-4" style="width: 260px;">Số Phiếu Bình Chọn <span class="text-danger">*</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($seriesList as $series): ?>
                                        <tr>
                                            <td class="ps-4 text-slate-500 font-monospace">#<?= htmlspecialchars($series['series_id']) ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($series['cover_image'])): 
                                                        $coverUrl = $series['cover_image'];
                                                        $resolvedCover = (strpos($coverUrl, 'http') === 0) ? $coverUrl : BASE_PATH . '/' . ltrim($coverUrl, '/');
                                                    ?>
                                                        <img src="<?= htmlspecialchars($resolvedCover) ?>" alt="Cover" width="36" height="52" class="me-3 object-fit-cover rounded shadow-sm">
                                                    <?php endif; ?>
                                                    <div>
                                                        <span class="fw-bold text-slate-800 d-block"><?= htmlspecialchars($series['title']) ?></span>
                                                        <small class="text-muted text-xs"><?= htmlspecialchars(($series['publish_type'] ?? 'weekly') === 'weekly' ? 'Hàng tuần' : 'Hàng tháng') ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-slate-700 fw-medium"><?= htmlspecialchars($series['mangaka_name'] ?? 'Chưa rõ') ?></td>
                                            <td>
                                                <span class="text-slate-600"><i class="far fa-user me-1 text-muted"></i><?= htmlspecialchars($series['editor_name'] ?? 'Chưa gán') ?></span>
                                            </td>
                                            <td>
                                                <?= $this->getSeriesStatusBadge($series) ?>
                                            </td>
                                            <td>
                                                <?php if ($series['published_chapters_count'] > 0): ?>
                                                    <div class="d-flex flex-column gap-1">
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 0.78rem; font-weight: 600; width: fit-content;">
                                                            <i class="fas fa-book-open me-1"></i><?= $series['published_chapters_count'] ?> Chương
                                                        </span>
                                                        <div class="d-flex flex-column gap-1 mt-1 ps-1" style="font-size: 0.72rem; max-height: 80px; overflow-y: auto;">
                                                            <?php if (!empty($series['published_chapters'])): ?>
                                                                <?php 
                                                                $chapsCount = count($series['published_chapters']);
                                                                $displayChaps = array_slice($series['published_chapters'], -3);
                                                                foreach ($displayChaps as $chap): ?>
                                                                    <div class="text-slate-600 text-xs text-start" style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="Chương <?= htmlspecialchars($chap['chapter_number']) ?>: <?= htmlspecialchars($chap['title']) ?>">
                                                                        <i class="far fa-file-alt text-muted me-1"></i>Ch.<?= htmlspecialchars($chap['chapter_number']) ?>: <?= htmlspecialchars($chap['title']) ?>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                                <?php if ($chapsCount > 3): ?>
                                                                    <div class="text-muted text-xs text-start ps-3"><i>và <?= $chapsCount - 3 ?> chương khác...</i></div>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1" style="font-size: 0.78rem; font-weight: 600;" title="Chưa có chapter nào được xuất bản công chúng">
                                                        <i class="fas fa-exclamation-circle me-1"></i>Chưa xuất bản
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end pe-4">
                                                <?php if ($series['published_chapters_count'] > 0): ?>
                                                    <div class="input-group input-group-sm ms-auto" style="max-width: 200px;">
                                                        <input type="number" class="form-control text-end fw-bold" 
                                                               name="votes[<?= $series['series_id'] ?>]" 
                                                               min="0" 
                                                               value="0" 
                                                               required 
                                                               style="border-radius: 8px 0 0 8px; border-right: none; font-size: 0.9rem;"
                                                               placeholder="Nhập số phiếu">
                                                        <span class="input-group-text bg-light text-muted" style="border-radius: 0 8px 8px 0; font-size: 0.75rem;">Phiếu</span>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="input-group input-group-sm ms-auto" style="max-width: 200px;" title="Bộ truyện này chưa có chapter nào được xuất bản công khai nên không thể nhận phiếu bình chọn">
                                                        <input type="text" class="form-control text-end bg-light text-muted fw-bold" 
                                                               value="Khóa" 
                                                               disabled 
                                                               style="border-radius: 8px 0 0 8px; border-right: none; font-size: 0.9rem; cursor: not-allowed;">
                                                        <span class="input-group-text bg-light text-muted" style="border-radius: 0 8px 8px 0; font-size: 0.75rem; cursor: not-allowed;"><i class="fas fa-lock text-danger"></i></span>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-ban fa-2x mb-3 text-secondary" style="opacity: 0.3;"></i>
                            <p class="mb-0 text-xs">Không tìm thấy bộ truyện hoạt động nào để thực hiện đánh giá.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Nút gửi -->
            <?php if (!empty($seriesList)): ?>
                <div class="d-flex justify-content-end mb-5">
                    <button type="submit" class="btn btn-primary btn-lg shadow fw-bold px-5 py-2.5" style="border-radius: 12px; font-size: 1rem;">
                        <i class="fas fa-calculator me-2"></i>Tính Toán & Công Bố Xếp Hạng Kỳ Này
                    </button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
