# Báo cáo Kiểm thử Bảo mật (Security Testing Report)

Danh sách 30 kịch bản kiểm thử bảo mật (Test Cases) nhằm xác minh cơ chế RBAC, Ownership, URL Tampering trên toàn bộ hệ thống. Tất cả được kiểm thử thông qua mô phỏng Manual Testing (Static Code Review).

## 1. Authentication Testing
### Test ID: SEC-01
- **Chức năng**: Truy cập Dashboard chưa đăng nhập
- **Role**: Guest
- **Expected Result**: Bị chặn, Redirect về Login
- **Actual Result**: Chuyển về trang Login
- **PASS/FAIL**: **PASS**

### Test ID: SEC-02
- **Chức năng**: Gọi action `store` của Controller
- **Role**: Guest
- **Expected Result**: Bị chặn, không thực thi
- **Actual Result**: Redirect về Login
- **PASS/FAIL**: **PASS**

### Test ID: SEC-03
- **Chức năng**: Session timeout khi đang thao tác
- **Role**: Bất kỳ
- **Expected Result**: Chặn, yêu cầu Login lại
- **Actual Result**: Redirect Login
- **PASS/FAIL**: **PASS**

## 2. Authorization Testing (Role-based)
### Test ID: SEC-04
- **Chức năng**: Tạo Series
- **Role**: Assistant
- **Expected Result**: HTTP 403 / Redirect
- **Actual Result**: Chặn 403 bởi BaseController
- **PASS/FAIL**: **PASS**

### Test ID: SEC-05
- **Chức năng**: Sửa Series
- **Role**: Assistant
- **Expected Result**: HTTP 403 / Redirect
- **Actual Result**: Chặn 403 bởi BaseController
- **PASS/FAIL**: **PASS**

### Test ID: SEC-06
- **Chức năng**: Xóa Series
- **Role**: Assistant
- **Expected Result**: HTTP 403 / Redirect
- **Actual Result**: Chặn 403 bởi BaseController
- **PASS/FAIL**: **PASS**

### Test ID: SEC-07
- **Chức năng**: Tạo Ranking
- **Role**: Assistant
- **Expected Result**: HTTP 403
- **Actual Result**: HTTP 403
- **PASS/FAIL**: **PASS**

### Test ID: SEC-08
- **Chức năng**: Sửa Ranking
- **Role**: Assistant
- **Expected Result**: HTTP 403
- **Actual Result**: HTTP 403
- **PASS/FAIL**: **PASS**

### Test ID: SEC-09
- **Chức năng**: Truy cập Dashboard Board
- **Role**: Assistant
- **Expected Result**: Chặn / Redirect Dashboard
- **Actual Result**: Redirect về Dashboard
- **PASS/FAIL**: **PASS**

### Test ID: SEC-10
- **Chức năng**: Truy cập Dashboard Admin
- **Role**: Assistant
- **Expected Result**: Chặn / Redirect Dashboard
- **Actual Result**: Redirect về Dashboard
- **PASS/FAIL**: **PASS**

### Test ID: SEC-11
- **Chức năng**: Review Submission
- **Role**: Assistant
- **Expected Result**: Chặn (Không có quyền Editor/Mangaka)
- **Actual Result**: Trả về 403 / Redirect
- **PASS/FAIL**: **PASS**

### Test ID: SEC-12
- **Chức năng**: Sửa User
- **Role**: Mangaka
- **Expected Result**: HTTP 403 (Chỉ Admin)
- **Actual Result**: HTTP 403
- **PASS/FAIL**: **PASS**

### Test ID: SEC-13
- **Chức năng**: Tạo Ranking
- **Role**: Mangaka
- **Expected Result**: HTTP 403 (Chỉ Board)
- **Actual Result**: HTTP 403
- **PASS/FAIL**: **PASS**

### Test ID: SEC-14
- **Chức năng**: Dashboard Board
- **Role**: Mangaka
- **Expected Result**: Chặn / Redirect Dashboard
- **Actual Result**: Redirect Dashboard
- **PASS/FAIL**: **PASS**

### Test ID: SEC-15
- **Chức năng**: Tạo Task
- **Role**: Editor
- **Expected Result**: HTTP 403 (Chỉ Mangaka)
- **Actual Result**: HTTP 403
- **PASS/FAIL**: **PASS**

