# Admin Dashboard Report

## Tổng quan
Dashboard Admin hiển thị toàn bộ thống kê hệ thống bao gồm:
- 9 Stat Cards chính: User, Series, Chapter, Page, Task, Submission, Review, Notification, Ranking.
- 3 Stat Cards phụ: Active Users, Inactive Users, Banned Users.
- 3 Biểu đồ Chart.js: User theo Vai trò (Bar), Task theo Trạng thái (Doughnut), Bản thảo theo Trạng thái (Doughnut).
- Widget thông báo gần đây.

## Dữ liệu
- Tất cả dữ liệu được truy vấn trực tiếp từ Database thông qua các Model.
- Không sử dụng cache hay bảng tạm.
- Biểu đồ sử dụng Chart.js v4 qua CDN, không cài đặt package.

## Phân quyền
- Chỉ Admin mới có quyền truy cập Dashboard Admin.
- Sử dụng `requireRole('admin')` để kiểm tra quyền.