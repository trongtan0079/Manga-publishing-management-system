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
    <button class="btn btn-xs btn-outline-secondary d-inline-flex align-items-center gap-1" type="button" data-bs-toggle="collapse" data-bs-target="#formulaGuide" aria-expanded="false" aria-controls="formulaGuide" style="border-radius: 6px; font-size: 0.78rem; padding: 4px 10px;">
        <i class="fas fa-calculator text-primary"></i> Xem cách tính điểm & cảnh báo
    </button>
    <div class="collapse mt-2" id="formulaGuide">
        <div class="card card-body border-0 shadow-sm bg-light p-4 rounded-3">
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Hướng dẫn Quy chế Xếp hạng & Cảnh báo</h6>
            
            <div class="mb-3">
                <strong class="text-dark d-block mb-1" style="font-size: 0.88rem;">1. Công thức quy chuẩn hóa điểm số:</strong>
                <p class="text-muted text-sm mb-2">
                    Điểm quy chuẩn của từng bộ truyện được tính dựa trên tỷ lệ phiếu bầu của bộ truyện đó so với bộ truyện nhận được nhiều phiếu bầu nhất trong cùng kỳ:
                </p>
                <div class="bg-white py-2.5 px-3 rounded-2 text-center mb-3 font-monospace fw-bold text-primary shadow-sm border border-light" style="font-size: 0.95rem; width: fit-content; min-width: 320px;">
                    Điểm Quy Chuẩn = ( Số Phiếu Của Bộ Truyện / Số Phiếu Cao Nhất Trong Kỳ ) x 100
                </div>
                <div class="text-muted text-xs">
                    <ul class="mb-0 ps-3">
                        <li class="mb-1"><strong>Số phiếu của bộ truyện:</strong> Tổng số phiếu bình chọn của độc giả cho bộ truyện đó trong kỳ đánh giá.</li>
                        <li class="mb-1"><strong>Số phiếu cao nhất trong kỳ:</strong> Số phiếu lớn nhất đạt được của bộ truyện dẫn đầu trong kỳ đó.</li>
                        <li><strong>Làm tròn:</strong> Điểm số cuối cùng được làm tròn tới 2 chữ số thập phân (ví dụ: 85.71).</li>
                    </ul>
                </div>
            </div>

            <hr class="my-3">

            <div>
                <strong class="text-dark d-block mb-1" style="font-size: 0.88rem;">2. Cơ chế cảnh báo nguy cơ đình bản (Series Warning):</strong>
                <p class="text-muted text-sm mb-2">
                    Hệ thống tích hợp tính năng cảnh báo sớm tự động gửi tới Tác giả (Mangaka) khi chất lượng hoặc độ phổ biến của tác phẩm giảm sút:
                </p>
                <div class="text-muted text-xs">
                    <ul class="mb-0 ps-3">
                        <li class="mb-1"><strong>Điều kiện kích hoạt:</strong> Ngay khi Hội đồng Biên tập lưu điểm xếp hạng, nếu bộ truyện có Điểm quy chuẩn &lt; 50.00 điểm hoặc Thứ hạng &ge; #5.</li>
                        <li><strong>Hành động tự động:</strong> Hệ thống sẽ gửi một thông báo cảnh báo khẩn cấp (loại <code class="text-danger">'series_warning'</code>) hiển thị nổi bật màu đỏ trên giao diện của Mangaka sở hữu bộ truyện đó: <em>"Cảnh báo: Bộ truyện của bạn đang xếp hạng thấp. Có nguy cơ bị Hội đồng Biên tập xem xét ngưng xuất bản hoặc hủy dự án."</em></li>
                    </ul>
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
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-folder-open fs-1 mb-3 opacity-25"></i>
                                <p class="mb-0">Truyện của bạn chưa được xếp hạng.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
