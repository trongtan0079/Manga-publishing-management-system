<?php 
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../models/SeriesRanking.php';

$role = $_SESSION['role_name'];
$pageTitle = 'Xếp hạng Của Tôi';
$current_page = 'rankings';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';

$rankingModel = new SeriesRanking();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Xếp hạng Truyện Của Tôi</h2>
        <p class="text-muted text-xs mb-0">Theo dõi thứ hạng và điểm số các bộ truyện do bạn sáng tác qua từng kỳ.</p>
    </div>
</div>

<!-- Collapsible Formula Guide -->
<div class="mb-4">
    <button class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#formulaGuide" aria-expanded="false" aria-controls="formulaGuide" style="border-radius: 10px; font-weight: 600; font-size: 0.82rem; transition: all 0.2s ease;">
        <i class="fas fa-calculator"></i> Hướng dẫn Quy chế Xếp hạng & Cảnh báo
    </button>
    <div class="collapse mt-2" id="formulaGuide">
        <div class="card card-body border-0 shadow-sm p-4" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0 !important; border-radius: 16px;">
            <h5 class="fw-bold text-dark mb-4 d-flex align-items-center" style="font-size: 1.05rem; letter-spacing: -0.01em;">
                <span class="p-2 bg-primary-subtle text-primary rounded-3 me-2.5 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="fas fa-info-circle" style="font-size: 0.95rem;"></i>
                </span>
                Quy chế Xếp hạng & Cơ chế Cảnh báo
            </h5>
            
            <div class="row g-4">
                <!-- Cột 1: Công thức tính điểm -->
                <div class="col-lg-6">
                    <div class="p-3 bg-white rounded-3 border border-light shadow-sm h-100">
                        <strong class="d-block mb-2.5" style="color: #0f172a; font-size: 0.88rem; font-weight: 700;">
                            1. Công thức quy chuẩn hóa điểm số
                        </strong>
                        <p class="text-muted mb-3" style="font-size: 0.78rem; line-height: 1.5;">
                            Điểm quy chuẩn của từng bộ truyện được tính dựa trên tỷ lệ phiếu bầu của bộ truyện đó so với bộ truyện nhận được nhiều phiếu bầu nhất trong cùng kỳ:
                        </p>
                        
                        <div class="py-3 px-3 text-center mb-3 font-monospace fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); border-radius: 10px; font-size: 0.88rem; text-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                            Điểm Quy Chuẩn = ( Số Phiếu / Số Phiếu Cao Nhất ) x 100
                        </div>
                        
                        <ul class="mb-0 text-muted ps-0" style="list-style: none; font-size: 0.78rem;">
                            <li class="mb-2 d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success mt-0.5" style="font-size: 0.82rem;"></i>
                                <span><strong>Số phiếu của bộ truyện:</strong> Lượng phiếu bình chọn thực tế từ Độc giả trong kỳ.</span>
                            </li>
                            <li class="mb-2 d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success mt-0.5" style="font-size: 0.82rem;"></i>
                                <span><strong>Số phiếu cao nhất trong kỳ:</strong> Mốc chuẩn của Manga dẫn đầu kỳ đó.</span>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success mt-0.5" style="font-size: 0.82rem;"></i>
                                <span><strong>Làm tròn:</strong> Điểm số cuối cùng làm tròn tới 2 chữ số thập phân (VD: 85.71).</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Cột 2: Cơ chế cảnh báo -->
                <div class="col-lg-6">
                    <div class="p-3 bg-white rounded-3 border border-light shadow-sm h-100">
                        <strong class="d-block mb-2.5" style="color: #0f172a; font-size: 0.88rem; font-weight: 700;">
                            2. Cảnh báo nguy cơ đình bản (Series Warning)
                        </strong>
                        <p class="text-muted mb-3" style="font-size: 0.78rem; line-height: 1.5;">
                            Hệ thống tự động kích hoạt chế độ cảnh báo sớm gửi tới Tác giả khi tác phẩm có dấu hiệu giảm sút hiệu suất:
                        </p>
                        
                        <div class="p-2.5 mb-3 border rounded-3 bg-warning-subtle text-warning border-warning-subtle d-flex align-items-center gap-2" style="font-size: 0.78rem; font-weight: 600;">
                            <i class="fas fa-exclamation-triangle fs-6"></i>
                            <span>Điều kiện: Điểm quy chuẩn &lt; 50.00 điểm hoặc Thứ hạng &ge; #5</span>
                        </div>
                        
                        <ul class="mb-0 text-muted ps-0" style="list-style: none; font-size: 0.78rem;">
                            <li class="mb-2 d-flex align-items-start gap-2">
                                <i class="fas fa-bell text-warning mt-0.5" style="font-size: 0.82rem;"></i>
                                <span><strong>Hành động tự động:</strong> Gửi thông báo khẩn cấp màu đỏ hiển thị nổi bật trên màn hình của Mangaka.</span>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="fas fa-info-circle text-warning mt-0.5" style="font-size: 0.82rem;"></i>
                                <span><strong>Nội dung:</strong> <em>"Cảnh báo: Bộ truyện của bạn đang xếp hạng thấp. Có nguy cơ bị Hội đồng Biên tập xem xét ngưng xuất bản hoặc hủy dự án."</em></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Kỳ Đánh Giá</th>
                        <th>Series</th>
                        <th>Thứ hạng</th>
                        <th>Phiếu Độc Giả</th>
                        <th>Điểm Quy Chuẩn (Max 100)</th>
                        <th>Biến Động</th>
                        <th class="text-end pe-4">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rankings)): ?>
                        <?php foreach ($rankings as $ranking): ?>
                            <?php 
                                $prevRanking = $rankingModel->getPreviousRanking($ranking['series_id'], $ranking['period_start_date']);
                                $trendIcon = '<span class="text-secondary"><i class="fas fa-minus"></i> ▬ Mới</span>';
                                
                                if ($prevRanking) {
                                    if ($ranking['rank_position'] < $prevRanking['rank_position']) {
                                        // Rank số nhỏ là hạng cao hơn -> Tăng hạng
                                        $trendIcon = '<span class="text-success fw-bold"><i class="fas fa-arrow-up"></i> ▲ Tăng hạng</span>';
                                    } elseif ($ranking['rank_position'] > $prevRanking['rank_position']) {
                                        // Rank số lớn là hạng thấp hơn -> Giảm hạng
                                        $trendIcon = '<span class="text-danger fw-bold"><i class="fas fa-arrow-down"></i> ▼ Giảm hạng</span>';
                                    } else {
                                        $trendIcon = '<span class="text-secondary fw-bold"><i class="fas fa-minus"></i> ▬ Không thay đổi</span>';
                                    }
                                }
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark border"><?= htmlspecialchars(date('m/Y', strtotime($ranking['period_start_date']))) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($ranking['series_title']) ?></strong>
                                    <?php if (!empty($ranking['latest_chapter_number'])): ?>
                                        <div class="text-muted text-xs mt-1" style="font-size: 0.75rem;">
                                            <i class="fas fa-book-open me-1"></i>Chương mới nhất: <?= htmlspecialchars($ranking['latest_chapter_number']) ?><?php if (!empty($ranking['latest_chapter_title'])): ?> - <?= htmlspecialchars($ranking['latest_chapter_title']) ?><?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted text-xs mt-1" style="font-size: 0.75rem;"><i class="fas fa-book-open me-1"></i>Chưa có chương xuất bản</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary fs-5">#<?= htmlspecialchars($ranking['rank_position']) ?></div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($ranking['votes'] ?? 0) ?></strong> phiếu
                                </td>
                                <td>
                                    <span class="badge bg-success"><?= htmlspecialchars($ranking['score']) ?> / 100</span>
                                </td>
                                <td>
                                    <?= $trendIcon ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=show&id=<?= $ranking['ranking_id'] ?>" class="btn btn-sm btn-outline-primary">Xem</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5" style="background-color: #ffffff;">
                                <div class="py-4">
                                    <i class="fas fa-folder-open fs-1 mb-3 opacity-25 text-slate-400"></i>
                                    <p class="mb-0 fw-semibold text-slate-500">Truyện của bạn chưa được xếp hạng.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
