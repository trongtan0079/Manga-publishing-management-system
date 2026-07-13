<?php 
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
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
$isLocked = ($this->isChapterLocked($chapter) || $page['status'] === 'published');
?>
<style>
.selected-card {
    background-color: #f8fafc !important; /* Tông xám pastel nhạt sạch sẽ */
    border-color: #4f46e5 !important;    /* Viền màu tím thương hiệu */
    border-width: 2px !important;
    box-shadow: 0 0 20px rgba(79, 70, 229, 0.25) !important; /* Đổ bóng phát sáng (Glowing shadow) */
    transform: translateY(-2px);
    transition: all 0.25s ease;
}

/* Tối/mờ đi các card phân vùng không được chọn ở danh sách bên phải */
#region-list-group.has-selected .list-group-item-action:not(.selected-card) {
    opacity: 0.45;
    filter: grayscale(15%);
    transition: opacity 0.3s ease, filter 0.3s ease;
}

/* Tối/mờ đi các phân vùng vẽ khác trên canvas khi có một phân vùng được chọn */
#mangaPageWrapper.has-selected-overlay .page-region-overlay:not(.selected-overlay) {
    opacity: 0.12 !important;
    border-style: dotted !important;
    box-shadow: none !important;
    transition: opacity 0.3s ease;
}

/* Tăng cường hiển thị phân vùng được chọn trên canvas */
.selected-overlay {
    opacity: 0.95 !important;
    border-style: solid !important;
    border-width: 4px !important;
    z-index: 100 !important;
    box-shadow: 0 0 25px currentColor !important;
}
</style>
<?php

// Spotlight logic for Assistant tasks
$highlightRegionIds = [];
$hrParam = $_GET['highlight_region'] ?? null;
if ($hrParam) {
    $highlightRegionIds = array_filter(array_map('trim', explode(',', $hrParam)));
}
if (empty($highlightRegionIds) && isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'assistant') {
    if (!empty($tasks)) {
        foreach ($tasks as $t) {
            if ($t['assistant_id'] == $_SESSION['user_id']) {
                if (!empty($t['grouped_region_ids'])) {
                    $highlightRegionIds = array_filter(array_map('trim', explode(',', $t['grouped_region_ids'])));
                    break;
                } elseif (!empty($t['page_region_id'])) {
                    $highlightRegionIds[] = $t['page_region_id'];
                    break;
                }
            }
        }
    }
}
$hasSpotlight = !empty($highlightRegionIds);
$highlightRegionId = $hasSpotlight ? reset($highlightRegionIds) : null;

