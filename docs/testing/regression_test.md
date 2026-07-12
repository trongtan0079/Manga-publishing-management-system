# Danh sách Kịch bản Kiểm thử Hồi quy (Regression Test Cases)

Quá trình Regression Testing được thực hiện sau Giai đoạn Bug Fix và Security Audit nhằm đảm bảo 100% tính nguyên vẹn của luồng nghiệp vụ. Dưới đây là kết quả rà soát:

## 1. Authentication
### ID: RT-01
- **Kịch bản**: Đăng nhập Admin / Mangaka / Assistant / Editor / Board
- **Mong đợi**: Chuyển đúng Dashboard của Role
- **Kết quả thực tế**: Chuyển đúng Role, tạo đúng Session
- **Trạng thái**: **PASS**

### ID: RT-02
- **Kịch bản**: Đăng xuất
- **Mong đợi**: Chuyển về trang chủ, xóa Session
- **Kết quả thực tế**: Session xóa sạch
- **Trạng thái**: **PASS**

### ID: RT-03
- **Kịch bản**: Quản lý Flash Message sau Login
- **Mong đợi**: Chỉ hiện thông báo thành công 1 lần
- **Kết quả thực tế**: Thông báo biến mất sau khi tải lại
- **Trạng thái**: **PASS**

## 2. Manga Production Workflow
### ID: RT-04
- **Kịch bản**: Mangaka tạo và xóa Page
- **Mong đợi**: Tạo Page thành công, khi xóa phải dọn dẹp file vật lý
- **Kết quả thực tế**: File trong thư mục `uploads/pages` bị xóa
- **Trạng thái**: **PASS**

### ID: RT-05
- **Kịch bản**: Mangaka giao Task cho Assistant
- **Mong đợi**: Task lưu vào DB với đúng trạng thái
- **Kết quả thực tế**: Phân bổ Task đúng
- **Trạng thái**: **PASS**

### ID: RT-06
- **Kịch bản**: Assistant Submit Submission
- **Mong đợi**: Nộp file khác đuôi nhưng cùng MIME type vẫn được lưu tự động
- **Kết quả thực tế**: File tự động đổi đuôi và lưu thành công
- **Trạng thái**: **PASS**

### ID: RT-07
- **Kịch bản**: Mangaka Approve Submission
- **Mong đợi**: Task đổi status thành `completed`
- **Kết quả thực tế**: Trạng thái cập nhật chính xác
- **Trạng thái**: **PASS**

### ID: RT-08
- **Kịch bản**: Editor duyệt Chapter
- **Mong đợi**: Chapter cập nhật sang `published` hoặc `rejected`
- **Kết quả thực tế**: Luồng review Chapter nguyên vẹn
- **Trạng thái**: **PASS**

## 3. Notification Module
### ID: RT-09
- **Kịch bản**: Push Notification khi giao Task
- **Mong đợi**: Có chuông báo, đúng ID
- **Kết quả thực tế**: Thông báo nhảy đúng người
- **Trạng thái**: **PASS**

### ID: RT-10
- **Kịch bản**: Mark as Read / Mark All As Read
- **Mong đợi**: Badge số lượng giảm xuống 0
- **Kết quả thực tế**: Trạng thái 'is_read' trong DB đổi thành 1
- **Trạng thái**: **PASS**

### ID: RT-11
- **Kịch bản**: Check Notification Ownership
- **Mong đợi**: Người khác không đọc được thông báo
- **Kết quả thực tế**: Chặn 403 nếu URL bị sửa
- **Trạng thái**: **PASS**

## 4. Dashboard & Ranking
### ID: RT-12
- **Kịch bản**: Thống kê Dashboard các Role
- **Mong đợi**: Tổng số chính xác, không biến Undefined
- **Kết quả thực tế**: Tất cả số liệu render đúng
- **Trạng thái**: **PASS**

### ID: RT-13
- **Kịch bản**: Board CRUD Ranking
- **Mong đợi**: Tạo, sửa, xóa Ranking thành công
- **Kết quả thực tế**: Bảng SeriesRanking lưu dữ liệu chuẩn
- **Trạng thái**: **PASS**

## 5. Security & DB Integrity
### ID: RT-14
- **Kịch bản**: Chạy lại 30 Test Cases Giai đoạn 2
- **Mong đợi**: Chặn 100% các Role cấm
- **Kết quả thực tế**: Mọi RBAC checks vẫn hoạt động tốt
- **Trạng thái**: **PASS**

### ID: RT-15
- **Kịch bản**: Dữ liệu rác (Orphan Data)
- **Mong đợi**: Không có page ảo khi bị xóa
- **Kết quả thực tế**: Khóa ngoại DB không bị vỡ
- **Trạng thái**: **PASS**

### ID: RT-16
- **Kịch bản**: UI Validation
- **Mong đợi**: Giao diện Bootstrap không vỡ hạt
- **Kết quả thực tế**: Form Upload và Bảng Dashboard căn lề chuẩn
- **Trạng thái**: **PASS**
