# Tài Liệu Hướng Dẫn Kiểm Thử Ràng Buộc Nghiệp Vụ Mangaka
*(Mangaka Business Logic Locks - Test Suite Guide)*

Tài liệu này hướng dẫn chi tiết các bước kiểm thử thủ công và các trường hợp kiểm thử (test cases) nhằm xác minh tính chính xác của 3 cơ chế khóa nghiệp vụ vừa được cập nhật cho Họa sĩ chính (Mangaka).

---

## 1. Test Case 1: Chặn nộp duyệt chương truyện rỗng (Empty Chapter Submission Lock)

### Mục đích:
Đảm bảo Mangaka không thể nộp duyệt một Chapter chưa có hình ảnh/trang truyện nào.

### Các bước thực hiện:
1. Đăng nhập hệ thống với tài khoản **Mangaka**.
2. Truy cập vào bộ truyện bất kỳ, bấm **Thêm Chapter mới**.
3. Điền thông tin, chọn trạng thái là **Bản nháp (Drafting)** hoặc **Đang vẽ (Drawing)** và bấm lưu.
4. Ở màn hình chi tiết Chapter vừa tạo, xác nhận danh sách trang truyện hiển thị: *"Chưa có trang truyện nào được thêm vào."*
5. Bấm nút **Sửa Chapter** ở đầu trang.
6. Thay đổi Trạng thái sang **Đang chờ duyệt (Reviewing)** và bấm **Lưu thay đổi**.

### Kết quả mong đợi (Pass Criteria):
- Hệ thống chặn lưu thay đổi, quay trở lại trang sửa kèm theo thông báo lỗi màu đỏ nổi bật: **"Chương truyện phải có ít nhất 1 trang vẽ mới có thể nộp duyệt."**
- Trạng thái của Chapter trong CSDL vẫn giữ nguyên như cũ, không bị đổi thành `reviewing`.

---

## 2. Test Case 2: Khóa chỉnh sửa/xóa Chương truyện đã duyệt hoặc xuất bản (Approved/Published Chapter Lock)

### Mục đích:
Đảm bảo khi Chapter đã được kiểm duyệt xong (`approved`) hoặc đã xuất bản công khai (`published`), Mangaka không thể tự ý sửa đổi nội dung hay xóa chương truyện.

### Các bước thực hiện:
1. Đăng nhập bằng tài khoản **Biên tập viên (Tantou Editor)** hoặc **Ban biên tập (Editorial Board)**.
2. Tìm đến Chapter đang ở trạng thái `reviewing` và thực hiện **Phê duyệt (Approve)** chương đó.
3. Đăng nhập lại bằng tài khoản **Mangaka** của tác giả bộ truyện đó.
4. Truy cập trang chi tiết Chapter đã được duyệt.

### Kết quả mong đợi (Pass Criteria):
- **Trên giao diện (UI)**:
  - Nút **Sửa Chapter** và **Xóa** ở đầu trang biến mất. Thay vào đó là nhãn tĩnh: **"Chương đã được duyệt / phát hành (Khóa)"**.
- **Khi thử truy cập trực tiếp qua URL/Request**:
  - Nhập trực tiếp URL sửa chương truyện: `/index.php?controller=chapter&action=edit&id=<CHAPTER_ID>`. Xác nhận form hiển thị cảnh báo: **"Chương truyện này đã được phê duyệt hoặc xuất bản..."** và toàn bộ các ô nhập liệu, nút bấm đều bị khóa (`disabled`).
  - Gửi POST request xóa chương truyện đến `/index.php?controller=chapter&action=delete&id=<CHAPTER_ID>`. Xác nhận hệ thống chặn lại và báo lỗi: **"Chương truyện đã được phê duyệt hoặc xuất bản, không thể xóa."**

---

## 3. Test Case 3: Chặn thêm/sửa/xóa Trang truyện trong Chapter đã khóa (Page Mutation Lock)

### Mục đích:
Đảm bảo khi một Chapter đã khóa, không ai có thể can thiệp thêm, bớt hoặc chỉnh sửa hình ảnh các trang đơn bên trong.

### Các bước thực hiện:
1. Đăng nhập bằng tài khoản **Mangaka**.
2. Truy cập vào chi tiết một Chapter có trạng thái là `approved` hoặc `published`.

### Kết quả mong đợi (Pass Criteria):
- **Thêm trang vẽ mới**:
  - Nút **+ Thêm trang** ở góc trên danh sách trang biến mất.
  - Thử truy cập trực tiếp URL thêm trang: `/index.php?controller=page&action=create&chapter_id=<CHAPTER_ID>`. Xác nhận hệ thống redirect về trang chapter và báo lỗi: **"Chương truyện đã phê duyệt hoặc xuất bản, không thể thêm trang vẽ mới."**
- **Sửa trang vẽ hiện tại**:
  - Cột *Thao tác* trong bảng danh sách trang vẽ không hiển thị nút **Sửa**.
  - Thử truy cập trực tiếp URL sửa trang vẽ: `/index.php?controller=page&action=edit&id=<PAGE_ID>`. Xác nhận hệ thống redirect và báo lỗi: **"Trang truyện hoặc chương truyện đã phê duyệt/xuất bản, không thể chỉnh sửa."**
- **Xóa trang vẽ hiện tại**:
  - Cột *Thao tác* không hiển thị nút **Xóa**.
  - Thử gửi POST request xóa trang đến `/index.php?controller=page&action=delete&id=<PAGE_ID>`. Xác nhận hệ thống chặn đứng và báo lỗi: **"Trang truyện hoặc chương truyện đã phê duyệt/xuất bản, không thể xóa."**
