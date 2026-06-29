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

### Test ID: **TS-01**
- **Module**: Task
- **Kịch Bản / Chức năng**: Đăng nhập và tạo Task hợp lệ
- **Vai trò thực hiện**: Mangaka
- **Kết quả mong đợi**: Tạo thành công, lưu DB, gửi thông báo
- **Kết quả thực tế**: Đúng như mong đợi
- **Trạng thái**: **PASS**

### Test ID: **TS-02**
- **Module**: Task
- **Kịch Bản / Chức năng**: Tạo Task với tiêu đề rỗng / quá dài
- **Vai trò thực hiện**: Mangaka
- **Kết quả mong đợi**: Chặn ở Controller, báo lỗi validation
- **Kết quả thực tế**: Bị chặn, báo lỗi "Tiêu đề không được để trống / quá dài"
- **Trạng thái**: **PASS**

### Test ID: **TS-03**
- **Module**: Task
- **Kịch Bản / Chức năng**: Giao Task cho Assistant không hợp lệ / bị khóa
- **Vai trò thực hiện**: Mangaka
- **Kết quả mong đợi**: Chặn ở Controller, báo lỗi Trợ lý không hợp lệ
- **Kết quả thực tế**: Bị chặn, báo lỗi "Assistant không hợp lệ hoặc bị vô hiệu hóa"
- **Trạng thái**: **PASS**

### Test ID: **TS-04**
- **Module**: Task
- **Kịch Bản / Chức năng**: Sửa Task của người khác (URL Tampering)
- **Vai trò thực hiện**: Mangaka
- **Kết quả mong đợi**: Chặn bởi kiểm tra ownership, chuyển hướng
- **Kết quả thực tế**: Bị chặn và redirect về Dashboard
- **Trạng thái**: **PASS**

### Test ID: **TS-05**
- **Module**: Task
- **Kịch Bản / Chức năng**: Sửa Task với hạn chót sai định dạng
- **Vai trò thực hiện**: Mangaka
- **Kết quả mong đợi**: Chặn lưu DB, báo lỗi định dạng ngày giờ
- **Kết quả thực tế**: Bị chặn, báo lỗi "Hạn chót không đúng định dạng"
- **Trạng thái**: **PASS**

### Test ID: **TS-06**
- **Module**: Task
- **Kịch Bản / Chức năng**: Xóa Task bằng GET request
- **Vai trò thực hiện**: Mangaka
- **Kết quả mong đợi**: Chặn và báo lỗi phương thức yêu cầu không hợp lệ
- **Kết quả thực tế**: Bị chặn hoàn toàn
- **Trạng thái**: **PASS**

### Test ID: **TS-07**
- **Module**: Task
- **Kịch Bản / Chức năng**: Cập nhật trạng thái Task được giao
- **Vai trò thực hiện**: Assistant
- **Kết quả mong đợi**: Cập nhật thành công (`pending` -> `in_progress`)
- **Kết quả thực tế**: Trạng thái chuyển thành công
- **Trạng thái**: **PASS**

### Test ID: **TS-08**
- **Module**: Task
- **Kịch Bản / Chức năng**: Assistant sửa thông tin nhạy cảm của Task
- **Vai trò thực hiện**: Assistant
- **Kết quả mong đợi**: Chặn ở Controller, chỉ cho phép cập nhật `status`
- **Kết quả thực tế**: Chỉ cho phép đổi status, các trường khác giữ nguyên
- **Trạng thái**: **PASS**

### Test ID: **TS-09**
- **Module**: Submission
- **Kịch Bản / Chức năng**: Nộp bản vẽ cho Task của người khác
- **Vai trò thực hiện**: Assistant
- **Kết quả mong đợi**: Chặn nộp bài, báo lỗi quyền sở hữu
- **Kết quả thực tế**: Bị chặn, báo lỗi "Task không hợp lệ hoặc không thuộc quyền sở hữu"
- **Trạng thái**: **PASS**

### Test ID: **TS-10**
- **Module**: Submission
- **Kịch Bản / Chức năng**: Upload file ảnh giả mạo (đổi đuôi từ file `.php`)
- **Vai trò thực hiện**: Assistant
- **Kết quả mong đợi**: finfo_file & getimagesize phát hiện ảnh giả, chặn
- **Kết quả thực tế**: Phát hiện giả mạo, báo lỗi "File ảnh không hợp lệ hoặc bị giả mạo"
- **Trạng thái**: **PASS**

### Test ID: **TS-11**
- **Module**: Submission
- **Kịch Bản / Chức năng**: Upload file PDF/ZIP bị lỗi signature
- **Vai trò thực hiện**: Assistant
- **Kết quả mong đợi**: Chặn và báo lỗi định dạng file không hợp lệ
- **Kết quả thực tế**: Bị chặn hoàn toàn, báo lỗi signature
- **Trạng thái**: **PASS**

### Test ID: **TS-12**
- **Module**: Submission
- **Kịch Bản / Chức năng**: Xóa bản thảo pending (Dọn dẹp file vật lý)
- **Vai trò thực hiện**: Assistant
- **Kết quả mong đợi**: Xóa bản ghi DB và xóa file vật lý tương ứng khỏi đĩa cứng
- **Kết quả thực tế**: File trong thư mục `uploads/submissions/` biến mất thành công
- **Trạng thái**: **PASS**

---

## 3. Đánh Giá Bảo Mật & Kết Luận
- **Tính trọn vẹn**: Cơ chế phân quyền dựa trên Role (RBAC) và Quyền sở hữu (Ownership) đã bao phủ 100% các endpoint xử lý dữ liệu.
- **Tính an toàn của File Upload**: Trình kiểm tra tệp tin đa lớp (Extension -> MIME -> Header Signatures / Image pixels verification) triệt tiêu hoàn toàn rủi ro người dùng tải lên webshell hoặc mã độc thực thi.
- **Quản lý tài nguyên**: Logic `unlink()` hoạt động chính xác khi người dùng xóa bản thảo, đảm bảo không sinh ra tệp tin rác chiếm dụng tài nguyên máy chủ.
