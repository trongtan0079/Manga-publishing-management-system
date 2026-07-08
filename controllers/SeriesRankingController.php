<?php


require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/SeriesRanking.php';
require_once __DIR__ . '/../models/Series.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/SystemLog.php';


class SeriesRankingController extends BaseController
{
    private $rankingModel;
    private $seriesModel;
    private $notificationModel;

    /**
     * Hàm khởi tạo (Constructor)
     * Đảm bảo người dùng đã đăng nhập.
     * Chặn hoàn toàn quyền truy cập của Assistant vào module Xếp hạng.
     */
    public function __construct() {
        parent::__construct();
        
        \requireLogin();

        $role = $_SESSION['role_name'];
        if ($role === 'assistant') {
            $_SESSION['error'] = 'Truy cập bị từ chối! Bạn không có quyền xem bảng xếp hạng.';
            header('Location: ' . BASE_PATH . '/index.php');
            exit;
        }

        $this->rankingModel = new SeriesRanking();
        $this->seriesModel = new Series();
        $this->notificationModel = new Notification();
    }

    /**
     * Action: Hiển thị danh sách Xếp hạng
     * - Mangaka: Chỉ nhìn thấy xếp hạng của các tác phẩm do mình sáng tác.
     * - Board: Nhìn thấy toàn bộ xếp hạng (được quyền Thêm/Sửa/Xóa).
     * - Admin, Editor: Nhìn thấy toàn bộ xếp hạng (chỉ xem, Read-only).
     */
    public function index() {
        $role = $_SESSION['role_name'];
        if ($role === 'mangaka') {
            $rankings = $this->rankingModel->findByMangakaId($_SESSION['user_id']);
            require_once __DIR__ . '/../views/mangaka/rankings.php';
        } elseif ($role === 'board') {
            $rankings = $this->rankingModel->findAllWithSeries();
            require_once __DIR__ . '/../views/board/rankings.php';
        } else {
            // Admin, Editor
            $rankings = $this->rankingModel->findAllWithSeries();
            // Reusing a common view or board view for read-only
            // Since Board view has edit/delete buttons, we should pass role to hide them or create a specific view.
            require_once __DIR__ . '/../views/board/rankings.php'; 
        }
    }

    /**
     * Action: Hiển thị form tạo Xếp hạng mới (Nhập phiếu bầu hàng loạt)
     * Quyền: Chỉ dành cho Board (Ban Giám đốc)
     */
    public function create() {
        \requireRole('board');
        // Lấy danh sách truyện đang hoạt động để đánh giá xếp hạng kèm tên mangaka và editor chuyên trách
        $sql = "SELECT s.*, u.full_name as mangaka_name, ed.full_name as editor_name
                FROM series s 
                JOIN users u ON s.mangaka_id = u.user_id 
                LEFT JOIN users ed ON s.editor_id = ed.user_id
                WHERE s.status IN ('ongoing', 'completed', 'suspended')
                ORDER BY s.title ASC";
        $stmt = $this->seriesModel->getConnection()->prepare($sql);
        $stmt->execute();
        $seriesList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/board/ranking_create.php';
    }