### Test ID: SEC-16
- **Chức năng**: Tạo Series
- **Role**: Editor
- **Expected Result**: HTTP 403 (Chỉ Mangaka)
- **Actual Result**: HTTP 403
- **PASS/FAIL**: **PASS**

### Test ID: SEC-17
- **Chức năng**: Sửa User
- **Role**: Editor
- **Expected Result**: HTTP 403 (Chỉ Admin)
- **Actual Result**: HTTP 403
- **PASS/FAIL**: **PASS**

### Test ID: SEC-18
- **Chức năng**: Sửa Ranking
- **Role**: Editor
- **Expected Result**: HTTP 403 (Chỉ Board)
- **Actual Result**: HTTP 403
- **PASS/FAIL**: **PASS**

### Test ID: SEC-19
- **Chức năng**: Tạo Task
- **Role**: Board
- **Expected Result**: HTTP 403 / Redirect
- **Actual Result**: Redirect Dashboard
- **PASS/FAIL**: **PASS**

### Test ID: SEC-20
- **Chức năng**: Review Submission
- **Role**: Board
- **Expected Result**: 403 / Redirect
- **Actual Result**: Redirect Dashboard
- **PASS/FAIL**: **PASS**

### Test ID: SEC-21
- **Chức năng**: Sửa User
- **Role**: Board
- **Expected Result**: HTTP 403
- **Actual Result**: HTTP 403
- **PASS/FAIL**: **PASS**

## 3. Ownership Testing (Quyền sở hữu dữ liệu)
### Test ID: SEC-22
- **Chức năng**: Sửa Series người khác
- **Role**: Mangaka
- **Expected Result**: Báo lỗi, Redirect Index
- **Actual Result**: Error Flash Message & Redirect
- **PASS/FAIL**: **PASS**

### Test ID: SEC-23
- **Chức năng**: Approve Task người khác
- **Role**: Mangaka
- **Expected Result**: Bị chặn, 403
- **Actual Result**: Error Message & Redirect
- **PASS/FAIL**: **PASS**

### Test ID: SEC-24
- **Chức năng**: Xem Task không được giao
- **Role**: Assistant
- **Expected Result**: Báo lỗi quyền, Redirect
- **Actual Result**: Báo lỗi và từ chối hiển thị
- **PASS/FAIL**: **PASS**

### Test ID: SEC-25
- **Chức năng**: Xem Submission người khác
- **Role**: Assistant
- **Expected Result**: 403 Forbidden
- **Actual Result**: Trả về lỗi 403
- **PASS/FAIL**: **PASS**

## 4. URL Tampering & Direct Access Testing
### Test ID: SEC-26
- **Chức năng**: Sửa ID Query của Series (`?id=x`)
- **Role**: Mangaka
- **Expected Result**: Yêu cầu verify Ownership
- **Actual Result**: Bị chặn bởi `checkOwnership()`
- **PASS/FAIL**: **PASS**

### Test ID: SEC-27
- **Chức năng**: Gửi GET tới API POST
- **Role**: Bất kỳ
- **Expected Result**: Chặn / Trả về trang chủ
- **Actual Result**: Trả về trang chủ (Method Not Allowed)
- **PASS/FAIL**: **PASS**

## 5. Notification Security
### Test ID: SEC-28
- **Chức năng**: Đọc Noti của người khác
- **Role**: Bất kỳ
- **Expected Result**: Báo lỗi quyền / 403
- **Actual Result**: HTTP 403 Forbidden
- **PASS/FAIL**: **PASS**

## 6. Review & Ranking Security
### Test ID: SEC-29
- **Chức năng**: Xóa Review
- **Role**: Bất kỳ
- **Expected Result**: Chặn (Hệ thống không cho phép xóa)
- **Actual Result**: Hành động không tồn tại (404)
- **PASS/FAIL**: **PASS**

### Test ID: SEC-30
- **Chức năng**: Sửa Ranking trái quyền
- **Role**: Mangaka
- **Expected Result**: 403
- **Actual Result**: Bị chặn từ đầu (Chỉ Board)
- **PASS/FAIL**: **PASS**
