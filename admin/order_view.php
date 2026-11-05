<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$pageTitle = 'Order Details';

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('orders.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = $_POST['status'];
    if (in_array($newStatus, ['pending','processing','ready','completed','cancelled'])) {
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param('si', $newStatus, $id);
        $stmt->execute();
        flash('success', 'Order status updated.');
        redirect("order_view.php?id=$id");
    }
}

$stmt = $conn->prepare("SELECT o.*, u.name customer_name, u.email customer_email FROM orders o JOIN users u ON u.id=o.customer_id WHERE o.id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) redirect('orders.php');

$items = $conn->query("SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=$id");

include __DIR__ . '/../includes/header.php';
?>
<a href="orders.php" class="btn btn-sm btn-secondary mb-3">← Back to Orders</a>
<div class="row g-4">
  <div class="col-md-8">
    <div class="card p-4">
      <h4>Order #<?= $order['id'] ?></h4>
      <p class="mb-1"><strong>Customer:</strong> <?= e($order['customer_name']) ?> (<?= e($order['customer_email']) ?>)</p>
      <p class="mb-1"><strong>Pickup:</strong> <?= date('M d, Y H:i', strtotime($order['pickup_time'])) ?></p>
      <p class="mb-1"><strong>Placed:</strong> <?= $order['created_at'] ?></p>
      <?php if ($order['notes']): ?><p class="mb-1"><strong>Notes:</strong> <?= e($order['notes']) ?></p><?php endif; ?>
      <hr>
      <table class="table table-sm">
        <thead><tr><th>Product</th><th>Qty</th><th>Price/kg</th><th>Subtotal</th></tr></thead>
        <tbody>
        <?php $total = 0; while ($i = $items->fetch_assoc()): $total += $i['subtotal']; ?>
          <tr><td><?= e($i['name']) ?></td><td><?= number_format($i['quantity_kg'],2) ?> kg</td><td><?= money($i['price_per_kg']) ?></td><td><?= money($i['subtotal']) ?></td></tr>
        <?php endwhile; ?>
        </tbody>
        <tfoot><tr><th colspan="3" class="text-end">Total</th><th><?= money($order['total_amount']) ?></th></tr></tfoot>
      </table>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-4">
      <h5>Update Status</h5>
      <p>Current: <span class="badge badge-<?= $order['status'] ?>"><?= $order['status'] ?></span></p>
      <form method="post">
        <select name="status" class="form-select mb-2">
          <?php foreach (['pending','processing','ready','completed','cancelled'] as $s): ?>
            <option value="<?= $s ?>" <?= $order['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-danger w-100">Update</button>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>