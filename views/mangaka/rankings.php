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

<style>
    /* ── Premium Rankings Page Styles ── */
    .rankings-header-banner {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
        border-radius: 20px;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 8px 32px -8px rgba(99, 102, 241, 0.35);
    }
    .rankings-header-banner::before {
        content: '';
        position: absolute;
        width: 220px; height: 220px;
        background: rgba(99, 102, 241, 0.18);
        filter: blur(60px);
        border-radius: 50%;
        top: -80px; right: -40px;
    }
    .rankings-header-banner::after {
        content: '';
        position: absolute;
        width: 150px; height: 150px;
        background: rgba(236, 72, 153, 0.12);
        filter: blur(50px);
        border-radius: 50%;
        bottom: -40px; left: 8%;
    }

    /* Guide Toggle Button */
    .btn-guide-toggle {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 18px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #4338ca;
        transition: all 0.25s ease;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.08);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-guide-toggle:hover {
        background: #f5f3ff;
        border-color: #6366f1;
        color: #4338ca;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.18);
        transform: translateY(-1px);
    }

    /* Formula Guide Card */
    .formula-guide-card {
        background: #ffffff;
        border: 1px solid #e2e8f0 !important;
        border-radius: 20px;
        padding: 28px;
        margin-top: 12px;
        box-shadow: 0 4px 24px -4px rgba(15, 23, 42, 0.06);
    }
    .formula-guide-title {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }
    .formula-guide-icon {
        width: 38px; height: 38px;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: white;
        font-size: 0.9rem;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
    }

    /* Inner Panel Cards */
    .guide-panel {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        height: 100%;
        position: relative;
        overflow: hidden;
        transition: box-shadow 0.25s ease;
    }
    .guide-panel:hover {
        box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.07);
    }
    .guide-panel-title {
        font-size: 0.85rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
        padding-left: 12px;
        position: relative;
    }
    .guide-panel-title::before {
        content: '';
        position: absolute;
        left: 0; top: 2px; bottom: 2px;
        width: 4px;
        border-radius: 4px;
    }
    .guide-panel-formula .guide-panel-title::before { background: #6366f1; }
    .guide-panel-warning .guide-panel-title::before { background: #f59e0b; }

    /* Formula Box */
    .formula-box {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        border-radius: 12px;
        padding: 14px 18px;
        color: white;
        font-family: 'Courier New', monospace;
        font-weight: 800;
        font-size: 0.85rem;
        text-align: center;
        margin: 14px 0;
        letter-spacing: 0.02em;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.35);
        position: relative;
        overflow: hidden;
    }
    .formula-box::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, transparent 60%);
    }

    /* Warning Condition Box */
    .warning-condition-box {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 1.5px solid #fbbf24;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #92400e;
        margin: 14px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Guide list items */
    .guide-list-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 0.78rem;
        color: #475569;
        margin-bottom: 10px;
        line-height: 1.55;
    }
    .guide-list-item:last-child { margin-bottom: 0; }
    .guide-list-icon {
        width: 20px; height: 20px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 0.62rem;
        margin-top: 1px;
    }
    .guide-list-icon.success { background: rgba(16, 185, 129, 0.12); color: #10b981; }
    .guide-list-icon.warning { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .guide-list-icon.info    { background: rgba(99, 102, 241, 0.12); color: #6366f1; }

    /* Rankings Table Card */
    .rankings-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 24px -4px rgba(15, 23, 42, 0.06);
    }
    .rankings-table-card .table {
        margin-bottom: 0;
    }
    .rankings-table-card thead th {
        background: #f8fafc;
        border-bottom: 1.5px solid #e2e8f0;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        padding: 14px 16px;
        white-space: nowrap;
    }
    .rankings-table-card tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.83rem;
        color: #1e293b;
    }
    .rankings-table-card tbody tr:last-child td { border-bottom: none; }
    .rankings-table-card tbody tr {
        transition: background 0.15s ease;
    }
    .rankings-table-card tbody tr:hover {
        background: #fafafa;
    }

    /* Period Badge */
    .period-badge {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        display: inline-block;
    }

    /* Rank Number */
    .rank-number {
        font-size: 1.3rem;
        font-weight: 900;
        color: #6366f1;
        line-height: 1;
    }
    .rank-number.rank-1 { color: #f59e0b; }
    .rank-number.rank-2 { color: #94a3b8; }
    .rank-number.rank-3 { color: #c2692f; }

    /* Score Badge */
    .score-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.06));
        border: 1px solid rgba(16, 185, 129, 0.25);
        color: #059669;
        font-weight: 800;
        font-size: 0.78rem;
        padding: 4px 10px;
        border-radius: 8px;
    }

    /* Trend Icons */
    .trend-up   { color: #10b981; font-weight: 700; font-size: 0.8rem; }
    .trend-down { color: #ef4444; font-weight: 700; font-size: 0.8rem; }
    .trend-flat { color: #94a3b8; font-weight: 600; font-size: 0.8rem; }
    .trend-new  { color: #8b5cf6; font-weight: 600; font-size: 0.8rem; }

    /* Detail Button */
    .btn-detail {
        background: #f5f3ff;
        border: 1px solid #ddd6fe;
        color: #4338ca;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 5px 14px;
        border-radius: 8px;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-detail:hover {
        background: #4338ca;
        border-color: #4338ca;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(67, 56, 202, 0.3);
        transform: translateY(-1px);
    }

    /* Empty State */
    .rankings-empty {
        padding: 60px 24px;
        text-align: center;
    }
    .rankings-empty-icon {
        width: 72px; height: 72px;
        background: linear-gradient(135deg, #f5f3ff, #ede9fe);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem;
        color: #8b5cf6;
        margin: 0 auto 16px;
        box-shadow: 0 0 0 8px rgba(139, 92, 246, 0.06);
    }
</style>

<!-- ── Premium Header Banner ── -->
<div class="rankings-header-banner">
    <div class="position-relative z-1">
        <h2 class="h4 fw-extrabold text-white mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-trophy"></i>
            Xếp hạng Truyện Của Tôi
        </h2>
        <p class="mb-0 text-white fw-semibold text-xs" style="opacity: 0.72;">
            Theo dõi thứ hạng và điểm số các bộ truyện do bạn sáng tác qua từng kỳ đánh giá.
        </p>
    </div>
</div>

<!-- ── Formula Guide Toggle ── -->
<div class="mb-4">
    <button class="btn-guide-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#formulaGuide" aria-expanded="false" aria-controls="formulaGuide">
        <i class="fas fa-book-open" style="color: #6366f1;"></i>
        Hướng dẫn Quy chế Xếp hạng &amp; Cảnh báo
        <i class="fas fa-chevron-down" style="font-size: 0.7rem; opacity: 0.6;"></i>
    </button>

    <div class="collapse" id="formulaGuide">
        <div class="formula-guide-card">
            <!-- Card Title -->
            <div class="formula-guide-title">
                <div class="formula-guide-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div>
                    <h6 class="fw-extrabold mb-0" style="color: #0f172a; font-size: 1rem; letter-spacing: -0.01em;">Quy chế Xếp hạng &amp; Cơ chế Cảnh báo</h6>
                    <p class="mb-0 text-muted" style="font-size: 0.72rem;">Thông tin chi tiết về cách tính điểm và điều kiện kích hoạt cảnh báo</p>
                </div>
            </div>

            <div class="row g-3">
                <!-- Cột 1: Công thức tính điểm -->
                <div class="col-lg-6">
                    <div class="guide-panel guide-panel-formula">
                        <div class="guide-panel-title">1. Công thức quy chuẩn hóa điểm số</div>
                        <p class="text-muted mb-0" style="font-size: 0.77rem; line-height: 1.55;">
                            Điểm quy chuẩn của từng bộ truyện được tính dựa trên tỷ lệ phiếu bầu so với bộ truyện nhận được nhiều phiếu nhất trong cùng kỳ:
                        </p>

                        <div class="formula-box">
                            Điểm Quy Chuẩn = ( Số Phiếu / Số Phiếu Cao Nhất ) × 100
                        </div>

                        <div>
                            <div class="guide-list-item">
                                <span class="guide-list-icon success"><i class="fas fa-check"></i></span>
                                <span><strong>Số phiếu của bộ truyện:</strong> Lượng phiếu bình chọn thực tế từ Độc giả trong kỳ.</span>
                            </div>
                            <div class="guide-list-item">
                                <span class="guide-list-icon success"><i class="fas fa-check"></i></span>
                                <span><strong>Số phiếu cao nhất trong kỳ:</strong> Mốc chuẩn của Manga dẫn đầu kỳ đó.</span>
                            </div>
                            <div class="guide-list-item">
                                <span class="guide-list-icon success"><i class="fas fa-check"></i></span>
                                <span><strong>Làm tròn:</strong> Điểm số cuối cùng làm tròn tới 2 chữ số thập phân (VD: 85.71).</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cột 2: Cơ chế cảnh báo -->
                <div class="col-lg-6">
                    <div class="guide-panel guide-panel-warning">
                        <div class="guide-panel-title">2. Cảnh báo nguy cơ đình bản (Series Warning)</div>
                        <p class="text-muted mb-0" style="font-size: 0.77rem; line-height: 1.55;">
                            Hệ thống tự động kích hoạt chế độ cảnh báo sớm gửi tới Tác giả khi tác phẩm có dấu hiệu giảm sút hiệu suất:
                        </p>

                        <div class="warning-condition-box">
                            <i class="fas fa-exclamation-triangle" style="font-size: 1rem; color: #d97706;"></i>
                            <span>Điều kiện: Điểm quy chuẩn &lt; 50.00 điểm hoặc Thứ hạng &ge; #5</span>
                        </div>

                        <div>
                            <div class="guide-list-item">
                                <span class="guide-list-icon warning"><i class="fas fa-bell"></i></span>
                                <span><strong>Hành động tự động:</strong> Gửi thông báo khẩn cấp màu đỏ hiển thị nổi bật trên màn hình của Mangaka.</span>
                            </div>
                            <div class="guide-list-item">
                                <span class="guide-list-icon info"><i class="fas fa-comment-dots"></i></span>
                                <span><strong>Nội dung:</strong> <em>"Cảnh báo: Bộ truyện của bạn đang xếp hạng thấp. Có nguy cơ bị Hội đồng Biên tập xem xét ngưng xuất bản hoặc hủy dự án."</em></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Rankings Table ── -->
<div class="rankings-table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Kỳ Đánh Giá</th>
                    <th>Bộ Truyện</th>
                    <th>Thứ Hạng</th>
                    <th>Phiếu Độc Giả</th>
                    <th>Điểm Quy Chuẩn</th>
                    <th>Biến Động</th>
                    <th class="pe-4 text-end">Chi Tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rankings)): ?>
                    <?php foreach ($rankings as $ranking): ?>
                        <?php 
                            $prevRanking = $rankingModel->getPreviousRanking($ranking['series_id'], $ranking['period_start_date']);
                            $trendHtml = '<span class="trend-new"><i class="fas fa-sparkles me-1"></i>Mới</span>';
                            
                            if ($prevRanking) {
                                if ($ranking['rank_position'] < $prevRanking['rank_position']) {
                                    $diff = $prevRanking['rank_position'] - $ranking['rank_position'];
                                    $trendHtml = '<span class="trend-up"><i class="fas fa-arrow-up me-1"></i>▲ +' . $diff . ' hạng</span>';
                                } elseif ($ranking['rank_position'] > $prevRanking['rank_position']) {
                                    $diff = $ranking['rank_position'] - $prevRanking['rank_position'];
                                    $trendHtml = '<span class="trend-down"><i class="fas fa-arrow-down me-1"></i>▼ -' . $diff . ' hạng</span>';
                                } else {
                                    $trendHtml = '<span class="trend-flat"><i class="fas fa-minus me-1"></i>Không thay đổi</span>';
                                }
                            }

                            $rankPos = $ranking['rank_position'];
                            $rankClass = 'rank-number';
                            if ($rankPos == 1) $rankClass .= ' rank-1';
                            elseif ($rankPos == 2) $rankClass .= ' rank-2';
                            elseif ($rankPos == 3) $rankClass .= ' rank-3';

                            $rankIcon = '';
                            if ($rankPos == 1) $rankIcon = '🥇';
                            elseif ($rankPos == 2) $rankIcon = '🥈';
                            elseif ($rankPos == 3) $rankIcon = '🥉';
                        ?>
                        <tr>
                            <td class="ps-4">
                                <span class="period-badge">
                                    <i class="fas fa-calendar-alt me-1" style="color: #94a3b8;"></i>
                                    <?= htmlspecialchars(date('m/Y', strtotime($ranking['period_start_date']))) ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold" style="color: #0f172a; font-size: 0.85rem;">
                                    <?= htmlspecialchars($ranking['series_title']) ?>
                                </div>
                                <?php if (!empty($ranking['latest_chapter_number'])): ?>
                                    <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                        <i class="fas fa-book-open me-1" style="color: #94a3b8;"></i>
                                        Chương <?= htmlspecialchars($ranking['latest_chapter_number']) ?>
                                        <?php if (!empty($ranking['latest_chapter_title'])): ?>
                                            <span class="text-slate-400"> — <?= htmlspecialchars($ranking['latest_chapter_title']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                        <i class="fas fa-book me-1" style="color: #94a3b8;"></i>Chưa có chương xuất bản
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="<?= $rankClass ?>"><?= $rankIcon ?>#<?= htmlspecialchars($rankPos) ?></div>
                            </td>
                            <td>
                                <span class="fw-bold" style="color: #0f172a;"><?= htmlspecialchars($ranking['votes'] ?? 0) ?></span>
                                <span class="text-muted ms-1" style="font-size: 0.75rem;">phiếu</span>
                            </td>
                            <td>
                                <span class="score-badge">
                                    <i class="fas fa-star" style="font-size: 0.6rem;"></i>
                                    <?= htmlspecialchars($ranking['score']) ?> / 100
                                </span>
                            </td>
                            <td><?= $trendHtml ?></td>
                            <td class="pe-4 text-end">
                                <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=show&id=<?= $ranking['ranking_id'] ?>" class="btn-detail">
                                    <i class="fas fa-chart-line"></i> Xem
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="rankings-empty">
                                <div class="rankings-empty-icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <h6 class="fw-bold mb-1" style="color: #334155;">Chưa có dữ liệu xếp hạng</h6>
                                <p class="text-muted mb-0" style="font-size: 0.82rem;">Truyện của bạn chưa được xếp hạng trong kỳ nào.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
