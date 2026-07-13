<?php
/**
 * View: Giao diện nộp sản phẩm cho công việc (upload_submission.php)
 * Vai trò: Assistant (Trợ lý)
 * Chức năng: Cho phép trợ lý tải lên file bản thảo (hình ảnh, tài liệu, zip) cho công việc được giao.
 * 
 * @var array $tasks Danh sách các công việc (Task) được phân công đang chờ xử lý
 */
$pageTitle = 'Nộp sản phẩm Task';
$current_page = 'submissions';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Nộp kết quả công việc (Task Submission)</h2>
        <p class="text-muted text-xs mb-0">Tải lên file bản vẽ hoặc tài nguyên đã hoàn thành cho nhiệm vụ được giao.</p>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=index" class="btn btn-outline-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left me-2"></i>Lịch sử nộp bài
    </a>
</div>

<div class="row">
    <!-- Cột trái: Form nộp bài -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                <h5 class="card-title mb-0"><i class="fas fa-cloud-upload-alt me-2 text-primary"></i>Chi tiết báo cáo công việc</h5>
            </div>
            <div class="card-body p-4">
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_PATH ?>/index.php?controller=submission&action=store" method="POST" enctype="multipart/form-data">
                    
                    <!-- Chọn Nhiệm vụ (Task) -->
                    <div class="mb-4">
                        <label for="task_id" class="form-label fw-bold text-dark"><i class="fas fa-tasks me-2 text-muted"></i>Chọn Công việc (Task) <span class="text-danger">*</span></label>
                        <?php 
                        $selectedTaskId = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
                        ?>
                        <select class="form-select" id="task_id" name="task_id" required>
                            <option value="" disabled <?= $selectedTaskId === 0 ? 'selected' : '' ?>>-- Chọn công việc đang xử lý --</option>
                            <?php if (!empty($tasks)): ?>
                                <?php foreach ($tasks as $t): 
                                    $selected = ($t['task_id'] == $selectedTaskId) ? 'selected' : '';
                                    $resolvedImage = (strpos($t['image_url'], 'http') === 0) ? $t['image_url'] : BASE_PATH . '/' . ltrim($t['image_url'], '/');
                                    $isGroupTask = !empty($t['grouped_region_ids']);
                                    $groupedRegionsJson = '';
                                    if ($isGroupTask && !empty($t['grouped_regions_data'])) {
                                        $groupedRegionsJson = htmlspecialchars(json_encode($t['grouped_regions_data']), ENT_QUOTES);
                                    }
                                    $labelSuffix = $isGroupTask ? ' — Nhóm vùng' : '';
                                ?>
                                    <option value="<?= $t['task_id'] ?>" 
                                            <?= $selected ?>
                                            data-image="<?= htmlspecialchars($resolvedImage) ?>"
                                            data-x="<?= htmlspecialchars($t['region_x'] ?? '') ?>"
                                            data-y="<?= htmlspecialchars($t['region_y'] ?? '') ?>"
                                            data-w="<?= htmlspecialchars($t['region_width'] ?? '') ?>"
                                            data-h="<?= htmlspecialchars($t['region_height'] ?? '') ?>"
                                            data-type="<?= htmlspecialchars($t['region_type'] ?? '') ?>"
                                            data-is-group="<?= $isGroupTask ? '1' : '0' ?>"
                                            data-grouped-regions="<?= $groupedRegionsJson ?>">
                                        <?= htmlspecialchars($t['series_title']) ?> - Ch.<?= htmlspecialchars($t['chapter_number']) ?> (<?= htmlspecialchars($t['title']) ?>)<?= $labelSuffix ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Không có công việc nào đang chờ nộp</option>
                            <?php endif; ?>
                        </select>
                        <div class="form-text text-muted">Chỉ hiển thị các công việc được giao và chưa hoàn thành.</div>
                    </div>

                    <!-- Upload File -->
                    <div class="mb-4">
                        <label for="file" class="form-label fw-bold text-dark"><i class="fas fa-file-upload me-2 text-muted"></i>Tải file sản phẩm <span class="text-danger">*</span></label>
                        <div class="upload-dropzone position-relative d-flex flex-column align-items-center justify-content-center border border-dashed rounded-3 p-4 bg-light text-center" id="dropzone" style="cursor: pointer; transition: background-color 0.2s, border-color 0.2s; border-width: 2px !important; border-color: #cbd5e1 !important; min-height: 150px;">
                            <input type="file" id="file" name="file" accept=".jpg,.jpeg,.png,.pdf,.zip" required class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer; z-index: 2;">
                            <div class="upload-icon-wrapper mb-3" style="width: 48px; height: 48px; background: rgba(79, 70, 229, 0.08); color: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                                <i class="fas fa-cloud-upload-alt fs-4"></i>
                            </div>
                            <h6 class="fw-semibold mb-1" id="upload-status-text" style="font-size: 0.9rem;">Kéo thả file vào đây hoặc click để chọn file</h6>
                            <p class="text-xs text-muted mb-0" style="font-size: 0.75rem;">Định dạng: .jpg, .jpeg, .png, .pdf, .zip (Tối đa 20MB)</p>
                        </div>
                    </div>

                    <!-- Ghi chú (Notes) -->
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-bold text-dark"><i class="fas fa-comment-alt me-2 text-muted"></i>Ghi chú gửi cho Họa sĩ chính (Mangaka)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Nhập ghi chú hoặc mô tả về sản phẩm bạn đã hoàn thành..." style="border-radius: 8px; font-size: 0.88rem;"></textarea>
                    </div>

                    <!-- Nút bấm -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=index" class="btn btn-light px-4" style="border-radius: 8px;">Hủy</a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm" style="border-radius: 8px;"><i class="fas fa-paper-plane me-2"></i>Nộp sản phẩm</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Cột phải: Xem phân vùng được giao -->
    <div class="col-lg-5 mb-4">
        <!-- Banner thông báo công việc nhóm -->
        <div class="alert alert-primary py-2 px-3 mb-3 d-flex align-items-start" id="groupTaskBanner" style="display: none; font-size: 0.85rem; border-radius: 10px; border: 1px solid rgba(99,102,241,0.3); background: rgba(99,102,241,0.06);">
            <i class="fas fa-layer-group me-2 mt-1 text-primary"></i>
            <div>
                <strong class="text-primary">Đây là công việc nhóm.</strong><br>
                <span class="text-slate-600">Bạn chỉ cần nộp bài <strong>1 lần duy nhất</strong> cho tất cả các vùng được đánh dấu bên dưới.</span>
                <div id="groupRegionList" class="mt-1"></div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-3" id="regionPreviewCard" style="display: none;">
            <div class="card-header bg-white text-dark py-3 border-bottom border-light">
                <h5 class="card-title mb-0" id="previewCardTitle"><i class="fas fa-eye me-2 text-primary"></i>Phân vùng nhiệm vụ</h5>
            </div>
            <div class="card-body p-3 text-center bg-light">
                <div id="previewWrapper" class="position-relative d-inline-block text-start shadow-sm" style="max-width: 100%; border: 1px solid #ccc; background-color: #eee;">
                    <img id="previewImage" src="" alt="Page Preview" class="img-fluid" style="display: block; max-width: 100%;">
                    <!-- Shadow Overlay to dim other regions (single mode only) -->
                    <div id="previewDimmerTop" style="position: absolute; left: 0; top: 0; right: 0; background: rgba(0,0,0,0.5); pointer-events: none; transition: all 0.2s;"></div>
                    <div id="previewDimmerLeft" style="position: absolute; left: 0; background: rgba(0,0,0,0.5); pointer-events: none; transition: all 0.2s;"></div>
                    <div id="previewDimmerRight" style="position: absolute; right: 0; background: rgba(0,0,0,0.5); pointer-events: none; transition: all 0.2s;"></div>
                    <div id="previewDimmerBottom" style="position: absolute; left: 0; bottom: 0; right: 0; background: rgba(0,0,0,0.5); pointer-events: none; transition: all 0.2s;"></div>
                    
                    <!-- Highlighted region boundary (single mode) -->
                    <div id="previewOverlay" style="position: absolute; border: 2.5px dashed #dc3545; box-shadow: 0 0 10px rgba(255,255,255,0.8); pointer-events: none; transition: all 0.2s;"></div>
                    <!-- Container for multi-overlay (group mode) -->
                    <div id="multiOverlayContainer"></div>
                </div>
                <div class="mt-3 text-start small">
                    <p class="mb-1"><strong>Loại phân vùng được giao:</strong> <span id="previewType" class="badge bg-secondary">-</span></p>
                    <p class="mb-0 text-muted" id="previewNote"><strong>Lưu ý:</strong> Vùng sáng là khu vực được phân công cho bạn. Xung quanh đã được làm mờ để dễ nhận diện.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const taskSelect = document.getElementById("task_id");
    const previewCard = document.getElementById("regionPreviewCard");
    const previewImage = document.getElementById("previewImage");
    const previewOverlay = document.getElementById("previewOverlay");
    const previewType = document.getElementById("previewType");
    const previewNote = document.getElementById("previewNote");
    const previewCardTitle = document.getElementById("previewCardTitle");
    const groupTaskBanner = document.getElementById("groupTaskBanner");
    const groupRegionList = document.getElementById("groupRegionList");
    const multiOverlayContainer = document.getElementById("multiOverlayContainer");
    
    // Dimmer divs (single mode only)
    const topD = document.getElementById("previewDimmerTop");
    const leftD = document.getElementById("previewDimmerLeft");
    const rightD = document.getElementById("previewDimmerRight");
    const bottomD = document.getElementById("previewDimmerBottom");

    // Color palette for multi-overlay borders
    const overlayColors = ['#dc3545', '#0d6efd', '#198754', '#fd7e14', '#6f42c1', '#20c997'];

    function getRegionTypeLabel(type) {
        const map = {
            'panel': 'Khung truyện', 'bubble': 'Bong bóng thoại', 'character': 'Nhân vật',
            'background': 'Bối cảnh/Nền', 'sfx': 'Hiệu ứng SFX'
        };
        return map[type] || 'Khung truyện';
    }

    function hideDimmers() {
        [topD, leftD, rightD, bottomD].forEach(d => d.style.display = 'none');
    }

    function clearMultiOverlays() {
        multiOverlayContainer.innerHTML = '';
    }

    function updatePreview() {
        const selectedOption = taskSelect.options[taskSelect.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            previewCard.style.display = "none";
            groupTaskBanner.style.display = "none";
            return;
        }

        const image = selectedOption.getAttribute("data-image");
        const isGroup = selectedOption.getAttribute("data-is-group") === '1';
        const groupedRegionsRaw = selectedOption.getAttribute("data-grouped-regions");

        // Reset state
        clearMultiOverlays();
        previewOverlay.style.display = 'none';
        hideDimmers();

        if (!image) {
            previewCard.style.display = "none";
            groupTaskBanner.style.display = "none";
            return;
        }

        previewImage.src = image;
        previewCard.style.display = "block";

        // === GROUP TASK MODE ===
        if (isGroup && groupedRegionsRaw) {
            let regions = [];
            try { regions = JSON.parse(groupedRegionsRaw); } catch(e) { regions = []; }

            if (regions.length > 0) {
                // Update card title
                previewCardTitle.innerHTML = '<i class="fas fa-layer-group me-2 text-primary"></i>Các vùng cần thực hiện (' + regions.length + ' vùng)';

                // Show group banner
                groupTaskBanner.style.display = 'flex';
                let badgesHtml = '';
                regions.forEach((r, i) => {
                    const color = overlayColors[i % overlayColors.length];
                    const typeLabel = getRegionTypeLabel(r.region_type);
                    badgesHtml += '<span class="badge me-1 mb-1" style="background:' + color + '; font-size: 0.72rem; padding: 3px 8px; border-radius: 6px;">#' + r.region_id + ' ' + typeLabel + '</span>';

                    // Create overlay for each region
                    const lPct = (parseInt(r.x) / 800) * 100;
                    const tPct = (parseInt(r.y) / 1000) * 100;
                    const wPct = (parseInt(r.width) / 800) * 100;
                    const hPct = (parseInt(r.height) / 1000) * 100;

                    const overlay = document.createElement('div');
                    overlay.style.cssText = 'position:absolute; border:2.5px dashed ' + color + '; box-shadow:0 0 8px rgba(255,255,255,0.6); pointer-events:none; transition:all 0.2s; z-index:10;';
                    overlay.style.left = lPct + '%';
                    overlay.style.top = tPct + '%';
                    overlay.style.width = wPct + '%';
                    overlay.style.height = hPct + '%';

                    // Add label inside overlay
                    const label = document.createElement('span');
                    label.style.cssText = 'position:absolute; top:2px; left:2px; background:' + color + '; color:#fff; font-size:9px; font-weight:700; padding:1px 5px; border-radius:3px; line-height:1.3;';
                    label.textContent = '#' + r.region_id;
                    overlay.appendChild(label);

                    multiOverlayContainer.appendChild(overlay);
                });
                groupRegionList.innerHTML = badgesHtml;

                // Show type as group
                previewType.textContent = 'Nhóm vùng (' + regions.length + ')';
                previewType.className = 'badge bg-primary';
                previewNote.innerHTML = '<strong>Lưu ý:</strong> Tất cả các vùng viền nét đứt là khu vực bạn cần thực hiện. Chỉ cần nộp bài <strong>1 lần</strong> cho cả nhóm.';
            }
            return;
        }

        // === SINGLE REGION MODE (original behavior) ===
        groupTaskBanner.style.display = 'none';
        previewCardTitle.innerHTML = '<i class="fas fa-eye me-2 text-primary"></i>Phân vùng nhiệm vụ';
        previewNote.innerHTML = '<strong>Lưu ý:</strong> Vùng sáng là khu vực được phân công cho bạn. Xung quanh đã được làm mờ để dễ nhận diện.';

        const x = selectedOption.getAttribute("data-x");
        const y = selectedOption.getAttribute("data-y");
        const w = selectedOption.getAttribute("data-w");
        const h = selectedOption.getAttribute("data-h");
        const type = selectedOption.getAttribute("data-type");

        if (x && y && w && h) {
            const lPct = (parseInt(x) / 800) * 100;
            const tPct = (parseInt(y) / 1000) * 100;
            const wPct = (parseInt(w) / 800) * 100;
            const hPct = (parseInt(h) / 1000) * 100;

            previewOverlay.style.left = lPct + "%";
            previewOverlay.style.top = tPct + "%";
            previewOverlay.style.width = wPct + "%";
            previewOverlay.style.height = hPct + "%";
            previewOverlay.style.display = "block";

            topD.style.height = tPct + "%";
            leftD.style.top = tPct + "%"; leftD.style.height = hPct + "%"; leftD.style.width = lPct + "%";
            rightD.style.top = tPct + "%"; rightD.style.height = hPct + "%"; rightD.style.width = (100 - lPct - wPct) + "%";
            bottomD.style.top = (tPct + hPct) + "%"; bottomD.style.height = (100 - tPct - hPct) + "%";
            [topD, leftD, rightD, bottomD].forEach(d => d.style.display = 'block');

            let typeText = "Khung truyện";
            let badgeClass = "bg-danger";
            if (type === "bubble") { typeText = "Bong bóng thoại"; badgeClass = "bg-primary"; previewOverlay.style.borderColor = "#0d6efd"; }
            else if (type === "character") { typeText = "Nhân vật"; badgeClass = "bg-success"; previewOverlay.style.borderColor = "#198754"; }
            else if (type === "background") { typeText = "Bối cảnh/Nền"; badgeClass = "bg-dark"; previewOverlay.style.borderColor = "#343a40"; }
            else if (type === "sfx") { typeText = "Hiệu ứng SFX"; badgeClass = "bg-warning text-dark"; previewOverlay.style.borderColor = "#fd7e14"; }
            else { previewOverlay.style.borderColor = "#dc3545"; }
            previewType.textContent = typeText;
            previewType.className = "badge " + badgeClass;
        } else {
            previewOverlay.style.display = "none";
            hideDimmers();
            previewType.textContent = "Toàn bộ trang";
            previewType.className = "badge bg-secondary";
        }
    }

    taskSelect.addEventListener("change", updatePreview);
    // Run initially in case page loaded with preselected task_id
    updatePreview();

    // Custom Drag and Drop styling & status handling
    const fileInput = document.getElementById('file');
    const dropzone = document.getElementById('dropzone');
    const statusText = document.getElementById('upload-status-text');
    const iconWrapper = dropzone.querySelector('.upload-icon-wrapper');

    if (fileInput && dropzone) {
        fileInput.addEventListener('change', function() {
            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                statusText.innerHTML = `<span class="text-success"><i class="fas fa-file-alt me-1"></i><strong>${file.name}</strong></span> <span class="text-muted" style="font-size: 0.8rem;">(${(file.size / (1024 * 1024)).toFixed(2)} MB)</span>`;
                dropzone.style.borderColor = "#10b981"; // success border
                dropzone.style.backgroundColor = "rgba(16, 185, 129, 0.02)";
                iconWrapper.style.backgroundColor = "rgba(16, 185, 129, 0.1)";
                iconWrapper.style.color = "#10b981";
                iconWrapper.innerHTML = '<i class="fas fa-check fs-4"></i>';
            } else {
                statusText.textContent = "Kéo thả file vào đây hoặc click để chọn file";
                dropzone.style.borderColor = "#cbd5e1";
                dropzone.style.backgroundColor = "#f8fafc";
                iconWrapper.style.backgroundColor = "rgba(79, 70, 229, 0.08)";
                iconWrapper.style.color = "#4f46e5";
                iconWrapper.innerHTML = '<i class="fas fa-cloud-upload-alt fs-4"></i>';
            }
        });

        // Highlights on drag
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.style.borderColor = "#4f46e5";
                dropzone.style.backgroundColor = "rgba(79, 70, 229, 0.04)";
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => {
                if (!fileInput.files || !fileInput.files[0]) {
                    dropzone.style.borderColor = "#cbd5e1";
                    dropzone.style.backgroundColor = "#f8fafc";
                }
            }, false);
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
