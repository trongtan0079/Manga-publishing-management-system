<?php 
/**
 * View: Giao Task mới
 * Khai báo biến để Editor/IDE hiểu và không báo lỗi
 * @var array $page Thông tin trang hiện tại
 * @var array $chapter Thông tin chapter chứa trang
 * @var array $series Thông tin series
 * @var array $assistants Danh sách assistant
 */
$pageTitle = 'Tạo Công Việc Mới';
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
/* Tinh chỉnh thanh công cụ Quill cho phân vùng nhỏ gọn, đẹp mắt */
.quill-region-editor-wrapper .ql-toolbar.ql-snow {
    border: none !important;
    border-bottom: 1px solid #cbd5e1 !important;
    background-color: #f8fafc;
    padding: 4px 8px !important;
}
.quill-region-editor-wrapper .ql-container.ql-snow {
    border: none !important;
}
.quill-region-editor-wrapper .ql-editor {
    padding: 8px 12px !important;
    min-height: 80px;
    font-size: 0.85rem;
}
</style>

<div class="mb-3">
    <a href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= htmlspecialchars($page['page_id']) ?>" class="btn btn-secondary">&larr; Quay lại Trang</a>
</div>

<!-- 
  Form tạo Task mới. Action trỏ về hàm store() trong TaskController bằng phương thức POST.
  - Sử dụng htmlspecialchars để chống lỗi XSS bảo mật.
