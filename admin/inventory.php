<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$pageTitle = 'Inventory';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int)($_POST['product_id'] ?? 0);
    $add = (float)($_POST['add_kg'] ?? 0);
    $reason = trim($_POST['reason'] ?? 'Delivery');
    if ($productId && $add != 0) {
        $stmt = $conn->prepare("UPDATE products SET stock_kg = stock_kg + ? WHERE id=?");
        $stmt->bind_param('di', $add, $productId);
        $stmt->execute();
        $stmt = $conn->prepare("INSERT INTO stock_history (product_id, change_kg, reason) VALUES (?,?,?)");
        $stmt->bind_param('ids', $productId, $add, $reason);
        $stmt->execute();
        flash('success', 'Stock updated.');
    }
    redirect('inventory.php');
}

$products = $conn->query("SELECT * FROM products WHERE is_active=1 ORDER BY name");
$history = $conn->query("SELECT sh.*, p.name FROM stock_history sh JOIN products p ON p.id=sh.product_id ORDER BY sh.id DESC LIMIT 20");
include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-3">Inventory Management</h2>
<div class="row g-4">
  <div class="col-lg-7">
    <div class="card p-3">
      <h5>Current Stock</h5>
      <table class="table table-sm">
        <thead><tr><th>Product</th><th>Stock</th><th>Threshold</th><th>Status</th></tr></thead>
        <tbody>
        <?php while ($p = $products->fetch_assoc()): $low = $p['stock_kg'] <= $p['low_stock_threshold']; ?>
          <tr class="<?= $low ? 'low-stock' : '' ?>">
            <td><?= e($p['name']) ?></td>
            <td><?= number_format($p['stock_kg'],2) ?> kg</td>
            <td><?= number_format($p['low_stock_threshold'],2) ?> kg</td>
            <td><?= $low ? '<span class="badge bg-warning text-dark">Low</span>' : '<span class="badge bg-success">OK</span>' ?></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card p-3">
      <h5>Record Delivery / Adjust Stock</h5>
      <form method="post">
        <div class="mb-2">
          <label class="form-label">Product</label>
          <select name="product_id" class="form-select" required>
            <option value="">-- select --</option>
            <?php $list = $conn->query("SELECT id,name FROM products WHERE is_active=1 ORDER BY name"); while($p=$list->fetch_assoc()): ?>
              <option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-2"><label class="form-label">Quantity (kg, use negative to deduct)</label><input type="number" step="0.01" name="add_kg" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Reason</label><input name="reason" class="form-control" value="Delivery"></div>
        <button class="btn btn-danger w-100">Update Stock</button>
      </form>
    </div>
    <div class="card p-3 mt-3">
      <h5>Recent Activity</h5>
      <ul class="list-unstyled small mb-0" style="max-height:200px;overflow:auto;">
        <?php while ($h = $history->fetch_assoc()): ?>
          <li class="border-bottom py-1"><?= e($h['name']) ?>: <strong><?= $h['change_kg']>0?'+':'' ?><?= number_format($h['change_kg'],2) ?> kg</strong> — <?= e($h['reason']) ?> <span class="text-muted">(<?= $h['created_at'] ?>)</span></li>
        <?php endwhile; ?>
      </ul>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>