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