// Filter regions and tasks for Assistant to only show their assigned items
if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'assistant') {
    $assignedRegionIds = [];
    if (!empty($tasks)) {
        // Filter tasks first to only keep the assistant's own tasks
        $tasks = array_filter($tasks, function($t) {
            return $t['assistant_id'] == $_SESSION['user_id'];
        });
        
        // Collect assigned region IDs from those tasks (single and grouped)
        foreach ($tasks as $t) {
            if (!empty($t['page_region_id'])) {
                $assignedRegionIds[] = $t['page_region_id'];
            }
            // Also collect IDs from grouped tasks
            if (!empty($t['grouped_region_ids'])) {
                $gids = array_filter(array_map('intval', explode(',', $t['grouped_region_ids'])));
                foreach ($gids as $gid) {
                    $assignedRegionIds[] = $gid;
                }
            }
        }
        $assignedRegionIds = array_unique($assignedRegionIds);
    }
    
    // Filter regions list to only include those assigned
    if (!empty($regions)) {
        $regions = array_filter($regions, function($r) use ($assignedRegionIds) {
            return in_array($r['region_id'], $assignedRegionIds);
        });
    }
}
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
    <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'assistant'): ?>
        <a href="<?= BASE_PATH ?>/index.php?controller=task&action=index" class="btn btn-secondary">&larr; Quay lại Danh sách Công việc</a>
    <?php else: ?>
        <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=show&id=<?= htmlspecialchars($chapter['chapter_id']) ?>" class="btn btn-secondary">&larr; Quay lại Chapter</a>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka'): ?>
    <div>
        <?php if (!$isLocked && !$this->isSeriesLocked($series)): ?>
            <!-- Nút sửa trang hiện tại -->
            <a href="<?= BASE_PATH ?>/index.php?controller=page&action=edit&id=<?= $page['page_id'] ?>" class="btn btn-warning">Sửa trang</a>
            <!-- Form xóa trang, dùng onsubmit để hỏi lại trước khi xóa -->
            <form action="<?= BASE_PATH ?>/index.php?controller=page&action=delete&id=<?= $page['page_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa trang này?');">
                <button type="submit" class="btn btn-danger">Xóa</button>
            </form>
        <?php else: ?>
            <?php 
                $lockMsg = 'Trang đã hoàn thành (Khóa)';
                if ($this->isSeriesLocked($series)) {
                    if ($series['status'] === 'suspended') $lockMsg = 'Bộ truyện đang tạm ngưng (Khóa)';
                    elseif ($series['status'] === 'canceled') $lockMsg = 'Bộ truyện đã hủy (Khóa)';
                    elseif ($series['status'] === 'completed') $lockMsg = 'Bộ truyện đã hoàn thành (Khóa)';
                } elseif ($this->isChapterLocked($chapter)) {
                    $lockMsg = 'Chương chứa trang đang chờ duyệt (Khóa)';
                }
            ?>
            <span class="badge bg-warning text-dark p-2 border border-warning"><i class="fas fa-lock me-1"></i><?= $lockMsg ?></span>
        <?php endif; ?>
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
        <div class="row">
            <div class="col-md-4">
                <p><strong>Trạng thái:</strong> <?= $this->getPageStatusBadge($page['status'], $chapter['status']) ?></p>
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
        <!-- Hộp hiển thị mô tả công việc của phân vùng đang được chọn (nằm riêng bên ngoài) -->
        <div id="selectedTaskDetailsBox" class="card border-0 shadow-sm mb-3 text-start d-none" style="background-color: #ffffff; border-radius: 12px; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03) !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-primary fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        YÊU CẦU CÔNG VIỆC CỦA PHÂN VÙNG
                    </span>
                    <button type="button" class="btn-close text-slate-400" style="font-size: 0.72rem; box-shadow: none;" onclick="closeSelectedTaskBox()"></button>
                </div>
                <h4 id="selectedTaskTitle" class="fw-bold text-slate-900 mb-3" style="font-size: 1.15rem; line-height: 1.35; letter-spacing: -0.01em;">Tiêu đề công việc</h4>
                <hr class="my-2 border-slate-200" style="opacity: 0.08;">
                
                <div class="d-flex flex-wrap gap-3 mb-2 text-xs" style="font-size: 0.72rem;">
                    <div><i class="fas fa-tag text-slate-400 me-1"></i>Loại: <span id="selectedTaskType" class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 font-semibold">Vẽ nền</span></div>
                    <div><i class="fas fa-user-circle text-slate-400 me-1"></i>Trợ lý: <strong id="selectedTaskAssistant" class="text-slate-700">Assistant One</strong></div>
                    <div><i class="fas fa-exclamation-triangle text-slate-400 me-1"></i>Độ ưu tiên: <span id="selectedTaskPriority" class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5">Trung bình</span></div>
                    <div><i class="far fa-calendar-alt text-slate-400 me-1"></i>Hạn chót: <strong id="selectedTaskDueDate" class="text-slate-700">09/07/2026</strong></div>
                </div>
                
                <hr class="my-2 border-slate-200" style="opacity: 0.08;">
                
                <div class="text-xs text-slate-500 font-semibold mb-1" style="font-size: 0.72rem;"><i class="fas fa-file-alt me-1 text-slate-400"></i>Mục tiêu & Yêu cầu chi tiết:</div>
                <div id="selectedTaskDescription" class="bg-slate-50 p-2.5 rounded border border-slate-100 text-slate-700 text-start overflow-y-auto mb-2" style="max-height: 180px; line-height: 1.5; font-size: 0.8rem; border-color: #e2e8f0 !important;">
                    Mô tả chi tiết...
                </div>

                <!-- Nút nộp bài dành cho Trợ lý -->
                <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'assistant'): ?>
                    <div class="mt-3 pt-2 text-end border-top" id="submissionButtonContainer" style="border-top-color: #f1f5f9 !important;">
                        <!-- Sẽ được fill bằng JavaScript -->
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-info">
            <div class="card-header bg-info text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-image me-2"></i>Bản vẽ trang truyện</h5>
            </div>
            <div class="card-body text-center bg-light d-flex flex-column align-items-center justify-content-center p-2" style="min-height: 400px;">
                <?php if (!empty($page['image_url'])): 
                    $resolvedImage = $this->resolvePageImageUrl($page['image_url']);
                    $oldImageUrl = $this->resolvePageImageUrl($page['old_image_url'] ?? '');
                ?>
                    <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked && !in_array($series['status'], ['suspended', 'canceled', 'completed'])): ?>
                    <div id="drawInstruction" class="alert alert-info py-2 mb-3 text-start w-100" style="font-size: 0.85rem;">
                        <i class="fas fa-info-circle me-2"></i><strong>Mẹo:</strong> Hãy rê chuột và kéo trên ảnh truyện bên dưới để vẽ phân vùng mới.
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($oldImageUrl)): ?>
                        <!-- Nút chuyển phiên bản bản vẽ cũ/mới -->
                        <div class="mb-3">
                            <div class="btn-group btn-group-sm shadow-sm" role="group">
                                <input type="radio" class="btn-check" name="page_version_toggle" id="btn-page-version-new" checked>
                                <label class="btn btn-outline-primary" for="btn-page-version-new"><i class="fas fa-image me-1"></i>Bản vẽ mới</label>

                                <input type="radio" class="btn-check" name="page_version_toggle" id="btn-page-version-old">
                                <label class="btn btn-outline-secondary" for="btn-page-version-old"><i class="fas fa-history me-1"></i>Bản vẽ cũ (Có lỗi)</label>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php
                        $canDraw = (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked && !in_array($series['status'], ['suspended', 'canceled', 'completed']));
                        $wrapperClasses = 'position-relative d-inline-block text-start';
                        if ($hasSpotlight) $wrapperClasses .= ' has-spotlight';
                        if ($canDraw) $wrapperClasses .= ' drawing-active';
                    ?>
                    <div id="mangaPageWrapper" class="<?= $wrapperClasses ?>" style="max-width: 100%; border: 1px solid #ccc; box-shadow: 0 4px 10px rgba(0,0,0,0.15); overflow: hidden;">
                        <img id="mangaPageImage" src="<?= htmlspecialchars($resolvedImage) ?>" alt="Page <?= htmlspecialchars($page['page_number']) ?>" class="img-fluid" style="display: block; max-width: 100%;">
                        
                        <?php if (!empty($regions)): ?>
                            <?php foreach ($regions as $region): 
                                // Tỷ lệ phần trăm dựa trên kích thước giả định 800 x 1000
                                $l = ($region['x'] / 800) * 100;
                                $t = ($region['y'] / 1000) * 100;
                                $w = ($region['width'] / 800) * 100;
                                $h = ($region['height'] / 1000) * 100;
                                
                                $borderColor = '#6c757d'; // Mặc định là xám cho custom type
                                $bgColor = 'rgba(108, 117, 125, 0.12)';
                                if ($region['region_type'] === 'panel') {
                                    $borderColor = '#dc3545'; // Đỏ cho panel
                                    $bgColor = 'rgba(220, 53, 69, 0.15)';
                                } elseif ($region['region_type'] === 'bubble') {
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
                                $isSpotlight = in_array($region['region_id'], $highlightRegionIds);
                                $spotlightClass = $isSpotlight ? ' assistant-spotlight' : '';
                                ?>
                                 <div class="ai-region-overlay page-region-overlay<?= $spotlightClass ?>" 
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

                        <?php if (!empty($editorAnnotations)): ?>
                            <?php foreach ($editorAnnotations as $index => $ann): 
                                $l = ($ann['x'] / 800) * 100;
                                $t = ($ann['y'] / 1000) * 100;
                                $w = ($ann['width'] / 800) * 100;
                                $h = ($ann['height'] / 1000) * 100;
                            ?>
                                <div class="editor-annotation-overlay" 
                                     style="position: absolute; left: <?= $l ?>%; top: <?= $t ?>%; width: <?= $w ?>%; height: <?= $h ?>%; border: 3px solid #dc3545; background-color: rgba(220, 53, 69, 0.15); cursor: help; z-index: 10;"
                                     data-bs-toggle="popover"
                                     data-bs-trigger="hover focus"
                                     data-bs-placement="top"
                                     data-bs-content="<?= htmlspecialchars($ann['comments']) ?>"
                                     title="Lỗi <?= $index + 1 ?> (<?= htmlspecialchars($ann['editor_name']) ?>)">
                                     <span class="badge bg-danger position-absolute top-0 start-0 translate-middle" style="font-size: 0.75rem; pointer-events: auto; z-index: 11;">
                                         <?= $index + 1 ?>
                                     </span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($editorAnnotations)): ?>
                        <div class="mt-4 w-100 text-start bg-white p-3 rounded shadow-sm border border-danger-subtle">
                            <h6 class="fw-bold text-danger mb-3 border-bottom border-danger-subtle pb-2"><i class="fas fa-exclamation-triangle me-2"></i>Lỗi Editor yêu cầu sửa đổi:</h6>
                            <ul class="list-unstyled mb-0 text-sm">
                                <?php foreach ($editorAnnotations as $index => $ann): ?>
                                    <li class="mb-3 pb-2 <?= $index < count($editorAnnotations) - 1 ? 'border-bottom border-slate-100' : '' ?>">
                                        <div class="d-flex align-items-start">
                                            <span class="badge bg-danger mt-1 me-2 shadow-sm">Lỗi <?= $index + 1 ?></span>
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($ann['editor_name']) ?> <span class="text-muted fw-normal fs-xs ms-1">đã ghi chú:</span></div>
                                                <div class="text-slate-700 mt-1" style="font-size: 0.85rem; line-height: 1.5;"><?= nl2br(htmlspecialchars($ann['comments'])) ?></div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-muted my-5">
                        <i class="fas fa-file-image fa-3x mb-3"></i>
                        <p>Trang này chưa có hình ảnh.</p>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked && !in_array($series['status'], ['suspended', 'canceled', 'completed'])): ?>
                    <hr class="my-4 text-slate-200">
                    <div class="w-100 text-start mt-2">
                        <h6 class="fw-bold text-primary mb-3"><i class="fas fa-upload me-2"></i>Cập nhật Bản vẽ Hoàn chỉnh (Genko)</h6>
                        <form action="<?= BASE_PATH ?>/index.php?controller=page&action=update&id=<?= $page['page_id'] ?>" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="page_number" value="<?= htmlspecialchars($page['page_number']) ?>">
                            <input type="hidden" name="status" value="<?= htmlspecialchars($page['status']) ?>">
                            <div class="upload-dropzone position-relative d-flex flex-column align-items-center justify-content-center border border-primary border-dashed rounded-3 p-4 bg-light text-center shadow-sm" style="cursor: pointer; min-height: 120px; transition: all 0.2s;">
                                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer; z-index: 2;" onchange="this.form.submit()">
                                <div class="upload-icon-wrapper mb-2" style="width: 40px; height: 40px; background: rgba(79, 70, 229, 0.1); color: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-cloud-upload-alt fs-5"></i>
                                </div>
                                <h6 class="fw-semibold mb-1" style="font-size: 0.9rem;">Kéo thả ảnh đã ghép vào đây để tải lên</h6>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">File ảnh sẽ được tự động lưu thay thế cho ảnh hiện tại.</p>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cột phải: Thông tin Phân Vùng Bản Vẽ Thủ Công -->
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom border-slate-100 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-slate-800 fw-bold" style="font-size: 1.05rem;"><i class="fas fa-crop me-2 text-primary"></i>Phân vùng bản vẽ</h5>
                <div class="d-flex gap-2">
                    <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked && !in_array($series['status'], ['suspended', 'canceled', 'completed'])): ?>
                        <button id="btnGroupAssign" class="btn btn-sm text-white px-3 rounded-pill fw-bold shadow-sm d-none" style="background: #6366f1; font-size: 0.78rem; border: none; transition: all 0.2s;" onclick="assignGroupedRegions()">
                            <i class="fas fa-layer-group me-1"></i>Giao việc nhóm
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body d-flex flex-column justify-content-between" style="min-height: 320px;">
                <?php if (empty($regions)): ?>
                    <div class="text-center my-auto py-5 d-flex flex-column align-items-center justify-content-center">
                        <div class="mb-3 d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 72px; height: 72px; background: rgba(79, 70, 229, 0.06); color: #4f46e5;">
                            <i class="fas fa-vector-square fa-2x"></i>
                        </div>
                        <h6 class="fw-bold text-slate-800 mb-2" style="font-size: 1.05rem;">Chưa có phân vùng nào</h6>
                        <p class="text-slate-500 px-4 mb-4 text-xs" style="max-width: 320px; line-height: 1.6; font-size: 0.8rem;">Hãy sử dụng bộ công cụ <strong>Vẽ thủ công</strong> chuyên nghiệp để tự vẽ và phân chia khung hình, ô thoại, nhân vật trên trang truyện.</p>
                        <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked && !in_array($series['status'], ['suspended', 'canceled', 'completed'])): ?>
                        <div class="d-flex gap-2 justify-content-center mt-2">
                            <span class="badge bg-primary px-3 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 0.8rem;">
                                <i class="fas fa-mouse-pointer me-1.5"></i>Rê chuột vào ảnh bên trái để vẽ
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div>
                        <p class="text-muted small mb-3">Các phân vùng bản vẽ hiện có. Bạn có thể chọn giao việc (Task) trực tiếp cho Assistant trên từng phân vùng.</p>
                        <div class="list-group" id="region-list-group">
                            <?php foreach ($regions as $region): 
                                $typeLabel = htmlspecialchars($region['region_type']);
                                $typeClass = 'bg-secondary';
                                $rowBorder = 'border-start border-secondary border-4';
                                
                                if ($region['region_type'] === 'panel') {
                                    $typeLabel = 'Khung truyện';
                                    $typeClass = 'bg-danger';
                                    $rowBorder = 'border-start border-danger border-4';
                                } elseif ($region['region_type'] === 'bubble') {
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
                                     onclick="highlightCanvasOverlay(<?= $region['region_id'] ?>)"
                                     onmouseenter="hoverOverlay(<?= $region['region_id'] ?>, true)"
                                     onmouseleave="hoverOverlay(<?= $region['region_id'] ?>, false)"
                                     style="cursor: pointer;">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h6 class="mb-1 fw-bold text-dark d-flex align-items-center">
                                            <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked && !in_array($series['status'], ['suspended', 'canceled', 'completed'])): ?>
                                                <input type="checkbox" class="form-check-input region-select-cb me-2" value="<?= $region['region_id'] ?>" onclick="event.stopPropagation(); updateGroupButton();" style="width: 15px; height: 15px; cursor: pointer; margin-top: 0;">
                                            <?php endif; ?>
                                            <span class="badge <?= $typeClass ?> me-2"><?= $typeLabel ?></span>
                                            ID #<?= $region['region_id'] ?>
                                        </h6>
                                        <small class="text-success fw-bold"><i class="fas fa-user-edit me-1"></i>Vẽ thủ công</small>
                                    </div>
                                    <p class="mb-1 text-muted small">
                                        Tọa độ: X:<?= $region['x'] ?>, Y:<?= $region['y'] ?> | Kích thước: <?= $region['width'] ?>x<?= $region['height'] ?>
                                    </p>
                                    
                                    <!-- Hiển thị công việc đã giao cho phân vùng này -->
                                    <?php
                                    // Khớp cả task đơn lẻ và task nhóm có chứa phân vùng này
                                    $regionTasks = array_filter($tasks, function($t) use ($region) {
                                        if (!empty($t['page_region_id']) && $t['page_region_id'] == $region['region_id']) return true;
                                        if (!empty($t['grouped_region_ids'])) {
                                            $gids = array_map('trim', explode(',', $t['grouped_region_ids']));
                                            if (in_array((string)$region['region_id'], $gids)) return true;
                                        }
                                        return false;
                                    });
                                    $hasRegionTask = !empty($regionTasks);
                                    if ($hasRegionTask):
                                    ?>
                                        <div class="mt-2 mb-2 p-2 rounded border" style="font-size: 0.78rem; background-color: #f8fafc; border-color: #e2e8f0 !important;">
                                            <?php foreach ($regionTasks as $rt):
                                                $rtColor = 'secondary';
                                                $rtLabel = $rt['status'];
                                                if ($rt['status'] == 'completed') { $rtColor = 'success'; $rtLabel = 'Hoàn thành'; }
                                                elseif ($rt['status'] == 'submitted') { $rtColor = 'info'; $rtLabel = 'Chờ duyệt'; }
                                                elseif ($rt['status'] == 'rejected') { $rtColor = 'danger'; $rtLabel = 'Yêu cầu sửa'; }
                                                elseif ($rt['status'] == 'in_progress') { $rtColor = 'primary'; $rtLabel = 'Đang làm'; }
                                                else { $rtColor = 'warning text-dark'; $rtLabel = 'Chờ xử lý'; }
                                                $isGroupTask = !empty($rt['grouped_region_ids']);
                                            ?>
                                                 <div class="d-flex align-items-center justify-content-between mb-1">
                                                     <span class="fw-semibold text-slate-800" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px;" title="<?= htmlspecialchars($rt['title']) ?>">
                                                         <i class="fas <?= $isGroupTask ? 'fa-layer-group' : 'fa-tasks' ?> me-1 text-indigo-500"></i><?= htmlspecialchars($rt['title']) ?>
                                                     </span>
                                                     <div class="d-flex align-items-center gap-1">
                                                         <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'assistant' && $rt['status'] !== 'completed'): ?>
                                                             <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=create&task_id=<?= $rt['task_id'] ?>" class="text-success text-decoration-none fw-bold" onclick="event.stopPropagation();" style="font-size: 10px;">Nộp bài</a>
                                                         <?php endif; ?>
                                                         <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka'): ?>
                                                             <a href="<?= BASE_PATH ?>/index.php?controller=task&action=edit&id=<?= $rt['task_id'] ?>" class="text-warning text-decoration-none fw-bold" onclick="event.stopPropagation();" style="font-size: 10px;" title="Chỉnh sửa công việc">Sửa</a>
                                                         <?php endif; ?>
                                                         <?php if (!empty($rt['description'])): ?>
                                                             <button class="btn btn-link btn-xs p-0 text-decoration-none text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#region-task-desc-<?= $rt['task_id'] ?>" onclick="event.stopPropagation();" aria-expanded="false" aria-controls="region-task-desc-<?= $rt['task_id'] ?>" title="Xem yêu cầu" style="font-size: 10px; font-weight: 500; box-shadow: none;">Chi tiết</button>
                                                         <?php endif; ?>
                                                         <span class="badge bg-<?= $rtColor ?> rounded-pill py-0.5 px-1.5" style="font-size: 8px;"><?= $rtLabel ?></span>
                                                     </div>
                                                 </div>
                                                 <?php if ($isGroupTask): ?>
                                                     <div class="text-slate-400 mb-1" style="font-size: 0.68rem; padding-left: 14px;">
                                                         <i class="fas fa-layer-group me-1"></i>Nhóm vùng: #<?= implode(', #', array_filter(array_map('trim', explode(',', $rt['grouped_region_ids'])))) ?>
                                                     </div>
                                                 <?php endif; ?>
                                                 <?php if (!empty($rt['description'])): ?>
                                                     <div class="collapse mt-1 mb-2" id="region-task-desc-<?= $rt['task_id'] ?>" onclick="event.stopPropagation();">
                                                         <div class="card card-body bg-light p-2 border-light text-slate-600 text-start" style="font-size: 0.72rem; line-height: 1.4; border-radius: 6px; max-height: 120px; overflow-y: auto;">
                                                             <strong>Yêu cầu:</strong><br>
                                                             <?= renderMarkdown($rt['description']) ?>
                                                         </div>
                                                     </div>
                                                 <?php endif; ?>
                                                 <div class="text-slate-500 d-flex justify-content-between align-items-center mb-2" style="font-size: 0.7rem; padding-left: 14px;">
                                                     <span><i class="fas fa-user-circle me-1 text-slate-400"></i>Trợ lý: <strong><?= htmlspecialchars($rt['assistant_name'] ?? 'Chưa rõ') ?></strong></span>
                                                 </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="badge bg-light text-dark border">Vẽ tay</span>
                                        <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked && !in_array($series['status'], ['suspended', 'canceled', 'completed'])): ?>
                                        <div class="btn-group" onclick="event.stopPropagation();">
                                            <?php if ($hasRegionTask): ?>
                                                <?php $firstTask = reset($regionTasks); ?>
                                                <a href="<?= BASE_PATH ?>/index.php?controller=task&action=edit&id=<?= $firstTask['task_id'] ?>" class="btn btn-xs btn-outline-warning py-0 px-2" style="font-size: 11px;" title="Chỉnh sửa công việc đã giao">
                                                    <i class="fas fa-edit me-1"></i>Sửa việc
                                                </a>
                                                <a href="<?= BASE_PATH ?>/index.php?controller=task&action=create&page_id=<?= $page['page_id'] ?>&page_region_id=<?= $region['region_id'] ?>" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 11px;" title="Thêm một công việc khác cho phân vùng này">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= BASE_PATH ?>/index.php?controller=task&action=create&page_id=<?= $page['page_id'] ?>&page_region_id=<?= $region['region_id'] ?>" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 11px;">
                                                    <i class="fas fa-plus me-1"></i>Giao việc
                                                </a>
                                            <?php endif; ?>
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

        <!-- Khối Ghi chú lỗi của Editor (nếu có) -->
        <?php if (!empty($editorAnnotations)): ?>
        <?php 
            $isUpdatedAfterAnnotation = $this->isPageUpdatedAfterLatestAnnotation($page, $editorAnnotations);
        ?>
        <div class="card border-danger mt-3">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Ý kiến sửa đổi từ Editor</h5>
                <?php if ($isUpdatedAfterAnnotation): ?>
                    <span class="badge bg-warning text-dark"><i class="fas fa-sync-alt me-1"></i>Đã cập nhật ảnh sửa</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if ($isUpdatedAfterAnnotation): ?>
                    <div class="alert alert-warning py-2 mb-3 small d-flex align-items-center" style="font-size: 0.825rem; border-radius: var(--radius-sm);">
                        <i class="fas fa-info-circle me-2"></i>
                        <span>Bạn đã tải lên bản vẽ mới sau các ghi chú lỗi này. Hãy chờ Editor kiểm tra và phê duyệt.</span>
                    </div>
                <?php endif; ?>
                <p class="text-muted small mb-3">Biên tập viên yêu cầu chỉnh sửa các vị trí được khoanh đỏ nét đứt trên trang truyện:</p>
                <div class="list-group">
                    <?php foreach ($editorAnnotations as $ann): ?>
                        <div class="list-group-item list-group-item-danger px-3 py-2 border-start border-danger border-4 mb-2 rounded shadow-sm">
                            <span class="fw-bold text-danger text-xs d-block"><i class="fas fa-user-edit me-1"></i>Editor: <?= htmlspecialchars($ann['editor_name']) ?></span>
                            <p class="mb-0 text-dark small" style="white-space: pre-wrap; font-size: 0.85rem;"><?= htmlspecialchars($ann['comments']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

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

/* Spotlight mode for Assistant */
.has-spotlight .ai-region-overlay:not(.assistant-spotlight) {
    opacity: 0.15;
    border-style: dotted !important;
    background-color: rgba(0, 0, 0, 0.05) !important;
    pointer-events: none;
}
.has-spotlight .editor-annotation-overlay {
    opacity: 0.25;
}
.has-spotlight .editor-annotation-overlay:hover {
    opacity: 1.0;
}
.assistant-spotlight {
    outline: 9999px solid rgba(0, 0, 0, 0.65) !important;
    border: 3px solid #ffc107 !important; /* Gold border */
    box-shadow: 0 0 20px rgba(255, 193, 7, 0.9) !important;
    z-index: 1050 !important;
    animation: pulse-spotlight 2s infinite ease-in-out;
}
@keyframes pulse-spotlight {
    0% { box-shadow: 0 0 15px rgba(255, 193, 7, 0.7); }
    50% { box-shadow: 0 0 25px rgba(255, 193, 7, 1.0); }
    100% { box-shadow: 0 0 15px rgba(255, 193, 7, 0.7); }
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
                            <option value="other">Khác (Tự nhập)...</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="custom_type_container">
                        <label for="custom_reg_type" class="form-label fw-bold" style="font-size: 0.9rem;">Nhập loại phân vùng tự chọn <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="custom_reg_type" name="custom_region_type" placeholder="Ví dụ: Đạo cụ, Vũ khí, v.v.">
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
<?php
// Chuẩn bị dữ liệu JSON của các task để đẩy vào JS
$jsTasks = [];
if (!empty($tasks)) {
    foreach ($tasks as $t) {
        $jsTasks[] = [
            'task_id' => $t['task_id'],
            'page_region_id' => $t['page_region_id'],
            'grouped_region_ids' => $t['grouped_region_ids'] ?? null,
            'title' => $t['title'],
            'description' => renderMarkdown($t['description']),
            'task_type' => $t['task_type'],
            'priority' => $t['priority'],
            'status' => $t['status'],
            'due_date' => $t['due_date'] ? date('d/m/Y H:i', strtotime($t['due_date'])) : 'Không có',
            'assistant_name' => $t['assistant_name'] ?? 'Chưa rõ'
        ];
    }
}
?>
const BASE_PATH = '<?= BASE_PATH ?>';
const pageTasksData = <?= json_encode($jsTasks) ?>;

function closeSelectedTaskBox() {
    document.getElementById('selectedTaskDetailsBox').classList.add('d-none');
    
    // Khôi phục độ mờ của danh sách card bên phải
    const listGroup = document.getElementById('region-list-group');
    if (listGroup) {
        listGroup.classList.remove('has-selected');
    }
    document.querySelectorAll('.list-group-item-action').forEach(el => {
        el.classList.remove('selected-card');
    });

    // Khôi phục độ mờ của các phân vùng vẽ bên trái
    const wrapper = document.getElementById('mangaPageWrapper');
    if (wrapper) {
        wrapper.classList.remove('has-selected-overlay');
    }
    document.querySelectorAll('.page-region-overlay').forEach(el => {
        el.classList.remove('selected-overlay');
    });
}

function updateSelectedTaskBox(regionId) {
    // 1. Quản lý các card bên phải (Spotlight Focus)
    const listGroup = document.getElementById('region-list-group');
    if (listGroup) {
        listGroup.classList.add('has-selected');
    }
    document.querySelectorAll('.list-group-item-action').forEach(el => {
        el.classList.remove('selected-card');
    });
    const activeCard = document.getElementById('list-region-' + regionId);
    if (activeCard) {
        activeCard.classList.add('selected-card');
    }

    // 2. Quản lý các phân vùng vẽ trên canvas bên trái (Spotlight Focus)
    const wrapper = document.getElementById('mangaPageWrapper');
    if (wrapper) {
        wrapper.classList.add('has-selected-overlay');
    }
    document.querySelectorAll('.page-region-overlay').forEach(el => {
        el.classList.remove('selected-overlay');
    });
    const activeOverlay = document.getElementById('overlay-region-' + regionId);
    if (activeOverlay) {
        activeOverlay.classList.add('selected-overlay');
    }

    // Match both single-region tasks and group tasks that contain this region
    const matchedTasks = pageTasksData.filter(t => {
        if (t.page_region_id == regionId) return true;
        if (t.grouped_region_ids) {
            const gids = t.grouped_region_ids.split(',').map(s => s.trim());
            if (gids.includes(String(regionId))) return true;
        }
        return false;
    });
    const infoBox = document.getElementById('selectedTaskDetailsBox');
    if (matchedTasks.length > 0) {
        const task = matchedTasks[0];
        
        document.getElementById('selectedTaskTitle').innerText = task.title;
        
        let typeLabel = task.task_type;
        if (task.title.indexOf('(Nhóm:') !== -1) typeLabel = 'Tổ hợp (Group)';
        else if (task.task_type === 'background') typeLabel = 'Vẽ nền (Background)';
        else if (task.task_type === 'inking') typeLabel = 'Đi nét (Inking)';
        else if (task.task_type === 'coloring') typeLabel = 'Lên màu (Coloring)';
        else if (task.task_type === 'effects') typeLabel = 'Hiệu ứng (Effects)';
        else typeLabel = task.task_type.charAt(0).toUpperCase() + task.task_type.slice(1);
        
        document.getElementById('selectedTaskType').innerText = typeLabel;
        document.getElementById('selectedTaskAssistant').innerText = task.assistant_name;
        
        const priorityEl = document.getElementById('selectedTaskPriority');
        priorityEl.innerText = task.priority === 'high' ? 'Cao' : (task.priority === 'medium' ? 'Trung bình' : (task.priority === 'low' ? 'Thấp' : 'Thường'));
        priorityEl.className = 'badge px-2 py-0.5 border ' + 
            (task.priority === 'high' ? 'bg-danger-subtle text-danger border-danger-subtle' : 
            (task.priority === 'medium' ? 'bg-warning-subtle text-warning border-warning-subtle' : 'bg-info-subtle text-info border-info-subtle'));
            
        document.getElementById('selectedTaskDueDate').innerText = task.due_date;
        document.getElementById('selectedTaskDescription').innerHTML = task.description || '<em class="text-muted">Không có mô tả chi tiết.</em>';
        
        // Cập nhật nút nộp bài cho trợ lý
        const submissionContainer = document.getElementById('submissionButtonContainer');
        if (submissionContainer) {
            if (task.status === 'completed') {
                submissionContainer.innerHTML = `
                    <span class="badge bg-success-subtle text-success border border-success-subtle py-1.5 px-3 d-inline-flex align-items-center" style="border-radius: 8px; font-size: 0.75rem; font-weight: 600;">
                        <i class="fas fa-check-circle me-1.5 text-success"></i>Công việc đã hoàn thành
                    </span>`;
            } else if (task.status === 'submitted') {
                submissionContainer.innerHTML = `
                    <span class="badge bg-info-subtle text-info border border-info-subtle py-1.5 px-3 d-inline-flex align-items-center" style="border-radius: 8px; font-size: 0.75rem; font-weight: 600; margin-right: 8px;">
                        <i class="fas fa-spinner fa-spin me-1.5 text-info"></i>Đang chờ duyệt bài
                    </span>
                    <a href="${BASE_PATH}/index.php?controller=submission&action=create&task_id=${task.task_id}" class="btn btn-sm btn-outline-success py-1.5 px-3" style="border-radius: 8px; font-size: 0.75rem; font-weight: 500;">
                        Nộp lại bản thảo mới
                    </a>`;
            } else if (task.status === 'rejected') {
                submissionContainer.innerHTML = `
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1.5 px-3 d-inline-flex align-items-center" style="border-radius: 8px; font-size: 0.75rem; font-weight: 600; margin-right: 8px;">
                        <i class="fas fa-exclamation-triangle me-1.5 text-danger"></i>Mangaka yêu cầu sửa lại (Bị từ chối)
                    </span>
                    <a href="${BASE_PATH}/index.php?controller=submission&action=create&task_id=${task.task_id}" class="btn btn-sm btn-success py-1.5 px-3 d-inline-flex align-items-center" style="border-radius: 8px; font-size: 0.75rem; font-weight: 500; background-color: #10b981; border-color: #10b981; transition: all 0.2s;">
                        <i class="fas fa-paper-plane me-1.5"></i>Nộp bài làm (Submit)
                    </a>`;
            } else {
                submissionContainer.innerHTML = `
                    <a href="${BASE_PATH}/index.php?controller=submission&action=create&task_id=${task.task_id}" class="btn btn-sm btn-success py-1.5 px-3 d-inline-flex align-items-center" style="border-radius: 8px; font-size: 0.75rem; font-weight: 500; background-color: #10b981; border-color: #10b981; transition: all 0.2s;">
                        <i class="fas fa-paper-plane me-1.5"></i>Nộp bài làm (Submit)
                    </a>`;
            }
        }

        infoBox.classList.remove('d-none');
    } else {
        infoBox.classList.add('d-none');
    }
}

function hoverOverlay(regionId, isHover) {
    const overlay = document.getElementById('overlay-region-' + regionId);
    const listItem = document.getElementById('list-region-' + regionId);
    if (overlay) {
        if (isHover) {
            overlay.style.borderStyle = 'solid';
            overlay.style.borderWidth = '3px';
            overlay.style.backgroundColor = overlay.style.backgroundColor.replace('0.12', '0.35').replace('0.15', '0.35');
            overlay.style.boxShadow = '0 0 15px ' + overlay.style.borderColor;
            overlay.style.zIndex = '10';
        } else {
            overlay.style.borderStyle = 'dashed';
            overlay.style.borderWidth = '2px';
            overlay.style.backgroundColor = overlay.style.backgroundColor.replace('0.35', '0.15').replace('0.35', '0.12');
            overlay.style.boxShadow = 'none';
            overlay.style.zIndex = '1';
        }
    }
    if (listItem) {
        if (isHover) {
            listItem.classList.add('active-region');
            listItem.style.backgroundColor = '#f0f4f8';
            listItem.style.borderColor = '#4f46e5';
            listItem.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
        } else {
            listItem.classList.remove('active-region');
            listItem.style.backgroundColor = '';
            listItem.style.borderColor = '';
            listItem.style.boxShadow = '';
        }
    }
}

function highlightCanvasOverlay(regionId) {
    updateSelectedTaskBox(regionId);

    const card = document.getElementById('list-region-' + regionId);
    if (card) {
        const collapses = card.querySelectorAll('.collapse');
        collapses.forEach(c => {
            const bsCollapse = bootstrap.Collapse.getInstance(c) || new bootstrap.Collapse(c);
            bsCollapse.toggle();
        });
    }

    const overlay = document.getElementById('overlay-region-' + regionId);
    if (overlay) {
        overlay.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        let count = 0;
        const oldBorder = overlay.style.borderStyle;
        const oldWidth = overlay.style.borderWidth;
        const oldBg = overlay.style.backgroundColor;
        
        const interval = setInterval(() => {
            if (count % 2 === 0) {
                overlay.style.borderStyle = 'solid';
                overlay.style.borderWidth = '4px';
                overlay.style.backgroundColor = oldBg.replace('0.12', '0.5').replace('0.15', '0.5');
                overlay.style.boxShadow = '0 0 25px ' + overlay.style.borderColor;
            } else {
                overlay.style.borderStyle = 'dashed';
                overlay.style.borderWidth = '2px';
                overlay.style.backgroundColor = oldBg;
                overlay.style.boxShadow = 'none';
            }
            count++;
            if (count > 6) {
                clearInterval(interval);
                overlay.style.borderStyle = oldBorder;
                overlay.style.borderWidth = oldWidth;
                overlay.style.backgroundColor = oldBg;
                overlay.style.boxShadow = 'none';
            }
        }, 200);
    }
}

function highlightTableRecord(regionIdOrList) {
    // Support both single regionId (number) and comma-separated string (group)
    const ids = String(regionIdOrList).split(',').map(s => s.trim()).filter(Boolean);
    if (ids.length === 0) return;

    // Use the first ID for task box selection
    updateSelectedTaskBox(ids[0]);

    ids.forEach(regionId => {
        const listItem = document.getElementById('list-region-' + regionId);
        if (listItem) {
            listItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            // Tự động mở rộng các collapse mô tả bên trong
            const collapses = listItem.querySelectorAll('.collapse');
            collapses.forEach(c => {
                const bsCollapse = bootstrap.Collapse.getInstance(c) || new bootstrap.Collapse(c, { toggle: false });
                bsCollapse.show();
            });

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
    });
}

function hoverGroupedOverlays(idsStr, isHover) {
    const ids = String(idsStr).split(',').map(s => s.trim()).filter(Boolean);
    ids.forEach(id => hoverOverlay(id, isHover));
}

// Xử lý vẽ phân vùng thủ công bằng cách nhấn giữ và kéo chuột
let isDrawingMode = <?= isset($canDraw) && $canDraw ? 'true' : 'false' ?>;
let isDragging = false;
let startX = 0, startY = 0;
let selectionBox = null;
const wrapper = document.getElementById('mangaPageWrapper');
const img = document.getElementById('mangaPageImage');
const drawInstruction = document.getElementById('drawInstruction');

if (wrapper && img) {
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
            }, { once: true });
        } else {
            if (selectionBox && selectionBox.parentNode) {
                selectionBox.parentNode.removeChild(selectionBox);
            }
            selectionBox = null;
        }
    });
}

