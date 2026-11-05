<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$pageTitle = 'Dashboard';

$stats = [
    'products' => $conn->query("SELECT COUNT(*) c FROM products WHERE is_active=1")->fetch_assoc()['c'],
    'pending' => $conn->query("SELECT COUNT(*) c FROM orders WHERE status='pending'")->fetch_assoc()['c'],
    'low_stock' => $conn->query("SELECT COUNT(*) c FROM products WHERE is_active=1 AND stock_kg <= low_stock_threshold")->fetch_assoc()['c'],
    'revenue' => $conn->query("SELECT COALESCE(SUM(total_amount),0) s FROM orders WHERE status IN ('ready','completed')")->fetch_assoc()['s'],
];
$low = $conn->query("SELECT * FROM products WHERE is_active=1 AND stock_kg <= low_stock_threshold ORDER BY stock_kg ASC LIMIT 10");
$recent = $conn->query("SELECT o.*, u.name customer_name FROM orders o JOIN users u ON u.id=o.customer_id ORDER BY o.created_at DESC LIMIT 8");
include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-4">Dashboard</h2>
<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="card stat-card p-3"><small class="text-muted">Active Products</small><div class="h3 mb-0"><?= $stats['products'] ?></div></div></div>
  <div class="col-md-3"><div class="card stat-card p-3" style="border-left-color:#ffc107"><small class="text-muted">Pending Orders</small><div class="h3 mb-0"><?= $stats['pending'] ?></div></div></div>
  <div class="col-md-3"><div class="card stat-card p-3" style="border-left-color:#dc3545"><small class="text-muted">Low Stock Items</small><div class="h3 mb-0"><?= $stats['low_stock'] ?></div></div></div>
  <div class="col-md-3"><div class="card stat-card p-3" style="border-left-color:#198754"><small class="text-muted">Revenue (ready+)</small><div class="h3 mb-0"><?= money($stats['revenue']) ?></div></div></div>
</div>
<div class="row g-4">
  <div class="col-lg-6">
    <div class="card p-3">
      <h5>⚠️ Low Stock</h5>
      <table class="table table-sm mb-0">
        <thead><tr><th>Product</th><th>Stock</th></tr></thead>
        <tbody>
        <?php if ($low->num_rows): while ($p = $low->fetch_assoc()): ?>
          <tr class="low-stock"><td><?= e($p['name']) ?></td><td><?= number_format($p['stock_kg'],2) ?> kg</td></tr>
        <?php endwhile; else: ?><tr><td colspan="2" class="text-muted">All stock levels healthy.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card p-3">
      <h5>🕒 Recent Orders</h5>
      <table class="table table-sm mb-0">
        <thead><tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>
        <?php while ($o = $recent->fetch_assoc()): ?>
          <tr>
            <td><a href="order_view.php?id=<?= $o['id'] ?>">#<?= $o['id'] ?></a></td>
            <td><?= e($o['customer_name']) ?></td>
            <td><?= money($o['total_amount']) ?></td>
            <td><span class="badge badge-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>