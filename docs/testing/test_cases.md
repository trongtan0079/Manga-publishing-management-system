# Tổng hợp Kịch bản Kiểm thử Hệ thống (System Test Cases)

Tài liệu này bao phủ tất cả Test Cases về mặt chức năng (Functional Testing) của mọi Module trên hệ thống Manga Publishing Management System.

### TC ID: FNC-01
- **Module**: Authentication
- **Chức năng**: Login
- **Tiền điều kiện**: Cung cấp tài khoản đúng
- **Các bước thực hiện**: Nhập username/password -> Submit
- **Kết quả mong đợi**: Chuyển hướng thành công tới Role Dashboard
- **Trạng thái**: **PASS**

### TC ID: FNC-02
- **Module**: Authentication
- **Chức năng**: Logout
- **Tiền điều kiện**: Đang đăng nhập
- **Các bước thực hiện**: Nhấn nút Logout trên Header
- **Kết quả mong đợi**: Xóa phiên, chuyển về trang Đăng nhập
- **Trạng thái**: **PASS**

### TC ID: FNC-03
- **Module**: User Mgmt
- **Chức năng**: Thêm User
- **Tiền điều kiện**: Đăng nhập Admin
- **Các bước thực hiện**: Điền Form tạo User -> Lưu
- **Kết quả mong đợi**: Báo thành công, User hiển thị ở DS
- **Trạng thái**: **PASS**

### TC ID: FNC-04
- **Module**: User Mgmt
- **Chức năng**: Sửa User
- **Tiền điều kiện**: Đăng nhập Admin
- **Các bước thực hiện**: Chọn User -> Đổi thông tin -> Lưu
- **Kết quả mong đợi**: Cập nhật DB, báo thành công
- **Trạng thái**: **PASS**

### TC ID: FNC-05
- **Module**: Series
- **Chức năng**: Tạo Series
- **Tiền điều kiện**: Đăng nhập Mangaka
- **Các bước thực hiện**: Tạo Truyện -> Điền Title/Status -> Lưu
- **Kết quả mong đợi**: Series xuất hiện trong DS truyện của tác giả
- **Trạng thái**: **PASS**

### TC ID: FNC-06
- **Module**: Series
- **Chức năng**: Sửa Series
- **Tiền điều kiện**: Mangaka có Series
- **Các bước thực hiện**: Chọn Sửa Series -> Cập nhật Title -> Lưu
- **Kết quả mong đợi**: Thông tin thay đổi, quay lại danh sách
- **Trạng thái**: **PASS**

### TC ID: FNC-07
- **Module**: Series
- **Chức năng**: Xóa Series
- **Tiền điều kiện**: Mangaka có Series
- **Các bước thực hiện**: Chọn Xóa Series -> Xác nhận
- **Kết quả mong đợi**: Series biến mất khỏi danh sách
- **Trạng thái**: **PASS**

### TC ID: FNC-08
- **Module**: Chapter
- **Chức năng**: Tạo Chapter
- **Tiền điều kiện**: Mangaka có Series
- **Các bước thực hiện**: Chọn Series -> Tạo Chapter -> Điền thông tin
- **Kết quả mong đợi**: Chapter hiển thị dưới Series
- **Trạng thái**: **PASS**

### TC ID: FNC-09
- **Module**: Chapter
- **Chức năng**: Submit Chapter
- **Tiền điều kiện**: Chapter chưa duyệt
- **Các bước thực hiện**: Nhấn Submit nộp cho Editor
- **Kết quả mong đợi**: Trạng thái chuyển sang `pending`
- **Trạng thái**: **PASS**

### TC ID: FNC-10
- **Module**: Page
- **Chức năng**: Tạo Page
- **Tiền điều kiện**: Mangaka có Chapter
- **Các bước thực hiện**: Chọn Chapter -> Upload hình ảnh -> Lưu
- **Kết quả mong đợi**: Page hiển thị, file lưu trong `uploads/`
- **Trạng thái**: **PASS**

### TC ID: FNC-11
- **Module**: Page
- **Chức năng**: Xóa Page
- **Tiền điều kiện**: Mangaka có Page
- **Các bước thực hiện**: Chọn Xóa Page
- **Kết quả mong đợi**: Page bị xóa khỏi DB và đĩa cứng (File vật lý)
- **Trạng thái**: **Verified Fixed**

### TC ID: FNC-12
- **Module**: Task
- **Chức năng**: Giao Task
- **Tiền điều kiện**: Mangaka có Page
- **Các bước thực hiện**: Chọn Page -> Giao việc -> Gắn cho Assistant
- **Kết quả mong đợi**: Task lưu vào DB, Assistant nhận Noti
- **Trạng thái**: **PASS**

