<?php
// Script kiểm thử dùng để tạo hash mật khẩu mã hóa BCrypt cho tài khoản.
echo password_hash('password123', PASSWORD_BCRYPT);

