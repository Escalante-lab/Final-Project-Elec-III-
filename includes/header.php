<?php
require_once __DIR__ . '/functions.php';
$BASE = defined('BASE_URL') ? BASE_URL : '/meat-inventory/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? 'Meat Inventory & Ordering') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= $BASE ?>assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="<?= $BASE ?>index.php">🥩 MeatShop</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <?php if (isLoggedIn()): ?>
          <?php if (isAdmin()): ?>
            <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>admin/dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>admin/products.php">Products</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>admin/inventory.php">Inventory</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>admin/orders.php">Orders</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>admin/reports.php">Reports</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>customer/shop.php">Shop</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>customer/cart.php">Cart</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>customer/orders.php">My Orders</a></li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav">
        <?php if (isLoggedIn()): ?>
          <li class="nav-item"><span class="navbar-text me-3">👤 <?= e($_SESSION['name']) ?></span></li>
          <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php if ($m = flash('Success')): ?>
  <div class="alert alert-success alert-dismissible fade show"><?= e($m) ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if ($m = flash('Error')): ?>
  <div class="alert alert-danger alert-dismissible fade show"><?= e($m) ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>