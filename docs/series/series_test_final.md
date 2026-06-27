# Báo Cáo Kiểm Thử Cuối Cùng (Final Test) - Module Series

**Ngày kiểm thử:** Theo timestamp hệ thống
**Kết quả chung:** PASS

| Hạng mục Kiểm thử | Các Case | Kết quả (PASS/FAIL) | Ghi chú |
| --- | --- | --- | --- |
| CRUD Series | Create, Read, Update, Delete. | **PASS** | Kiểm tra độ dài title hoạt động. |
| CRUD Chapter | Create, Read, Update, Delete. | **PASS** | Kiểm tra trùng `chapter_number` hoạt động. |
| CRUD Page | Upload ảnh mới, Đổi ảnh cũ, Xóa trang. | **PASS** | Xóa vật lý (unlink) chạy bình thường. |
| Role Check | Đăng nhập bằng tài khoản không phải Mangaka. | **PASS** | Nhận lỗi 403 hoặc Redirect ra ngoài. |
| Ownership Tamper | Cố tình truyền ID bài của người khác. | **PASS** | Bị từ chối và báo lỗi sở hữu. |
| Malicious Upload | Upload tệp `.php` đuôi `.jpg`. | **PASS** | Bị chặn bởi `finfo_file` MIME validation. |

**Quyết định:** Mọi tính năng cốt lõi và giới hạn an ninh đã được nghiệm thu và cho kết quả XANH (PASS). Sẵn sàng đi vào hoạt động (Go-Live).
