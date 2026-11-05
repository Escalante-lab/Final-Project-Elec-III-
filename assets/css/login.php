<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../../includes/functions.php';

$pageTitle = 'Login';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        redirect(isAdmin()
            ? BASE_URL . 'admin/dashboard.php'
            : BASE_URL . 'customer/shop.php');
    }
    $error = 'Invalid email or password.';
}

include __DIR__ . '/../../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-5">
  <div class="card p-4">
    <h3 class="mb-3">Login</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button class="btn btn-danger w-100">Login</button>
    </form>
    <p class="mt-3 text-center small">No account? <a href="register.php">Register</a></p>
    <p class="text-center small text-muted mb-0">Demo admin: admin@gmail.com / admin123</p>
  </div>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>