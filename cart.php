<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

$pdo = new PDO(
    "mysql:host=localhost;dbname=froglabs_shop;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$_SESSION['cart'] ??= [];

if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][(int)$_GET['remove']]);
    header("Location: cart.php");
    exit;
}

$cartItems = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cartItems as $item) {
        $total += $item['price'] * $_SESSION['cart'][$item['id']];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart – Froglabs</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/Ecommerce/froglabs.css">
</head>

<body>

<nav class="navbar navbar-expand-lg px-4">
    <a href="/Ecommerce/Froglabs.php" class="navbar-brand d-flex align-items-center gap-2">
        <img src="/Ecommerce/icon.png" class="brand-icon">
        <span class="brand-text">Froglabs Inc.</span>
    </a>
</nav>

<main class="container py-5">

<h1 class="text-center mb-4">🛒 Your Cart</h1>

<?php if (empty($_SESSION['cart'])): ?>

    <div class="alert alert-warning text-center text-dark">
        Your cart is empty
    </div>

    <div class="text-center">
        <a href="/Ecommerce/Froglabs.php" class="btn btn-success">
            Back to Shop
        </a>
    </div>

<?php else: ?>

<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Frog</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cartItems as $item):
            $qty = $_SESSION['cart'][$item['id']];
            $subtotal = $item['price'] * $qty;
        ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= $qty ?></td>
            <td>₱<?= number_format($item['price'], 2) ?></td>
            <td>₱<?= number_format($subtotal, 2) ?></td>
            <td>
                <a href="cart.php?remove=<?= $item['id'] ?>"
                   class="btn btn-danger btn-sm">
                   Remove
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h3 class="text-center mt-4">
    Total: ₱<?= number_format($total, 2) ?>
</h3>

<div class="text-center mt-4">
    <a href="/Ecommerce/Froglabs.php" class="btn btn-outline-light me-2">
        Continue Shopping
    </a>
    <a href="/Ecommerce/checkout.php" class="btn btn-success">
        Checkout
    </a>
</div>

<?php endif; ?>

</main>

<footer class="footer">
    <small>&copy; <?= date('Y') ?> Froglabs Inc. All rights reserved.</small>
</footer>

<script>
if (localStorage.getItem('theme') === 'light') {
    document.body.classList.add('light-mode');
}
</script>

</body>
</html>