-->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Tạo công việc mới</h4>
        <!-- Hiển thị ngữ cảnh công việc đang được giao cho trang nào, chương nào -->
        <small>Trang <?= htmlspecialchars($page['page_number']) ?> - Chapter <?= htmlspecialchars($chapter['chapter_number']) ?> (<?= htmlspecialchars($series['title']) ?>)</small>
    </div>
    <div class="card-body">
        <form action="<?= BASE_PATH ?>/index.php?controller=task&action=store" method="POST">
            <!-- page_id được truyền ngầm để Controller biết task này thuộc về trang nào -->
            <input type="hidden" name="page_id" value="<?= htmlspecialchars($page['page_id']) ?>">
            <?php if (!empty($groupedRegionIds)): ?>
                <div class="alert alert-warning py-2 px-3 mb-3 d-flex align-items-center shadow-sm" style="font-size: 0.85rem; border-radius: 8px;">
                    <i class="fas fa-exclamation-triangle me-2 text-warning fs-5"></i>
                    <div>
                        <strong>Lưu ý quan trọng:</strong> Khi tạo Công việc Nhóm này cho các phân vùng <strong>#<?= htmlspecialchars($groupedRegionIds) ?></strong>, nếu có các công việc riêng lẻ cũ (chưa hoàn thành) đang được giao trên các phân vùng này, hệ thống sẽ tự động gộp/xóa chúng để tránh trùng lặp cho Trợ lý.
                    </div>
                </div>
                <input type="hidden" name="grouped_region_ids" value="<?= htmlspecialchars($groupedRegionIds) ?>">
            <?php endif; ?>
            
            <!-- Trường Tiêu đề (Bắt buộc) -->
            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề công việc <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required placeholder="Ví dụ: Vẽ background, Tô màu nhân vật...">
            </div>

            <?php
            $selectedRegionId = isset($_GET['page_region_id']) ? intval($_GET['page_region_id']) : 0;
            $groupedRegionIds = isset($_GET['grouped_region_ids']) ? $_GET['grouped_region_ids'] : '';
            ?>

            <!-- Trường Mô tả chi tiết (Tùy chọn) -->
            <div class="mb-3">
                <label for="description" class="form-label fw-bold text-slate-700">Mô tả chi tiết (Tùy chọn)</label>
                <!-- Hidden textarea to store the HTML content for backend submission -->
                <textarea id="description" name="description" style="display: none;"></textarea>
                
                <?php if (!empty($groupedRegionData)): ?>
                    <div id="grouped-regions-inputs" class="d-flex flex-column gap-3">
                        <?php foreach($groupedRegionData as $gData): 
                            $rId = $gData['region_id'];
                            $rType = $gData['region_type'];
                            $oldTitle = $gData['old_title'];
                            $oldTaskType = $gData['old_task_type'];
                            $oldDesc = $gData['old_description'];
                            
                            $isStandardType = in_array($oldTaskType, ['background', 'inking', 'coloring', 'effects', '']);
                            $selectedType = $isStandardType ? $oldTaskType : 'other';
                            $oldCustomType = !$isStandardType ? $oldTaskType : '';
                            
                            $typeLabel = 'Khác';
                            $badgeColor = '#6c757d'; // secondary
                            $bgColor = '#f8f9fa';
                            switch ($rType) {
                                case 'panel': $typeLabel = 'Khung truyện'; $badgeColor = '#dc3545'; $bgColor = '#fdf1f2'; break;
                                case 'bubble': $typeLabel = 'Bong bóng thoại'; $badgeColor = '#0d6efd'; $bgColor = '#f0f5ff'; break;
                                case 'character': $typeLabel = 'Nhân vật'; $badgeColor = '#198754'; $bgColor = '#f0f9f4'; break;
                                case 'background': $typeLabel = 'Bối cảnh/Nền'; $badgeColor = '#212529'; $bgColor = '#f8f9fa'; break;
                                case 'sfx': $typeLabel = 'Hiệu ứng SFX'; $badgeColor = '#ffc107'; $bgColor = '#fffdf0'; break;
                            }
                        ?>
                        <div class="card shadow-sm border-0 mb-3" style="border: 1px solid <?= $badgeColor ?>33 !important; border-radius: 8px; overflow: hidden; background-color: <?= $bgColor ?> !important;">
                            <div class="card-header border-bottom-0 py-2.5 px-3 d-flex align-items-center justify-content-between" style="background-color: transparent;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge" style="background-color: <?= $badgeColor ?>; font-size: 0.75rem; padding: 0.4em 0.7em; border-radius: 4px; font-weight: 600; box-shadow: 0 2px 4px <?= $badgeColor ?>33;"><?= htmlspecialchars($typeLabel) ?></span>
                                </div>
                                <span class="fw-bold text-slate-600" style="font-size: 0.82rem; letter-spacing: 0.5px;">PHÂN VÙNG #<?= $rId ?></span>
                            </div>
                            <div class="card-body pt-1 pb-3 px-3">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label mb-1 text-uppercase fw-bold text-slate-600" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                            <i class="fas fa-edit me-1 text-slate-500"></i>Tiêu đề công việc (Tùy chọn)
                                        </label>
                                        <input type="text" class="form-control form-control-sm region-specific-title border-light-subtle" data-region-id="<?= $rId ?>" placeholder="Tiêu đề cho vùng này..." value="<?= htmlspecialchars($oldTitle) ?>" style="font-weight: 500; font-size: 0.85rem; border-radius: 6px; background-color: #fff; box-shadow: none; border: 1px solid #cbd5e1;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label mb-1 text-uppercase fw-bold text-slate-600" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                            <i class="fas fa-tasks me-1 text-slate-500"></i>Loại công việc chi tiết
                                        </label>
                                        <select class="form-select form-select-sm region-specific-type border-light-subtle" data-region-id="<?= $rId ?>" style="font-size: 0.85rem; border-radius: 6px; background-color: #fff; color: #475569; font-weight: 500; box-shadow: none; border: 1px solid #cbd5e1;">
                                            <option value="" <?= $selectedType == '' ? 'selected' : '' ?>>-- Chọn loại công việc chi tiết --</option>
                                            <option value="background" <?= $selectedType == 'background' ? 'selected' : '' ?>>Vẽ nền (Background)</option>
                                            <option value="inking" <?= $selectedType == 'inking' ? 'selected' : '' ?>>Đi nét (Inking)</option>
                                            <option value="coloring" <?= $selectedType == 'coloring' ? 'selected' : '' ?>>Lên màu (Coloring)</option>
                                            <option value="effects" <?= $selectedType == 'effects' ? 'selected' : '' ?>>Hiệu ứng (Effects)</option>
                                            <option value="other" <?= $selectedType == 'other' ? 'selected' : '' ?>>Khác</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 region-custom-type-container <?= $selectedType === 'other' ? '' : 'd-none' ?>" data-region-id="<?= $rId ?>">
                                    <label class="form-label mb-1 text-uppercase fw-bold text-slate-600" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                        <i class="fas fa-keyboard me-1 text-slate-500"></i>Nhập loại công việc khác <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-sm region-specific-custom-type border-light-subtle" data-region-id="<?= $rId ?>" placeholder="Ví dụ: Đổ tone, Dán decal..." value="<?= htmlspecialchars($oldCustomType) ?>" style="font-size: 0.85rem; border-radius: 6px; background-color: #fff; box-shadow: none; border: 1px solid #cbd5e1;">
                                </div>
                                <div>
                                    <label class="form-label mb-1 text-uppercase fw-bold text-slate-600" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                        <i class="fas fa-align-left me-1 text-slate-500"></i>Ghi chú mô tả chi tiết công việc
                                    </label>
                                    <div class="quill-region-editor-wrapper border rounded" style="background-color: #fff; border: 1px solid #cbd5e1 !important; border-radius: 6px; overflow: hidden;">
                                        <div class="quill-region-editor region-specific-desc" data-region-id="<?= $rId ?>" data-badge-color="<?= $badgeColor ?>" data-bg-color="<?= $bgColor ?>" data-type-label="<?= htmlspecialchars($typeLabel) ?>" style="height: 120px; font-size: 0.85rem;">
                                            <?= $oldDesc ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <!-- Quill container for single task -->
                    <div id="quill-editor"></div>
                <?php endif; ?>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Quill Editor ONLY if the container exists
                let quill = null;
                const quillContainer = document.getElementById('quill-editor');
                if (quillContainer) {
                    quill = new Quill('#quill-editor', {
                        theme: 'snow',
                        placeholder: 'Mô tả cụ thể yêu cầu của bạn cho assistant...',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline', 'strike'],        // text styling
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],     // lists
                                ['clean']                                         // clear formatting
                            ]
                        }
                    });
                }

                // Xử lý loại công việc tự chọn (Single)
                const taskTypeSelect = document.getElementById('task_type');
                const customTaskTypeContainer = document.getElementById('custom_task_type_container');
                const customTaskTypeInput = document.getElementById('custom_task_type');

                function toggleCustomTaskType() {
                    if (taskTypeSelect && taskTypeSelect.value === 'other') {
                        customTaskTypeContainer.classList.remove('d-none');
                        customTaskTypeInput.required = true;
                    } else {
                        if (customTaskTypeContainer) customTaskTypeContainer.classList.add('d-none');
                        if (customTaskTypeInput) customTaskTypeInput.required = false;
                    }
                }
                
                if (taskTypeSelect && customTaskTypeContainer && customTaskTypeInput) {
                    taskTypeSelect.addEventListener('change', toggleCustomTaskType);
                    toggleCustomTaskType();
                }

                // Xử lý chuyển đổi giữa Single Task và Multi Task
                const multiTaskToggle = document.getElementById('multi_task_toggle');
                const singleContainer = document.getElementById('single_task_type_container');
                const multiContainer = document.getElementById('multi_task_types_container');
                const cbOther = document.getElementById('cb_other');
                const customMultiInput = document.getElementById('custom_multi_task_type');

                if (multiTaskToggle) {
                    multiTaskToggle.addEventListener('change', function() {
                        if (this.checked) {
                            singleContainer.classList.add('d-none');
                            multiContainer.classList.remove('d-none');
                            if (taskTypeSelect) taskTypeSelect.required = false;
                            if (customTaskTypeInput) customTaskTypeInput.required = false;
                        } else {
                            singleContainer.classList.remove('d-none');
                            multiContainer.classList.add('d-none');
                            if (taskTypeSelect) taskTypeSelect.required = true;
                            toggleCustomTaskType();
                        }
                    });
                }

                if (cbOther && customMultiInput) {
                    cbOther.addEventListener('change', function() {
                        if (this.checked) {
                            customMultiInput.classList.remove('d-none');
                            customMultiInput.required = true;
                        } else {
                            customMultiInput.classList.add('d-none');
                            customMultiInput.required = false;
                        }
                    });
                }

                // Khởi tạo Quill Editor cho từng phân vùng (Region)
                const regionQuills = {};
                document.querySelectorAll('.quill-region-editor').forEach(function(el) {
                    const rId = el.getAttribute('data-region-id');
                    regionQuills[rId] = new Quill(el, {
                        theme: 'snow',
                        placeholder: 'Nhập ghi chú chi tiết mô tả công việc cần xử lý trong vùng này...',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline'],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                ['clean']
                            ]
                        }
                    });
                });

                // Xử lý loại công việc tự chọn cho từng phân vùng (Region)
                document.querySelectorAll('.region-specific-type').forEach(function(select) {
                    const rId = select.getAttribute('data-region-id');
                    const customContainer = document.querySelector(`.region-custom-type-container[data-region-id="${rId}"]`);
                    
                    function toggleRegionCustom() {
                        if (customContainer) {
                            if (select.value === 'other') {
                                customContainer.classList.remove('d-none');
                            } else {
                                customContainer.classList.add('d-none');
                            }
                        }
                    }

                    select.addEventListener('change', toggleRegionCustom);
                    toggleRegionCustom();
                });
                
                // Sync Quill editor HTML with the hidden textarea on submit and validate checkboxes
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        // Check if multi-task is active and at least one checkbox is checked
                        if (multiTaskToggle && multiTaskToggle.checked) {
                            const checkedBoxes = document.querySelectorAll('.task-type-cb:checked');
                            const errorMsg = document.getElementById('cb_error_msg');
                            if (checkedBoxes.length === 0) {
                                e.preventDefault();
                                errorMsg.classList.remove('d-none');
                                return false;
                            } else {
                                errorMsg.classList.add('d-none');
                            }
                        }

                        const descriptionTextarea = document.getElementById('description');
                        const groupedInputs = document.querySelectorAll('.region-specific-desc');
                        
                        if (groupedInputs.length > 0) {
                            // Cấu trúc lại HTML đẹp mắt cho các thẻ vùng
                            let combinedHtml = '<div class="grouped-task-instructions" style="display: flex; flex-direction: column; gap: 12px;">';
                            groupedInputs.forEach(input => {
                                const rId = input.getAttribute('data-region-id');
                                
                                // Lấy các input phụ
                                const titleInput = document.querySelector(`.region-specific-title[data-region-id="${rId}"]`);
                                const typeSelect = document.querySelector(`.region-specific-type[data-region-id="${rId}"]`);
                                
                                // Lấy giá trị từ Quill editor tương ứng
                                const quillInstance = regionQuills[rId];
                                const valHtml = quillInstance ? quillInstance.root.innerHTML.trim() : '';
                                // Lấy text thuần để check xem người dùng có thực sự nhập mô tả không
                                const plainText = quillInstance ? quillInstance.getText().trim() : '';
                                
                                const rTitle = titleInput ? titleInput.value.trim() : '';
                                let rType = typeSelect ? typeSelect.options[typeSelect.selectedIndex].text : '';
                                const hasType = typeSelect && typeSelect.value !== '';
                                
                                // Nếu chọn "Khác", lấy giá trị từ ô nhập custom
                                if (typeSelect && typeSelect.value === 'other') {
                                    const customInput = document.querySelector(`.region-specific-custom-type[data-region-id="${rId}"]`);
                                    if (customInput && customInput.value.trim()) {
                                        rType = customInput.value.trim();
                                    }
                                }
                                
                                // Nếu có bất kỳ nội dung nào được nhập ở thẻ này
                                if (plainText !== '' || rTitle || hasType) {
                                    const bColor = input.getAttribute('data-badge-color');
                                    const bgColor = input.getAttribute('data-bg-color') || '#f8fafc';
                                    const tLabel = input.getAttribute('data-type-label');
                                    
                                    const safeTitle = rTitle.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                                    
                                    let contentHtml = '';
                                    if (safeTitle) contentHtml += `<h6 style="color: #0f172a; font-weight: 700; font-size: 14.5px; margin-bottom: 4px;">${safeTitle}</h6>`;
                                    if (hasType) contentHtml += `<p style="color: #64748b; font-size: 12px; margin-bottom: 6px; font-weight: 600;"><i class="fas fa-tag me-1"></i> Loại việc: ${rType}</p>`;
                                    if (plainText !== '') contentHtml += `<div class="region-desc-content ql-editor" style="font-size: 13.5px; color: #334155; line-height: 1.5; padding: 0; background: transparent;">${valHtml}</div>`;
                                    
                                    combinedHtml += `
                                    <div class="region-instruction-card" style="border: 1px solid ${bColor}; padding: 12px; border-radius: 6px; background-color: ${bgColor};">
                                        <div style="margin-bottom: 8px; border-bottom: 1px solid ${bColor}40; padding-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
                                            <span style="background-color: ${bColor}; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">${tLabel}</span>
                                            <span style="font-size: 13px; font-weight: bold; color: #475569;">Phân vùng #${rId}</span>
                                        </div>
                                        ${contentHtml}
                                    </div>`;
                                }
                            });
                            combinedHtml += '</div>';
                            
                            // Nếu không có vùng nào được nhập, để trống
                            descriptionTextarea.value = (combinedHtml === '<div class="grouped-task-instructions" style="display: flex; flex-direction: column; gap: 12px;"></div>') ? '' : combinedHtml;
                            
                        } else if (quill) {
                            // Dùng Quill như bình thường
                            if (quill.getText().trim().length > 0) {
                                descriptionTextarea.value = quill.root.innerHTML;
                            } else {
                                descriptionTextarea.value = '';
                            }
                        }
                    });
                }
            });
            </script>

            <!-- Loại công việc và Phân vùng (New) -->
            <div class="row mb-3">
                <?php if (empty($groupedRegionIds)): ?>
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0">Loại công việc <span class="text-danger">*</span></label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="multi_task_toggle" name="is_multi_task" value="1">
                            <label class="form-check-label text-slate-600 font-semibold" for="multi_task_toggle" style="font-size: 0.8rem; cursor: pointer;">Giao nhiều việc cùng lúc</label>
                        </div>
                    </div>
                    
                    <!-- Single task type container (Default) -->
                    <div id="single_task_type_container">
                        <select class="form-select" id="task_type" name="task_type" required>
                            <option value="background">Vẽ nền (Background)</option>
                            <option value="inking">Đi nét (Inking)</option>
                            <option value="coloring">Lên màu (Coloring)</option>
                            <option value="effects">Hiệu ứng (Effects)</option>
                            <option value="other" selected>Khác (Other)</option>
                        </select>
                        
                        <div class="mt-2 d-none" id="custom_task_type_container">
                            <label for="custom_task_type" class="form-label fw-semibold text-slate-700" style="font-size: 0.85rem;">Nhập loại công việc tự chọn <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="custom_task_type" name="custom_task_type" placeholder="Ví dụ: Đổ tone, Dán decal, v.v.">
                        </div>
                    </div>
                    
                    <!-- Multi task types container (hidden by default) -->
                    <div id="multi_task_types_container" class="d-none border p-3 rounded bg-light">
                        <div class="form-check mb-2">
                            <input class="form-check-input task-type-cb" type="checkbox" name="task_types[]" value="background" id="cb_background">
                            <label class="form-check-label" for="cb_background">Vẽ nền (Background)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input task-type-cb" type="checkbox" name="task_types[]" value="inking" id="cb_inking">
                            <label class="form-check-label" for="cb_inking">Đi nét (Inking)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input task-type-cb" type="checkbox" name="task_types[]" value="coloring" id="cb_coloring">
                            <label class="form-check-label" for="cb_coloring">Lên màu (Coloring)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input task-type-cb" type="checkbox" name="task_types[]" value="effects" id="cb_effects">
                            <label class="form-check-label" for="cb_effects">Hiệu ứng (Effects)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input task-type-cb" type="checkbox" name="task_types[]" value="other" id="cb_other">
                            <label class="form-check-label" for="cb_other">Khác</label>
                            <input type="text" class="form-control form-control-sm mt-1 d-none" id="custom_multi_task_type" name="custom_multi_task_type" placeholder="Nhập loại công việc khác...">
                        </div>
                        <div class="form-text text-danger d-none" id="cb_error_msg" style="font-size: 0.75rem;">Vui lòng chọn ít nhất một loại công việc.</div>
                    </div>
                </div>
                <?php else: ?>
                    <input type="hidden" name="task_type" value="other">
                <?php endif; ?>
                
                <div class="<?= empty($groupedRegionIds) ? 'col-md-6' : 'col-md-12' ?>">
                    <label for="page_region_id" class="form-label fw-bold">Phân vùng ảnh</label>
                    <?php if (!empty($groupedRegionIds)): 
                        $firstRegionId = explode(',', $groupedRegionIds)[0];
                    ?>
                        <input type="hidden" name="page_region_id" value="<?= htmlspecialchars($firstRegionId) ?>">
                        <div class="form-control bg-light text-muted">Nhóm các phân vùng: #<?= htmlspecialchars($groupedRegionIds) ?></div>
                    <?php else: ?>
                        <select class="form-select" id="page_region_id" name="page_region_id">
                            <option value="">-- Toàn bộ trang truyện --</option>
                            <?php if (!empty($regions)): ?>
                                <?php foreach ($regions as $region): 
                                    $lbl = ucfirst($region['region_type']) . " #" . $region['region_id'] . " (" . $region['width'] . "x" . $region['height'] . ")";
                                    $selected = ($region['region_id'] == $selectedRegionId) ? 'selected' : '';
                                ?>
                                    <option value="<?= $region['region_id'] ?>" <?= $selected ?>><?= htmlspecialchars($lbl) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Link tài nguyên đính kèm (New) -->
            <div class="mb-3">
                <label for="resource_url" class="form-label">Tài nguyên hỗ trợ đính kèm (URL)</label>
                <input type="url" class="form-control" id="resource_url" name="resource_url" placeholder="https://drive.google.com/drive/folders/... hoặc liên kết khác">
                <div class="form-text">Đường dẫn Google Drive, Figma... chứa tài liệu mẫu hoặc asset hỗ trợ vẽ.</div>
            </div>

            <div class="row mb-3">
                <!-- Dropdown chọn Assistant (Bắt buộc) -->
                <div class="col-md-6">
                    <label for="assistant_id" class="form-label">Giao cho (Assistant) <span class="text-danger">*</span></label>
                    <select class="form-select" id="assistant_id" name="assistant_id" required>
                        <option value="">-- Chọn Assistant --</option>
                        <!-- Duyệt danh sách các assistant đang active để hiển thị -->
                        <?php foreach ($assistants as $assistant): ?>
                            <option value="<?= $assistant['user_id'] ?>"><?= htmlspecialchars($assistant['full_name']) ?> (<?= htmlspecialchars($assistant['username']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Dropdown chọn mức độ ưu tiên -->
                <div class="col-md-6">
                    <label for="priority" class="form-label">Mức độ ưu tiên</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="low">Thấp (Low)</option>
                        <option value="medium" selected>Trung bình (Medium)</option>
                        <option value="high">Cao (High)</option>
                    </select>
                </div>
            </div>

            <!-- Trường hạn chót (Sử dụng datetime-local để chọn giờ) -->
            <div class="mb-3">
                <label for="due_date" class="form-label">Hạn chót</label>
                <input type="datetime-local" class="form-control" id="due_date" name="due_date">
                <div class="form-text">Bạn có thể bỏ trống nếu công việc này không có hạn chót cụ thể.</div>
            </div>

            <button type="submit" class="btn btn-primary">Giao công việc</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
