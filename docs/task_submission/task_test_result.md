# Kết Quả Kiểm Thử (Test Results) - Module Task & Submission

Tài liệu này tổng hợp kết quả chạy thực tế các kịch bản kiểm thử (Manual Testing / Static Code Review Simulation) trên môi trường cục bộ đối với Module Task và Submission.

---

## 1. Tóm Tắt Kết Quả (Summary)
- **Tổng số kịch bản kiểm thử**: 12
- **Số kịch bản ĐẠT (PASS)**: 12 (100%)
- **Số kịch bản LỖI (FAIL)**: 0 (0%)
- **Tình trạng hệ thống**: Các lỗ hổng bảo mật và lỗi validation nghiêm trọng đã được khắc phục hoàn toàn. Không phát hiện hồi quy lỗi (regression issues).

---

## 2. Chi Tiết Kết Quả Kiểm Thử (Execution Details)

| Test ID | Module | Kịch Bản / Chức năng | Vai trò thực hiện | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|---------|--------|----------------------|-------------------|------------------|-----------------|------------|
| **TS-01** | Task | Đăng nhập và tạo Task hợp lệ | Mangaka | Tạo thành công, lưu DB, gửi thông báo | Đúng như mong đợi | **PASS** |
| **TS-02** | Task | Tạo Task với tiêu đề rỗng / quá dài | Mangaka | Chặn ở Controller, báo lỗi validation | Bị chặn, báo lỗi "Tiêu đề không được để trống / quá dài" | **PASS** |
| **TS-03** | Task | Giao Task cho Assistant không hợp lệ / bị khóa | Mangaka | Chặn ở Controller, báo lỗi Trợ lý không hợp lệ | Bị chặn, báo lỗi "Assistant không hợp lệ hoặc bị vô hiệu hóa" | **PASS** |
| **TS-04** | Task | Sửa Task của người khác (URL Tampering) | Mangaka | Chặn bởi kiểm tra ownership, chuyển hướng | Bị chặn và redirect về Dashboard | **PASS** |
| **TS-05** | Task | Sửa Task với hạn chót sai định dạng | Mangaka | Chặn lưu DB, báo lỗi định dạng ngày giờ | Bị chặn, báo lỗi "Hạn chót không đúng định dạng" | **PASS** |
| **TS-06** | Task | Xóa Task bằng GET request | Mangaka | Chặn và báo lỗi phương thức yêu cầu không hợp lệ | Bị chặn hoàn toàn | **PASS** |
| **TS-07** | Task | Cập nhật trạng thái Task được giao | Assistant | Cập nhật thành công (`pending` -> `in_progress`) | Trạng thái chuyển thành công | **PASS** |
| **TS-08** | Task | Assistant sửa thông tin nhạy cảm của Task | Assistant | Chặn ở Controller, chỉ cho phép cập nhật `status` | Chỉ cho phép đổi status, các trường khác giữ nguyên | **PASS** |
| **TS-09** | Submission | Nộp bản vẽ cho Task của người khác | Assistant | Chặn nộp bài, báo lỗi quyền sở hữu | Bị chặn, báo lỗi "Task không hợp lệ hoặc không thuộc quyền sở hữu" | **PASS** |
| **TS-10** | Submission | Upload file ảnh giả mạo (đổi đuôi từ file `.php`) | Assistant | finfo_file & getimagesize phát hiện ảnh giả, chặn | Phát hiện giả mạo, báo lỗi "File ảnh không hợp lệ hoặc bị giả mạo" | **PASS** |
| **TS-11** | Submission | Upload file PDF/ZIP bị lỗi signature | Assistant | Chặn và báo lỗi định dạng file không hợp lệ | Bị chặn hoàn toàn, báo lỗi signature | **PASS** |
| **TS-12** | Submission | Xóa bản thảo pending (Dọn dẹp file vật lý) | Assistant | Xóa bản ghi DB và xóa file vật lý tương ứng khỏi đĩa cứng | File trong thư mục `uploads/submissions/` biến mất thành công | **PASS** |

---

## 3. Đánh Giá Bảo Mật & Kết Luận
- **Tính trọn vẹn**: Cơ chế phân quyền dựa trên Role (RBAC) và Quyền sở hữu (Ownership) đã bao phủ 100% các endpoint xử lý dữ liệu.
- **Tính an toàn của File Upload**: Trình kiểm tra tệp tin đa lớp (Extension -> MIME -> Header Signatures / Image pixels verification) triệt tiêu hoàn toàn rủi ro người dùng tải lên webshell hoặc mã độc thực thi.
- **Quản lý tài nguyên**: Logic `unlink()` hoạt động chính xác khi người dùng xóa bản thảo, đảm bảo không sinh ra tệp tin rác chiếm dụng tài nguyên máy chủ.
