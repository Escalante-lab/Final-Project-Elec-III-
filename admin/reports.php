<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$pageTitle = 'Reports';

$invSummary = $conn->query("SELECT COUNT(*) total_products, COALESCE(SUM(stock_kg),0) total_stock, COALESCE(SUM(stock_kg*price_per_kg),0) stock_value FROM products WHERE is_active=1")->fetch_assoc();
$popular = $conn->query("SELECT p.name, SUM(oi.quantity_kg) qty, SUM(oi.subtotal) revenue FROM order_items oi JOIN products p ON p.id=oi.product_id JOIN orders o ON o.id=oi.order_id WHERE o.status != 'cancelled' GROUP BY p.id ORDER BY qty DESC LIMIT 10");
$low = $conn->query("SELECT * FROM products WHERE is_active=1 AND stock_kg <= low_stock_threshold ORDER BY stock_kg ASC");

include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-3">Reports</h2>
<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="card p-3"><small class="text-muted">Total Active Products</small><div class="h4 mb-0"><?= $invSummary['total_products'] ?></div></div></div>
  <div class="col-md-4"><div class="card p-3"><small class="text-muted">Total Stock (kg)</small><div class="h4 mb-0"><?= number_format($invSummary['total_stock'],2) ?></div></div></div>
  <div class="col-md-4"><div class="card p-3"><small class="text-muted">Stock Value</small><div class="h4 mb-0"><?= money($invSummary['stock_value']) ?></div></div></div>
</div>
<div class="row g-4">
  <div class="col-lg-7">
    <div class="card p-3">
      <h5>🏆 Popular Products</h5>
      <table class="table table-sm">
        <thead><tr><th>Product</th><th>Qty Sold (kg)</th><th>Revenue</th></tr></thead>
        <tbody>
        <?php while ($p = $popular->fetch_assoc()): ?>
          <tr><td><?= e($p['name']) ?></td><td><?= number_format($p['qty'],2) ?></td><td><?= money($p['revenue']) ?></td></tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card p-3">
      <h5>⚠️ Low Stock Items</h5>
      <table class="table table-sm">
        <thead><tr><th>Product</th><th>Stock</th></tr></thead>
        <tbody>
        <?php if ($low->num_rows): while ($p = $low->fetch_assoc()): ?>
          <tr class="low-stock"><td><?= e($p['name']) ?></td><td><?= number_format($p['stock_kg'],2) ?> kg</td></tr>
        <?php endwhile; else: ?><tr><td colspan="2" class="text-muted">None.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>