# Security Test Cases (Manual/Static Analysis)

Danh sách này ghi nhận 30 kịch bản kiểm thử bảo mật (Test Cases) đã được phân tích thông qua rà soát tĩnh (Static Code Analysis) giả lập thao tác Manual Testing. Tất cả mã nguồn đều đã được đối chiếu để xác nhận hành vi phản hồi từ Server.

## 1. Authentication (Xác thực)
| ID | Tên Kịch Bản | Role | Controller | Action | Kết quả mong đợi | Kết quả thực tế | PASS/FAIL |
|----|--------------|------|------------|--------|------------------|-----------------|-----------|
| TC-01 | Khách vãng lai truy cập Dashboard | Guest | Dashboard | index | Redirect Login | Redirect Login (`\requireLogin()`) | **PASS** |
| TC-02 | Khách vãng lai gọi action `store` | Guest | Series | store | Redirect Login | Redirect Login (`\requireLogin()`) | **PASS** |
| TC-03 | Session hết hạn thao tác Review | Bất kỳ | Review | create | Redirect Login | Redirect Login (`\requireLogin()`) | **PASS** |

## 2. Authorization (Phân quyền Role)
| ID | Tên Kịch Bản | Role | Controller | Action | Kết quả mong đợi | Kết quả thực tế | PASS/FAIL |
|----|--------------|------|------------|--------|------------------|-----------------|-----------|
| TC-04 | Assistant tạo Series | Assistant | Series | create | HTTP 403 / Denied | HTTP 403 (Chặn ở Base `requireRole`) | **PASS** |
| TC-05 | Assistant sửa Series | Assistant | Series | edit | HTTP 403 / Denied | HTTP 403 (Chặn ở Base `requireRole`) | **PASS** |
| TC-06 | Assistant xóa Series | Assistant | Series | delete | HTTP 403 / Denied | HTTP 403 (Chặn ở Base `requireRole`) | **PASS** |
| TC-07 | Assistant tạo Ranking | Assistant | SeriesRanking | create | HTTP 403 / Denied | HTTP 403 | **PASS** |
| TC-08 | Assistant sửa Ranking | Assistant | SeriesRanking | edit | HTTP 403 / Denied | HTTP 403 | **PASS** |
| TC-09 | Assistant truy cập Dashboard Board | Assistant | Dashboard | board | Redirect Dashboard | Redirect Dashboard | **PASS** |
| TC-10 | Assistant truy cập Dashboard Admin | Assistant | Dashboard | admin | Redirect Dashboard | Redirect Dashboard | **PASS** |
| TC-11 | Assistant Review Submission | Assistant | Review | create | 403 / Redirect | Redirect Dashboard | **PASS** |
| TC-12 | Mangaka sửa thông tin User | Mangaka | User | edit | 403 / Redirect | HTTP 403 | **PASS** |
| TC-13 | Mangaka tạo Ranking | Mangaka | SeriesRanking | create | HTTP 403 | HTTP 403 | **PASS** |
| TC-14 | Mangaka truy cập Dashboard Board | Mangaka | Dashboard | board | Redirect Dashboard | Redirect Dashboard | **PASS** |
| TC-15 | Editor tạo Task | Editor | Task | create | 403 / Redirect | Redirect / Lỗi Quyền | **PASS** |
| TC-16 | Editor tạo Series | Editor | Series | create | 403 / Redirect | HTTP 403 | **PASS** |
| TC-17 | Editor sửa User | Editor | User | edit | 403 / Redirect | HTTP 403 | **PASS** |
| TC-18 | Editor sửa Ranking | Editor | SeriesRanking | edit | 403 / Redirect | HTTP 403 | **PASS** |
| TC-19 | Board tạo Task | Board | Task | create | 403 / Redirect | Redirect Dashboard | **PASS** |
| TC-20 | Board duyệt Submission | Board | Review | create | 403 / Redirect | Redirect Dashboard | **PASS** |
| TC-21 | Board sửa User | Board | User | edit | 403 / Redirect | HTTP 403 | **PASS** |

## 3. Ownership (Sở hữu dữ liệu)
| ID | Tên Kịch Bản | Role | Controller | Action | Kết quả mong đợi | Kết quả thực tế | PASS/FAIL |
|----|--------------|------|------------|--------|------------------|-----------------|-----------|
| TC-22 | Mangaka quản lý Series người khác | Mangaka | Series | edit | 403 / Error Alert | Trả về Error Alert & Redirect | **PASS** |
| TC-23 | Mangaka Review Task người khác | Mangaka | Review | create | Chặn (Không hiển thị, chặn lưu) | Báo lỗi và chuyển hướng | **PASS** |
| TC-24 | Assistant xem Task của người khác | Assistant | Task | show | Chặn / Error Alert | Trả về Error Alert & Redirect | **PASS** |
| TC-25 | Assistant xem Submission người khác | Assistant | Submission | show | Chặn (Chỉ query DB WHERE user_id) | Chặn truy vấn | **PASS** |
| TC-26 | Đọc Notification của người khác | Bất kỳ | Notification | markAsRead | 403 Forbidden | HTTP 403 Trả về kèm Alert | **PASS** |
| TC-27 | Mangaka sửa/xóa Ranking | Mangaka | SeriesRanking | edit | HTTP 403 | HTTP 403 | **PASS** |

## 4. URL Tampering (Truy cập trực tiếp)
| ID | Tên Kịch Bản | Role | Controller | Action | Kết quả mong đợi | Kết quả thực tế | PASS/FAIL |
|----|--------------|------|------------|--------|------------------|-----------------|-----------|
| TC-28 | Truy cập POST API qua GET URL | Bất kỳ | Task | store | Chặn không thực thi | Redirect về index do không phải POST | **PASS** |
| TC-29 | Xóa Review trực tiếp qua URL | Bất kỳ | Review | delete | Không tồn tại action | HTTP 404 / Bỏ qua | **PASS** |
| TC-30 | Thay đổi Tham số ID trên Query | Mangaka | Page | edit | Kiểm tra Quyền sở hữu | Bắt buộc thông qua `checkChapterOwnership` | **PASS** |
