<?php include __DIR__ . '/../layouts/header.php'; ?>

<!-- Nút quay lại trang danh sách -->
<div class="mb-3">
    <a href="/index.php?controller=user&action=index" class="btn btn-secondary">&larr; Back to Users</a>
</div>

<div class="card">
    <div class="card-header">
        <h4>Create New User</h4>
    </div>
    <div class="card-body">
        <!-- Form gọi action store bằng phương thức POST để lưu dữ liệu -->
        <form action="/index.php?controller=user&action=store" method="POST">
            
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <!-- Nếu để trống, UserController sẽ đặt pass mặc định (ví dụ: password123) -->
                <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to use default 'password123'">
            </div>

            <div class="mb-3">
                <label for="role_id" class="form-label">Role</label>
                <!-- Lặp qua danh sách Roles từ Database để hiển thị option -->
                <select class="form-select" id="role_id" name="role_id" required>
                    <option value="" disabled selected>Select Role</option>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['role_id'] ?>"><?= htmlspecialchars($role['role_name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <!-- Chọn trạng thái ban đầu cho user -->
                <select class="form-select" id="status" name="status" required>
                    <option value="active" selected>Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="banned">Banned</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Create User</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
