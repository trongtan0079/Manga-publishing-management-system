<?php

namespace App\Controllers;

use App\Models\SeriesRanking;

class SeriesRankingController extends BaseController
{
    public function index() {
        // TODO: Implement index()
    }

    public function create() {
        // TODO: Implement create()
    }

    public function store() {
        // TODO: Implement store() logic for SeriesRanking
        
        // Đoạn code mẫu sinh Notification khi công bố Ranking mới
        /*
        require_once __DIR__ . '/../models/Notification.php';
        $notificationModel = new \Notification();
        
        // Giả sử $mangakaId là ID của tác giả bộ truyện vừa được xếp hạng
        $notificationModel->createNotification(
            $mangakaId,
            'ranking_published',
            'Bảng xếp hạng mới đã được công bố. Bộ truyện của bạn đạt top ...'
        );
        */
    }

    public function edit($id) {
        // TODO: Implement edit()
    }

    public function update($id) {
        // TODO: Implement update()
    }

    public function delete($id) {
        // TODO: Implement delete()
    }
}
