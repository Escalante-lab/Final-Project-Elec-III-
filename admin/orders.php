<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$pageTitle = 'Orders';

$filter = $_GET['status'] ?? '';
$sql = "SELECT o.*, u.name customer_name FROM orders o JOIN users u ON u.id=o.customer_id";
if (in_array($filter, ['pending','processing','ready','completed','cancelled'])) {
    $sql .= " WHERE o.status='" . $conn->real_escape_string($filter) . "'";
}
$sql .= " ORDER BY o.created_at DESC";
$orders = $conn->query($sql);
include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-3">Orders</h2>
<div class="mb-3">
  <a class="btn btn-sm btn-outline-dark <?= $filter===''?'active':'' ?>" href="orders.php">All</a>
  <?php foreach (['pending','processing','ready','completed','cancelled'] as $s): ?>
    <a class="btn btn-sm btn-outline-dark <?= $filter===$s?'active':'' ?>" href="orders.php?status=<?= $s ?>"><?= ucfirst($s) ?></a>
  <?php endforeach; ?>
</div>
<div class="card p-3">
<table class="table align-middle">
  <thead><tr><th>#</th><th>Customer</th><th>Total</th><th>Pickup</th><th>Status</th><th>Placed</th><th></th></tr></thead>
  <tbody>
  <?php if ($orders->num_rows): while ($o = $orders->fetch_assoc()): ?>
    <tr>
      <td>#<?= $o['id'] ?></td>
      <td><?= e($o['customer_name']) ?></td>
      <td><?= money($o['total_amount']) ?></td>
      <td><?= date('M d, H:i', strtotime($o['pickup_time'])) ?></td>
      <td><span class="badge badge-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
      <td class="small text-muted"><?= $o['created_at'] ?></td>
      <td class="text-end"><a href="order_view.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary">View</a></td>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="7" class="text-muted">No orders found.</td></tr>
  <?php endif; ?>
  </tbody>
</table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>