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
$isLocked = ($chapter['status'] === 'approved' || $chapter['status'] === 'published');
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
    
    <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked): ?>
    <div>
        <!-- Nút sửa trang hiện tại -->
        <a href="<?= BASE_PATH ?>/index.php?controller=page&action=edit&id=<?= $page['page_id'] ?>" class="btn btn-warning">Sửa trang</a>
        <!-- Form xóa trang, dùng onsubmit để hỏi lại trước khi xóa -->
        <form action="<?= BASE_PATH ?>/index.php?controller=page&action=delete&id=<?= $page['page_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa trang này?');">
            <button type="submit" class="btn btn-danger">Xóa</button>
        </form>
    </div>
    <?php endif; ?>
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
        $statusLabel = $page['status'];
        switch ($page['status']) {
            case 'drafting': $pBadge = 'bg-secondary'; $statusLabel = 'Bản nháp'; break;
            case 'drawing': $pBadge = 'bg-primary'; $statusLabel = 'Đang vẽ'; break;
            case 'reviewing': $pBadge = 'bg-warning text-dark'; $statusLabel = 'Đang chờ duyệt'; break;
            case 'approved': $pBadge = 'bg-info text-dark'; $statusLabel = 'Đã duyệt'; break;
            case 'published': $pBadge = 'bg-success'; $statusLabel = 'Đã xuất bản'; break;
        }
        ?>
        <div class="row">
            <div class="col-md-4">
                <p><strong>Trạng thái:</strong> <span class="badge <?= $pBadge ?>"><?= htmlspecialchars($statusLabel) ?></span></p>
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
        <div class="card border-info">
            <div class="card-header bg-info text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-image me-2"></i>Bản vẽ trang truyện</h5>
            </div>
            <div class="card-body text-center bg-light d-flex flex-column align-items-center justify-content-center p-2" style="min-height: 400px;">
                <?php if (!empty($page['image_url'])): 
                    $imageUrl = $page['image_url'];
                    $resolvedImage = (strpos($imageUrl, 'http') === 0) ? $imageUrl : BASE_PATH . '/' . ltrim($imageUrl, '/');
                ?>
                    <div id="drawInstruction" class="alert alert-info d-none py-2 mb-3 text-start w-100" style="font-size: 0.85rem;">
                        <i class="fas fa-info-circle me-2"></i><strong>Chế độ vẽ thủ công:</strong> Hãy nhấn giữ chuột trái và kéo trên ảnh truyện để vẽ phân vùng mới.
                    </div>
                    
                    <div id="mangaPageWrapper" class="position-relative d-inline-block text-start" style="max-width: 100%; border: 1px solid #ccc; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                        <img id="mangaPageImage" src="<?= htmlspecialchars($resolvedImage) ?>" alt="Page <?= htmlspecialchars($page['page_number']) ?>" class="img-fluid" style="display: block; max-width: 100%;">
                        
                        <?php if (!empty($regions)): ?>
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
                                } elseif ($region['region_type'] === 'background') {
                                    $borderColor = '#343a40'; // Đen xám cho background
                                    $bgColor = 'rgba(52, 58, 64, 0.15)';
                                } elseif ($region['region_type'] === 'sfx') {
                                    $borderColor = '#fd7e14'; // Cam cho SFX
                                    $bgColor = 'rgba(253, 126, 20, 0.15)';
                                }
                            ?>
                                <div class="ai-region-overlay" 
                                     id="overlay-region-<?= $region['region_id'] ?>"
                                     style="position: absolute; left: <?= $l ?>%; top: <?= $t ?>%; width: <?= $w ?>%; height: <?= $h ?>%; border: 2px dashed <?= $borderColor ?>; background-color: <?= $bgColor ?>; cursor: pointer; transition: all 0.2s;"
                                     title="<?= htmlspecialchars(ucfirst($region['region_type'])) ?> (Vẽ tay)"
                                     onclick="highlightTableRecord(<?= $region['region_id'] ?>)"
                                     onmouseenter="hoverOverlay(<?= $region['region_id'] ?>, true)"
                                     onmouseleave="hoverOverlay(<?= $region['region_id'] ?>, false)">
                                     <span class="badge bg-dark text-white position-absolute p-1" style="font-size: 8px; top: 2px; left: 2px; opacity: 0.85;">
                                         <?= ucfirst($region['region_type']) ?>
                                     </span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-muted my-5">
                        <i class="fas fa-file-image fa-3x mb-3"></i>
                        <p>Trang này chưa có hình ảnh.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cột phải: Thông tin Phân Vùng Bản Vẽ Thủ Công -->
    <div class="col-md-5 mb-4">
        <div class="card border-secondary">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-crop me-2"></i>Phân vùng bản vẽ</h5>
                <div class="d-flex gap-2">
                    <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked): ?>
                        <button id="btnDrawToggle" class="btn btn-sm btn-info text-white">
                            <i class="fas fa-edit me-1"></i>Vẽ thủ công
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
                <?php if (empty($regions)): ?>
                    <div class="text-center my-auto py-4">
                        <i class="fas fa-edit fa-3x text-muted mb-3"></i>
                        <h6 class="fw-bold">Chưa có phân vùng nào</h6>
                        <p class="text-muted small px-3">Hãy sử dụng bộ công cụ <strong>Vẽ thủ công</strong> chuyên nghiệp để tự vẽ và phân chia khung hình, ô thoại, nhân vật trên trang truyện.</p>
                        <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked): ?>
                        <div class="d-flex gap-2 justify-content-center mt-2">
                            <button onclick="document.getElementById('btnDrawToggle').click();" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-2"></i>Bắt đầu vẽ phân vùng
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div>
                        <p class="text-muted small mb-3">Các phân vùng bản vẽ hiện có. Bạn có thể chọn giao việc (Task) trực tiếp cho Assistant trên từng phân vùng.</p>
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
                                } elseif ($region['region_type'] === 'background') {
                                    $typeLabel = 'Bối cảnh/Nền';
                                    $typeClass = 'bg-dark';
                                    $rowBorder = 'border-start border-dark border-4';
                                } elseif ($region['region_type'] === 'sfx') {
                                    $typeLabel = 'Hiệu ứng SFX';
                                    $typeClass = 'bg-warning text-dark';
                                    $rowBorder = 'border-start border-warning border-4';
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
                                        <small class="text-success fw-bold"><i class="fas fa-user-edit me-1"></i>Vẽ thủ công</small>
                                    </div>
                                    <p class="mb-1 text-muted small">
                                        Tọa độ: X:<?= $region['x'] ?>, Y:<?= $region['y'] ?> | Kích thước: <?= $region['width'] ?>x<?= $region['height'] ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="badge bg-light text-dark border">Vẽ tay</span>
                                        <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked): ?>
                                        <div class="btn-group">
                                            <a href="<?= BASE_PATH ?>/index.php?controller=task&action=create&page_id=<?= $page['page_id'] ?>&page_region_id=<?= $region['region_id'] ?>" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 11px;">
                                                <i class="fas fa-plus me-1"></i>Giao việc
                                            </a>
                                            <form action="<?= BASE_PATH ?>/index.php?controller=pageregion&action=delete&id=<?= $region['region_id'] ?>&page_id=<?= $page['page_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa phân vùng này?');">
                                                <input type="hidden" name="page_id" value="<?= htmlspecialchars($page['page_id']) ?>">
                                                <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size: 11px; margin-left: 2px;">
                                                    <i class="fas fa-trash-alt me-1"></i>Xóa
                                                </button>
                                            </form>
                                        </div>
                                        <?php endif; ?>
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

