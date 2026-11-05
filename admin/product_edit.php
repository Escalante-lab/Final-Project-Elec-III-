<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$pageTitle = 'Edit Product';

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('products.php');

$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) redirect('products.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $cut = trim($_POST['cut_type'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price_per_kg'] ?? 0);
    $stock = (float)($_POST['stock_kg'] ?? 0);
    $threshold = (float)($_POST['low_stock_threshold'] ?? 5);

    if ($name === '' || $price <= 0 || $stock < 0) {
        $error = 'Invalid data.';
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, cut_type=?, description=?, price_per_kg=?, stock_kg=?, low_stock_threshold=? WHERE id=?");
        $stmt->bind_param('sssdddi', $name, $cut, $desc, $price, $stock, $threshold, $id);
        $stmt->execute();
        flash('success', 'Product updated.');
        redirect('products.php');
    }
}
include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-3">Edit Product</h2>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="card p-4 col-md-8">
<form method="post">
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label">Name</label><input name="name" class="form-control" value="<?= e($product['name']) ?>" required></div>
    <div class="col-md-6"><label class="form-label">Cut Type</label><input name="cut_type" class="form-control" value="<?= e($product['cut_type']) ?>"></div>
    <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"><?= e($product['description']) ?></textarea></div>
    <div class="col-md-4"><label class="form-label">Price / kg</label><input type="number" step="0.01" min="0.01" name="price_per_kg" class="form-control" value="<?= e($product['price_per_kg']) ?>" required></div>
    <div class="col-md-4"><label class="form-label">Stock (kg)</label><input type="number" step="0.01" min="0" name="stock_kg" class="form-control" value="<?= e($product['stock_kg']) ?>"></div>
    <div class="col-md-4"><label class="form-label">Low Stock Threshold (kg)</label><input type="number" step="0.01" min="0" name="low_stock_threshold" class="form-control" value="<?= e($product['low_stock_threshold']) ?>"></div>
  </div>
  <div class="mt-3">
    <button class="btn btn-danger">Update</button>
    <a href="products.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>