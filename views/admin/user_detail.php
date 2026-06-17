<?php include __DIR__ . '/../layouts/header.php'; ?>

<!-- Thanh điều hướng cơ bản -->
<div class="mb-3">
    <a href="/index.php?controller=user&action=index" class="btn btn-secondary">&larr; Back to Users</a>
    <a href="/index.php?controller=user&action=edit&id=<?= $user['user_id'] ?>" class="btn btn-warning">Edit User</a>
</div>

<div class="card">
    <div class="card-header">
        <h4>User Details: <?= htmlspecialchars($user['username']) ?></h4>
    </div>
    <div class="card-body">
        <!-- Bảng hiển thị thông tin chi tiết -->
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th style="width: 200px;">User ID</th>
                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                </tr>
                <tr>
                    <th>Username</th>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                </tr>
                <tr>
                    <th>Full Name</th>
                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td>
                        <span class="badge bg-info text-dark">
                            <?= htmlspecialchars($user['role_name'] ?? 'N/A') ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <!-- Hiển thị badge theo trạng thái -->
                        <?php if ($user['status'] === 'active'): ?>
                            <span class="badge bg-success">Active</span>
                        <?php elseif ($user['status'] === 'inactive'): ?>
                            <span class="badge bg-secondary">Inactive</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Banned</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <!-- Thời gian tạo tài khoản -->
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <!-- Thời gian cập nhật gần nhất -->
                    <td><?= htmlspecialchars($user['updated_at']) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
