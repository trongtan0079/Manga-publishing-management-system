# Danh sách Lỗi và Các Khắc Phục (Bug List & Fixes)

Quá trình Review Module **Authentication, User & Permission** đã phát hiện và xử lý trực tiếp các lỗi/rủi ro sau:

### ID: BUG-001
- **Vị trí**: `AuthController::authenticate`
- **Lỗi / Rủi ro phát hiện**: Không kiểm tra trạng thái hoạt động (`status`) của User. Tài khoản bị khóa (inactive) vẫn có thể Login nếu đúng Password.
- **Mức độ**: Cao
- **Biện pháp Khắc phục**: Thêm logic `if ($user['status'] !== 'active')` để chặn đăng nhập và trả về thông báo lỗi.
- **Trạng thái**: ✅ Đã Fix

### ID: BUG-002
- **Vị trí**: `AuthController::authenticate`
- **Lỗi / Rủi ro phát hiện**: Không cấp phát Session ID mới sau khi người dùng đăng nhập thành công (Lỗ hổng **Session Fixation**).
- **Mức độ**: Cao
- **Biện pháp Khắc phục**: Thêm hàm `session_regenerate_id(true);` ngay sau dòng kiểm tra mật khẩu thành công.
- **Trạng thái**: ✅ Đã Fix

### ID: BUG-003
- **Vị trí**: `UserController::store, update`
- **Lỗi / Rủi ro phát hiện**: Quá trình nhận input Email từ Admin thiếu kiểm tra định dạng hợp lệ.
- **Mức độ**: Trung Bình
- **Biện pháp Khắc phục**: Sử dụng hàm `filter_var($email, FILTER_VALIDATE_EMAIL)` để chặn các Email sai cấu trúc.
- **Trạng thái**: ✅ Đã Fix

### ID: BUG-004
- **Vị trí**: `UserController::store, update`
- **Lỗi / Rủi ro phát hiện**: Bỏ qua việc xác thực xem `role_id` từ Form có thực sự nằm trong CSDL hay không. (Nguy cơ chèn Role ảo).
- **Mức độ**: Trung Bình
- **Biện pháp Khắc phục**: Bổ sung truy vấn `$this->roleModel->findById($role_id)` để đảm bảo Role tồn tại trước khi Insert/Update.
- **Trạng thái**: ✅ Đã Fix

### ID: BUG-005
- **Vị trí**: `UserController::store, update`
- **Lỗi / Rủi ro phát hiện**: Form có thể tạo mật khẩu quá ngắn hoặc không có giới hạn độ dài.
- **Mức độ**: Thấp
- **Biện pháp Khắc phục**: Thêm logic bắt buộc `strlen($_POST['password']) >= 6`.
- **Trạng thái**: ✅ Đã Fix

### ID: BUG-006
- **Vị trí**: `UserController::delete`
- **Lỗi / Rủi ro phát hiện**: Admin có thể vô tình hoặc cố tình xóa chính mình, dẫn đến mất quyền đăng nhập và kẹt tài khoản.
- **Mức độ**: Cao
- **Biện pháp Khắc phục**: Bổ sung câu lệnh `if ($id == $_SESSION['user_id'])` để cấm hành vi tự xóa tài khoản của bản thân.
- **Trạng thái**: ✅ Đã Fix

## Ghi chú về MVC & Kiến trúc
- Không phát hiện bất kỳ lỗi SQL Injection nào do toàn bộ Project đã sử dụng cơ chế Prepared Statements (`bindParam`, `bindValue`) trong tầng Model.
- Không phát hiện lỗi Cross-Site Scripting (XSS) vì tất cả các View khi render thông tin do user nhập (Title, Name, Username...) đều được bọc trong hàm `htmlspecialchars()`.
- Các Controller không in HTML, View không gọi Database. Kiến trúc MVC được duy trì ở mức tối đa.
