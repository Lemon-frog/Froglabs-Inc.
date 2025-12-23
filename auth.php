<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

/* ---------- DATABASE ---------- */
$pdo = new PDO(
    "mysql:host=localhost;dbname=froglabs_shop;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

/* ---------- SIGNUP ---------- */
if (isset($_POST['signup'])) {

    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, password, address)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $hash,
            $_POST['address']
        ]);
    } catch (PDOException $e) {
        $_SESSION['signup_error'] = "Email already exists";
        header("Location: Froglabs.php");
        exit;
    }

    $_SESSION['user'] = [
        "id"    => $pdo->lastInsertId(),
        "name"  => $_POST['name'],
        "email" => $_POST['email']
    ];

    header("Location: Froglabs.php");
    exit;
}

/* ---------- LOGIN ---------- */
if (isset($_POST['login'])) {

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($_POST['password'], $user['password'])) {

        $_SESSION['user'] = [
            "id"    => $user['id'],
            "name"  => $user['name'],
            "email" => $user['email']
        ];

        header("Location: Froglabs.php");
        exit;
    }

    $_SESSION['login_error'] = "Invalid email or password";
    header("Location: Froglabs.php");
    exit;
}

/* ---------- LOGOUT ---------- */
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: Froglabs.php");
    exit;
}