// Kích hoạt bootstrap popovers để xem ghi chú Editor khi rê chuột
document.addEventListener("DOMContentLoaded", function() {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });

    // Xử lý chuyển đổi nhanh phiên bản vẽ cũ/mới của Mangaka
    const btnPageNew = document.getElementById('btn-page-version-new');
    const btnPageOld = document.getElementById('btn-page-version-old');
    const mangaPageImage = document.getElementById('mangaPageImage');
    
    if (btnPageNew && btnPageOld && mangaPageImage) {
        const currentImgUrl = <?= json_encode($resolvedImage) ?>;
        const oldImgUrl = <?= json_encode($oldImageUrl) ?>;
        
        btnPageNew.addEventListener('change', function() {
            if (this.checked) {
                mangaPageImage.src = currentImgUrl;
                // Khôi phục con trỏ vẽ hoặc phân vùng
                const overlay = document.getElementById('annoOverlayContainer');
                if (overlay) overlay.style.pointerEvents = 'auto';
            }
        });
        
        btnPageOld.addEventListener('change', function() {
            if (this.checked) {
                mangaPageImage.src = oldImgUrl;
                // Chặn tương tác vẽ trên bản vẽ cũ
                const overlay = document.getElementById('annoOverlayContainer');
                if (overlay) overlay.style.pointerEvents = 'none';
            }
        });
    }

    // Xử lý loại phân vùng tự chọn
    const regTypeSelect = document.getElementById('reg_type');
    const customTypeContainer = document.getElementById('custom_type_container');
    const customTypeInput = document.getElementById('custom_reg_type');

    if (regTypeSelect && customTypeContainer && customTypeInput) {
        regTypeSelect.addEventListener('change', function() {
            if (regTypeSelect.value === 'other') {
                customTypeContainer.classList.remove('d-none');
                customTypeInput.required = true;
            } else {
                customTypeContainer.classList.add('d-none');
                customTypeInput.required = false;
            }
        });
    }

    <?php if ($highlightRegionId): ?>
        // Tự động cuộn đến, tô màu viền/bóng và mở rộng chi tiết công việc của phân vùng được highlight
        const targetRegionId = <?= intval($highlightRegionId) ?>;
        setTimeout(() => {
            const card = document.getElementById('list-region-' + targetRegionId);
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Mở rộng tất cả collapse mô tả bên trong
                const collapses = card.querySelectorAll('.collapse');
                collapses.forEach(c => {
                    const bsCollapse = bootstrap.Collapse.getInstance(c) || new bootstrap.Collapse(c, { toggle: false });
                    bsCollapse.show();
                });
            }
            // Kích hoạt hiệu ứng nhấp nháy trên canvas
            highlightCanvasOverlay(targetRegionId);
        }, 500);
    <?php endif; ?>
});

