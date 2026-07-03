<?php 
/**
 * View: Chi tiết một trang truyện
 * Khai báo biến để Editor/IDE hiểu và không báo lỗi
 * @var array $page Thông tin trang hiện tại
 * @var array $chapter Thông tin chapter chứa trang này
 * @var array $series Thông tin bộ truyện
 */
$pageTitle = 'Chi tiết Trang ' . htmlspecialchars($page['page_number']);
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

<!-- Khối thanh điều hướng và nút hành động -->
<div class="mb-3 d-flex justify-content-between align-items-center">
    <!-- Nút quay lại danh sách trang của chapter -->
    <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=show&id=<?= htmlspecialchars($chapter['chapter_id']) ?>" class="btn btn-secondary">&larr; Quay lại Chapter</a>
    
    <div>
        <!-- Nút sửa trang hiện tại -->
        <a href="<?= BASE_PATH ?>/index.php?controller=page&action=edit&id=<?= $page['page_id'] ?>" class="btn btn-warning">Sửa trang</a>
        <!-- Form xóa trang, dùng onsubmit để hỏi lại trước khi xóa -->
        <form action="<?= BASE_PATH ?>/index.php?controller=page&action=delete&id=<?= $page['page_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa trang này?');">
            <button type="submit" class="btn btn-danger">Xóa</button>
        </form>
    </div>
</div>

<!-- Khối thông tin chung của trang -->
<div class="card mb-4">
    <div class="card-header">
        <h4 class="mb-0">
            Chi tiết Trang <?= htmlspecialchars($page['page_number']) ?>
        </h4>
        <small class="text-muted">Chapter <?= htmlspecialchars($chapter['chapter_number']) ?> - <?= htmlspecialchars($series['title']) ?></small>
    </div>
    <div class="card-body">
        <?php
        // Gán màu huy hiệu (badge) tùy theo trạng thái (status)
        $pBadge = 'bg-secondary';
        switch ($page['status']) {
            case 'drafting': $pBadge = 'bg-secondary'; break;
            case 'drawing': $pBadge = 'bg-primary'; break;
            case 'reviewing': $pBadge = 'bg-warning text-dark'; break;
            case 'approved': $pBadge = 'bg-info text-dark'; break;
            case 'published': $pBadge = 'bg-success'; break;
        }
        ?>
        <div class="row">
            <div class="col-md-4">
                <p><strong>Trạng thái:</strong> <span class="badge <?= $pBadge ?>"><?= ucfirst(htmlspecialchars($page['status'])) ?></span></p>
                <p><strong>Ngày tạo:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($page['created_at']))) ?></p>
                <p><strong>Cập nhật lần cuối:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($page['updated_at']))) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Khối hiển thị hình ảnh chi tiết và phân đoạn AI -->
