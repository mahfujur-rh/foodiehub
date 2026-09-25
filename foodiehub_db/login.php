<?php

session_start();

require_once "config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.html");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    die("Email and password are required.");
}

$stmt = $conn->prepare("
    SELECT user_id, name, email, password, role
    FROM users
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param("s", $email);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

if ($user && password_verify($password, $user["password"])) {

    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["name"] = $user["name"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["role"] = $user["role"];

    // Admin → Admin Dashboard
    if ($user["role"] === "admin") {

        header("Location: admin/dashboard.php");
        exit;

    }

    // Customer → Home
    header("Location: index.html");
    exit;
}

die("Invalid email or password.");

?>