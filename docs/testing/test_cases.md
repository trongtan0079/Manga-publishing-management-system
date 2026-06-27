# Tổng hợp Kịch bản Kiểm thử Hệ thống (System Test Cases)

Tài liệu này bao phủ tất cả Test Cases về mặt chức năng (Functional Testing) của mọi Module trên hệ thống Manga Publishing Management System.

| TC ID | Module | Chức năng | Tiền điều kiện | Các bước thực hiện | Kết quả mong đợi | Trạng thái |
|-------|--------|-----------|----------------|--------------------|------------------|------------|
| FNC-01 | Authentication | Login | Cung cấp tài khoản đúng | Nhập username/password -> Submit | Chuyển hướng thành công tới Role Dashboard | **PASS** |
| FNC-02 | Authentication | Logout | Đang đăng nhập | Nhấn nút Logout trên Header | Xóa phiên, chuyển về trang Đăng nhập | **PASS** |
| FNC-03 | User Mgmt | Thêm User | Đăng nhập Admin | Điền Form tạo User -> Lưu | Báo thành công, User hiển thị ở DS | **PASS** |
| FNC-04 | User Mgmt | Sửa User | Đăng nhập Admin | Chọn User -> Đổi thông tin -> Lưu | Cập nhật DB, báo thành công | **PASS** |
| FNC-05 | Series | Tạo Series | Đăng nhập Mangaka | Tạo Truyện -> Điền Title/Status -> Lưu | Series xuất hiện trong DS truyện của tác giả | **PASS** |
| FNC-06 | Series | Sửa Series | Mangaka có Series | Chọn Sửa Series -> Cập nhật Title -> Lưu | Thông tin thay đổi, quay lại danh sách | **PASS** |
| FNC-07 | Series | Xóa Series | Mangaka có Series | Chọn Xóa Series -> Xác nhận | Series biến mất khỏi danh sách | **PASS** |
| FNC-08 | Chapter | Tạo Chapter | Mangaka có Series | Chọn Series -> Tạo Chapter -> Điền thông tin | Chapter hiển thị dưới Series | **PASS** |
| FNC-09 | Chapter | Submit Chapter | Chapter chưa duyệt | Nhấn Submit nộp cho Editor | Trạng thái chuyển sang `pending` | **PASS** |
| FNC-10 | Page | Tạo Page | Mangaka có Chapter | Chọn Chapter -> Upload hình ảnh -> Lưu | Page hiển thị, file lưu trong `uploads/` | **PASS** |
| FNC-11 | Page | Xóa Page | Mangaka có Page | Chọn Xóa Page | Page bị xóa khỏi DB và đĩa cứng (File vật lý) | **Verified Fixed** |
| FNC-12 | Task | Giao Task | Mangaka có Page | Chọn Page -> Giao việc -> Gắn cho Assistant | Task lưu vào DB, Assistant nhận Noti | **PASS** |
| FNC-13 | Task | Cập nhật Status| Đăng nhập Assistant | Mở Task -> Đổi status thành In Progress | Status chuyển thành màu xanh/vàng | **PASS** |
| FNC-14 | Submission | Nộp File (Submit)| Assistant có Task | Upload file kết quả -> Submit | Submission lưu vào DB, Mangaka nhận Noti | **Verified Fixed** |
| FNC-15 | Submission | Sửa đuôi File lỗi | Assistant Upload lỗi | Upload file `test.png` nhưng gốc là `jpeg` | File tự động sửa đuôi thành `.jpeg` | **Verified Fixed** |
| FNC-16 | Review | Duyệt Task | Đăng nhập Mangaka | Mở Review -> Cho ý kiến -> Approve | Task thành `completed`, Assistant nhận Noti | **PASS** |
| FNC-17 | Review | Duyệt Chapter | Đăng nhập Editor | Mở Pending Chapter -> Cho ý kiến -> Approve | Chapter thành `published`, gửi Noti | **PASS** |
| FNC-18 | Review | Từ chối Chapter | Đăng nhập Editor | Mở Pending Chapter -> Reject | Chapter thành `drafting`, yêu cầu sửa | **PASS** |
| FNC-19 | Ranking | Tạo Bảng Xếp hạng| Đăng nhập Board | Chọn Series -> Nhập Rank/Xu hướng -> Lưu | Ranking hiển thị, Mangaka nhận thông báo | **PASS** |
| FNC-20 | Ranking | Xóa Ranking | Đăng nhập Board | Chọn Xóa ở dòng Ranking | Ranking biến mất | **PASS** |
| FNC-21 | Notification | Xem Thông báo | Bất kỳ | Click biểu tượng Chuông ở Navbar | Hiển thị Dropdown các thông báo | **PASS** |
| FNC-22 | Notification | Đánh dấu Đã đọc | Bất kỳ có Noti | Click `Mark as Read` trên thông báo | Thông báo mờ đi, Badge trừ 1 | **PASS** |
| FNC-23 | Dashboard | Thống kê số liệu | Tất cả Role | Truy cập trang chủ ngay sau khi Đăng nhập | Render đủ các Panel Chart / Bảng biểu | **PASS** |
| FNC-24 | Dashboard | Flash Message | Bất kỳ | Thực hiện 1 thao tác lỗi/thành công | Message hiển thị 1 lần, F5 biến mất | **Verified Fixed** |
