<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../../includes/functions.php';

$pageTitle = 'Register';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $pass    = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
        $error = 'Provide a valid name, email, and password (min 6 chars).';
    } elseif ($pass !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        if ($stmt->get_result()->fetch_row()) {
            $error = 'Email already registered.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
            $stmt->bind_param('sss', $name, $email, $hash);
            $stmt->execute();
            flash('success', 'Registration successful. Please login.');
            redirect(BASE_URL . 'login.php');
        }
    }
}

include __DIR__ . '/../../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-5">
  <div class="card p-4">
    <h3 class="mb-3">Create Account</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <div class="mb-3"><label class="form-label">Full Name</label>
        <input name="name" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" minlength="6" required></div>
      <div class="mb-3"><label class="form-label">Confirm Password</label>
        <input type="password" name="confirm" class="form-control" required></div>
      <button class="btn btn-danger w-100">Register</button>
    </form>
    <p class="mt-3 text-center small">Already have an account? <a href="login.php">Login</a></p>
  </div>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>