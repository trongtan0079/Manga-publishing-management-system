# Báo cáo Kiểm thử Bảo mật (Security Testing Report)

Danh sách 30 kịch bản kiểm thử bảo mật (Test Cases) nhằm xác minh cơ chế RBAC, Ownership, URL Tampering trên toàn bộ hệ thống. Tất cả được kiểm thử thông qua mô phỏng Manual Testing (Static Code Review).

## 1. Authentication Testing
| Test ID | Chức năng | Role | Expected Result | Actual Result | PASS/FAIL |
|---------|----------|------|-----------------|---------------|-----------|
| SEC-01 | Truy cập Dashboard chưa đăng nhập | Guest | Bị chặn, Redirect về Login | Chuyển về trang Login | **PASS** |
| SEC-02 | Gọi action `store` của Controller | Guest | Bị chặn, không thực thi | Redirect về Login | **PASS** |
| SEC-03 | Session timeout khi đang thao tác | Bất kỳ | Chặn, yêu cầu Login lại | Redirect Login | **PASS** |

## 2. Authorization Testing (Role-based)
| Test ID | Chức năng | Role | Expected Result | Actual Result | PASS/FAIL |
|---------|----------|------|-----------------|---------------|-----------|
| SEC-04 | Tạo Series | Assistant | HTTP 403 / Redirect | Chặn 403 bởi BaseController | **PASS** |
| SEC-05 | Sửa Series | Assistant | HTTP 403 / Redirect | Chặn 403 bởi BaseController | **PASS** |
| SEC-06 | Xóa Series | Assistant | HTTP 403 / Redirect | Chặn 403 bởi BaseController | **PASS** |
| SEC-07 | Tạo Ranking | Assistant | HTTP 403 | HTTP 403 | **PASS** |
| SEC-08 | Sửa Ranking | Assistant | HTTP 403 | HTTP 403 | **PASS** |
| SEC-09 | Truy cập Dashboard Board | Assistant | Chặn / Redirect Dashboard | Redirect về Dashboard | **PASS** |
| SEC-10 | Truy cập Dashboard Admin | Assistant | Chặn / Redirect Dashboard | Redirect về Dashboard | **PASS** |
| SEC-11 | Review Submission | Assistant | Chặn (Không có quyền Editor/Mangaka) | Trả về 403 / Redirect | **PASS** |
| SEC-12 | Sửa User | Mangaka | HTTP 403 (Chỉ Admin) | HTTP 403 | **PASS** |
| SEC-13 | Tạo Ranking | Mangaka | HTTP 403 (Chỉ Board) | HTTP 403 | **PASS** |
| SEC-14 | Dashboard Board | Mangaka | Chặn / Redirect Dashboard | Redirect Dashboard | **PASS** |
| SEC-15 | Tạo Task | Editor | HTTP 403 (Chỉ Mangaka) | HTTP 403 | **PASS** |
| SEC-16 | Tạo Series | Editor | HTTP 403 (Chỉ Mangaka) | HTTP 403 | **PASS** |
| SEC-17 | Sửa User | Editor | HTTP 403 (Chỉ Admin) | HTTP 403 | **PASS** |
| SEC-18 | Sửa Ranking | Editor | HTTP 403 (Chỉ Board) | HTTP 403 | **PASS** |
| SEC-19 | Tạo Task | Board | HTTP 403 / Redirect | Redirect Dashboard | **PASS** |
| SEC-20 | Review Submission | Board | 403 / Redirect | Redirect Dashboard | **PASS** |
| SEC-21 | Sửa User | Board | HTTP 403 | HTTP 403 | **PASS** |

## 3. Ownership Testing (Quyền sở hữu dữ liệu)
| Test ID | Chức năng | Role | Expected Result | Actual Result | PASS/FAIL |
|---------|----------|------|-----------------|---------------|-----------|
| SEC-22 | Sửa Series người khác | Mangaka | Báo lỗi, Redirect Index | Error Flash Message & Redirect | **PASS** |
| SEC-23 | Approve Task người khác | Mangaka | Bị chặn, 403 | Error Message & Redirect | **PASS** |
| SEC-24 | Xem Task không được giao | Assistant | Báo lỗi quyền, Redirect | Báo lỗi và từ chối hiển thị | **PASS** |
| SEC-25 | Xem Submission người khác | Assistant | 403 Forbidden | Trả về lỗi 403 | **PASS** |

## 4. URL Tampering & Direct Access Testing
| Test ID | Chức năng | Role | Expected Result | Actual Result | PASS/FAIL |
|---------|----------|------|-----------------|---------------|-----------|
| SEC-26 | Sửa ID Query của Series (`?id=x`) | Mangaka | Yêu cầu verify Ownership | Bị chặn bởi `checkOwnership()` | **PASS** |
| SEC-27 | Gửi GET tới API POST | Bất kỳ | Chặn / Trả về trang chủ | Trả về trang chủ (Method Not Allowed) | **PASS** |

## 5. Notification Security
| Test ID | Chức năng | Role | Expected Result | Actual Result | PASS/FAIL |
|---------|----------|------|-----------------|---------------|-----------|
| SEC-28 | Đọc Noti của người khác | Bất kỳ | Báo lỗi quyền / 403 | HTTP 403 Forbidden | **PASS** |

## 6. Review & Ranking Security
| Test ID | Chức năng | Role | Expected Result | Actual Result | PASS/FAIL |
|---------|----------|------|-----------------|---------------|-----------|
| SEC-29 | Xóa Review | Bất kỳ | Chặn (Hệ thống không cho phép xóa) | Hành động không tồn tại (404) | **PASS** |
| SEC-30 | Sửa Ranking trái quyền | Mangaka | 403 | Bị chặn từ đầu (Chỉ Board) | **PASS** |
