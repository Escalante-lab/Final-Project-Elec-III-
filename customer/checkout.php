<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
$pageTitle = 'Checkout';

if (empty($_SESSION['cart'])) redirect('cart.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup = $_POST['pickup_time'] ?? '';
    $notes = trim($_POST['notes'] ?? '');
    $dt = strtotime($pickup);
    if (!$dt || $dt < time()) {
        $error = 'Pickup time must be in the future.';
    } else {
        $conn->begin_transaction();
        try {
            $total = 0;
            $items = [];
            $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
            // lock rows for concurrency
            $res = $conn->query("SELECT * FROM products WHERE id IN ($ids) FOR UPDATE");
            while ($p = $res->fetch_assoc()) {
                $qty = $_SESSION['cart'][$p['id']];
                if ($qty > $p['stock_kg']) {
                    throw new Exception("Insufficient stock for {$p['name']}. Available: {$p['stock_kg']} kg");
                }
                $sub = $qty * $p['price_per_kg'];
                $total += $sub;
                $items[] = ['p' => $p, 'qty' => $qty, 'sub' => $sub];
            }
            if (!$items) throw new Exception("Cart is empty.");

            $stmt = $conn->prepare("INSERT INTO orders (customer_id,total_amount,pickup_time,status,notes) VALUES (?,?,?,'pending',?)");
            $uid = $_SESSION['user_id'];
            $pickupFmt = date('Y-m-d H:i:s', $dt);
            $stmt->bind_param('idss', $uid, $total, $pickupFmt, $notes);
            $stmt->execute();
            $orderId = $conn->insert_id;

            $ins = $conn->prepare("INSERT INTO order_items (order_id,product_id,quantity_kg,price_per_kg,subtotal) VALUES (?,?,?,?,?)");
            $upd = $conn->prepare("UPDATE products SET stock_kg = stock_kg - ? WHERE id=?");
            $hist = $conn->prepare("INSERT INTO stock_history (product_id,change_kg,reason) VALUES (?,?,?)");

            foreach ($items as $it) {
                $pid = $it['p']['id'];
                $qty = $it['qty'];
                $price = $it['p']['price_per_kg'];
                $sub = $it['sub'];
                $ins->bind_param('iiddd', $orderId, $pid, $qty, $price, $sub);
                $ins->execute();
                $upd->bind_param('di', $qty, $pid);
                $upd->execute();
                $neg = -$qty;
                $reason = "Order #$orderId";
                $hist->bind_param('ids', $pid, $neg, $reason);
                $hist->execute();
            }
            $conn->commit();
            unset($_SESSION['cart']);
            flash('success', "Order #$orderId placed! Pickup on " . date('M d, H:i', $dt));
            redirect('orders.php');
        } catch (Exception $ex) {
            $conn->rollback();
            $error = $ex->getMessage();
        }
    }
}
include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-3">Checkout</h2>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="card p-4 col-md-6">
<form method="post">
  <div class="mb-3">
    <label class="form-label">Pickup Date &amp; Time</label>
    <input type="datetime-local" name="pickup_time" class="form-control" required min="<?= date('Y-m-d\TH:i') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Notes (optional)</label>
    <textarea name="notes" class="form-control" rows="2"></textarea>
  </div>
  <p class="text-muted small">Payment is done at pickup. No online payment required.</p>
  <button class="btn btn-danger w-100">Place Order</button>
</form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>