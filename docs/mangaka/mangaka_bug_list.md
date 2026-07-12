# Danh Sách Lỗi & Tồn Đọng (Mangaka Bug List)

## 1. Trạng Thái Giao Diện (UI Status)
- **Chuỗi văn bản tiếng Anh:** 0 lỗi (Đã Việt hóa 100% toàn bộ các văn bản giao diện hiển thị trên 11 file view của Mangaka).
- **Lỗi đứt gãy liên kết (Broken Links):** 0 lỗi (Giữ nguyên toàn bộ tham số URL, controller, action và ID).
- **Trùng lặp mã nguồn:** 0 lỗi.

## 2. Ghi Chú Tồn Đọng Kỹ Thuật (Chờ Người 1 Cập Nhật)
Dưới đây là các ghi chú kiến trúc/nghiệp vụ thuộc phạm vi quản lý của Người 1, được ghi nhận lại để Người 1 tiếp tục hoàn thiện:
1. **Dashboard Statistics:** Thống kê trên trang [dashboard.php](file:///d:/xampp/htdocs/BTapManga/views/mangaka/dashboard.php) cần Người 1 hoàn thiện hàm tính toán trong `DashboardController`.
2. **Page Status ENUM Integration:** Chuẩn hóa hiển thị nhãn cho các trạng thái chi tiết của Page (`sketch`, `inked`, `toned`, `finished`) khi Người 1 cập nhật Workflow xử lý ảnh.
3. **Submission Flow Validation:** Các ràng buộc kiểm tra file nộp bài phía server thuộc về `SubmissionController`.