<div class="row">
    <!-- Cột trái: Ảnh trang truyện tích hợp vẽ Bounding Box của AI -->
    <div class="col-md-7 mb-4">
        <div class="card border-info h-100">
            <div class="card-header bg-info text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-image me-2"></i>Bản vẽ trang truyện</h5>
                <?php if (!empty($regions)): ?>
                    <span class="badge bg-dark">Đã nhận dạng <?= count($regions) ?> vùng</span>
                <?php endif; ?>
            </div>
            <div class="card-body text-center bg-light d-flex align-items-center justify-content-center p-2" style="min-height: 400px;">
                <?php if (!empty($page['image_url'])): 
                    $imageUrl = $page['image_url'];
                    $resolvedImage = (strpos($imageUrl, 'http') === 0) ? $imageUrl : BASE_PATH . '/' . ltrim($imageUrl, '/');
                ?>
                    <?php if (!empty($regions)): ?>
                        <div class="position-relative d-inline-block text-start" style="max-width: 100%; border: 1px solid #ccc; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                            <img id="mangaPageImage" src="<?= htmlspecialchars($resolvedImage) ?>" alt="Page <?= htmlspecialchars($page['page_number']) ?>" class="img-fluid" style="display: block; max-width: 100%;">
                            <?php foreach ($regions as $region): 
                                // Tỷ lệ phần trăm dựa trên kích thước giả định 800 x 1000
                                $l = ($region['x'] / 800) * 100;
                                $t = ($region['y'] / 1000) * 100;
                                $w = ($region['width'] / 800) * 100;
                                $h = ($region['height'] / 1000) * 100;
                                
                                $borderColor = '#dc3545'; // Đỏ cho panel
                                $bgColor = 'rgba(220, 53, 69, 0.15)';
                                if ($region['region_type'] === 'bubble') {
                                    $borderColor = '#0d6efd'; // Xanh dương cho bubble
                                    $bgColor = 'rgba(13, 110, 253, 0.15)';
                                } elseif ($region['region_type'] === 'character') {
                                    $borderColor = '#198754'; // Xanh lá cho nhân vật
                                    $bgColor = 'rgba(25, 135, 84, 0.15)';
                                }
                            ?>
                                <div class="ai-region-overlay" 
                                     id="overlay-region-<?= $region['region_id'] ?>"
                                     style="position: absolute; left: <?= $l ?>%; top: <?= $t ?>%; width: <?= $w ?>%; height: <?= $h ?>%; border: 2px dashed <?= $borderColor ?>; background-color: <?= $bgColor ?>; cursor: pointer; transition: all 0.2s;"
                                     title="<?= htmlspecialchars(ucfirst($region['region_type'])) ?> (Tin cậy: <?= number_format($region['confidence'] * 100, 2) ?>%)"
                                     onclick="highlightTableRecord(<?= $region['region_id'] ?>)"
                                     onmouseenter="hoverOverlay(<?= $region['region_id'] ?>, true)"
                                     onmouseleave="hoverOverlay(<?= $region['region_id'] ?>, false)">
                                     <span class="badge bg-dark text-white position-absolute p-1" style="font-size: 8px; top: 2px; left: 2px; opacity: 0.85;">
                                         <?= ucfirst($region['region_type']) ?>
                                     </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="position-relative d-inline-block text-start" style="max-width: 100%;">
                            <img src="<?= htmlspecialchars($resolvedImage) ?>" alt="Page <?= htmlspecialchars($page['page_number']) ?>" class="img-fluid border shadow-sm">
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-muted my-5">
                        <i class="fas fa-file-image fa-3x mb-3"></i>
                        <p>Trang này chưa có hình ảnh.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cột phải: Thông tin phân đoạn vùng bằng AI & Bảng điều khiển -->
    <div class="col-md-5 mb-4">
        <div class="card border-secondary h-100">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-robot me-2"></i>Trình phân đoạn AI</h5>
                <?php if (!empty($regions)): ?>
                    <a href="<?= BASE_PATH ?>/index.php?controller=pageregion&action=runai&page_id=<?= $page['page_id'] ?>" class="btn btn-sm btn-light" onclick="return confirm('Bạn có chắc muốn chạy lại thuật toán quét phân đoạn AI? Dữ liệu cũ sẽ được làm mới.');">
                        <i class="fas fa-sync-alt me-1"></i>Quét lại
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
                <?php if (empty($regions)): ?>
                    <div class="text-center my-auto py-4">
                        <i class="fas fa-brain fa-3x text-muted mb-3"></i>
                        <h6 class="fw-bold">Hệ thống AI Phân đoạn chưa chạy</h6>
                        <p class="text-muted small px-3">Sử dụng mô hình AI (YOLOv8-Segmentation & SAM) để tự động phát hiện và đánh dấu các vùng Khung truyện (Panel), Bong bóng thoại (Bubble), Nhân vật trên trang.</p>
                        <a href="<?= BASE_PATH ?>/index.php?controller=pageregion&action=runai&page_id=<?= $page['page_id'] ?>" class="btn btn-primary mt-2">
                            <i class="fas fa-play me-2"></i>Chạy AI phân đoạn vùng
                        </a>
                    </div>
                <?php else: ?>
                    <div>
                        <p class="text-muted small mb-3">Các phân vùng được nhận diện thành công bởi AI. Bạn có thể chọn giao việc (Task) trực tiếp cho Assistant trên từng phân vùng.</p>
                        <div class="list-group" id="region-list-group">
                            <?php foreach ($regions as $region): 
                                $typeLabel = 'Khung truyện';
                                $typeClass = 'bg-danger';
                                $rowBorder = 'border-start border-danger border-4';
                                if ($region['region_type'] === 'bubble') {
                                    $typeLabel = 'Bong bóng thoại';
                                    $typeClass = 'bg-primary';
                                    $rowBorder = 'border-start border-primary border-4';
                                } elseif ($region['region_type'] === 'character') {
                                    $typeLabel = 'Nhân vật';
                                    $typeClass = 'bg-success';
                                    $rowBorder = 'border-start border-success border-4';
                                }
                            ?>
                                <div class="list-group-item list-group-item-action mb-2 <?= $rowBorder ?> shadow-sm transition-all" 
                                     id="list-region-<?= $region['region_id'] ?>"
                                     onmouseenter="hoverOverlay(<?= $region['region_id'] ?>, true)"
                                     onmouseleave="hoverOverlay(<?= $region['region_id'] ?>, false)">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h6 class="mb-1 fw-bold text-dark">
                                            <span class="badge <?= $typeClass ?> me-2"><?= $typeLabel ?></span>
                                            ID #<?= $region['region_id'] ?>
                                        </h6>
                                        <small class="text-muted">Độ tin cậy: <strong><?= number_format($region['confidence'] * 100, 1) ?>%</strong></small>
                                    </div>
                                    <p class="mb-1 text-muted small">
                                        Tọa độ: X:<?= $region['x'] ?>, Y:<?= $region['y'] ?> | Kích thước: <?= $region['width'] ?>x<?= $region['height'] ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="badge bg-light text-dark border">AI Generated</span>
                                        <a href="<?= BASE_PATH ?>/index.php?controller=task&action=create&page_id=<?= $page['page_id'] ?>&page_region_id=<?= $region['region_id'] ?>" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 11px;">
                                            <i class="fas fa-plus me-1"></i>Giao việc trên vùng này
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function hoverOverlay(regionId, isHover) {
    const overlay = document.getElementById('overlay-region-' + regionId);
    const listItem = document.getElementById('list-region-' + regionId);
    if (overlay) {
        if (isHover) {
            overlay.style.transform = 'scale(1.02)';
            overlay.style.boxShadow = '0 0 12px rgba(0,0,0,0.5)';
            overlay.style.zIndex = '10';
        } else {
            overlay.style.transform = 'scale(1)';
            overlay.style.boxShadow = 'none';
            overlay.style.zIndex = '1';
        }
    }
    if (listItem) {
        if (isHover) {
            listItem.classList.add('active-region');
            listItem.style.backgroundColor = '#f0f4f8';
        } else {
            listItem.classList.remove('active-region');
            listItem.style.backgroundColor = '';
        }
    }
}

