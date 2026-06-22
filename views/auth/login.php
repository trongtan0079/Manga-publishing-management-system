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
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            margin: 0;
        }
        /* Background Shapes */
        .shape {
            position: absolute;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.6;
        }
        .shape-1 {
            top: -10%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: var(--primary-color);
            border-radius: 50%;
        }
        .shape-2 {
            bottom: -10%;
            right: -5%;
            width: 600px;
            height: 600px;
            background: var(--secondary-color);
            border-radius: 50%;
        }
        .shape-3 {
            bottom: 20%;
            left: 20%;
            width: 300px;
            height: 300px;
            background: #ec4899;
            border-radius: 50%;
            opacity: 0.4;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 440px;
            padding: 3rem 2.5rem;
            z-index: 1;
        }
        
        .brand-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }
        
        .form-control {
            border-radius: 12px;
            padding: 0.75rem 1.25rem;
            border: 1px solid #e2e8f0;
            background-color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            border-color: var(--primary-color);
            background-color: #fff;
        }
        
        .input-group-text {
            background-color: transparent;
            border-radius: 12px 0 0 12px;
            color: #94a3b8;
            border: 1px solid #e2e8f0;
            border-right: none;
            padding-left: 1.2rem;
        }
        .input-group .form-control {
            border-left: none;
            padding-left: 0.5rem;
        }
        /* Fix input border on focus when using input-group */
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: var(--primary-color);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 12px;
            padding: 0.8rem;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.25);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(99, 102, 241, 0.35);
        }
        
        .alert {
            border-radius: 12px;
            font-size: 0.9rem;
            border: none;
        }
    </style>
</head>
<body>
    <!-- Background Elements -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>

    <div class="container px-4 d-flex justify-content-center">
        <div class="login-card">
            <div class="text-center mb-4">
                <div class="brand-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1">Manga PMS</h3>
                <p class="text-muted small">Nền tảng Quản lý Xuất bản</p>
            </div>
            
            <?php if (isset($error) && !empty($error)): ?>
                <div class="alert alert-danger bg-danger bg-opacity-10 text-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <form action="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=auth&action=authenticate" method="POST">
                <div class="mb-4">
                    <label for="login_id" class="form-label fw-semibold text-dark small">Tài khoản hoặc Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="login_id" name="login_id" placeholder="Nhập tên đăng nhập..." required autofocus>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between">
                        <label for="password" class="form-label fw-semibold text-dark small">Mật khẩu</label>
                        <a href="#" class="text-primary text-decoration-none small fw-semibold" style="color: var(--primary-color) !important;">Quên mật khẩu?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>
                
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe">
                    <label class="form-check-label text-muted small" for="rememberMe">Ghi nhớ đăng nhập</label>
                </div>
                
                <div class="d-grid mt-2">
                    <button type="submit" class="btn btn-primary">Đăng Nhập <i class="fas fa-arrow-right ms-2"></i></button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <p class="text-muted small mb-0">Bạn chưa có tài khoản? <a href="#" class="text-decoration-none fw-bold" style="color: var(--primary-color);">Đăng ký ngay</a></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