function updateGroupButton() {
    const checked = document.querySelectorAll('.region-select-cb:checked');
    const btn = document.getElementById('btnGroupAssign');
    if (btn) {
        if (checked.length > 0) {
            btn.classList.remove('d-none');
        } else {
            btn.classList.add('d-none');
        }
    }
}

function assignGroupedRegions() {
    const checked = document.querySelectorAll('.region-select-cb:checked');
    const ids = Array.from(checked).map(cb => cb.value).join(',');
    const pageId = "<?= $page['page_id'] ?>";
    window.location.href = `<?= BASE_PATH ?>/index.php?controller=task&action=create&page_id=${pageId}&grouped_region_ids=${ids}`;
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
        <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked && !in_array($series['status'], ['suspended', 'canceled', 'completed'])): ?>
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
                            if (!empty($task['grouped_region_ids'])) {
                                $escapedIds = htmlspecialchars($task['grouped_region_ids'], ENT_QUOTES);
                                $hoverAttr = ' onmouseenter="hoverGroupedOverlays(\'' . $escapedIds . '\', true)" onmouseleave="hoverGroupedOverlays(\'' . $escapedIds . '\', false)"';
                            } elseif (!empty($task['page_region_id'])) {
                                $hoverAttr = ' onmouseenter="hoverOverlay(' . $task['page_region_id'] . ', true)" onmouseleave="hoverOverlay(' . $task['page_region_id'] . ', false)"';
                            }
                        ?>
                            <tr<?= $hoverAttr ?>>
                                <!-- Tiêu đề task -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                            <strong><?= htmlspecialchars($task['title']) ?></strong>
                                            <?php if (!empty($task['description'])): ?>
                                                <button class="btn btn-link btn-xs p-0 text-decoration-none text-primary ms-1" type="button" data-bs-toggle="collapse" data-bs-target="#task-desc-<?= $task['task_id'] ?>" aria-expanded="false" aria-controls="task-desc-<?= $task['task_id'] ?>" title="Xem chi tiết mô tả" style="font-size: 0.75rem;">
                                                    <i class="fas fa-info-circle me-1"></i>Chi tiết
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($task['description'])): ?>
                                            <div class="collapse mt-2" id="task-desc-<?= $task['task_id'] ?>">
                                                <div class="card card-body bg-light p-2.5 border-slate-100 text-muted" style="font-size: 0.8rem; max-width: 400px; max-height: 250px; overflow-y: auto; line-height: 1.5; border-radius: 8px;">
                                                    <?= renderMarkdown($task['description']) ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($task['resource_url'])): ?>
                                            <small class="text-muted mt-1"><i class="fas fa-link me-1"></i>Tài nguyên: <a href="<?= htmlspecialchars($task['resource_url']) ?>" target="_blank">Xem link</a></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <!-- Loại công việc -->
                                <td>
                                    <?php
                                    $typeLabel = htmlspecialchars($task['task_type'] ?? 'Khác');
                                    $typeBadge = 'bg-secondary';
                                    if (!empty($task['grouped_region_ids']) || strpos($task['title'], '(Nhóm:') !== false) {
                                        $typeLabel = 'Tổ hợp (Group)';
                                        $typeBadge = 'bg-primary';
                                    } else {
                                        switch ($task['task_type']) {
                                            case 'background': $typeLabel = 'Vẽ nền'; $typeBadge = 'bg-dark'; break;
                                            case 'inking': $typeLabel = 'Đi nét'; $typeBadge = 'bg-secondary'; break;
                                            case 'coloring': $typeLabel = 'Lên màu'; $typeBadge = 'bg-success'; break;
                                            case 'effects': $typeLabel = 'Hiệu ứng'; $typeBadge = 'bg-info text-dark'; break;
                                            case 'other': $typeLabel = 'Khác'; $typeBadge = 'bg-secondary'; break;
                                        }
                                    }
                                    ?>
                                    <span class="badge <?= $typeBadge ?>"><?= $typeLabel ?></span>
                                </td>
                                <!-- Phân vùng -->
                                <td>
                                    <?php if (!empty($task['grouped_region_ids'])): ?>
                                        <?php
                                        $gidArr = array_filter(array_map('trim', explode(',', $task['grouped_region_ids'])));
                                        $gidList = implode(', ', array_map(fn($id) => '#'.$id, $gidArr));
                                        $gidStr = htmlspecialchars($task['grouped_region_ids']);
                                        ?>
                                        <span class="badge bg-primary text-white" style="cursor: pointer;" onclick="highlightTableRecord('<?= $gidStr ?>')" title="Click để highlight nhóm vùng này">
                                            <i class="fas fa-layer-group me-1" style="font-size: 9px;"></i>Nhóm: <?= $gidList ?>
                                        </span>
                                    <?php elseif (!empty($task['page_region_id'])): ?>
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
                                    elseif ($task['status'] == 'submitted') { $sColor = 'info'; $sLabel = 'Chờ duyệt'; }
                                    elseif ($task['status'] == 'in_progress') { $sColor = 'primary'; $sLabel = 'Đang làm'; }
                                    else { $sColor = 'warning text-dark'; $sLabel = 'Chờ xử lý'; }
                                    ?>
                                    <span class="badge bg-<?= $sColor ?>"><?= htmlspecialchars($sLabel) ?></span>
                                </td>
                                <!-- Hạn chót, định dạng d/m/Y -->
                                <td><?= $task['due_date'] ? htmlspecialchars(date('d/m/Y', strtotime($task['due_date']))) : '<span class="text-muted">Không có</span>' ?></td>
                                <!-- Các nút thao tác Sửa và Xóa dành cho Mangaka -->
                                 <td>
                                     <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka' && !$isLocked && !in_array($series['status'], ['suspended', 'canceled', 'completed'])): ?>
                                     <!-- Nút Sửa chuyển hướng sang TaskController@edit -->
                                     <a href="<?= BASE_PATH ?>/index.php?controller=task&action=edit&id=<?= $task['task_id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                                     <!-- Nút Xóa thực hiện qua form POST để bảo mật -->
                                     <form action="<?= BASE_PATH ?>/index.php?controller=task&action=delete&id=<?= $task['task_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa công việc này?');">
                                         <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                     </form>
                                     <?php else: ?>
                                         <span class="text-muted small"><?= ($isLocked || in_array($series['status'], ['suspended', 'canceled', 'completed'])) ? 'Khóa' : '-' ?></span>
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