### TC ID: FNC-13
- **Module**: Task
- **Chức năng**: Cập nhật Status
- **Tiền điều kiện**: Đăng nhập Assistant
- **Các bước thực hiện**: Mở Task -> Đổi status thành In Progress
- **Kết quả mong đợi**: Status chuyển thành màu xanh/vàng
- **Trạng thái**: **PASS**

### TC ID: FNC-14
- **Module**: Submission
- **Chức năng**: Nộp File (Submit)
- **Tiền điều kiện**: Assistant có Task
- **Các bước thực hiện**: Upload file kết quả -> Submit
- **Kết quả mong đợi**: Submission lưu vào DB, Mangaka nhận Noti
- **Trạng thái**: **Verified Fixed**

### TC ID: FNC-15
- **Module**: Submission
- **Chức năng**: Sửa đuôi File lỗi
- **Tiền điều kiện**: Assistant Upload lỗi
- **Các bước thực hiện**: Upload file `test.png` nhưng gốc là `jpeg`
- **Kết quả mong đợi**: File tự động sửa đuôi thành `.jpeg`
- **Trạng thái**: **Verified Fixed**

### TC ID: FNC-16
- **Module**: Review
- **Chức năng**: Duyệt Task
- **Tiền điều kiện**: Đăng nhập Mangaka
- **Các bước thực hiện**: Mở Review -> Cho ý kiến -> Approve
- **Kết quả mong đợi**: Task thành `completed`, Assistant nhận Noti
- **Trạng thái**: **PASS**

### TC ID: FNC-17
- **Module**: Review
- **Chức năng**: Duyệt Chapter
- **Tiền điều kiện**: Đăng nhập Editor
- **Các bước thực hiện**: Mở Pending Chapter -> Cho ý kiến -> Approve
- **Kết quả mong đợi**: Chapter thành `published`, gửi Noti
- **Trạng thái**: **PASS**

### TC ID: FNC-18
- **Module**: Review
- **Chức năng**: Từ chối Chapter
- **Tiền điều kiện**: Đăng nhập Editor
- **Các bước thực hiện**: Mở Pending Chapter -> Reject
- **Kết quả mong đợi**: Chapter thành `drafting`, yêu cầu sửa
- **Trạng thái**: **PASS**

### TC ID: FNC-19
- **Module**: Ranking
- **Chức năng**: Tạo Bảng Xếp hạng
- **Tiền điều kiện**: Đăng nhập Board
- **Các bước thực hiện**: Chọn Series -> Nhập Rank/Xu hướng -> Lưu
- **Kết quả mong đợi**: Ranking hiển thị, Mangaka nhận thông báo
- **Trạng thái**: **PASS**

### TC ID: FNC-20
- **Module**: Ranking
- **Chức năng**: Xóa Ranking
- **Tiền điều kiện**: Đăng nhập Board
- **Các bước thực hiện**: Chọn Xóa ở dòng Ranking
- **Kết quả mong đợi**: Ranking biến mất
- **Trạng thái**: **PASS**

### TC ID: FNC-21
- **Module**: Notification
- **Chức năng**: Xem Thông báo
- **Tiền điều kiện**: Bất kỳ
- **Các bước thực hiện**: Click biểu tượng Chuông ở Navbar
- **Kết quả mong đợi**: Hiển thị Dropdown các thông báo
- **Trạng thái**: **PASS**

### TC ID: FNC-22
- **Module**: Notification
- **Chức năng**: Đánh dấu Đã đọc
- **Tiền điều kiện**: Bất kỳ có Noti
- **Các bước thực hiện**: Click `Mark as Read` trên thông báo
- **Kết quả mong đợi**: Thông báo mờ đi, Badge trừ 1
- **Trạng thái**: **PASS**

### TC ID: FNC-23
- **Module**: Dashboard
- **Chức năng**: Thống kê số liệu
- **Tiền điều kiện**: Tất cả Role
- **Các bước thực hiện**: Truy cập trang chủ ngay sau khi Đăng nhập
- **Kết quả mong đợi**: Render đủ các Panel Chart / Bảng biểu
- **Trạng thái**: **PASS**

### TC ID: FNC-24
- **Module**: Dashboard
- **Chức năng**: Flash Message
- **Tiền điều kiện**: Bất kỳ
- **Các bước thực hiện**: Thực hiện 1 thao tác lỗi/thành công
- **Kết quả mong đợi**: Message hiển thị 1 lần, F5 biến mất
- **Trạng thái**: **Verified Fixed**