    /**
     * Action: Xử lý lưu dữ liệu Xếp hạng (nhập phiếu hàng loạt)
     * Tự động tính toán thứ hạng và quy chuẩn hóa điểm số dựa trên số phiếu độc giả.
     */
    public function store() {
        \requireRole('board');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $periodStartDate = trim($_POST['period_start_date'] ?? '');
            $votes = $_POST['votes'] ?? []; // Mảng [series_id => votes]

            if (empty($periodStartDate) || empty($votes)) {
                $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin kỳ đánh giá và số phiếu bình chọn.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=create');
                exit;
            }

            $periodStartTimestamp = strtotime($periodStartDate);
            if ($periodStartTimestamp === false) {
                $_SESSION['error'] = 'Kỳ đánh giá không hợp lệ.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=create');
                exit;
            }
            // Lỗ hổng 9: Chặn ngày đánh giá ở tương lai
            if ($periodStartTimestamp > time()) {
                $_SESSION['error'] = 'Kỳ đánh giá không thể ở tương lai.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=create');
                exit;
            }
            $periodStartDateFormatted = date('Y-m-d', $periodStartTimestamp);

            // Tìm số phiếu cao nhất để tính điểm quy chuẩn (0 - 100)
            $maxVotes = 0;
            $validVotes = [];

            foreach ($votes as $seriesId => $voteCount) {
                $seriesId = (int)$seriesId;
                $voteCount = (int)$voteCount;
                if ($voteCount < 0) $voteCount = 0;

                $series = $this->seriesModel->findById($seriesId);
                if ($series && in_array($series['status'], ['ongoing', 'completed', 'suspended'])) {
                    $validVotes[$seriesId] = [
                        'votes' => $voteCount,
                        'mangaka_id' => $series['mangaka_id'],
                        'title' => $series['title']
                    ];
                    if ($voteCount > $maxVotes) {
                        $maxVotes = $voteCount;
                    }
                }
            }

            if (empty($validVotes)) {
                $_SESSION['error'] = 'Không có bộ truyện hợp lệ để đánh giá.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=create');
                exit;
            }

            // Sắp xếp các bộ truyện giảm dần theo số phiếu
            uasort($validVotes, function($a, $b) {
                return $b['votes'] <=> $a['votes'];
            });

            $conn = $this->rankingModel->getConnection();
            $conn->beginTransaction();

            try {
                // Xóa xếp hạng cũ trong cùng kỳ nếu có để tránh trùng lặp dữ liệu
                $deleteSql = "DELETE FROM series_rankings WHERE period_start_date = :period_date";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->execute(['period_date' => $periodStartDateFormatted]);

                $rank = 1;
                $prevVotes = null;
                $itemCount = 0;
                foreach ($validVotes as $seriesId => $info) {
                    $itemCount++;
                    $votesVal = $info['votes'];

                    // Lỗ hổng 5: Tính toán thứ hạng đồng hạng thi đấu chuẩn
                    if ($prevVotes !== null && $votesVal < $prevVotes) {
                        $rank = $itemCount;
                    }
                    $prevVotes = $votesVal;

                    // Tính điểm quy chuẩn (maxVotes tương ứng 100 điểm)
                    $score = ($maxVotes > 0) ? round(($votesVal / $maxVotes) * 100, 2) : 0.00;

                    $insertData = [
                        'series_id' => $seriesId,
                        'board_member_id' => $_SESSION['user_id'],
                        'rank_position' => $rank,
                        'score' => $score,
                        'period_start_date' => $periodStartDateFormatted
                    ];
                    $this->rankingModel->insert($insertData);

                    // Gửi thông báo đến Mangaka phụ trách bộ truyện
                    $message = "Thông báo xếp hạng kỳ mới (" . date('d/m/Y', $periodStartTimestamp) . "): Bộ truyện '{$info['title']}' của bạn đạt thứ hạng #{$rank} với điểm số bình chọn quy chuẩn là {$score}/100 ({$votesVal} phiếu).";
                    $this->notificationModel->createNotification($info['mangaka_id'], 'ranking_published', $message, $seriesId);

                    // Gửi cảnh báo đặc biệt (series_warning) nếu điểm thấp (< 50) hoặc thứ hạng thấp (>= 5)
                    if ($score < 50 && $rank >= 5) {
                        $warningMsg = "Cảnh báo: Bộ truyện '{$info['title']}' của bạn đang xếp hạng thấp (Hạng {$rank}, Điểm {$score}). Có nguy cơ bị Hội đồng Biên tập xem xét ngưng xuất bản hoặc hủy dự án.";
                        $this->notificationModel->createNotification($info['mangaka_id'], 'series_warning', $warningMsg, $seriesId);
                    }
                }

                $conn->commit();

                // Lỗ hổng 4: Ghi nhật ký hoạt động của Board
                $logDetails = "Board nhập phiếu bầu và tự động xếp hạng kỳ '{$periodStartDateFormatted}'. Tổng số: " . count($validVotes) . " truyện, số phiếu cao nhất: {$maxVotes}.";
                \SystemLog::logAction($_SESSION['user_id'], 'Nhập phiếu bầu & Xếp hạng', $logDetails);

                $_SESSION['success'] = 'Nhập phiếu bầu độc giả và tự động xếp hạng thành công!';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=index');
                exit;

            } catch (Exception $e) {
                $conn->rollBack();
                $_SESSION['error'] = 'Lỗi hệ thống khi cập nhật xếp hạng: ' . $e->getMessage();
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=create');
                exit;
            }
        }
    }

    /**
     * Action: Xem chi tiết một Xếp hạng cụ thể
     * Nếu người dùng là Mangaka, chặn xem xếp hạng của truyện không thuộc quyền sở hữu.
     */
    public function show($id) {
        $id = (int)$id;
        $ranking = $this->rankingModel->findWithDetails($id);
        if (!$ranking) {
            $_SESSION['error'] = 'Không tìm thấy Ranking.';
            header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=index');
            exit;
        }

        $role = $_SESSION['role_name'];
        if ($role === 'mangaka' && $ranking['mangaka_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Truy cập bị từ chối.';
            header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=index');
            exit;
        }

        require_once __DIR__ . '/../views/board/ranking_detail.php';
    }

    /**
     * Action: Hiển thị form chỉnh sửa Xếp hạng
     * Quyền: Chỉ dành cho Board
     */
    public function edit($id) {
        \requireRole('board');
        $id = (int)$id;
        $ranking = $this->rankingModel->findById($id);
        if (!$ranking) {
            $_SESSION['error'] = 'Không tìm thấy Ranking.';
            header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=index');
            exit;
        }
        $allSeries = $this->seriesModel->findAll(); 
        $seriesList = array_filter($allSeries, function($s) use ($ranking) {
            return in_array($s['status'], ['ongoing', 'completed', 'suspended']) || $s['series_id'] == $ranking['series_id'];
        });
        require_once __DIR__ . '/../views/board/ranking_edit.php';
    }

    /**
     * Action: Xử lý lưu cập nhật Xếp hạng vào Database
     * Có kiểm tra trùng lặp nếu người dùng đổi Series hoặc Kỳ đánh giá.
     */
    public function update($id) {
        \requireRole('board');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$id;
            $ranking = $this->rankingModel->findById($id);
            if (!$ranking) {
                $_SESSION['error'] = 'Không tìm thấy Ranking.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=index');
                exit;
            }

            $seriesId = (int)($_POST['series_id'] ?? 0);
            $rankPosition = (int)($_POST['rank_position'] ?? 0);
            $score = (float)($_POST['score'] ?? -1);
            $periodStartDate = trim($_POST['period_start_date'] ?? '');

            if (!$seriesId || !$rankPosition || $score < 0 || !$periodStartDate) {
                $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=edit&id=' . $id);
                exit;
            }

            $series = $this->seriesModel->findById($seriesId);
            if (!$series) {
                $_SESSION['error'] = 'Series không tồn tại.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=edit&id=' . $id);
                exit;
            }
            if (in_array($series['status'], ['planning', 'canceled'])) {
                $_SESSION['error'] = 'Không thể xếp hạng cho bộ truyện đang ở trạng thái Kế hoạch hoặc Đã hủy.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=edit&id=' . $id);
                exit;
            }
            if ($rankPosition < 1) {
                $_SESSION['error'] = 'Vị trí xếp hạng phải lớn hơn hoặc bằng 1.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=edit&id=' . $id);
                exit;
            }
            if ($score < 0 || $score > 100) {
                $_SESSION['error'] = 'Điểm số phải nằm trong khoảng từ 0 đến 100.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=edit&id=' . $id);
                exit;
            }
            $periodStartTimestamp = strtotime($periodStartDate);
            if ($periodStartTimestamp === false) {
                $_SESSION['error'] = 'Kỳ đánh giá không hợp lệ.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=edit&id=' . $id);
                exit;
            }
            // Lỗ hổng 9: Chặn ngày đánh giá ở tương lai
            if ($periodStartTimestamp > time()) {
                $_SESSION['error'] = 'Kỳ đánh giá không thể ở tương lai.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=edit&id=' . $id);
                exit;
            }
            $periodStartDateFormatted = date('Y-m-d', $periodStartTimestamp);

            // Chỉ kiểm tra trùng lặp dữ liệu (Duplicate) nếu Series hoặc Kỳ đánh giá bị thay đổi
            if (($seriesId != $ranking['series_id'] || $periodStartDateFormatted != $ranking['period_start_date']) && 
                $this->rankingModel->checkDuplicateRanking($seriesId, $periodStartDateFormatted)) {
                $_SESSION['error'] = 'Series này đã có đánh giá trong kỳ này.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=edit&id=' . $id);
                exit;
            }

            $data = [
                'series_id' => $seriesId,
                'rank_position' => $rankPosition,
                'score' => $score,
                'period_start_date' => $periodStartDateFormatted
            ];

            try {
                $this->rankingModel->update($id, $data);

                // Tự động gửi cảnh báo nếu điểm số cập nhật thủ công dưới 50 hoặc thứ hạng thấp (>= 5)
                if ($score < 50 && $rankPosition >= 5) {
                    $warningMsg = "Cảnh báo chỉnh sửa: Bộ truyện '{$series['title']}' của bạn đang xếp hạng thấp (Hạng {$rankPosition}, Điểm {$score}). Có nguy cơ bị Hội đồng Biên tập xem xét ngưng xuất bản hoặc hủy dự án.";
                    $this->notificationModel->createNotification($series['mangaka_id'], 'series_warning', $warningMsg, $seriesId);
                }

                // Lỗ hổng 4: Ghi nhật ký hoạt động
                $logDetails = "Board chỉnh sửa xếp hạng thủ công (ID dòng: {$id}) của bộ truyện '{$series['title']}' (ID truyện: {$seriesId}). Hạng: '{$ranking['rank_position']}' -> '{$rankPosition}', Điểm: '{$ranking['score']}' -> '{$score}', Kỳ: '{$ranking['period_start_date']}' -> '{$periodStartDateFormatted}'";
                \SystemLog::logAction($_SESSION['user_id'], 'Sửa xếp hạng thủ công', $logDetails);

                $_SESSION['success'] = 'Cập nhật Ranking thành công!';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=index');
                exit;
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=edit&id=' . $id);
                exit;
            }
        }
    }

    /**
     * Action: Xóa một Xếp hạng
     * Quyền: Chỉ dành cho Board
     */
    public function delete($id) {
        \requireRole('board');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$id;
            $ranking = $this->rankingModel->findById($id);
            if (!$ranking) {
                $_SESSION['error'] = 'Không tìm thấy Ranking.';
            } else {
                try {
                    $this->rankingModel->delete($id);

                    // Lỗ hổng 4: Ghi nhật ký hoạt động
                    $seriesTitle = 'Không rõ';
                    $series = $this->seriesModel->findById($ranking['series_id']);
                    if ($series) {
                        $seriesTitle = $series['title'];
                    }
                    $logDetails = "Board xóa xếp hạng thủ công (ID dòng: {$id}) của bộ truyện '{$seriesTitle}' (ID truyện: {$ranking['series_id']}). Hạng cũ: {$ranking['rank_position']}, Điểm cũ: {$ranking['score']}, Kỳ cũ: {$ranking['period_start_date']}";
                    \SystemLog::logAction($_SESSION['user_id'], 'Xóa xếp hạng thủ công', $logDetails);

                    $_SESSION['success'] = 'Xóa Ranking thành công!';
                } catch (PDOException $e) {
                    $_SESSION['error'] = 'Không thể xóa do ràng buộc dữ liệu.';
                }
            }
            header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=index');
            exit;
        }
    }
}
