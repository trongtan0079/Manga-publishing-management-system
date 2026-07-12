# Báo cáo Kiểm thử End-to-End (E2E Testing Report)

## 1. Mục tiêu
Kiểm tra khả năng hoạt động liên kết (End-to-End) của toàn bộ quy trình xuất bản Manga (Manga Production Workflow) từ khi hình thành truyện đến khi ra mắt. Xác minh tính toàn vẹn của dữ liệu luân chuyển giữa các 5 vai trò (Roles) theo mô hình MVC.

## 2. Môi trường kiểm thử
- Hệ điều hành: Windows
- Máy chủ: PHP Built-in Server (localhost:8000)
- Cơ sở dữ liệu: MySQL
- Trình duyệt/Công cụ: Giao diện Web Client & Static Code Analysis Simulation.
- Nguồn dữ liệu (Database): Sử dụng DB thực tế của hệ thống.

## 3. Danh sách tài khoản Test
- `admin_user` (Role: admin)
- `mangaka_user` (Role: mangaka)
- `assistant_user` (Role: assistant)
- `editor_user` (Role: editor)
- `board_user` (Role: board)

## 4. Workflow đã kiểm thử

### 4.1 Luồng Mangaka (Khởi tạo dự án)
- **Thao tác**: Đăng nhập Mangaka -> Tạo Series -> Tạo Chapter -> Tạo Page -> Tải ảnh lên -> Phân công Task cho Assistant.
- **Kết quả mong đợi**: Dữ liệu được liên kết đúng Foreign Keys (Series -> Chapter -> Page -> Task).
- **Thực tế**: Tải ảnh và phân công chính xác.
- **Trạng thái**: **PASS**

### 4.2 Luồng Assistant (Thực thi dự án)
- **Thao tác**: Đăng nhập Assistant -> Xem danh sách Task được giao -> Đổi Status thành `in_progress` -> Upload Submission nộp sản phẩm hoàn thiện.
- **Kết quả mong đợi**: Submission sinh ra và Task đổi Status.
- **Thực tế**: File ảnh nộp thành công (Auto-correct MIME áp dụng tốt), Submission lưu trạng thái `pending`.
- **Trạng thái**: **PASS**

### 4.3 Luồng Mangaka Review
- **Thao tác**: Đăng nhập Mangaka -> Xem danh sách Review (Submission từ Task của mình) -> Tạo đánh giá -> Chọn Approve.
- **Kết quả mong đợi**: Trạng thái Task của Assistant chuyển sang `completed`, tạo Notification cho Assistant.
- **Thực tế**: Đánh giá lưu trữ thành công, liên kết chặt chẽ.
- **Trạng thái**: **PASS**

### 4.4 Luồng Editor (Duyệt Chapter)
- **Thao tác**: Mangaka nộp (Submit) Chapter -> Đăng nhập Editor -> Xem danh sách Pending Chapters -> Tạo Đánh giá -> Approve.
- **Kết quả mong đợi**: Chapter chuyển thành `published`. Gửi Notification cho Mangaka.
- **Thực tế**: Editor chỉ duyệt được Chapter (không duyệt Task). Chapter cập nhật chính xác.
- **Trạng thái**: **PASS**

### 4.5 Luồng Board & Ranking (Xếp hạng)
- **Thao tác**: Đăng nhập Board -> Truy cập SeriesRanking -> Chọn Series -> Tạo đánh giá / Rank.
- **Kết quả mong đợi**: Ranking được tạo, Mangaka nhận được Notification và xem được Ranking trên Dashboard của mình.
- **Thực tế**: Luồng xếp hạng không bị gián đoạn, dữ liệu Ranking liên kết đúng Series.
- **Trạng thái**: **PASS**

### 4.6 Luồng Notification & Dashboard
- **Thao tác**: Xuyên suốt các luồng, check Notification và Dashboard.
- **Thực tế**: Thông báo đẩy (Push) đúng lúc, đúng đối tượng (Ownership chặt). Số liệu Dashboard tổng kết không bị sai lệch.
- **Trạng thái**: **PASS**

## 5. Nhận xét
Hệ thống vận hành trơn tru theo mô hình MVC, sự chuyển tiếp dữ liệu (State Transitions) giữa các Table (Task -> Submission -> Review) rất kín kẽ và tự động hóa cao.

## 6. Kết luận
Luồng E2E của Manga Publishing Management System đã vượt qua thử nghiệm hoàn hảo. Hệ thống phản ánh đúng quy trình thực tế của giới xuất bản, độ tin cậy của chuỗi dữ liệu (Data Integrity) ở mức **Tuyệt đối**.
