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

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$user = $_SESSION['user'] ?? ['name' => 'Guest', 'id' => 'guest'];

$ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $_SESSION['cart'][$item['id']];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $_SESSION['cart'] = [];
    $_SESSION['order_success'] = true;
    header("Location: checkout.php");
    exit;
}

$success = $_SESSION['order_success'] ?? false;
unset($_SESSION['order_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout – Froglabs</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/Ecommerce/froglabs.css">

<style>
/* SUCCESS ANIMATION */
.success-box {
    animation: pop-in .6s ease forwards;
}
@keyframes pop-in {
    0% { opacity: 0; transform: scale(.85); }
    60% { opacity: 1; transform: scale(1.05); }
    100% { transform: scale(1); }
}
.success-icon {
    font-size: 3rem;
    animation: bounce 1s ease infinite;
}
@keyframes bounce {
    50% { transform: translateY(-6px); }
}
</style>
</head>

<body>

<nav class="navbar navbar-expand-lg px-4">
    <a href="/Ecommerce/Froglabs.php" class="navbar-brand d-flex align-items-center gap-2">
        <img src="/Ecommerce/icon.png" class="brand-icon">
        <span class="brand-text">Froglabs Inc.</span>
    </a>
</nav>

<main class="container py-5">

<h2 class="text-center mb-4">Checkout 🐸</h2>
<p class="text-center">Hello, <strong><?= htmlspecialchars($user['name']) ?></strong></p>

<?php if ($success): ?>

<div class="alert alert-success text-center success-box">
    <div class="success-icon">🐸</div>
    <h4 class="mt-2">Order Placed Successfully!</h4>
    <p class="mb-3">Thank you for shopping with Froglabs 💚</p>
    <a href="/Ecommerce/Froglabs.php" class="btn btn-success">
        Back to Shop
    </a>
</div>

<?php else: ?>

<div class="checkout-card mx-auto p-4" style="max-width:600px">
    <ul class="list-group mb-3">
        <?php foreach ($items as $item):
            $qty = $_SESSION['cart'][$item['id']];
        ?>
        <li class="list-group-item d-flex justify-content-between">
            <?= htmlspecialchars($item['name']) ?> (x<?= $qty ?>)
            <span>₱<?= number_format($item['price'] * $qty, 2) ?></span>
        </li>
        <?php endforeach; ?>

        <li class="list-group-item d-flex justify-content-between fw-bold">
            <span>Total</span>
            <span>₱<?= number_format($total, 2) ?></span>
        </li>
    </ul>

    <form method="POST">
        <button class="btn btn-success w-100" name="place_order">
            Place Order
        </button>
    </form>
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
