<?php
define('BASE_URL', '/Final%20project%20Elec%20III/');
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$pageTitle = 'Add Product';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $cut = trim($_POST['cut_type'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price_per_kg'] ?? 0);
    $stock = (float)($_POST['stock_kg'] ?? 0);
    $threshold = (float)($_POST['low_stock_threshold'] ?? 5);

    if ($name === '' || $price <= 0 || $stock < 0) {
        $error = 'Name required; price must be > 0; stock >= 0.';
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name,cut_type,description,price_per_kg,stock_kg,low_stock_threshold) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param('sssddd', $name, $cut, $desc, $price, $stock, $threshold);
        $stmt->execute();
        flash('success', 'Product added.');
        redirect('products.php');
    }
}
include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-3">Add Product</h2>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="card p-4 col-md-8">
<form method="post">
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Cut Type</label><input name="cut_type" class="form-control"></div>
    <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
    <div class="col-md-4"><label class="form-label">Price / kg</label><input type="number" step="0.01" min="0.01" name="price_per_kg" class="form-control" required></div>
    <div class="col-md-4"><label class="form-label">Initial Stock (kg)</label><input type="number" step="0.01" min="0" name="stock_kg" class="form-control" value="0"></div>
    <div class="col-md-4"><label class="form-label">Low Stock Threshold (kg)</label><input type="number" step="0.01" min="0" name="low_stock_threshold" class="form-control" value="5"></div>
  </div>
  <div class="mt-3">
    <button class="btn btn-danger">Save</button>
    <a href="products.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>