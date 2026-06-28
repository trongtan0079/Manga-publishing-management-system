<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold"><i class="fas fa-bell text-primary me-2"></i>Thông báo mới (<?= $this->unreadCount ?>)</h6>
        <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=notification&action=index" class="text-decoration-none text-sm">Xem tất cả</a>
    </div>
    <div class="card-body">
        <?php if (!empty($this->latestNotifications)): ?>
            <div class="list-group list-group-flush">
                <?php foreach ($this->latestNotifications as $notif): ?>
                    <div class="list-group-item list-group-item-action d-flex gap-3 py-3 <?= !$notif['is_read'] ? 'bg-light' : '' ?>" style="border-radius: 8px; margin-bottom: 5px; border: none;">
                        <div class="d-flex align-items-center">
                            <?php if (!$notif['is_read']): ?>
                                <span class="badge bg-primary p-1 border border-light rounded-circle me-2" style="width: 10px; height: 10px;"><span class="visually-hidden">New alerts</span></span>
                            <?php else: ?>
                                <span class="p-1 me-2" style="width: 10px; height: 10px;"></span>
                            <?php endif; ?>
                            
                            <?php 
                                $icon = 'fa-bell';
                                $color = 'text-secondary';
                                switch($notif['type']) {
                                    case 'task_assigned': $icon = 'fa-tasks'; $color = 'text-warning'; break;
                                    case 'submission_submitted':
                                    case 'chapter_submitted': $icon = 'fa-file-upload'; $color = 'text-info'; break;
                                    case 'review_created': $icon = 'fa-comment-dots'; $color = 'text-primary'; break;
                                    case 'submission_approved': $icon = 'fa-check-circle'; $color = 'text-success'; break;
                                    case 'submission_rejected': $icon = 'fa-times-circle'; $color = 'text-danger'; break;
                                    case 'ranking_published': $icon = 'fa-trophy'; $color = 'text-warning'; break;
                                }
                            ?>
                            <i class="fas <?= $icon ?> <?= $color ?> fs-5"></i>
                        </div>
                        <div class="d-flex flex-column justify-content-center w-100">
                            <h6 class="mb-1 text-dark text-sm fw-normal"><?= htmlspecialchars($notif['message']) ?></h6>
                            <small class="text-muted" style="font-size: 0.75rem;"><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></small>
                        </div>
                        <?php if (!$notif['is_read']): ?>
                            <div class="d-flex align-items-center ms-auto">
                                <form action="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/index.php?controller=notification&action=markAsRead&id=<?= $notif['notification_id'] ?>" method="POST" class="m-0">
                                    <button type="submit" class="btn btn-sm btn-link text-decoration-none" title="Đánh dấu đã đọc">
                                        <i class="fas fa-check text-success"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <div class="text-muted mb-2"><i class="fas fa-bell-slash fs-1 text-light"></i></div>
                <p class="text-muted mb-0">Bạn không có thông báo nào.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
