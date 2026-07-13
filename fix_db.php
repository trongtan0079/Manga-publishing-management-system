<?php
session_start();
require_once __DIR__ . '/config/database.php';

$steps = [];
$error = null;

try {
    $db = new Database();
    $conn = $db->connect();

    // Step 1: Add is_head_board to users
    $conn->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_head_board TINYINT DEFAULT 0 AFTER status");
    $steps[] = "Đã thêm cột 'is_head_board' vào bảng 'users' (nếu chưa có).";

    // Step 2: Add old_image_url to pages
    $conn->exec("ALTER TABLE pages ADD COLUMN IF NOT EXISTS old_image_url VARCHAR(255) DEFAULT NULL AFTER image_url");
    $steps[] = "Đã thêm cột 'old_image_url' vào bảng 'pages' (nếu chưa có).";

    // Step 3: Upgrade chapters status enum
    $conn->exec("ALTER TABLE chapters MODIFY COLUMN status ENUM('drafting', 'drawing', 'reviewing_draft', 'reviewing_final', 'approved', 'published') DEFAULT 'drafting'");
    $steps[] = "Đã nâng cấp kiểu ENUM trạng thái cho bảng 'chapters'.";

    // Step 4: Upgrade pages status enum
    $conn->exec("ALTER TABLE pages MODIFY COLUMN status ENUM('drafting', 'drawing', 'reviewing_draft', 'reviewing_final', 'approved', 'published') DEFAULT 'drafting'");
    $steps[] = "Đã nâng cấp kiểu ENUM trạng thái cho bảng 'pages'.";

    // Step 5: Execute seed_users.sql
    $seedFile = __DIR__ . '/database/seed_users.sql';
    if (file_exists($seedFile)) {
        $sql = file_get_contents($seedFile);
        // Execute queries
        $conn->exec($sql);
        $steps[] = "Đã gieo dữ liệu tài khoản mẫu (seed_users.sql) thành công.";
    } else {
        // Fallback update if file not found
        $conn->exec("UPDATE users SET is_head_board = 1 WHERE username = 'board_user'");
        $steps[] = "Đã cập nhật quyền Trưởng ban Hội đồng cho tài khoản board_user.";
    }

    // Step 6: Execute create_board_votes_table.sql
    $votesFile = __DIR__ . '/database/create_board_votes_table.sql';
    if (file_exists($votesFile)) {
        $sql = file_get_contents($votesFile);
        $conn->exec($sql);
        $steps[] = "Đã khởi tạo bảng bỏ phiếu board_votes và gieo phiếu bầu mẫu.";
    }

    // Step 7: Execute add_editor_annotations_table.sql
    $annoFile = __DIR__ . '/database/add_editor_annotations_table.sql';
    if (file_exists($annoFile)) {
        $sql = file_get_contents($annoFile);
        $conn->exec($sql);
        $steps[] = "Đã tạo bảng ghi chú sửa lỗi editor_annotations thành công.";
    }

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Auto Repair - MangaPMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #f1f5f9;
        }
        .repair-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            max-width: 600px;
            width: 90%;
            padding: 40px;
            text-align: center;
        }
        .status-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        .step-list {
            text-align: left;
            margin: 30px 0;
            background: rgba(15, 23, 42, 0.5);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .step-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }
        .step-item:last-child {
            margin-bottom: 0;
        }
        .step-item i {
            margin-right: 12px;
            font-size: 1.1rem;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="repair-card">
        <?php if ($error): ?>
            <div class="status-icon text-danger">
                <i class="fas fa-circle-xmark"></i>
            </div>
            <h2 class="mb-3 text-danger fw-bold">Sửa Lỗi Thất Bại</h2>
            <p class="text-slate-400">Không thể cấu hình và đồng bộ cơ sở dữ liệu tự động.</p>
            <div class="alert alert-danger text-start mt-4" role="alert">
                <h5 class="alert-heading fw-bold"><i class="fas fa-bug me-2"></i>Chi tiết lỗi:</h5>
                <p class="mb-0 font-monospace text-xs"><?= htmlspecialchars($error) ?></p>
            </div>
            <a href="index.php" class="btn btn-secondary w-100 py-2.5 mt-4" style="border-radius: 10px;">
                <i class="fas fa-arrow-left me-2"></i> Quay lại trang chủ
            </a>
        <?php else: ?>
            <div class="status-icon text-success">
                <i class="fas fa-circle-check"></i>
            </div>
            <h2 class="mb-3 text-success fw-bold">Sửa Lỗi Thành Công!</h2>
            <p class="text-muted">Cơ sở dữ liệu của bạn đã được cập nhật và đồng bộ hoàn chỉnh.</p>
            
            <div class="step-list">
                <?php foreach ($steps as $step): ?>
                    <div class="step-item text-success-light">
                        <i class="fas fa-square-check text-success"></i>
                        <span><?= htmlspecialchars($step) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="d-flex gap-3">
                <a href="index.php?controller=auth&action=login" class="btn btn-primary flex-fill py-2.5" style="border-radius: 10px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                    <i class="fas fa-right-to-bracket me-2"></i> Đăng nhập ngay
                </a>
                <a href="index.php" class="btn btn-outline-light flex-fill py-2.5" style="border-radius: 10px;">
                    <i class="fas fa-home me-2"></i> Trang chủ
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
