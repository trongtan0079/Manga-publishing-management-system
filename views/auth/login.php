<?php include '../layouts/header.php'; ?>

<h2>Login</h2>
<form action="/login" method="POST">
    <label>Email: <input type="email" name="email"></label>
    <label>Password: <input type="password" name="password"></label>
    <button type="submit">Login</button>
</form>

<?php include '../layouts/footer.php'; ?>
