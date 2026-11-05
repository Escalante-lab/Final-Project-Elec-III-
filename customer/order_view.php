<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
$pageTitle = 'Order Details';

$id = (int)($_GET['id'] ?? 0);
$uid = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE id=? AND customer_id=?");
$stmt->bind_param('ii', $id, $uid);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) redirect('orders.php');

$items = $conn->query("SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=$id");
include __DIR__ . '/../includes/header.php';
?>
<a href="orders.php" class="btn btn-sm btn-secondary mb-3">← Back</a>
<div class="card p-4">
  <h4>Order #<?= $order['id'] ?> <span class="badge badge-<?= $order['status'] ?>"><?= $order['status'] ?></span></h4>
  <p class="mb-1"><strong>Pickup:</strong> <?= date('M d, Y H:i', strtotime($order['pickup_time'])) ?></p>
  <?php if ($order['notes']): ?><p class="mb-1"><strong>Notes:</strong> <?= e($order['notes']) ?></p><?php endif; ?>
  <hr>
  <table class="table table-sm">
    <thead><tr><th>Product</th><th>Qty</th><th>Price/kg</th><th>Subtotal</th></tr></thead>
    <tbody>
    <?php while ($i = $items->fetch_assoc()): ?>
      <tr><td><?= e($i['name']) ?></td><td><?= number_format($i['quantity_kg'],2) ?> kg</td><td><?= money($i['price_per_kg']) ?></td><td><?= money($i['subtotal']) ?></td></tr>
    <?php endwhile; ?>
    </tbody>
    <tfoot><tr><th colspan="3" class="text-end">Total</th><th><?= money($order['total_amount']) ?></th></tr></tfoot>
  </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>