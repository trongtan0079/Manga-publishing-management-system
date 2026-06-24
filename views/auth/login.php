<?php
// Đảm bảo BASE_PATH được thiết lập
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    if ($pos !== false) {
        $basePath = substr($scriptName, 0, $pos);
    } else {
        $basePath = dirname($scriptName);
    }
    if ($basePath === '/' || $basePath === '\\') $basePath = '';
    define('BASE_PATH', str_replace('\\', '/', $basePath));
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Manga PMS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #6366f1;     /* Màu chủ đạo Indigo */
            --secondary-color: #8b5cf6;   /* Tím nhạt Gradient */
            --text-main: #1e293b;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            background-color: #0f172a; /* Màu lót dự phòng */
            /* Cài đặt ảnh nền tràn viền */
            background-image: url('<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/assets/images/anhnen_login.webp');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        
        /* Lớp phủ gradient làm dịu ảnh nền để nổi bật form đăng nhập */
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.7) 0%, rgba(30, 41, 59, 0.4) 100%);
            z-index: 1;
        }
        
        .login-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        /* Glassmorphism Card Design */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 440px;
            padding: 3rem 2.5rem;
            animation: floatUp 0.6s ease-out forwards;
        }
        
        @keyframes floatUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Logo Icon */
        .brand-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin: 0 auto 1.25rem auto;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }
        
        .brand-icon:hover {
            transform: rotate(0deg) scale(1.05);
        }

        .login-title {
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.5px;
            font-size: 1.75rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        
        /* Custom Inputs */
        .input-group {
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            overflow: hidden;
        }
        
        .input-group:focus-within {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            background: #ffffff;
        }
        
        .input-group-text {
            background-color: transparent;
            border: none;
            color: #94a3b8;
            padding-left: 1.2rem;
        }
        
        .form-control {
            border: none;
            background: transparent;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            font-weight: 500;
            color: var(--text-main);
        }
        
        .form-control:focus {
            box-shadow: none;
            background: transparent;
        }
        
        /* Gradient Button */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 12px;
            padding: 0.85rem;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.25);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(99, 102, 241, 0.35);
        }
        
        .btn-primary:active {
            transform: translateY(1px);
        }

        .alert-danger {
            border-radius: 10px;
            border: none;
            background-color: #fef2f2;
            color: #ef4444;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="text-center mb-4">
                <div class="brand-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h2 class="login-title mb-1">Manga<span style="color: var(--primary-color)">PMS</span></h2>
                <p class="text-muted" style="font-weight: 500;">Hệ thống Quản lý Xuất bản Toàn diện</p>
            </div>
            
            <?php if (isset($error) && !empty($error)): ?>
                <div class="alert alert-danger py-2 d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <form action="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=auth&action=authenticate" method="POST">
                <div class="mb-4">
                    <label for="login_id" class="form-label">Tài khoản hoặc Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="login_id" name="login_id" placeholder="Nhập tên đăng nhập..." required autofocus>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="password" class="form-label mb-0">Mật khẩu</label>
                        <a href="#" class="text-decoration-none small" style="color: var(--primary-color); font-weight: 600;">Quên mật khẩu?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>
                
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe" style="cursor: pointer;">
                    <label class="form-check-label text-muted" for="rememberMe" style="cursor: pointer; font-weight: 500; font-size: 0.9rem;">Ghi nhớ phiên đăng nhập</label>
                </div>
                
                <div class="d-grid mt-2">
                    <button type="submit" class="btn btn-primary">Đăng Nhập Vào Hệ Thống</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
