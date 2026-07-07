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

<style>
.series-title-link {
    color: var(--slate-800, #1e293b);
    transition: color 0.15s ease-in-out;
}
.series-title-link:hover {
    color: var(--primary, #6366f1) !important;
}
.badge-warning-custom {
    background-color: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}
.badge-danger-custom {
    background-color: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fca5a5;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1 text-dark fw-bold"><i class="fas fa-paste me-2 text-primary"></i>Hội Đồng Thẩm Định & Phê Duyệt</h2>
        <p class="text-muted text-xs mb-0">Hội đồng quyết định thông qua series mới, theo dõi sát sao điểm số của độc giả để xem xét hủy hoặc điều chỉnh.</p>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px;">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px;">
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- BẢNG 1: ĐỀ XUẤT TRUYỆN MỚI (BỎ PHIẾU THÔNG QUA) -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3 border-bottom border-light">
        <h5 class="card-title mb-0 fw-bold text-primary"><i class="fas fa-file-signature me-2"></i>Đề xuất bộ truyện mới (Chờ Bỏ Phiếu Thông Qua)</h5>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($pendingSeries)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom border-slate-100">
                        <tr>
                            <th class="ps-4 py-3 text-slate-500 fw-bold text-uppercase" style="width: 80px; font-size: 0.72rem; letter-spacing: 0.5px;">ID</th>
                            <th class="py-3 text-slate-500 fw-bold text-uppercase" style="min-width: 250px; font-size: 0.72rem; letter-spacing: 0.5px;">Tên Truyện</th>
                            <th class="py-3 text-slate-500 fw-bold text-uppercase" style="min-width: 140px; font-size: 0.72rem; letter-spacing: 0.5px;">Tác giả</th>
                            <th class="py-3 text-slate-500 fw-bold text-uppercase" style="min-width: 260px; font-size: 0.72rem; letter-spacing: 0.5px;">Thảo luận Hội đồng</th>
                            <th class="py-3 text-slate-500 fw-bold text-uppercase" style="min-width: 120px; font-size: 0.72rem; letter-spacing: 0.5px;">Ngày tạo</th>
                            <th class="py-3 text-slate-500 fw-bold text-uppercase text-end pe-4" style="min-width: 380px; font-size: 0.72rem; letter-spacing: 0.5px;">Bỏ phiếu & Lịch phát hành</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingSeries as $series): ?>
                            <tr>
                                <td class="ps-4 text-slate-400 font-monospace" style="font-size: 0.85rem;">#<?= htmlspecialchars($series['series_id']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($series['cover_image'])): 
                                            $coverUrl = $series['cover_image'];
                                            $resolvedCover = (strpos($coverUrl, 'http') === 0) ? $coverUrl : BASE_PATH . '/' . ltrim($coverUrl, '/');
                                        ?>
                                            <img src="<?= htmlspecialchars($resolvedCover) ?>" alt="Cover" width="40" height="56" class="me-3 object-fit-cover rounded shadow" style="border: 1.5px solid #fff; outline: 1px solid #e2e8f0;">
                                        <?php endif; ?>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1" style="white-space: nowrap;">
                                                <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= $series['series_id'] ?>" class="fw-bold text-slate-800 text-decoration-none series-title-link" style="font-size: 0.95rem; transition: color 0.15s ease-in-out;" title="Xem chi tiết bộ truyện"><?= htmlspecialchars($series['title']) ?></a>
                                                <?php if (!empty($series['proposal_file'])): ?>
                                                    <a href="<?= BASE_PATH . htmlspecialchars($series['proposal_file']) ?>" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 text-decoration-none d-inline-flex align-items-center gap-1" target="_blank" style="font-size: 0.7rem; font-weight: 600;" title="Tải tài liệu đề xuất / bản thảo sơ bộ">
                                                        <i class="fas fa-file-pdf"></i>Bản thảo sơ bộ
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-slate-800 fw-semibold" style="font-size: 0.88rem; white-space: nowrap;"><?= htmlspecialchars($series['mangaka_name'] ?? 'Không rõ') ?></div>
                                    <span class="text-muted text-xs">Tác giả</span>
                                 </td>
                                <td>
                                    <?php 
                                    $stats = $series['approval_stats'] ?? ['percentage' => 0, 'approve_count' => 0, 'reject_count' => 0, 'total_members' => 1];
                                    $myVote = $series['my_vote'] ?? null;
                                    $percent = $stats['percentage'];
                                    $approve = $stats['approve_count'];
                                    $reject = $stats['reject_count'] ?? 0;
                                    $total = $stats['total_members'];
                                    $totalVotes = $approve + $reject;
                                    $isVotingComplete = ($totalVotes === $total);
                                    
                                    $textColor = 'text-success';
                                    if ($percent < 50) {
                                        $textColor = 'text-warning';
                                    }
                                    ?>
                                    <div class="d-flex flex-column gap-2" style="width: 220px; max-width: 220px;">
                                        <!-- Hàng 1: Tiến trình & Con số -->
                                        <div class="d-flex align-items-center justify-content-between" style="white-space: nowrap;">
                                            <span class="text-slate-500 fw-semibold text-xs"><i class="fas fa-users me-1 text-slate-400"></i>Hội đồng tán thành:</span>
                                            <span class="fw-bold <?= $textColor ?>" style="font-size: 0.88rem;"><?= $percent ?>% <small class="text-muted text-xs fw-normal">(<?= $approve ?>/<?= $total ?>)</small></span>
                                        </div>
                                        <!-- Hàng 2: Thanh progress phân tầng (Stacked Progress Bar) -->
                                        <div class="progress shadow-inner" style="height: 8px; background-color: #e2e8f0; border-radius: 999px; overflow: hidden;" title="Đồng ý: <?= $approve ?> - Từ chối: <?= $reject ?>">
                                            <?php if ($approve > 0): ?>
                                                <div class="progress-bar bg-success" style="width: <?= round(($approve / $total) * 100) ?>%; transition: width 0.4s ease;"></div>
                                            <?php endif; ?>
                                            <?php if ($reject > 0): ?>
                                                <div class="progress-bar bg-danger" style="width: <?= round(($reject / $total) * 100) ?>%; transition: width 0.4s ease;"></div>
                                            <?php endif; ?>
                                        </div>
                                        <!-- Hàng 3: Cụm nút bấm bỏ phiếu đẹp mắt -->
                                        <div class="d-flex gap-2 mt-1" style="white-space: nowrap;">
                                            <form action="<?= BASE_PATH ?>/index.php?controller=series&action=vote&id=<?= $series['series_id'] ?>" method="POST" class="m-0">
                                                <input type="hidden" name="vote" value="approve">
                                                <button type="submit" class="btn btn-sm py-1 px-3 rounded-pill d-inline-flex align-items-center gap-1.5 fw-semibold <?= $myVote === 'approve' ? 'btn-success text-white border-success' : 'btn-outline-success border-success-subtle bg-white text-success' ?>" style="font-size: 0.72rem; letter-spacing: 0.2px; transition: all 0.2s; white-space: nowrap;" title="Bỏ phiếu Đồng ý" <?= $myVote === 'approve' ? 'disabled' : '' ?>>
                                                    <i class="fas fa-thumbs-up"></i>Đồng ý
                                                </button>
                                            </form>
                                            <form action="<?= BASE_PATH ?>/index.php?controller=series&action=vote&id=<?= $series['series_id'] ?>" method="POST" class="m-0">
                                                <input type="hidden" name="vote" value="reject">
                                                <button type="submit" class="btn btn-sm py-1 px-3 rounded-pill d-inline-flex align-items-center gap-1.5 fw-semibold <?= $myVote === 'reject' ? 'btn-danger text-white border-danger' : 'btn-outline-danger border-danger-subtle bg-white text-danger' ?>" style="font-size: 0.72rem; letter-spacing: 0.2px; transition: all 0.2s; white-space: nowrap;" title="Bỏ phiếu Từ chối" <?= $myVote === 'reject' ? 'disabled' : '' ?>>
                                                    <i class="fas fa-thumbs-down"></i>Từ chối
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-slate-600 font-monospace text-xs" style="font-weight: 500; letter-spacing: 0.3px; white-space: nowrap;">
                                        <i class="far fa-calendar-alt text-slate-400 me-1.5"></i><?= htmlspecialchars(date('d/m/Y', strtotime($series['created_at']))) ?>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if (!$isVotingComplete): ?>
                                         <div class="d-inline-flex align-items-center gap-2 py-1.5 px-3 border rounded-pill shadow-sm" style="background-color: #fffbeb; border-color: #fde68a; color: #b45309; font-size: 0.75rem; font-weight: 600; white-space: nowrap;" title="Cần có đủ <?= $total ?> phiếu bầu từ các thành viên Hội đồng để mở khóa quyết định.">
                                             <span class="spinner-grow spinner-grow-sm text-warning" role="status" style="width: 8px; height: 8px;"></span>
                                             <span>Chờ biểu quyết (<?= $totalVotes ?>/<?= $total ?> phiếu)</span>
                                         </div>
                                    <?php else: ?>
                                         <form action="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=series&action=updateStatus&id=<?= $series['series_id'] ?>" method="POST" class="d-flex justify-content-end align-items-center gap-2" onsubmit="return confirm('Xác nhận bỏ phiếu và lưu quyết định phê duyệt bộ truyện này?');">
                                             <select name="status" class="form-select form-select-sm border-slate-200 shadow-sm" style="width: 140px; border-radius: 20px; font-size: 0.8rem; font-weight: 500;" title="Quyết định">
                                                 <option value="planning" selected>Chờ quyết định</option>
                                                 <option value="ongoing" <?= ($percent < 50) ? 'disabled title="Chưa đạt tỉ lệ tán thành tối thiểu (>= 50%)"' : '' ?>>Thông qua (Phê duyệt)</option>
                                                 <option value="canceled">Từ chối (Hủy dự án)</option>
                                             </select>
                                             <select name="editor_id" class="form-select form-select-sm border-slate-200 shadow-sm" style="width: 150px; border-radius: 20px; font-size: 0.8rem; font-weight: 500;" title="Biên tập viên chuyên trách">
                                                  <option value="">-- Editor phụ trách --</option>
                                                  <?php if (!empty($editors)): ?>
                                                      <?php foreach ($editors as $ed): ?>
                                                          <option value="<?= $ed['user_id'] ?>" <?= $series['editor_id'] == $ed['user_id'] ? 'selected' : '' ?>>
                                                              <?= htmlspecialchars($ed['full_name']) ?>
                                                          </option>
                                                      <?php endforeach; ?>
                                                  <?php endif; ?>
                                              </select>
                                             <select name="publish_type" class="form-select form-select-sm border-slate-200 shadow-sm" style="width: 110px; border-radius: 20px; font-size: 0.8rem; font-weight: 500;" title="Lịch xuất bản">
                                                 <option value="weekly">Hàng tuần</option>
                                                 <option value="monthly">Hàng tháng</option>
                                             </select>
                                             <button type="submit" class="btn btn-sm btn-primary px-3 rounded-pill shadow-sm d-flex align-items-center gap-1.5 fw-semibold" style="font-size: 0.8rem;" title="Lưu quyết định">
                                                 <i class="fas fa-check-circle"></i> Ghi nhận
                                             </button>
                                         </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <div class="mb-2"><i class="fas fa-check-circle fa-2x text-success" style="opacity: 0.4;"></i></div>
                <p class="mb-0 text-xs text-muted fw-medium">Không có đề xuất bộ truyện mới nào chờ bỏ phiếu.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- BẢNG 2: CÁC BỘ TRUYỆN ĐANG HOẠT ĐỘNG (THEO DÕI XẾP HẠNG & QUYẾT ĐỊNH HỦY/DỪNG) -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3 border-bottom border-light">
        <h5 class="card-title mb-0 fw-bold text-success"><i class="fas fa-book-open me-2"></i>Bộ truyện đang phát hành (Giám sát Xếp hạng & Quyết định đình bản)</h5>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($activeSeries)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 80px;">ID</th>
                            <th style="min-width: 200px;">Tên Truyện</th>
                            <th>Tác giả</th>
                            <th>Xếp hạng & Điểm</th>
                            <th>Trạng thái</th>
                            <th>Tiến độ Chapter</th>
                            <th>Hồ sơ bảo vệ</th>
                            <th class="text-end pe-4" style="min-width: 320px;">Hành động điều chỉnh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activeSeries as $series): ?>
                            <tr>
                                <td class="ps-4 text-slate-500 font-monospace">#<?= htmlspecialchars($series['series_id']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($series['cover_image'])): 
                                            $coverUrl = $series['cover_image'];
                                            $resolvedCover = (strpos($coverUrl, 'http') === 0) ? $coverUrl : BASE_PATH . '/' . ltrim($coverUrl, '/');
                                        ?>
                                            <img src="<?= htmlspecialchars($resolvedCover) ?>" alt="Cover" width="36" height="52" class="me-2 object-fit-cover rounded flex-shrink-0 shadow-sm">
                                        <?php endif; ?>
                                        <div>
                                            <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= $series['series_id'] ?>" class="fw-bold text-decoration-none series-title-link" title="Xem chi tiết bộ truyện"><?= htmlspecialchars($series['title']) ?></a>
                                            <small class="text-muted d-block text-xs"><?= htmlspecialchars(($series['publish_type'] ?? 'weekly') === 'weekly' ? 'Hàng tuần' : 'Hàng tháng') ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                     <?= htmlspecialchars($series['mangaka_name'] ?? 'Không rõ') ?>
                                     <br>
                                     <small class="text-slate-500 text-xs">Editor: <?= htmlspecialchars($series['editor_name'] ?? 'Chưa gán') ?></small>
                                 </td>
                                <td>
                                    <?php if (isset($series['latest_rank']) && $series['latest_rank'] > 0): ?>
                                        <div class="d-flex flex-column gap-1">
                                            <div>
                                                <span class="badge bg-dark">Hạng #<?= htmlspecialchars($series['latest_rank']) ?></span>
                                                <span class="badge bg-primary"><?= htmlspecialchars($series['latest_score']) ?> điểm</span>
                                            </div>
                                            <!-- Cảnh báo nếu hạng thấp (từ hạng 5 trở đi) hoặc điểm số quá thấp (< 50) -->
                                            <?php if ($series['latest_rank'] >= 5 || $series['latest_score'] < 50): ?>
                                                <span class="badge badge-danger-custom text-xs" style="max-width: fit-content;">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Có nguy cơ bị hủy
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle text-xs" style="max-width: fit-content;">An toàn</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted text-xs">Chưa có xếp hạng</span>
                                    <?php endif; ?>
                                </td>
                                 <td>
                                     <?php
                                     $badgeClass = 'bg-secondary';
                                     $statusLabel = $series['status'];
                                     switch ($series['status']) {
                                         case 'ongoing': $badgeClass = 'bg-primary'; $statusLabel = 'Đang phát hành'; break;
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
                                           </div>
                                       <?php else: ?>
                                           <span class="text-muted text-xs">Chưa có Chapter</span>
                                       <?php endif; ?>
                                  </td>
                                  <td>
                                      <?php if (!empty($series['dossier_notes'])): ?>
                                          <button type="button" class="btn btn-xs btn-outline-danger fw-bold" data-bs-toggle="modal" data-bs-target="#dossierModal<?= $series['series_id'] ?>" style="font-size: 0.72rem; border-radius: 6px;">
                                              <i class="fas fa-shield-alt me-1"></i>Có Biện Hộ
                                          </button>
                                      <?php else: ?>
                                          <span class="text-muted text-xs">Không có</span>
                                      <?php endif; ?>
                                  </td>
                                  <td class="text-end pe-4">
                                     <form action="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=series&action=updateStatus&id=<?= $series['series_id'] ?>" method="POST" class="d-flex justify-content-end align-items-center gap-2" onsubmit="return confirm('Xác nhận thay đổi trạng thái và định hướng phát hành cho bộ truyện này?');">
                                         <select name="status" class="form-select form-select-sm w-auto" style="max-width: 130px; border-radius: 6px;" title="Trạng thái">
                                             <option value="ongoing" <?= $series['status'] == 'ongoing' ? 'selected' : '' ?>>Đang phát hành</option>
                                             <option value="completed" <?= $series['status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                             <option value="canceled" <?= $series['status'] == 'canceled' ? 'selected' : '' ?>>Đình bản (Hủy)</option>
                                             <option value="suspended" <?= $series['status'] == 'suspended' ? 'selected' : '' ?>>Tạm ngưng</option>
                                         </select>
                                         <select name="editor_id" class="form-select form-select-sm w-auto" style="max-width: 130px; border-radius: 6px;" title="Biên tập viên chuyên trách">
                                             <option value="">-- Editor phụ trách --</option>
                                             <?php if (!empty($editors)): ?>
                                                 <?php foreach ($editors as $ed): ?>
                                                     <option value="<?= $ed['user_id'] ?>" <?= $series['editor_id'] == $ed['user_id'] ? 'selected' : '' ?>>
                                                         <?= htmlspecialchars($ed['full_name']) ?>
                                                     </option>
                                                 <?php endforeach; ?>
                                             <?php endif; ?>
                                         </select>
                                         <select name="publish_type" class="form-select form-select-sm w-auto" style="max-width: 110px; border-radius: 6px;" title="Lịch xuất bản">
                                             <option value="weekly" <?= ($series['publish_type'] ?? 'weekly') == 'weekly' ? 'selected' : '' ?>>Hàng tuần</option>
                                             <option value="monthly" <?= ($series['publish_type'] ?? 'weekly') == 'monthly' ? 'selected' : '' ?>>Hàng tháng</option>
                                         </select>
                                         <button type="submit" class="btn btn-sm btn-primary" style="border-radius: 6px;" title="Cập nhật">
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
                <div class="mb-2"><i class="fas fa-folder-open fa-2x" style="opacity: 0.4;"></i></div>
                <p class="mb-0 text-xs text-muted">Không tìm thấy bộ truyện hoạt động nào.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ĐĂNG KÝ MODAL XEM HỒ SƠ BIỆN HỘ CỦA EDITOR CHO TỪNG SERIES -->
<?php if (!empty($activeSeries)): ?>
    <?php foreach ($activeSeries as $series): ?>
        <?php if (!empty($series['dossier_notes'])): ?>
            <div class="modal fade" id="dossierModal<?= $series['series_id'] ?>" tabindex="-1" aria-labelledby="dossierModalLabel<?= $series['series_id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: var(--shadow-lg);">
                        <div class="modal-header bg-danger text-white py-3">
                            <h5 class="modal-title fw-bold" id="dossierModalLabel<?= $series['series_id'] ?>"><i class="fas fa-shield-alt me-2"></i>Biện Hộ Bảo Vệ Series: <?= htmlspecialchars($series['title']) ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 bg-light">
                            <div class="p-3 bg-white border rounded-3 shadow-sm mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong class="text-slate-800" style="font-size: 0.85rem;"><i class="fas fa-user-shield text-danger me-1"></i>Ý kiến giải trình của Editor chuyên trách:</strong>
                                    <span class="badge bg-info text-xs"><?= htmlspecialchars($series['editor_name'] ?? 'Editor') ?></span>
                                </div>
                                <p class="text-slate-700 mb-0" style="font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap;"><?= htmlspecialchars($series['dossier_notes']) ?></p>
                            </div>
                            <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 0.78rem; border-radius: 8px;">
                                <i class="fas fa-info-circle me-1"></i>**Lưu ý:** Vui lòng xem xét cẩn trọng giải trình này của Biên tập viên trước khi đưa ra quyết định đình bản (hủy) hay chuyển đổi hình thức phát hành của series.
                            </div>
                        </div>
                        <div class="modal-footer py-2 bg-white">
                            <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal" style="border-radius: 6px;">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const statusSelects = document.querySelectorAll("select[name='status']");
    statusSelects.forEach(select => {
        const form = select.closest("form");
        const publishTypeSelect = form.querySelector("select[name='publish_type']");
        const editorSelect = form.querySelector("select[name='editor_id']");
        const submitBtn = form.querySelector("button[type='submit']");
        
        function updateFormState() {
            const val = select.value;
            
            // 1. Quản lý trạng thái các ô chọn phụ thuộc
            if (val === 'ongoing') {
                if (publishTypeSelect) {
                    publishTypeSelect.disabled = false;
                    publishTypeSelect.style.opacity = '1';
                    publishTypeSelect.style.cursor = 'default';
                }
                if (editorSelect) {
                    editorSelect.disabled = false;
                    editorSelect.style.opacity = '1';
                    editorSelect.style.cursor = 'default';
                }
            } else {
                if (publishTypeSelect) {
                    publishTypeSelect.disabled = true;
                    publishTypeSelect.style.opacity = '0.5';
                    publishTypeSelect.style.cursor = 'not-allowed';
                }
                if (editorSelect) {
                    editorSelect.disabled = true;
                    editorSelect.style.opacity = '0.5';
                    editorSelect.style.cursor = 'not-allowed';
                }
            }
            
            // 2. Chặn nút Ghi nhận nếu đang ở trạng thái Chờ quyết định (planning)
            if (submitBtn) {
                if (val === 'planning') {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.5';
                    submitBtn.style.cursor = 'not-allowed';
                } else {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'default';
                }
            }
        }
        
        updateFormState();
        select.addEventListener("change", updateFormState);
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
