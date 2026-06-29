# Notification Module Report
- Chức năng tự động gửi và hiển thị thông báo hoạt động ổn định.
- Chức năng Mark As Read và Mark All As Read đã được chuyển đổi sang POST request để tăng cường bảo mật.
- Áp dụng các biện pháp chống giả mạo tham số (URL Tampering) và kiểm tra quyền sở hữu đối với thông báo.
- Thay thế các liên kết `<a>` không an toàn bằng form POST để tránh lỗi HTML lồng nhau và nâng cao tính bảo mật.