<?php
session_start();

require_once "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$user_id = (int) $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT order_id, total_amount, delivery_address, phone,
            order_status, created_at
     FROM orders
     WHERE user_id = ?
     ORDER BY created_at DESC"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Orders - FoodieHub</title>

    <link rel="stylesheet" href="style.css">

    <style>
        .orders-container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
        }

        .orders-container h1 {
            margin-bottom: 25px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th,
        td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #ff6b35;
            color: white;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            background: #fff0e8;
            color: #ff6b35;
            font-weight: bold;
        }

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 20px;
            background: #ff6b35;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>

<body>

<div class="orders-container">

    <h1>My Orders</h1>

    <?php if ($orders->num_rows === 0): ?>

        <p>You have not placed any orders yet.</p>

    <?php else: ?>

        <div class="table-wrapper">

            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Total Amount</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>

                <?php while ($order = $orders->fetch_assoc()): ?>

                    <tr>
                        <td>
                            #<?php echo htmlspecialchars($order["order_id"]); ?>
                        </td>

                        <td>
                            ৳<?php echo number_format($order["total_amount"], 2); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($order["phone"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($order["delivery_address"]); ?>
                        </td>

                        <td>
                            <span class="status">
                                <?php echo htmlspecialchars($order["order_status"]); ?>
                            </span>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($order["created_at"]); ?>
                        </td>
                    </tr>

                <?php endwhile; ?>

                </tbody>
            </table>

        </div>

    <?php endif; ?>

    <a href="index.html" class="back-btn">Back to Home</a>

</div>

</body>
</html>