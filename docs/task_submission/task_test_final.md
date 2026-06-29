# Báo Cáo Kết Quả Kiểm Thử Cuối Cùng (Final Test Report) - Module Task & Submission

Tài liệu này tổng hợp kết quả của đợt kiểm thử cuối cùng trước khi nghiệm thu nhằm đảm bảo tuyệt đối không còn lỗi tồn đọng hay lỗi hồi quy.

---

## 1. Kết Quả Kiểm Thử Theo Nhóm Chức Năng

### Nhóm 1: Kiểm thử Phân Quyền (Authorization & RBAC)
### Test ID: **T-AUTH-01**
- **Chức Năng Kiểm Tra**: Truy cập quản lý Task chưa đăng nhập
- **Vai Trò Thực Hiện**: Guest
- **Kết Quả Mong Đợi**: Bị chặn, redirect về trang Login
- **Kết Quả Thực Tế**: Chuyển hướng về trang Login
- **Trạng Thái**: **PASS**

### Test ID: **T-AUTH-02**
- **Chức Năng Kiểm Tra**: Tạo Task mới
- **Vai Trò Thực Hiện**: Assistant
- **Kết Quả Mong Đợi**: Bị chặn truy cập (HTTP 403 / Redirect)
- **Kết Quả Thực Tế**: Bị chặn bởi `requireRole('mangaka')`
- **Trạng Thái**: **PASS**

### Test ID: **T-AUTH-03**
- **Chức Năng Kiểm Tra**: Tạo Task mới
- **Vai Trò Thực Hiện**: Editor / Board
- **Kết Quả Mong Đợi**: Bị chặn truy cập
- **Kết Quả Thực Tế**: Bị chặn và redirect về Dashboard
- **Trạng Thái**: **PASS**

### Test ID: **T-AUTH-04**
- **Chức Năng Kiểm Tra**: Xóa Task
- **Vai Trò Thực Hiện**: Assistant
- **Kết Quả Mong Đợi**: Chặn hoàn toàn hành động
- **Kết Quả Thực Tế**: Bị chặn ngay lập tức
- **Trạng Thái**: **PASS**

### Nhóm 2: Kiểm thử Validation & Ràng Buộc Dữ Liệu
### Test ID: **T-VAL-01**
- **Chức Năng Kiểm Tra**: Gửi tiêu đề Task trống hoặc dài quá 255 ký tự
- **Vai Trò Thực Hiện**: Mangaka
- **Kết Quả Mong Đợi**: Báo lỗi validation, chặn lưu CSDL
- **Kết Quả Thực Tế**: Báo lỗi "Tiêu đề không được để trống / quá dài"
- **Trạng Thái**: **PASS**

### Test ID: **T-VAL-02**
- **Chức Năng Kiểm Tra**: Chọn Trợ lý không tồn tại / bị khóa
- **Vai Trò Thực Hiện**: Mangaka
- **Kết Quả Mong Đợi**: Báo lỗi Assistant không hợp lệ
- **Kết Quả Thực Tế**: Báo lỗi "Assistant không hợp lệ hoặc bị vô hiệu hóa"
- **Trạng Thái**: **PASS**

### Test ID: **T-VAL-03**
- **Chức Năng Kiểm Tra**: Nhập hạn chót sai định dạng
- **Vai Trò Thực Hiện**: Mangaka
- **Kết Quả Mong Đợi**: Chặn và thông báo ngày giờ không đúng
- **Kết Quả Thực Tế**: Báo lỗi "Hạn chót không đúng định dạng"
- **Trạng Thái**: **PASS**

### Test ID: **T-VAL-04**
- **Chức Năng Kiểm Tra**: Gửi trạng thái Task sai Whitelist
- **Vai Trò Thực Hiện**: Assistant / Mangaka
- **Kết Quả Mong Đợi**: Báo lỗi trạng thái không hợp lệ
- **Kết Quả Thực Tế**: Báo lỗi "Trạng thái không hợp lệ"
- **Trạng Thái**: **PASS**

### Nhóm 3: Kiểm thử Quyền Sở Hữu (Ownership) & URL Tampering
### Test ID: **T-OWN-01**
- **Chức Năng Kiểm Tra**: Thay đổi ID Task trên URL để sửa Task người khác
- **Vai Trò Thực Hiện**: Mangaka
- **Kết Quả Mong Đợi**: Chặn, báo lỗi và chuyển hướng
- **Kết Quả Thực Tế**: Báo lỗi quyền truy cập và redirect
- **Trạng Thái**: **PASS**

### Test ID: **T-OWN-02**
- **Chức Năng Kiểm Tra**: Nộp bản vẽ cho Task của Assistant khác
- **Vai Trò Thực Hiện**: Assistant
- **Kết Quả Mong Đợi**: Chặn nộp bài, báo lỗi Task không thuộc sở hữu
- **Kết Quả Thực Tế**: Báo lỗi "Task không hợp lệ hoặc không thuộc quyền sở hữu"
- **Trạng Thái**: **PASS**

### Test ID: **T-OWN-03**
- **Chức Năng Kiểm Tra**: Xem chi tiết bản thảo của người khác
- **Vai Trò Thực Hiện**: Assistant
- **Kết Quả Mong Đợi**: Trả về lỗi 403 Forbidden
- **Kết Quả Thực Tế**: Trả về 430/Redirect với thông điệp lỗi
- **Trạng Thái**: **PASS**

### Test ID: **T-OWN-04**
- **Chức Năng Kiểm Tra**: Xóa bản thảo của người khác
- **Vai Trò Thực Hiện**: Assistant / Mangaka
- **Kết Quả Mong Đợi**: Từ chối xóa bản thảo
- **Kết Quả Thực Tế**: Bị chặn ngay lập tức
- **Trạng Thái**: **PASS**

### Nhóm 4: Kiểm thử Tải File & Quản Lý File Vật Lý (Upload Security)
### Test ID: **T-FILE-01**
- **Chức Năng Kiểm Tra**: Upload tệp mã độc PHP đổi đuôi thành `.png`
- **Vai Trò Thực Hiện**: Assistant
- **Kết Quả Mong Đợi**: finfo_file & getimagesize phát hiện và chặn
- **Kết Quả Thực Tế**: Chặn thành công, báo ảnh giả mạo
- **Trạng Thái**: **PASS**

### Test ID: **T-FILE-02**
- **Chức Năng Kiểm Tra**: Upload tệp văn bản giả PDF/ZIP
- **Vai Trò Thực Hiện**: Assistant
- **Kết Quả Mong Đợi**: Kiểm tra signature chặn tệp giả
- **Kết Quả Thực Tế**: Phát hiện sai signature và từ chối tải
- **Trạng Thái**: **PASS**

### Test ID: **T-FILE-03**
- **Chức Năng Kiểm Tra**: Xóa bản thảo pending thành công
- **Vai Trò Thực Hiện**: Assistant
- **Kết Quả Mong Đợi**: Bản ghi biến mất khỏi DB và file bị xóa vật lý
- **Kết Quả Thực Tế**: File trong `uploads/submissions/` bị xóa sạch
- **Trạng Thái**: **PASS**

---

## 2. Kết Luận
Tất cả 15 kịch bản thử nghiệm bảo mật, chức năng và chịu tải tệp đều hoàn thành xuất sắc với trạng thái **ĐẠT (PASS)**.
Không phát hiện thấy bất kỳ lỗi hồi quy hay rủi ro tiềm ẩn nào đối với hệ thống.

**Task & Submission Module hoàn thành 100%, sẵn sàng Merge Git và Demo.**
