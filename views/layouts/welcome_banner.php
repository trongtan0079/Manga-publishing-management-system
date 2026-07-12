<?php
// Get user info
$displayName = isset($_SESSION) ? ($_SESSION['full_name'] ?? 'User') : 'User';
$role = isset($_SESSION) ? ($_SESSION['role_name'] ?? '') : '';

// Custom greeting & subtitle based on role
$greetingName = htmlspecialchars($displayName);
$subtitle = "Tiếp tục hành trình sáng tác của bạn hôm nay.";
$quote = "Mỗi trang truyện là một bước gần hơn đến tác phẩm hoàn hảo.";
$author = "Keep Drawing ✍️";

$badgeStyle = "background: rgba(239, 68, 68, 0.08); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 6px; padding: 4px 12px;";
$badgeIcon = "fa-magic";
$badgeLabel = "CẢM HỨNG SÁNG TÁC";

switch ($role) {
    case 'admin':
        $subtitle = "Hệ thống đang hoạt động ổn định. Hãy quản lý người dùng và cấu hình hệ thống.";
        $quote = "Sự ổn định của hệ thống là nền tảng vững chắc cho mọi tác phẩm vĩ đại ra đời.";
        $author = "System Control ⚙️";
        $badgeStyle = "background: rgba(16, 185, 129, 0.08); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 6px; padding: 4px 12px;";
        $badgeIcon = "fa-shield-alt";
        $badgeLabel = "QUẢN TRỊ VIÊN";
        break;
    case 'assistant':
        $subtitle = "Hoàn thành các nhiệm vụ vẽ nét và lên màu xuất sắc nhé.";
        $quote = "Mỗi nét vẽ phụ trợ tỉ mỉ đều góp phần tạo nên bức tranh toàn cảnh tráng lệ.";
        $author = "Assist Team 🎨";
        $badgeStyle = "background: rgba(59, 130, 246, 0.08); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 6px; padding: 4px 12px;";
        $badgeIcon = "fa-palette";
        $badgeLabel = "TRỢ LÝ MỸ THUẬT";
        break;
    case 'editor':
        $subtitle = "Theo dõi tiến độ các bộ truyện và phê duyệt chương mới.";
        $quote = "Con mắt tinh tường của biên tập viên định hình nên những kiệt tác truyện tranh.";
        $author = "Editorial 📖";
        $badgeStyle = "background: rgba(139, 92, 246, 0.08); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.2); border-radius: 6px; padding: 4px 12px;";
        $badgeIcon = "fa-book-open";
        $badgeLabel = "BIÊN TẬP VIÊN";
        break;
    case 'board':
        $subtitle = "Đánh giá chất lượng và định hướng phát hành tác phẩm.";
        $quote = "Tầm nhìn chiến lược mở ra con đường tiếp cận hàng triệu độc giả toàn cầu.";
        $author = "Board Council 🏛️";
        $badgeStyle = "background: rgba(251, 191, 36, 0.08); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.2); border-radius: 6px; padding: 4px 12px;";
        $badgeIcon = "fa-chart-line";
        $badgeLabel = "BAN GIÁM ĐỐC";
        break;
}
?>
<style>
    @keyframes wave-animation {
        0% {
            transform: rotate(0.0deg)
        }

        10% {
            transform: rotate(14.0deg)
        }

        20% {
            transform: rotate(-8.0deg)
        }

        30% {
            transform: rotate(14.0deg)
        }

        40% {
            transform: rotate(-4.0deg)
        }

        50% {
            transform: rotate(10.0deg)
        }

        60% {
            transform: rotate(0.0deg)
        }

        100% {
            transform: rotate(0.0deg)
        }
    }

    .waving-hand {
        animation: wave-animation 2.5s infinite;
        transform-origin: 70% 70%;
        display: inline-block;
    }

    /* Premium card design with rich dark glassmorphism styling */
    .welcome-banner-card {
        background: linear-gradient(135deg, #0b0f19 0%, #111827 50%, #1e1b4b 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3) !important;
        border-radius: 24px;
        min-height: 255px;
        transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .welcome-banner-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 30px rgba(99, 102, 241, 0.15) !important;
        border-color: rgba(99, 102, 241, 0.3) !important;
    }

    .sheet-bottom {
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .welcome-banner-card:hover .sheet-bottom {
        transform: rotate(10deg) translate(14px, 12px);
    }

    .sheet-middle {
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .welcome-banner-card:hover .sheet-middle {
        transform: rotate(-8deg) translate(-14px, -6px);
    }

    .sheet-top {
        transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .welcome-banner-card:hover .sheet-top {
        transform: scale(1.03) rotate(-1.5deg) translate(2px, -2px);
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.55) !important;
    }
</style>

<div class="card border-0 mb-4 overflow-hidden position-relative welcome-banner-card">

    <!-- Decorative background patterns -->
    <!-- Soft, glowing background color blobs -->
    <div style="position: absolute; width: 350px; height: 350px; background: rgba(99, 102, 241, 0.12); filter: blur(85px); top: -100px; right: -50px; border-radius: 50%; pointer-events: none;"></div>
    <div style="position: absolute; width: 300px; height: 300px; background: rgba(236, 72, 153, 0.1); filter: blur(75px); bottom: -80px; left: -50px; border-radius: 50%; pointer-events: none;"></div>

    <div class="card-body p-0 position-relative" style="z-index: 1;">
        <div class="row align-items-center g-0" style="min-height: 255px;">
            <!-- Left Side: Greet -->
            <div class="col-lg-5 p-4 p-md-5">
                <span class="mb-3 d-inline-block" style="<?= $badgeStyle ?> font-weight: 700; font-size: 0.7rem; letter-spacing: 0.8px; text-transform: uppercase;">
                    <i class="fas <?= $badgeIcon ?> me-1"></i> <?= $badgeLabel ?>
                </span>
                <h3 class="fw-bold text-white mb-2" style="font-size: 1.85rem; letter-spacing: -0.5px; white-space: nowrap; font-family: 'Inter', sans-serif;">
                    Xin chào, <span style="background: linear-gradient(to right, #818cf8, #c084fc, #f472b6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800;"><?= $greetingName ?></span>!</span>
                </h3>
                <p class="mb-0" style="font-size: 0.95rem; font-weight: 500; line-height: 1.6; color: #94a3b8 !important;"><?= $subtitle ?></p>
            </div>

            <!-- Center Side: Slideshow (Stacked Drawing Papers Effect) -->
            <div class="col-lg-4 text-center py-3 py-lg-0 d-flex justify-content-center align-items-center">
                <div style="position: relative; width: 380px; height: 215px; margin: 15px 0;">
                    <!-- Bottom sketch paper sheet (light warm white paper) -->
                    <div class="sheet-bottom" style="position: absolute; top: 8px; left: 10px; width: 100%; height: 100%; background-color: #f3f4f6; border: 1px solid rgba(0, 0, 0, 0.05); box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 12px; transform: rotate(5deg); z-index: 1; pointer-events: none;">
                        <!-- Crop marks style for authenticity -->
                        <div style="position: absolute; top: 8px; bottom: 8px; left: 8px; right: 8px; border: 1px solid rgba(0, 180, 216, 0.06); border-radius: 8px;"></div>
                    </div>

                    <!-- Middle sketch paper sheet (clean off-white paper) -->
                    <div class="sheet-middle" style="position: absolute; top: -6px; left: -8px; width: 100%; height: 100%; background-color: #fafafa; border: 1px solid rgba(0, 0, 0, 0.07); box-shadow: 0 6px 16px rgba(0,0,0,0.2); border-radius: 12px; transform: rotate(-4deg); z-index: 2; pointer-events: none;">
                        <!-- Crop marks style for authenticity -->
                        <div style="position: absolute; top: 8px; bottom: 8px; left: 8px; right: 8px; border: 1px dashed rgba(0, 180, 216, 0.08); border-radius: 8px;"></div>
                    </div>

                    <!-- Top main sketch paper sheet containing slideshow -->
                    <div class="dashboard-slideshow-container sheet-top"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; background-color: #ffffff; border: 1px solid rgba(0, 0, 0, 0.1); box-shadow: 0 10px 25px rgba(0,0,0,0.3); border-radius: 12px; transform: rotate(-0.5deg); z-index: 3;">
                        
                        <!-- Real Manga Manuscript Non-Photo Blue crop mark frame guidelines -->
                        <div style="position: absolute; top: 12px; bottom: 12px; left: 12px; right: 12px; border: 1px solid rgba(0, 180, 216, 0.15); border-radius: 6px; pointer-events: none; z-index: 4;"></div>
                        <div style="position: absolute; top: 16px; bottom: 16px; left: 16px; right: 16px; border: 1px dashed rgba(0, 180, 216, 0.08); pointer-events: none; z-index: 4;"></div>
                        
                        <?php
                        $imageFiles = glob(__DIR__ . '/../../assets/images/Manga_dashboard_*.{png,jpg,jpeg,webp,PNG,JPG,JPEG,WEBP}', GLOB_BRACE);
                        if ($imageFiles) {
                            natsort($imageFiles);
                            $imageFiles = array_values($imageFiles);
                        } else {
                            $imageFiles = [];
                        }

                        foreach ($imageFiles as $index => $file):
                            $fileName = basename($file);
                            $version = filemtime($file);
                            $isActive = ($index === 0) ? 'active' : '';
                            $opacity = ($index === 0) ? '1' : '0';
                        ?>
                            <div class="dashboard-slide <?= $isActive ?>"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('<?= BASE_PATH ?>/assets/images/<?= $fileName ?>?v=<?= $version ?>'); background-size: contain; background-repeat: no-repeat; background-position: center; opacity: <?= $opacity ?>; transition: opacity 0.8s ease-in-out; z-index: 3;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right Side: Quote -->
            <div class="col-lg-3 p-4 p-md-5 d-flex justify-content-lg-end position-relative">
                <div class="position-relative p-4" style="max-width: 320px; z-index: 1;">
                    <!-- Large artistic quote symbol in background -->
                    <span style="position: absolute; top: -15px; left: -10px; font-size: 5rem; color: rgba(129, 140, 248, 0.15); font-family: Georgia, serif; line-height: 1; pointer-events: none; user-select: none;">“</span>

                    <p class="mb-2" style="font-size: 0.9rem; line-height: 1.65; font-style: italic; font-weight: 500; color: #cbd5e1 !important; position: relative; z-index: 2;">
                        <?= $quote ?>
                    </p>
                    <div class="text-end fw-bold" style="font-size: 0.8rem; color: #a5b4fc !important; position: relative; z-index: 2;">
                        – <?= $author ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const slides = document.querySelectorAll(".dashboard-slide");
        if (slides.length === 0) return;

        let currentSlide = 0;

        setInterval(function() {
            // Fade out current slide
            slides[currentSlide].style.opacity = "0";

            // Next slide
            currentSlide = (currentSlide + 1) % slides.length;

            // Fade in next slide
            slides[currentSlide].style.opacity = "1";
        }, 4000); // Transitions every 5 seconds
    });
</script>