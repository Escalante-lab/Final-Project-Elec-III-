<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
$pageTitle = 'Cart';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove'])) {
        unset($_SESSION['cart'][(int)$_POST['remove']]);
        flash('success', 'Item removed.');
        redirect('cart.php');
    }
    if (isset($_POST['update_qty'])) {
        $pid = (int)$_POST['pid'];
        $qty = (float)$_POST['qty'];
        if ($qty > 0) $_SESSION['cart'][$pid] = $qty;
        else unset($_SESSION['cart'][$pid]);
        redirect('cart.php');
    }
}

$items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $res = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    while ($p = $res->fetch_assoc()) {
        $qty = $_SESSION['cart'][$p['id']];
        $sub = $qty * $p['price_per_kg'];
        $total += $sub;
        $items[] = ['p' => $p, 'qty' => $qty, 'sub' => $sub];
    }
}
include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-3">Your Cart</h2>
<?php if (!$items): ?>
  <div class="card p-4 text-center">
    <p class="mb-3">Your cart is empty.</p>
    <a href="shop.php" class="btn btn-danger">Go Shopping</a>
  </div>
<?php else: ?>
  <div class="card p-3">
    <table class="table align-middle">
      <thead><tr><th>Product</th><th>Qty (kg)</th><th>Price/kg</th><th>Subtotal</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($items as $it): $p = $it['p']; ?>
        <tr>
          <td><?= e($p['name']) ?></td>
          <td>
            <form method="post" class="d-flex gap-1">
              <input type="hidden" name="pid" value="<?= $p['id'] ?>">
              <input type="number" step="0.1" min="0.1" max="<?= $p['stock_kg'] ?>" name="qty" value="<?= $it['qty'] ?>" class="form-control form-control-sm" style="width:80px">
              <button name="update_qty" class="btn btn-sm btn-outline-secondary">↻</button>
            </form>
          </td>
          <td><?= money($p['price_per_kg']) ?></td>
          <td><?= money($it['sub']) ?></td>
          <td>
            <form method="post"><input type="hidden" name="remove" value="<?= $p['id'] ?>"><button class="btn btn-sm btn-outline-danger">✕</button></form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
      <tfoot><tr><th colspan="3" class="text-end">Total</th><th colspan="2"><?= money($total) ?></th></tr></tfoot>
    </table>
    <div class="text-end">
      <a href="shop.php" class="btn btn-secondary">Continue Shopping</a>
      <a href="checkout.php" class="btn btn-danger">Proceed to Checkout</a>
    </div>
  </div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>