<style>
.drawing-active {
    cursor: crosshair !important;
}
.selection-box {
    position: absolute;
    border: 2px dashed #0d6efd;
    background-color: rgba(13, 110, 253, 0.25);
    z-index: 1000;
    pointer-events: none;
}
</style>

<!-- Modal chọn loại phân vùng thủ công -->
<div class="modal fade" id="saveRegionModal" tabindex="-1" aria-labelledby="saveRegionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2 bg-primary text-white">
                <h6 class="modal-title fw-bold" id="saveRegionModalLabel">Lưu phân vùng mới</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= BASE_PATH ?>/index.php?controller=pageregion&action=store" method="POST">
                <div class="modal-body py-3">
                    <input type="hidden" name="page_id" value="<?= htmlspecialchars($page['page_id']) ?>">
                    <!-- Coordinates mapped to 800x1000 standard -->
                    <input type="hidden" id="reg_x" name="x">
                    <input type="hidden" id="reg_y" name="y">
                    <input type="hidden" id="reg_width" name="width">
                    <input type="hidden" id="reg_height" name="height">
                    
                    <div class="mb-3">
                        <label for="reg_type" class="form-label fw-bold" style="font-size: 0.9rem;">Loại phân vùng <span class="text-danger">*</span></label>
                        <select class="form-select select-sm" id="reg_type" name="region_type" required>
                            <option value="panel">Khung truyện (Panel)</option>
                            <option value="bubble">Bong bóng thoại (Bubble)</option>
                            <option value="character">Nhân vật (Character)</option>
                            <option value="background">Bối cảnh/Nền (Background)</option>
                            <option value="sfx">Hiệu ứng chữ (SFX)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer py-1">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm">Lưu vùng</button>
                </div>
            </form>
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

// Xử lý vẽ phân vùng thủ công bằng cách nhấn giữ và kéo chuột
let isDrawingMode = false;
let isDragging = false;
let startX = 0, startY = 0;
let selectionBox = null;
const wrapper = document.getElementById('mangaPageWrapper');
const img = document.getElementById('mangaPageImage');
const btnDrawToggle = document.getElementById('btnDrawToggle');
const drawInstruction = document.getElementById('drawInstruction');

