<?php

session_start();

require_once "config/db.php";

header("Content-Type: application/json");

// Check login
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);

    echo json_encode([
        "error" => "Please login first"
    ]);

    exit;
}

// Read JSON data
$data = json_decode(file_get_contents("php://input"), true);

$address = trim($data["address"] ?? "");
$phone = trim($data["phone"] ?? "");
$items = $data["items"] ?? [];

// Validate data
if (empty($address) || empty($phone) || empty($items)) {
    http_response_code(400);

    echo json_encode([
        "error" => "Incomplete order data"
    ]);

    exit;
}

$user_id = (int) $_SESSION["user_id"];

$conn->begin_transaction();

try {

    // Get logged-in user information
    $user_stmt = $conn->prepare("
        SELECT name, email
        FROM users
        WHERE user_id = ?
        LIMIT 1
    ");

    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();

    $user = $user_stmt->get_result()->fetch_assoc();

    if (!$user) {
        throw new Exception("User not found");
    }

    $customer_name = $user["name"];
    $customer_email = $user["email"];

    // Calculate total
    $subtotal = 0;

    foreach ($items as $item) {

        $food_id = (int) ($item["id"] ?? 0);
        $quantity = max(1, (int) ($item["qty"] ?? 1));

        $stmt = $conn->prepare("
            SELECT price
            FROM foods
            WHERE food_id = ?
            AND status = 'available'
        ");

        $stmt->bind_param("i", $food_id);
        $stmt->execute();

        $food = $stmt->get_result()->fetch_assoc();

        if (!$food) {
            throw new Exception("Food unavailable");
        }

        $subtotal += (float) $food["price"] * $quantity;
    }

    // Delivery charge
    $delivery_charge = 0;

    // Total amount
    $total = $subtotal + $delivery_charge;

    // Insert order
    $order_stmt = $conn->prepare("
        INSERT INTO orders
        (
            user_id,
            customer_name,
            customer_email,
            customer_mobile,
            delivery_address,
            subtotal,
            delivery_charge,
            total_amount,
            payment_method,
            payment_status,
            order_status
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, 'cash_on_delivery', 'pending', 'pending')
    ");

    $order_stmt->bind_param(
        "issssddd",
        $user_id,
        $customer_name,
        $customer_email,
        $phone,
        $address,
        $subtotal,
        $delivery_charge,
        $total
    );

    if (!$order_stmt->execute()) {
        throw new Exception($order_stmt->error);
    }

    $order_id = $order_stmt->insert_id;

    // Insert order items
    foreach ($items as $item) {

        $food_id = (int) ($item["id"] ?? 0);
        $quantity = max(1, (int) ($item["qty"] ?? 1));

        $price_stmt = $conn->prepare("
            SELECT price
            FROM foods
            WHERE food_id = ?
            AND status = 'available'
            LIMIT 1
        ");

        $price_stmt->bind_param("i", $food_id);
        $price_stmt->execute();

        $food_data = $price_stmt->get_result()->fetch_assoc();

        if (!$food_data) {
            throw new Exception("Food not found");
        }

        $price = (float) $food_data["price"];

        $item_stmt = $conn->prepare("
            INSERT INTO order_items
            (
                order_id,
                food_id,
                quantity,
                price
            )
            VALUES (?, ?, ?, ?)
        ");

        $item_stmt->bind_param(
            "iiid",
            $order_id,
            $food_id,
            $quantity,
            $price
        );

        if (!$item_stmt->execute()) {
            throw new Exception($item_stmt->error);
        }
    }

    // Complete transaction
    $conn->commit();

    echo json_encode([
        "success" => true,
        "order_id" => $order_id,
        "message" => "Order placed successfully"
    ]);

} catch (Exception $e) {

    $conn->rollback();

    http_response_code(500);

    echo json_encode([
        "error" => $e->getMessage()
    ]);
}

?>