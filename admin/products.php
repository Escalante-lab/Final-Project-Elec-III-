<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$pageTitle = 'Products';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $stmt = $conn->prepare("UPDATE products SET is_active=0 WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    flash('success', 'Product archived.');
    redirect('products.php');
}

$products = $conn->query("SELECT * FROM products WHERE is_active=1 ORDER BY name");
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Products</h2>
  <a href="product_add.php" class="btn btn-danger">+ Add Product</a>
</div>
<div class="card p-3">
<table class="table align-middle">
  <thead><tr><th>Name</th><th>Cut Type</th><th>Price/kg</th><th>Stock</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php while ($p = $products->fetch_assoc()): $low = $p['stock_kg'] <= $p['low_stock_threshold']; ?>
    <tr class="<?= $low ? 'low-stock' : '' ?>">
      <td><?= e($p['name']) ?></td>
      <td><?= e($p['cut_type']) ?></td>
      <td><?= money($p['price_per_kg']) ?></td>
      <td><?= number_format($p['stock_kg'],2) ?> kg</td>
      <td><?= $low ? '<span class="badge bg-warning text-dark">Low</span>' : '<span class="badge bg-success">OK</span>' ?></td>
      <td class="text-end">
        <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
        <form method="post" class="d-inline" onsubmit="return confirm('Archive this product?')">
          <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
          <button class="btn btn-sm btn-outline-danger">Archive</button>
        </form>
      </td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>