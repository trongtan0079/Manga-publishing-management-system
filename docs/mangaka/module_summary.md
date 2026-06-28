# Tóm Tắt Module Mangaka (Module Summary)

## Mục Đích
Module Mangaka chịu trách nhiệm cung cấp giao diện tương tác và không gian làm việc chuyên biệt dành cho Tác giả (Mangaka) trong hệ thống quản lý xuất bản Manga. Module bao gồm các màn hình quản lý bộ truyện (Series), quản lý chương hồi (Chapter), quản lý trang vẽ (Page), giao việc cho trợ lý (Task), nộp bản thảo cho biên tập viên (Submission), và theo dõi bảng xếp hạng tác phẩm (Rankings).

## Cấu Trúc Giao Diện (Views Directory: `views/mangaka/`)
Danh sách các view đang hoạt động chính thức:
- **Dự án & Truyện:** [series.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/series.php), [series_create.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/series_create.php), [series_detail.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/series_detail.php), [series_edit.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/series_edit.php)
- **Chương hồi:** [chapter_create.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/chapter_create.php), [chapter_detail.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/chapter_detail.php), [chapter_edit.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/chapter_edit.php)
- **Trang truyện:** [page_create.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/page_create.php), [page_detail.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/page_detail.php), [page_edit.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/page_edit.php)
- **Công việc Trợ lý (Task):** [task_create.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/task_create.php), [task_edit.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/task_edit.php)
- **Nộp bài & Xếp hạng:** [submission_create.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/submission_create.php), [rankings.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/rankings.php)
- **Bảng điều khiển (Thuộc phạm vi Người 1):** [dashboard.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/dashboard.php)

## Phân Phối Nhiệm Vụ Phát Triển
- **Người 1:** Đảm nhận Controller nghiệp vụ, Model, Database Schema, Workflow, Logic nghiệp vụ, Page Status ENUM, BASE_PATH, Layout chung (header/navbar/sidebar/footer) và Dashboard.
- **Người 2:** Đảm nhận Việt hóa giao diện chuẩn hóa (UI Localization), kiểm tra dọn dẹp file thừa không sử dụng, và xây dựng bộ tài liệu kỹ thuật (Documentation).