if (btnDrawToggle && wrapper && img) {
    btnDrawToggle.addEventListener('click', function() {
        isDrawingMode = !isDrawingMode;
        if (isDrawingMode) {
            btnDrawToggle.innerHTML = '<i class="fas fa-times me-1"></i>Hủy vẽ';
            btnDrawToggle.classList.remove('btn-info');
            btnDrawToggle.classList.add('btn-danger');
            wrapper.classList.add('drawing-active');
            drawInstruction.classList.remove('d-none');
        } else {
            resetDrawingMode();
        }
    });

    wrapper.addEventListener('mousedown', function(e) {
        if (!isDrawingMode) return;
        
        // Prevent default dragging behavior of the image
        e.preventDefault();
        
        isDragging = true;
        
        // Get coordinates relative to the wrapper
        const rect = wrapper.getBoundingClientRect();
        startX = e.clientX - rect.left;
        startY = e.clientY - rect.top;
        
        // Create selection box
        selectionBox = document.createElement('div');
        selectionBox.className = 'selection-box';
        selectionBox.style.left = startX + 'px';
        selectionBox.style.top = startY + 'px';
        wrapper.appendChild(selectionBox);
    });

    wrapper.addEventListener('mousemove', function(e) {
        if (!isDragging || !selectionBox) return;
        
        const rect = wrapper.getBoundingClientRect();
        const currentX = e.clientX - rect.left;
        const currentY = e.clientY - rect.top;
        
        const x = Math.min(startX, currentX);
        const y = Math.min(startY, currentY);
        const width = Math.abs(startX - currentX);
        const height = Math.abs(startY - currentY);
        
        selectionBox.style.left = x + 'px';
        selectionBox.style.top = y + 'px';
        selectionBox.style.width = width + 'px';
        selectionBox.style.height = height + 'px';
    });

    wrapper.addEventListener('mouseup', function(e) {
        if (!isDragging) return;
        isDragging = false;
        
        if (!selectionBox) return;
        
        const rect = wrapper.getBoundingClientRect();
        const endX = e.clientX - rect.left;
        const endY = e.clientY - rect.top;
        
        const x = Math.min(startX, endX);
        const y = Math.min(startY, endY);
        const width = Math.abs(startX - endX);
        const height = Math.abs(startY - endY);
        
        // Don't save tiny clicks
        if (width > 10 && height > 10) {
            // Get visible dimensions of image
            const clientW = img.clientWidth;
            const clientH = img.clientHeight;
            
            // Map to 800x1000 standard
            const dbX = Math.round((x / clientW) * 800);
            const dbY = Math.round((y / clientH) * 1000);
            const dbW = Math.round((width / clientW) * 800);
            const dbH = Math.round((height / clientH) * 1000);
            
            // Set input values
            document.getElementById('reg_x').value = dbX;
            document.getElementById('reg_y').value = dbY;
            document.getElementById('reg_width').value = dbW;
            document.getElementById('reg_height').value = dbH;
            
            // Show modal
            const saveModal = new bootstrap.Modal(document.getElementById('saveRegionModal'));
            saveModal.show();
            
            // Listen for modal hide to clean up selection box
            document.getElementById('saveRegionModal').addEventListener('hidden.bs.modal', function () {
                if (selectionBox && selectionBox.parentNode) {
                    selectionBox.parentNode.removeChild(selectionBox);
                }
                selectionBox = null;
                resetDrawingMode();
            }, { once: true });
        } else {
            if (selectionBox && selectionBox.parentNode) {
                selectionBox.parentNode.removeChild(selectionBox);
            }
            selectionBox = null;
        }
    });
}

function resetDrawingMode() {
    isDrawingMode = false;
    if (btnDrawToggle) {
        btnDrawToggle.innerHTML = '<i class="fas fa-edit me-1"></i>Vẽ thủ công';
        btnDrawToggle.classList.remove('btn-danger');
        btnDrawToggle.classList.add('btn-info');
    }
    if (wrapper) {
        wrapper.classList.remove('drawing-active');
    }
    if (drawInstruction) {
        drawInstruction.classList.add('d-none');
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
        <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked): ?>
        <a href="<?= BASE_PATH ?>/index.php?controller=task&action=create&page_id=<?= $page['page_id'] ?>" class="btn btn-sm btn-light">+ Tạo công việc</a>
        <?php endif; ?>
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
                                    <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked): ?>
                                    <!-- Nút Sửa chuyển hướng sang TaskController@edit -->
                                    <a href="<?= BASE_PATH ?>/index.php?controller=task&action=edit&id=<?= $task['task_id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                                    <!-- Nút Xóa thực hiện qua form POST để bảo mật -->
                                    <form action="<?= BASE_PATH ?>/index.php?controller=task&action=delete&id=<?= $task['task_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa công việc này?');">
                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                    <?php else: ?>
                                        <span class="text-muted small"><?= $isLocked ? 'Khóa' : '-' ?></span>
                                    <?php endif; ?>
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
