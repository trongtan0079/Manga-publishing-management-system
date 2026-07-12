# Báo Cáo Kiểm Thử Cuối Cùng (Final Test) - Module Series

**Ngày kiểm thử:** Theo timestamp hệ thống
**Kết quả chung:** PASS

### Hạng mục Kiểm thử: CRUD Series
- **Các Case**: Create, Read, Update, Delete.
- **Kết quả (PASS/FAIL)**: **PASS**
- **Ghi chú**: Kiểm tra độ dài title hoạt động.

### Hạng mục Kiểm thử: CRUD Chapter
- **Các Case**: Create, Read, Update, Delete.
- **Kết quả (PASS/FAIL)**: **PASS**
- **Ghi chú**: Kiểm tra trùng `chapter_number` hoạt động.

### Hạng mục Kiểm thử: CRUD Page
- **Các Case**: Upload ảnh mới, Đổi ảnh cũ, Xóa trang.
- **Kết quả (PASS/FAIL)**: **PASS**
- **Ghi chú**: Xóa vật lý (unlink) chạy bình thường.

### Hạng mục Kiểm thử: Role Check
- **Các Case**: Đăng nhập bằng tài khoản không phải Mangaka.
- **Kết quả (PASS/FAIL)**: **PASS**
- **Ghi chú**: Nhận lỗi 403 hoặc Redirect ra ngoài.

### Hạng mục Kiểm thử: Ownership Tamper
- **Các Case**: Cố tình truyền ID bài của người khác.
- **Kết quả (PASS/FAIL)**: **PASS**
- **Ghi chú**: Bị từ chối và báo lỗi sở hữu.

### Hạng mục Kiểm thử: Malicious Upload
- **Các Case**: Upload tệp `.php` đuôi `.jpg`.
- **Kết quả (PASS/FAIL)**: **PASS**
- **Ghi chú**: Bị chặn bởi `finfo_file` MIME validation.

**Quyết định:** Mọi tính năng cốt lõi và giới hạn an ninh đã được nghiệm thu và cho kết quả XANH (PASS). Sẵn sàng đi vào hoạt động (Go-Live).
