<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../../includes/functions.php';
$pageTitle = 'Home';
include __DIR__ . '/../../includes/header.php';
?>
<div class="p-5 mb-4 bg-white rounded-3 shadow-sm text-center">
  <h1 class="display-5 fw-bold">🥩 Meat Inventory &amp; Ordering Management System</h1>
  <p class="lead text-muted">Order fresh meat online, skip the queue, pick up when ready.</p>
  <?php if (!isLoggedIn()): ?>
    <a href="register.php" class="btn btn-danger btn-lg">Get Started</a>
    <a href="login.php" class="btn btn-outline-dark btn-lg">Login</a>
  <?php else: ?>
    <?php if (isAdmin()): ?>
      <a href="admin/dashboard.php" class="btn btn-danger btn-lg">Go to Dashboard</a>
    <?php else: ?>
      <a href="customer/shop.php" class="btn btn-danger btn-lg">Shop Now</a>
    <?php endif; ?>
  <?php endif; ?>
</div>
<div class="row g-4">
  <div class="col-md-4"><div class="card p-4 h-100"><h4>📦 Inventory Tracking</h4><p class="text-muted">Real-time stock levels with low-stock alerts and automatic deduction when orders are placed.</p></div></div>
  <div class="col-md-4"><div class="card p-4 h-100"><h4>🛒 Online Ordering</h4><p class="text-muted">Browse cuts, check availability, and schedule a convenient pickup time.</p></div></div>
  <div class="col-md-4"><div class="card p-4 h-100"><h4>📊 Reporting</h4><p class="text-muted">Monitor popular products, track inventory, and view orders at a glance.</p></div></div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>