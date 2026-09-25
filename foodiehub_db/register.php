<?php

session_start();

require_once "config/db.php";

/* =====================================================
   ONLY POST REQUEST
===================================================== */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.html");
    exit;
}

/* =====================================================
   GET FORM DATA
===================================================== */
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$address = trim($_POST["address"] ?? "");
$password = $_POST["password"] ?? "";

/* =====================================================
   VALIDATION
===================================================== */
if (!$name || !$email || !$mobile || !$address || !$password) {
    die("All fields are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email.");
}

if (strlen($password) < 6) {
    die("Password must be at least 6 characters.");
}

/* =====================================================
   CHECK EMAIL
===================================================== */
$check = $conn->prepare(
    "SELECT user_id FROM users WHERE email = ?"
);

$check->bind_param("s", $email);
$check->execute();

$result = $check->get_result();

if ($result->num_rows > 0) {
    die("Email already registered.");
}

/* =====================================================
   HASH PASSWORD
===================================================== */
$hash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

/* =====================================================
   INSERT USER
===================================================== */
$stmt = $conn->prepare("
    INSERT INTO users
    (
        name,
        email,
        mobile,
        address,
        password,
        role
    )
    VALUES
    (?, ?, ?, ?, ?, 'customer')
");

$stmt->bind_param(
    "sssss",
    $name,
    $email,
    $mobile,
    $address,
    $hash
);

/* =====================================================
   REGISTER SUCCESS
===================================================== */
if ($stmt->execute()) {

    $_SESSION["user_id"] = $stmt->insert_id;
    $_SESSION["name"] = $name;
    $_SESSION["email"] = $email;
    $_SESSION["role"] = "customer";

    // Registration successful → Home page
    header("Location: index.html");
    exit;
}

/* =====================================================
   REGISTER FAILED
===================================================== */
die("Registration failed.");

?>