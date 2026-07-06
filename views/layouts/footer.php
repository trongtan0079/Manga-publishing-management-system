    </div> <!-- /container-fluid -->

    <!-- Footer -->
    <footer class="py-3 mt-auto bg-white border-top border-light-subtle" style="font-size: 0.825rem; color: var(--slate-500);">
        <div class="container-fluid px-lg-5">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    &copy; <?= date('Y') ?> <strong class="text-dark">Manga<span style="color: #6366f1;">PMS</span></strong>. Toàn bộ bản quyền được bảo lưu.
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span>Phiên bản <strong>1.1.0 Stable</strong></span>
                    <span style="opacity: 0.5;">|</span>
                    <span>Hệ thống quản lý quy trình xuất bản</span>
                </div>
            </div>
        </div>
    </footer>
</main> <!-- /main -->

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var toggleBtn = document.getElementById("desktopSidebarToggle");
        if (toggleBtn) {
            toggleBtn.addEventListener("click", function() {
                document.body.classList.toggle("sidebar-collapsed");
            });
        }
    });

    // Hàm hỗ trợ chèn cú pháp in đậm, in nghiêng, gạch ngang cho textarea
    function insertFormatting(id, syntax) {
        const textarea = document.getElementById(id);
        if (!textarea) return;
        
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        let selectedText = text.substring(start, end);
        
        let placeholderUsed = false;
        if (selectedText.length === 0) {
            placeholderUsed = true;
            if (syntax === '**') selectedText = 'chữ in đậm';
            else if (syntax === '*') selectedText = 'chữ in nghiêng';
            else if (syntax === '~~') selectedText = 'chữ gạch ngang';
            else selectedText = 'văn bản';
        }
        
        const replacement = syntax + selectedText + syntax;
        
        textarea.value = text.substring(0, start) + replacement + text.substring(end);
        textarea.focus();
        
        if (placeholderUsed) {
            // Bôi đen chữ placeholder để người dùng gõ đè lên được ngay
            textarea.setSelectionRange(start + syntax.length, start + syntax.length + selectedText.length);
        } else {
            // Đưa con trỏ chuột về sau cụm từ định dạng
            textarea.setSelectionRange(start + replacement.length, start + replacement.length);
        }
        
        // Trigger input event
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
    }

    // Hàm hỗ trợ chèn danh sách gạch đầu dòng
    function insertList(id) {
        const textarea = document.getElementById(id);
        if (!textarea) return;
        
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        let selectedText = text.substring(start, end);
        
        let replacement = '';
        if (selectedText.length > 0) {
            replacement = selectedText.split('\n').map(line => line.startsWith('- ') ? line : '- ' + line).join('\n');
        } else {
            selectedText = 'dòng danh sách';
            replacement = '\n- ' + selectedText;
        }
        
        textarea.value = text.substring(0, start) + replacement + text.substring(end);
        textarea.focus();
        
        if (selectedText === 'dòng danh sách') {
            textarea.setSelectionRange(start + 3, start + 3 + selectedText.length);
        } else {
            textarea.setSelectionRange(start + replacement.length, start + replacement.length);
        }
        
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
    }
</script>
<?php
// Chuẩn hóa Flash Message: Đảm bảo thông báo chỉ hiển thị 1 lần
unset($_SESSION['success']);
unset($_SESSION['error']);
unset($_SESSION['warning']);
unset($_SESSION['info']);
?>
</body>
</html>
