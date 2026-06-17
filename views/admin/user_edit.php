<?php 
/**
 * @var array $user 
 * @var array $roles 
 */
include __DIR__ . '/../layouts/header.php'; 
?>

<!-- Nút quay lại -->
<div class="mb-3">
    <a href="/index.php?controller=user&action=index" class="btn btn-secondary">&larr; Back to Users</a>
</div>

<div class="card">
    <div class="card-header">
        <h4>Edit User: <?= htmlspecialchars($user['username']) ?></h4>
    </div>
    <div class="card-body">
        <!-- Form cập nhật, action trỏ tới update với user_id tương ứng -->
        <form action="/index.php?controller=user&action=update&id=<?= $user['user_id'] ?>" method="POST">
            
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <!-- Điền sẵn dữ liệu cũ vào value -->
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <!-- Để trống nếu không muốn đổi mật khẩu (xử lý logic trong UserController) -->
                <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
            </div>

            <div class="mb-3">
                <label for="role_id" class="form-label">Role</label>
                <select class="form-select" id="role_id" name="role_id" required>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <!-- So sánh role_id để đánh dấu selected cho role hiện tại của user -->
                            <option value="<?= $role['role_id'] ?>" <?= ($role['role_id'] == $user['role_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role['role_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <!-- So sánh status để đánh dấu selected -->
                    <option value="active" <?= ($user['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($user['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                    <option value="banned" <?= ($user['status'] === 'banned') ? 'selected' : '' ?>>Banned</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
