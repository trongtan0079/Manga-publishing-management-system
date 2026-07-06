<?php
/**
 * View: Giao diện duyệt và xuất bản các bộ truyện (publish_series.php)
 * Vai trò: Board (Hội đồng/Ban giám đốc)
 * Chức năng: Cho phép Hội đồng xem xét và duyệt các bộ truyện (Series) để đưa vào xuất bản hoặc đổi trạng thái hoạt động.
 * 
 * @var array $seriesList Danh sách các bộ truyện cần duyệt hoặc thay đổi trạng thái
 */
$pageTitle = 'Duyệt Series (Publish Series)';
$current_page = 'publish_series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';

// Phân lọc danh sách truyện theo nhóm trạng thái
$pendingSeries = [];
$activeSeries = [];
if (!empty($seriesList)) {
    foreach ($seriesList as $series) {
        if ($series['status'] === 'planning') {
            $pendingSeries[] = $series;
        } else {
            $activeSeries[] = $series;
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1 text-dark fw-bold">Duyệt & Quản lý Series</h2>
        <p class="text-muted text-xs mb-0">Hội đồng xem xét duyệt các Series mới và quản lý trạng thái các bộ truyện đang hoạt động.</p>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- BẢNG 1: ĐỀ XUẤT TRUYỆN MỚI (CHỜ DUYỆT) -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3 border-bottom border-light">
        <h5 class="card-title mb-0 fw-bold text-primary"><i class="fas fa-file-signature me-2"></i>Đề xuất bộ truyện mới (Chờ phê duyệt)</h5>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($pendingSeries)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 80px;">ID</th>
                            <th style="min-width: 280px;">Tên Truyện</th>
                            <th>Tác giả</th>
                            <th>Trạng thái hiện tại</th>
                            <th>Ngày tạo</th>
                            <th class="text-end pe-4" style="min-width: 340px;">Phê duyệt & Lịch phát hành</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingSeries as $series): ?>
                            <tr>
                                <td class="ps-4"><?= htmlspecialchars($series['series_id']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($series['cover_image'])): 
                                            $coverUrl = $series['cover_image'];
                                            $resolvedCover = (strpos($coverUrl, 'http') === 0) ? $coverUrl : BASE_PATH . '/' . ltrim($coverUrl, '/');
                                        ?>
                                            <img src="<?= htmlspecialchars($resolvedCover) ?>" alt="Cover" width="40" height="60" class="me-2 object-fit-cover rounded flex-shrink-0">
                                        <?php endif; ?>
                                        <span class="fw-bold text-slate-800"><?= htmlspecialchars($series['title']) ?></span>
                                    </div>
                                </td>
                                <td>
                                     <?= htmlspecialchars($series['mangaka_name'] ?? 'Không rõ') ?>
                                     <br>
                                     <small class="text-info" style="font-size: 0.72rem; font-weight: 600;">Editor: <?= htmlspecialchars($series['editor_name'] ?? 'Chưa gán') ?></small>
                                 </td>
                                <td>
                                    <span class="badge bg-info text-dark">Kế hoạch (Chờ duyệt)</span>
                                </td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($series['created_at']))) ?></td>
                                <td class="text-end pe-4" style="min-width: 340px;">
                                    <form action="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=series&action=updateStatus&id=<?= $series['series_id'] ?>" method="POST" class="d-flex justify-content-end align-items-center gap-2" onsubmit="return confirm('Bạn có chắc chắn muốn phê duyệt quyết định này cho bộ truyện?');">
                                        <select name="status" class="form-select form-select-sm w-auto" style="max-width: 140px;" title="Trạng thái">
                                            <option value="planning" selected>Kế hoạch</option>
                                            <option value="ongoing">Đang triển khai</option>
                                            <option value="canceled">Đã hủy</option>
                                        </select>
                                        <select name="editor_id" class="form-select form-select-sm w-auto" style="max-width: 150px;" title="Biên tập viên chuyên trách">
                                             <option value="">-- Chọn Editor --</option>
                                             <?php if (!empty($editors)): ?>
                                                 <?php foreach ($editors as $ed): ?>
                                                     <option value="<?= $ed['user_id'] ?>" <?= $series['editor_id'] == $ed['user_id'] ? 'selected' : '' ?>>
                                                         <?= htmlspecialchars($ed['full_name']) ?>
                                                     </option>
                                                 <?php endforeach; ?>
                                             <?php endif; ?>
                                         </select>
                                        <select name="publish_type" class="form-select form-select-sm w-auto" style="max-width: 110px;" title="Lịch xuất bản">
                                            <option value="weekly">Hàng tuần</option>
                                            <option value="monthly">Hàng tháng</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary" title="Lưu quyết định">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <div class="mb-2"><i class="fas fa-check-circle fa-2x text-success"></i></div>
                <p class="mb-0 text-xs text-muted fw-medium">Hiện tại không có đề xuất bộ truyện mới nào cần phê duyệt.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- BẢNG 2: CÁC BỘ TRUYỆN ĐANG HOẠT ĐỘNG (GIÁM SÁT & QUẢN LÝ TRẠNG THÁI) -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3 border-bottom border-light">
        <h5 class="card-title mb-0 fw-bold text-success"><i class="fas fa-book-open me-2"></i>Bộ truyện đang hoạt động (Giám sát & Quản lý)</h5>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($activeSeries)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 80px;">ID</th>
                            <th style="min-width: 280px;">Tên Truyện</th>
                            <th>Tác giả</th>
                            <th>Lịch xuất bản</th>
                            <th>Trạng thái</th>
                            <th>Tiến độ Chapter</th>
                            <th>Ngày tạo</th>
                            <th class="text-end pe-4" style="min-width: 340px;">Cập nhật</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activeSeries as $series): ?>
                            <tr>
                                <td class="ps-4"><?= htmlspecialchars($series['series_id']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($series['cover_image'])): 
                                            $coverUrl = $series['cover_image'];
                                            $resolvedCover = (strpos($coverUrl, 'http') === 0) ? $coverUrl : BASE_PATH . '/' . ltrim($coverUrl, '/');
                                        ?>
                                            <img src="<?= htmlspecialchars($resolvedCover) ?>" alt="Cover" width="40" height="60" class="me-2 object-fit-cover rounded flex-shrink-0">
                                        <?php endif; ?>
                                        <span class="fw-bold text-slate-800"><?= htmlspecialchars($series['title']) ?></span>
                                    </div>
                                </td>
                                 <td>
                                     <?= htmlspecialchars($series['mangaka_name'] ?? 'Không rõ') ?>
                                     <br>
                                     <small class="text-info" style="font-size: 0.72rem; font-weight: 600;">Editor: <?= htmlspecialchars($series['editor_name'] ?? 'Chưa gán') ?></small>
                                 </td>
                                 <td>
                                     <span class="badge bg-secondary"><?= htmlspecialchars(($series['publish_type'] ?? 'weekly') === 'weekly' ? 'Hàng tuần' : 'Hàng tháng') ?></span>
                                 </td>
                                 <td>
                                     <?php
                                     $badgeClass = 'bg-secondary';
                                     $statusLabel = $series['status'];
                                     switch ($series['status']) {
                                         case 'ongoing': $badgeClass = 'bg-primary'; $statusLabel = 'Đang triển khai'; break;
                                         case 'completed': $badgeClass = 'bg-success'; $statusLabel = 'Hoàn thành'; break;
                                         case 'canceled': $badgeClass = 'bg-danger'; $statusLabel = 'Đã hủy'; break;
                                         case 'suspended': $badgeClass = 'bg-warning text-dark'; $statusLabel = 'Tạm ngưng'; break;
                                     }
                                     ?>
                                     <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
                                 </td>
                                 <td>
                                      <?php if ($series['total_chapters'] > 0): ?>
                                          <?php 
                                          $chapterPercent = round(($series['finished_chapters'] / $series['total_chapters']) * 100);
                                          ?>
                                          <div class="d-flex align-items-center" style="gap: 10px;">
                                              <div class="flex-grow-1" style="min-width: 90px; max-width: 120px;">
                                                  <div class="progress" style="height: 6px; background-color: #e2e8f0; border-radius: 3px;" title="<?= $series['finished_chapters'] ?>/<?= $series['total_chapters'] ?> chương">
                                                      <div class="progress-bar bg-success" role="progressbar" style="width: <?= $chapterPercent ?>%; border-radius: 3px;" aria-valuenow="<?= $chapterPercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                  </div>
                                                  <small class="text-muted" style="font-size: 0.7rem; font-weight: 500;"><?= $series['finished_chapters'] ?>/<?= $series['total_chapters'] ?> chương (<?= $chapterPercent ?>%)</small>
                                              </div>
                                              <?php if (!empty($series['has_final_approved'])): ?>
                                                  <span class="badge bg-success text-xs">Hoàn tất</span>
                                              <?php else: ?>
                                                  <span class="badge bg-warning text-dark text-xs">Đang làm</span>
                                              <?php endif; ?>
                                          </div>
                                      <?php else: ?>
                                          <span class="text-muted text-xs">Chưa có Chapter</span>
                                      <?php endif; ?>
                                  </td>
                                 <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($series['created_at']))) ?></td>
                                 <td class="text-end pe-4" style="min-width: 340px;">
                                     <form action="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=series&action=updateStatus&id=<?= $series['series_id'] ?>" method="POST" class="d-flex justify-content-end align-items-center gap-2" onsubmit="return confirm('Xác nhận cập nhật trạng thái hoạt động và lịch phát hành cho bộ truyện này?');">
                                         <select name="status" class="form-select form-select-sm w-auto" style="max-width: 140px;" title="Trạng thái">
                                             <option value="ongoing" <?= $series['status'] == 'ongoing' ? 'selected' : '' ?>>Đang triển khai</option>
                                             <option value="completed" <?= $series['status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                             <option value="canceled" <?= $series['status'] == 'canceled' ? 'selected' : '' ?>>Đã hủy</option>
                                             <option value="suspended" <?= $series['status'] == 'suspended' ? 'selected' : '' ?>>Tạm ngưng</option>
                                         </select>
                                         <select name="editor_id" class="form-select form-select-sm w-auto" style="max-width: 150px;" title="Biên tập viên chuyên trách">
                                             <option value="">-- Chọn Editor --</option>
                                             <?php if (!empty($editors)): ?>
                                                 <?php foreach ($editors as $ed): ?>
                                                     <option value="<?= $ed['user_id'] ?>" <?= $series['editor_id'] == $ed['user_id'] ? 'selected' : '' ?>>
                                                         <?= htmlspecialchars($ed['full_name']) ?>
                                                     </option>
                                                 <?php endforeach; ?>
                                             <?php endif; ?>
                                         </select>
                                         <select name="publish_type" class="form-select form-select-sm w-auto" style="max-width: 110px;" title="Lịch xuất bản">
                                             <option value="weekly" <?= ($series['publish_type'] ?? 'weekly') == 'weekly' ? 'selected' : '' ?>>Hàng tuần</option>
                                             <option value="monthly" <?= ($series['publish_type'] ?? 'weekly') == 'monthly' ? 'selected' : '' ?>>Hàng tháng</option>
                                         </select>
                                         <button type="submit" class="btn btn-sm btn-primary" title="Cập nhật">
                                             <i class="fas fa-save"></i>
                                         </button>
                                     </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <div class="mb-2"><i class="fas fa-folder-open fa-2x"></i></div>
                <p class="mb-0 text-xs text-muted">Hiện tại không có bộ truyện nào đang phát hành hoạt động.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const statusSelects = document.querySelectorAll("select[name='status']");
    statusSelects.forEach(select => {
        const form = select.closest("form");
        const publishTypeSelect = form.querySelector("select[name='publish_type']");
        if (!publishTypeSelect) return; // Bảng 2 sử dụng hidden input, không có select publish_type
        
        function updatePublishTypeState() {
            if (select.value === 'ongoing') {
                publishTypeSelect.disabled = false;
                publishTypeSelect.style.opacity = '1';
                publishTypeSelect.style.cursor = 'default';
            } else {
                publishTypeSelect.disabled = true;
                publishTypeSelect.style.opacity = '0.5';
                publishTypeSelect.style.cursor = 'not-allowed';
            }
        }
        
        // Chạy lần đầu khi load trang
        updatePublishTypeState();
        
        // Lắng nghe sự thay đổi
        select.addEventListener("change", updatePublishTypeState);
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
