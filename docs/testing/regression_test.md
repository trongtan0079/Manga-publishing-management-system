# Danh sách Kịch bản Kiểm thử Hồi quy (Regression Test Cases)

Quá trình Regression Testing được thực hiện sau Giai đoạn Bug Fix và Security Audit nhằm đảm bảo 100% tính nguyên vẹn của luồng nghiệp vụ. Dưới đây là kết quả rà soát:

## 1. Authentication
| ID | Kịch bản | Mong đợi | Kết quả thực tế | Trạng thái |
|----|----------|----------|-----------------|------------|
| RT-01 | Đăng nhập Admin / Mangaka / Assistant / Editor / Board | Chuyển đúng Dashboard của Role | Chuyển đúng Role, tạo đúng Session | **PASS** |
| RT-02 | Đăng xuất | Chuyển về trang chủ, xóa Session | Session xóa sạch | **PASS** |
| RT-03 | Quản lý Flash Message sau Login | Chỉ hiện thông báo thành công 1 lần | Thông báo biến mất sau khi tải lại | **PASS** |

## 2. Manga Production Workflow
| ID | Kịch bản | Mong đợi | Kết quả thực tế | Trạng thái |
|----|----------|----------|-----------------|------------|
| RT-04 | Mangaka tạo và xóa Page | Tạo Page thành công, khi xóa phải dọn dẹp file vật lý | File trong thư mục `uploads/pages` bị xóa | **PASS** |
| RT-05 | Mangaka giao Task cho Assistant | Task lưu vào DB với đúng trạng thái | Phân bổ Task đúng | **PASS** |
| RT-06 | Assistant Submit Submission | Nộp file khác đuôi nhưng cùng MIME type vẫn được lưu tự động | File tự động đổi đuôi và lưu thành công | **PASS** |
| RT-07 | Mangaka Approve Submission | Task đổi status thành `completed` | Trạng thái cập nhật chính xác | **PASS** |
| RT-08 | Editor duyệt Chapter | Chapter cập nhật sang `published` hoặc `rejected` | Luồng review Chapter nguyên vẹn | **PASS** |

## 3. Notification Module
| ID | Kịch bản | Mong đợi | Kết quả thực tế | Trạng thái |
|----|----------|----------|-----------------|------------|
| RT-09 | Push Notification khi giao Task | Có chuông báo, đúng ID | Thông báo nhảy đúng người | **PASS** |
| RT-10 | Mark as Read / Mark All As Read | Badge số lượng giảm xuống 0 | Trạng thái 'is_read' trong DB đổi thành 1 | **PASS** |
| RT-11 | Check Notification Ownership | Người khác không đọc được thông báo | Chặn 403 nếu URL bị sửa | **PASS** |

## 4. Dashboard & Ranking
| ID | Kịch bản | Mong đợi | Kết quả thực tế | Trạng thái |
|----|----------|----------|-----------------|------------|
| RT-12 | Thống kê Dashboard các Role | Tổng số chính xác, không biến Undefined | Tất cả số liệu render đúng | **PASS** |
| RT-13 | Board CRUD Ranking | Tạo, sửa, xóa Ranking thành công | Bảng SeriesRanking lưu dữ liệu chuẩn | **PASS** |

## 5. Security & DB Integrity
| ID | Kịch bản | Mong đợi | Kết quả thực tế | Trạng thái |
|----|----------|----------|-----------------|------------|
| RT-14 | Chạy lại 30 Test Cases Giai đoạn 2 | Chặn 100% các Role cấm | Mọi RBAC checks vẫn hoạt động tốt | **PASS** |
| RT-15 | Dữ liệu rác (Orphan Data) | Không có page ảo khi bị xóa | Khóa ngoại DB không bị vỡ | **PASS** |
| RT-16 | UI Validation | Giao diện Bootstrap không vỡ hạt | Form Upload và Bảng Dashboard căn lề chuẩn | **PASS** |
