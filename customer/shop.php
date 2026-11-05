<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
$pageTitle = 'Shop';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $pid = (int)$_POST['product_id'];
    $qty = (float)$_POST['qty'];
    if ($qty > 0) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id=? AND is_active=1");
        $stmt->bind_param('i', $pid);
        $stmt->execute();
        $p = $stmt->get_result()->fetch_assoc();
        if ($p) {
            if ($qty > $p['stock_kg']) {
                flash('error', "Only {$p['stock_kg']} kg of {$p['name']} available.");
            } else {
                $_SESSION['cart'][$pid] = ($_SESSION['cart'][$pid] ?? 0) + $qty;
                flash('success', "{$p['name']} added to cart.");
            }
        }
    }
    redirect('shop.php');
}

$products = $conn->query("SELECT * FROM products WHERE is_active=1 ORDER BY name");
include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-3">Fresh Meat Shop</h2>
<div class="row g-4">
<?php while ($p = $products->fetch_assoc()): $out = $p['stock_kg'] <= 0; ?>
  <div class="col-md-4">
    <div class="card product-card p-3 h-100">
      <h5><?= e($p['name']) ?></h5>
      <p class="text-muted small mb-1"><?= e($p['cut_type']) ?></p>
      <p class="small"><?= e($p['description']) ?></p>
      <p class="mb-1"><strong><?= money($p['price_per_kg']) ?></strong> / kg</p>
      <p class="mb-2 small">
        <?php if ($out): ?><span class="badge bg-danger">Out of stock</span>
        <?php else: ?><span class="badge bg-success"><?= number_format($p['stock_kg'],2) ?> kg available</span>
        <?php endif; ?>
      </p>
      <?php if (!$out): ?>
      <form method="post" class="d-flex gap-2">
        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
        <input type="number" step="0.1" min="0.1" max="<?= $p['stock_kg'] ?>" name="qty" class="form-control form-control-sm" value="1" required>
        <button name="add_to_cart" class="btn btn-sm btn-danger">Add</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
<?php endwhile; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>