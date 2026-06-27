# Báo Cáo Kết Quả Kiểm Thử Cuối Cùng (Final Test Report) - Module Task & Submission

Tài liệu này tổng hợp kết quả của đợt kiểm thử cuối cùng trước khi nghiệm thu nhằm đảm bảo tuyệt đối không còn lỗi tồn đọng hay lỗi hồi quy.

---

## 1. Kết Quả Kiểm Thử Theo Nhóm Chức Năng

### Nhóm 1: Kiểm thử Phân Quyền (Authorization & RBAC)
| Test ID | Chức Năng Kiểm Tra | Vai Trò Thực Hiện | Kết Quả Mong Đợi | Kết Quả Thực Tế | Trạng Thái |
|---------|---------------------|-------------------|------------------|-----------------|------------|
| **T-AUTH-01** | Truy cập quản lý Task chưa đăng nhập | Guest | Bị chặn, redirect về trang Login | Chuyển hướng về trang Login | **PASS** |
| **T-AUTH-02** | Tạo Task mới | Assistant | Bị chặn truy cập (HTTP 403 / Redirect) | Bị chặn bởi `requireRole('mangaka')` | **PASS** |
| **T-AUTH-03** | Tạo Task mới | Editor / Board | Bị chặn truy cập | Bị chặn và redirect về Dashboard | **PASS** |
| **T-AUTH-04** | Xóa Task | Assistant | Chặn hoàn toàn hành động | Bị chặn ngay lập tức | **PASS** |

### Nhóm 2: Kiểm thử Validation & Ràng Buộc Dữ Liệu
| Test ID | Chức Năng Kiểm Tra | Vai Trò Thực Hiện | Kết Quả Mong Đợi | Kết Quả Thực Tế | Trạng Thái |
|---------|---------------------|-------------------|------------------|-----------------|------------|
| **T-VAL-01** | Gửi tiêu đề Task trống hoặc dài quá 255 ký tự | Mangaka | Báo lỗi validation, chặn lưu CSDL | Báo lỗi "Tiêu đề không được để trống / quá dài" | **PASS** |
| **T-VAL-02** | Chọn Trợ lý không tồn tại / bị khóa | Mangaka | Báo lỗi Assistant không hợp lệ | Báo lỗi "Assistant không hợp lệ hoặc bị vô hiệu hóa" | **PASS** |
| **T-VAL-03** | Nhập hạn chót sai định dạng | Mangaka | Chặn và thông báo ngày giờ không đúng | Báo lỗi "Hạn chót không đúng định dạng" | **PASS** |
| **T-VAL-04** | Gửi trạng thái Task sai Whitelist | Assistant / Mangaka | Báo lỗi trạng thái không hợp lệ | Báo lỗi "Trạng thái không hợp lệ" | **PASS** |

### Nhóm 3: Kiểm thử Quyền Sở Hữu (Ownership) & URL Tampering
| Test ID | Chức Năng Kiểm Tra | Vai Trò Thực Hiện | Kết Quả Mong Đợi | Kết Quả Thực Tế | Trạng Thái |
|---------|---------------------|-------------------|------------------|-----------------|------------|
| **T-OWN-01** | Thay đổi ID Task trên URL để sửa Task người khác | Mangaka | Chặn, báo lỗi và chuyển hướng | Báo lỗi quyền truy cập và redirect | **PASS** |
| **T-OWN-02** | Nộp bản vẽ cho Task của Assistant khác | Assistant | Chặn nộp bài, báo lỗi Task không thuộc sở hữu | Báo lỗi "Task không hợp lệ hoặc không thuộc quyền sở hữu" | **PASS** |
| **T-OWN-03** | Xem chi tiết bản thảo của người khác | Assistant | Trả về lỗi 403 Forbidden | Trả về 430/Redirect với thông điệp lỗi | **PASS** |
| **T-OWN-04** | Xóa bản thảo của người khác | Assistant / Mangaka | Từ chối xóa bản thảo | Bị chặn ngay lập tức | **PASS** |

### Nhóm 4: Kiểm thử Tải File & Quản Lý File Vật Lý (Upload Security)
| Test ID | Chức Năng Kiểm Tra | Vai Trò Thực Hiện | Kết Quả Mong Đợi | Kết Quả Thực Tế | Trạng Thái |
|---------|---------------------|-------------------|------------------|-----------------|------------|
| **T-FILE-01** | Upload tệp mã độc PHP đổi đuôi thành `.png` | Assistant | finfo_file & getimagesize phát hiện và chặn | Chặn thành công, báo ảnh giả mạo | **PASS** |
| **T-FILE-02** | Upload tệp văn bản giả PDF/ZIP | Assistant | Kiểm tra signature chặn tệp giả | Phát hiện sai signature và từ chối tải | **PASS** |
| **T-FILE-03** | Xóa bản thảo pending thành công | Assistant | Bản ghi biến mất khỏi DB và file bị xóa vật lý | File trong `uploads/submissions/` bị xóa sạch | **PASS** |

---

## 2. Kết Luận
Tất cả 15 kịch bản thử nghiệm bảo mật, chức năng và chịu tải tệp đều hoàn thành xuất sắc với trạng thái **ĐẠT (PASS)**.
Không phát hiện thấy bất kỳ lỗi hồi quy hay rủi ro tiềm ẩn nào đối với hệ thống.

**Task & Submission Module hoàn thành 100%, sẵn sàng Merge Git và Demo.**
