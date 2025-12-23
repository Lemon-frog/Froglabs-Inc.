<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

/* ---------------- DATABASE ---------------- */
$pdo = new PDO(
    "mysql:host=localhost;dbname=froglabs_shop;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

/* ---------------- PRODUCTS ---------------- */
$frogs = $pdo->query("SELECT * FROM products ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

/* ---------------- CART ---------------- */
$_SESSION['cart'] ??= [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $id = (int)$_POST['add'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    echo json_encode(['cartCount' => array_sum($_SESSION['cart'])]);
    exit;
}

$cartCount = array_sum($_SESSION['cart']);
$userId = $_SESSION['user']['id'] ?? 'guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Froglabs Inc.</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="/Ecommerce/froglabs.css">
</head>

<body data-userid="<?= htmlspecialchars($userId) ?>">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg px-4">
    <a href="/Ecommerce/Froglabs.php" class="navbar-brand d-flex align-items-center gap-2">
        <img src="/Ecommerce/icon.png" class="brand-icon">
        <span class="brand-text">Froglabs Inc.</span>
    </a>

    <div class="ms-auto d-flex align-items-center gap-2">

        <button class="btn btn-outline-light btn-sm" id="themeToggle">
            <i class="bi bi-moon-fill"></i>
        </button>

        <?php if (!empty($_SESSION['user'])): ?>
            <span class="small"><?= htmlspecialchars($_SESSION['user']['name']) ?></span>
            <a href="/Ecommerce/auth.php?logout=1" class="btn btn-outline-warning btn-sm">Logout</a>
        <?php else: ?>
            <button class="btn btn-outline-light btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#authModal">
                Login / Signup
            </button>
        <?php endif; ?>

        <a href="/Ecommerce/cart.php" class="btn btn-outline-light position-relative">
            <i class="bi bi-cart-fill"></i>
            <span class="badge bg-danger floating-count"><?= $cartCount ?></span>
        </a>
    </div>
</nav>

<!-- HEADER -->
<header class="site-banner">
    <h1 class="banner-title">
        <img src="/Ecommerce/icon.png" class="header-icon">
        Froglabs Inc.
    </h1>
    <p class="banner-sub">Buy exotic frogs from Froglabs</p>
</header>

<!-- PRODUCTS -->
<main class="container my-4">
    <div class="products-grid">
        <?php foreach ($frogs as $frog): ?>
        <div class="product-card">

            <img src="/Ecommerce/<?= htmlspecialchars($frog['image']) ?>"
                 alt="<?= htmlspecialchars($frog['name']) ?>"
                    class="product-image">

            <div class="card-body">
                <h5><?= htmlspecialchars($frog['name']) ?></h5>

                <?php if ($frog['sale'] && $frog['old_price']): ?>
                    <div class="old-price">
                        ₱<?= number_format($frog['old_price'], 2) ?>
                    </div>
                <?php endif; ?>

                <div class="price">
                    ₱<?= number_format($frog['price'], 2) ?>
                </div>
            </div>

            <div class="card-footer">
                <button class="btn-add btn w-100"
                        data-id="<?= (int)$frog['id'] ?>">
                    Add to Cart
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- AUTH MODAL -->
<div class="modal fade" id="authModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content text-dark">

      <div class="modal-header">
        <h5 class="modal-title">Account</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- LOGIN -->
        <form method="POST" action="/Ecommerce/auth.php" class="mb-3">
          <h6>Login</h6>
          <input class="form-control mb-2" name="email" placeholder="Email" required>
          <input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
          <button class="btn btn-success w-100" name="login">Login</button>
        </form>

        <hr>

        <!-- SIGNUP -->
        <form method="POST" action="/Ecommerce/auth.php">
          <h6>Signup</h6>
          <input class="form-control mb-2" name="name" placeholder="Name" required>
          <input class="form-control mb-2" name="email" placeholder="Email" required>
          <input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
          <input class="form-control mb-2" name="address" placeholder="Address" required>
          <button class="btn btn-primary w-100" name="signup">Create Account</button>
        </form>

      </div>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer class="footer py-4">
    <small>&copy; <?= date('Y') ?> Froglabs Inc.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* THEME TOGGLE */
const toggle = document.getElementById('themeToggle');
toggle.onclick = () => {
    document.body.classList.toggle('light-mode');
    localStorage.setItem(
        'theme',
        document.body.classList.contains('light-mode') ? 'light' : 'dark'
    );
};
if (localStorage.getItem('theme') === 'light') {
    document.body.classList.add('light-mode');
}

/* ADD TO CART */
document.querySelectorAll('.btn-add').forEach(btn => {
    btn.onclick = () => {
        fetch('/Ecommerce/Froglabs.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'add=' + btn.dataset.id
        })
        .then(r => r.json())
        .then(d => {
            const badge = document.querySelector('.floating-count');
            badge.textContent = d.cartCount;
            badge.classList.add('pulse');
            setTimeout(() => badge.classList.remove('pulse'), 400);
        });
    };
});
</script>

</body>
</html>
