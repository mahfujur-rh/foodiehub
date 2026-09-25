<?php

session_start();

require_once "../config/db.php";

if (($_SESSION["role"] ?? "") !== "admin") {
    http_response_code(403);
    die("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

$order_id = (int)($_POST["order_id"] ?? 0);

$order_status = trim($_POST["order_status"] ?? "");


$allowed_statuses = [
    "Pending",
    "Confirmed",
    "Preparing",
    "Out for Delivery",
    "Delivered",
    "Cancelled"
];


if (
    $order_id <= 0 ||
    !in_array($order_status, $allowed_statuses, true)
) {
    header("Location: dashboard.php?error=invalid");
    exit;
}


$stmt = $conn->prepare("
    UPDATE orders
    SET order_status = ?
    WHERE order_id = ?
");


$stmt->bind_param(
    "si",
    $order_status,
    $order_id
);


if ($stmt->execute()) {

    header("Location: dashboard.php?success=updated");
    exit;

} else {

    header("Location: dashboard.php?error=update_failed");
    exit;

}


$stmt->close();
$conn->close();

?>