<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
$pageTitle = 'My Orders';

$uid = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE customer_id=? ORDER BY created_at DESC");
$stmt->bind_param('i', $uid);
$stmt->execute();
$orders = $stmt->get_result();
include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-3">My Orders</h2>
<div class="card p-3">
<table class="table align-middle">
  <thead><tr><th>#</th><th>Total</th><th>Pickup</th><th>Status</th><th>Placed</th><th></th></tr></thead>
  <tbody>
  <?php if ($orders->num_rows): while ($o = $orders->fetch_assoc()): ?>
    <tr>
      <td>#<?= $o['id'] ?></td>
      <td><?= money($o['total_amount']) ?></td>
      <td><?= date('M d, H:i', strtotime($o['pickup_time'])) ?></td>
      <td><span class="badge badge-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
      <td class="small text-muted"><?= $o['created_at'] ?></td>
      <td><a href="order_view.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary">View</a></td>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="6" class="text-muted">You have no orders yet.</td></tr>
  <?php endif; ?>
  </tbody>
</table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>