function highlightTableRecord(regionId) {
    const listItem = document.getElementById('list-region-' + regionId);
    if (listItem) {
        listItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        // Tạo hiệu ứng nhấp nháy
        let count = 0;
        const interval = setInterval(() => {
            listItem.style.opacity = listItem.style.opacity === '0.5' ? '1' : '0.5';
            count++;
            if (count > 5) {
                clearInterval(interval);
                listItem.style.opacity = '1';
            }
        }, 150);
    }
}
</script>

<!-- 
  Khối Quản lý Công việc (Task Management)
  Được hiển thị ngay dưới nội dung chính của Trang truyện.
  Giúp Mangaka theo dõi và quản lý các công việc đang giao cho Assistant trên trang này.
-->
<div class="card border-primary mt-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Quản lý công việc</h5>
        <!-- Nút tạo công việc mới, truyền sẵn page_id qua URL GET parameter -->
        <a href="<?= BASE_PATH ?>/index.php?controller=task&action=create&page_id=<?= $page['page_id'] ?>" class="btn btn-sm btn-light">+ Tạo công việc</a>
    </div>
    <div class="card-body">
        <?php if (!empty($tasks)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Công việc (Task)</th>
                            <th>Loại công việc</th>
                            <th>Phân vùng</th>
                            <th>Người phụ trách</th>
                            <th>Độ ưu tiên</th>
                            <th>Trạng thái</th>
                            <th>Hạn chót</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Duyệt qua các task thuộc trang này -->
                        <?php foreach ($tasks as $task): 
                            $hoverAttr = '';
                            if (!empty($task['page_region_id'])) {
                                $hoverAttr = ' onmouseenter="hoverOverlay(' . $task['page_region_id'] . ', true)" onmouseleave="hoverOverlay(' . $task['page_region_id'] . ', false)"';
                            }
                        ?>
                            <tr<?= $hoverAttr ?>>
                                <!-- Tiêu đề task -->
                                <td>
                                    <strong><?= htmlspecialchars($task['title']) ?></strong>
                                    <?php if (!empty($task['resource_url'])): ?>
                                        <br><small class="text-muted"><i class="fas fa-link me-1"></i>Tài nguyên: <a href="<?= htmlspecialchars($task['resource_url']) ?>" target="_blank">Xem link</a></small>
                                    <?php endif; ?>
                                </td>
                                <!-- Loại công việc -->
                                <td>
                                    <?php
                                    $typeLabel = 'Khác';
                                    $typeBadge = 'bg-secondary';
                                    switch ($task['task_type']) {
                                        case 'background': $typeLabel = 'Vẽ nền'; $typeBadge = 'bg-dark'; break;
                                        case 'inking': $typeLabel = 'Đi nét'; $typeBadge = 'bg-secondary'; break;
                                        case 'coloring': $typeLabel = 'Lên màu'; $typeBadge = 'bg-success'; break;
                                        case 'effects': $typeLabel = 'Hiệu ứng'; $typeBadge = 'bg-info text-dark'; break;
                                        case 'other': $typeLabel = 'Khác'; $typeBadge = 'bg-secondary'; break;
                                    }
                                    ?>
                                    <span class="badge <?= $typeBadge ?>"><?= $typeLabel ?></span>
                                </td>
                                <!-- Phân vùng -->
                                <td>
                                    <?php if (!empty($task['page_region_id'])): ?>
                                        <span class="badge bg-light text-dark border border-secondary" style="cursor: pointer;" onclick="highlightTableRecord(<?= $task['page_region_id'] ?>)">
                                            Vùng #<?= $task['page_region_id'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">Cả trang</span>
                                    <?php endif; ?>
                                </td>
                                <!-- Tên người thực hiện -->
                                <td><?= htmlspecialchars($task['assistant_name']) ?></td>
                                <!-- Hiển thị mức độ ưu tiên với màu sắc (badge) tương ứng -->
                                <td>
                                    <?php 
                                    $pColor = 'secondary';
                                    $pLabel = $task['priority'];
                                    if ($task['priority'] == 'high') { $pColor = 'danger'; $pLabel = 'Cao'; }
                                    elseif ($task['priority'] == 'medium') { $pColor = 'warning'; $pLabel = 'Trung bình'; }
                                    else { $pColor = 'info'; $pLabel = 'Thấp'; }
                                    ?>
                                    <span class="badge bg-<?= $pColor ?>"><?= htmlspecialchars($pLabel) ?></span>
                                </td>
                                <!-- Hiển thị trạng thái tiến độ với màu sắc (badge) tương ứng -->
                                <td>
                                    <?php 
                                    $sColor = 'secondary';
                                    $sLabel = $task['status'];
                                    if ($task['status'] == 'completed') { $sColor = 'success'; $sLabel = 'Hoàn thành'; }
                                    elseif ($task['status'] == 'in_progress') { $sColor = 'primary'; $sLabel = 'Đang làm'; }
                                    else { $sColor = 'warning text-dark'; $sLabel = 'Chờ xử lý'; }
                                    ?>
                                    <span class="badge bg-<?= $sColor ?>"><?= htmlspecialchars($sLabel) ?></span>
                                </td>
                                <!-- Hạn chót, định dạng d/m/Y -->
                                <td><?= $task['due_date'] ? htmlspecialchars(date('d/m/Y', strtotime($task['due_date']))) : '<span class="text-muted">Không có</span>' ?></td>
                                <!-- Các nút thao tác Sửa và Xóa dành cho Mangaka -->
                                <td>
                                    <!-- Nút Sửa chuyển hướng sang TaskController@edit -->
                                    <a href="<?= BASE_PATH ?>/index.php?controller=task&action=edit&id=<?= $task['task_id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                                    <!-- Nút Xóa thực hiện qua form POST để bảo mật -->
                                    <form action="<?= BASE_PATH ?>/index.php?controller=task&action=delete&id=<?= $task['task_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa công việc này?');">
                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Thông báo khi chưa có task nào -->
            <p class="text-muted mb-0">Chưa có task nào được giao cho trang này.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
