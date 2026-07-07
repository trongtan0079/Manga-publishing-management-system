<?php
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
/**
 * View: Chi tiết hồ sơ & số liệu bảo vệ Series (dossier_detail.php)
 * Vai trò: Editor (Biên tập viên)
 * Chức năng: Xem thông số hiệu năng của truyện (Lịch sử xếp hạng, điểm bình chọn), cập nhật biện hộ và hồ sơ bảo vệ gửi Ban giám đốc.
 * 
 * @var array $series Thông tin chi tiết của bộ truyện đang xem
 * @var array $mangaka Thông tin tác giả
 * @var array $rankingHistory Lịch sử xếp hạng của bộ truyện
 * @var array $chapters Danh sách các chương truyện
 */
$pageTitle = 'Chi tiết Hồ sơ bảo vệ Series: ' . htmlspecialchars($series['title']);
$current_page = 'dossiers';
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

<div class="mb-4">
    <a href="<?= BASE_PATH ?>/index.php?controller=series&action=dossiers" class="btn btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
    </a>
</div>

<div class="row">
    <!-- Cột trái: Thông tin Bộ truyện & Form viết biện hộ bảo vệ -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Thông tin hồ sơ tác phẩm</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-start mb-4">
                    <?php if (!empty($series['cover_image'])): 
                        $coverUrl = (strpos($series['cover_image'], 'http') === 0) ? $series['cover_image'] : BASE_PATH . '/' . ltrim($series['cover_image'], '/');
                    ?>
                        <img src="<?= htmlspecialchars($coverUrl) ?>" alt="Cover" class="rounded border shadow me-3" style="width: 100px; height: 135px; object-fit: cover;">
                    <?php endif; ?>
                    <div>
                        <h4 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($series['title']) ?></h4>
                        <p class="text-muted text-xs mb-2">Tác giả: <strong><?= htmlspecialchars($mangaka['full_name'] ?? 'Chưa rõ') ?></strong></p>
                        
                        <div class="mb-2">
                            <span class="text-muted text-xs d-block mb-1">Trạng thái xuất bản</span>
                            <?php 
                                $statusClass = 'secondary';
                                $statusText = $series['status'];
                                if ($series['status'] === 'ongoing') { $statusClass = 'success'; $statusText = 'Đang phát hành'; }
                                elseif ($series['status'] === 'planning') { $statusClass = 'warning text-dark'; $statusText = 'Bản nháp'; }
                                elseif ($series['status'] === 'completed') { $statusClass = 'info text-dark'; $statusText = 'Hoàn thành'; }
                                elseif ($series['status'] === 'suspended') { $statusClass = 'dark'; $statusText = 'Tạm ngưng'; }
                                elseif ($series['status'] === 'canceled') { $statusClass = 'danger'; $statusText = 'Đã hủy'; }
                            ?>
                            <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                            <span class="badge bg-light text-dark border ms-1"><?= $series['publish_type'] === 'weekly' ? 'Hàng tuần' : 'Hàng tháng' ?></span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block text-uppercase text-xs fw-bold mb-1">Mô tả tóm tắt</small>
                    <p class="text-sm text-dark bg-light p-3 rounded" style="white-space: pre-wrap;"><?= htmlspecialchars($series['description'] ?: 'Không có mô tả.') ?></p>
                </div>
            </div>
        </div>

        <!-- Form nhập biện hộ bảo vệ series -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-shield-alt text-danger me-2"></i>Biện hộ & Bảo vệ Series (Dossier Notes)</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted text-xs mb-3">
                    Nhập các lý lẽ, số liệu tích cực khác (ví dụ: lượng tương tác mạng xã hội tăng, tiềm năng kịch bản kỳ tới bùng nổ, doanh số bán vật phẩm) để thuyết phục <strong>Hội đồng Biên tập</strong> duy trì xuất bản bộ truyện trong trường hợp thứ hạng tụt giảm.
                </p>
                <form action="<?= BASE_PATH ?>/index.php?controller=series&action=updateDossierNotes&id=<?= $series['series_id'] ?>" method="POST">
                    <div class="mb-3">
                        <label for="dossier_notes" class="form-label fw-bold text-xs text-uppercase text-muted">Nội dung biện hộ bảo vệ tác phẩm</label>
                        <textarea class="form-control" id="dossier_notes" name="dossier_notes" rows="8" placeholder="Nhập lý do biện hộ để bảo vệ tác phẩm trước nguy cơ bị Board hủy truyện... (Hỗ trợ định dạng văn bản)" style="border-radius: 8px; font-size: 0.88rem;"><?= htmlspecialchars($series['dossier_notes'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 py-2.5 fw-bold" style="border-radius: 8px;">
                        <i class="fas fa-save me-2"></i>Lưu & Cập nhật Hồ sơ bảo vệ
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Cột phải: Số liệu Biến động Thứ hạng & Điểm bình chọn (Charts & History) -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-chart-line text-success me-2"></i>Đồ thị biến động thứ hạng độc giả</h5>
            </div>
            <div class="card-body p-4 text-center">
                <?php if (!empty($rankingHistory)): ?>
                    <div style="position: relative; height: 260px; width: 100%;">
                        <canvas id="seriesRankingChart" style="max-height: 260px;"></canvas>
                    </div>
                    <div class="text-muted small mt-2">
                        <i class="fas fa-info-circle me-1"></i>Đồ thị biểu diễn thứ hạng (đường đỏ - càng thấp vị trí càng cao) và điểm số (đường xanh) qua các kỳ.
                    </div>
                <?php else: ?>
                    <div class="py-5 text-muted">
                        <i class="fas fa-chart-bar fa-3x mb-3 text-secondary" style="opacity: 0.35;"></i>
                        <h6 class="fw-bold">Chưa có số liệu xếp hạng</h6>
                        <p class="small">Series này chưa được Hội đồng Biên tập nhập dữ liệu bình chọn kỳ nào.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-history text-muted me-2"></i>Lịch sử Xếp hạng chi tiết</h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($rankingHistory)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Kỳ đánh giá</th>
                                    <th>Vị trí xếp hạng</th>
                                    <th>Điểm bình chọn</th>
                                    <th class="text-end pe-4">Đánh giá chung</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankingHistory as $rank): ?>
                                    <tr>
                                        <td class="ps-4"><?= htmlspecialchars(date('d/m/Y', strtotime($rank['period_start_date']))) ?></td>
                                        <td>
                                            <span class="badge bg-warning text-dark fw-bold">Hạng #<?= $rank['rank_position'] ?></span>
                                        </td>
                                        <td><strong><?= $rank['score'] ?> / 100</strong></td>
                                        <td class="text-end pe-4">
                                            <?php if ($rank['score'] < 50 || $rank['rank_position'] >= 5): ?>
                                                <span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i>Nguy cơ bị hủy</span>
                                            <?php else: ?>
                                                <span class="text-success"><i class="fas fa-check-circle me-1"></i>An toàn</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 px-3 text-muted small">
                        Không có lịch sử xếp hạng nào được ghi nhận.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($rankingHistory)): 
    // Chuẩn bị dữ liệu cho Chart.js
    $labels = [];
    $positions = [];
    $scores = [];
    // Đảo ngược thứ tự để vẽ theo thời gian từ cũ đến mới
    $historyReverse = array_reverse($rankingHistory);
    foreach ($historyReverse as $rh) {
        $labels[] = date('d/m', strtotime($rh['period_start_date']));
        $positions[] = intval($rh['rank_position']);
        $scores[] = floatval($rh['score']);
    }
?>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('seriesRankingChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [
                {
                    label: 'Vị trí Xếp hạng (Hạng)',
                    data: <?= json_encode($positions) ?>,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 2,
                    yAxisID: 'yRank',
                    tension: 0.15,
                    fill: false
                },
                {
                    label: 'Điểm bình chọn (Score)',
                    data: <?= json_encode($scores) ?>,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    yAxisID: 'yScore',
                    tension: 0.15,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yRank: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Vị trí xếp hạng (Thấp tốt hơn)'
                    },
                    // Đảo ngược trục Y cho xếp hạng (hạng 1 ở trên cùng)
                    reverse: true,
                    suggestedMin: 1,
                    suggestedMax: 10,
                    ticks: {
                        stepSize: 1
                    }
                },
                yScore: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Điểm bình chọn (Lớn tốt hơn)'
                    },
                    min: 0,
                    max: 100,
                    grid: {
                        drawOnChartArea: false // Không vẽ lưới chồng lên trục trái
                    }
                }
            }
        }
    });
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
