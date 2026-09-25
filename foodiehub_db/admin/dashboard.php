<?php

session_start();
require_once "../config/db.php";

if (($_SESSION["role"] ?? "") !== "admin") {
    http_response_code(403);
    die("Access denied.");
}

$foods = $conn->query("
    SELECT f.*, c.category_name
    FROM foods f
    JOIN categories c ON f.category_id = c.category_id
    ORDER BY f.food_id DESC
");

$orders = $conn->query("
    SELECT o.*, u.name
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.created_at DESC
");

?>

<!doctype html>

<html>

<head>

    <meta charset="UTF-8">

    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="../style.css">

    <style>

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        .admin-table th,
        .admin-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .admin-table th {
            background: #ff6b35;
            color: white;
        }

        .status-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .status-select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        .update-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            background: #ff6b35;
            color: white;
            cursor: pointer;
        }

        .update-btn:hover {
            opacity: 0.9;
        }

        .success-message {
            padding: 12px;
            margin-bottom: 20px;
            background: #d4edda;
            color: #155724;
            border-radius: 6px;
        }

        .error-message {
            padding: 12px;
            margin-bottom: 20px;
            background: #f8d7da;
            color: #721c24;
            border-radius: 6px;
        }

    </style>

</head>

<body>

<main class="section">

    <h1>Admin Dashboard</h1>


    <!-- SUCCESS / ERROR MESSAGE -->

    <?php if (isset($_GET["success"])): ?>

        <div class="success-message">
            Order status updated successfully.
        </div>

    <?php endif; ?>


    <?php if (isset($_GET["error"])): ?>

        <div class="error-message">
            Failed to update order status.
        </div>

    <?php endif; ?>


    <!-- =========================
         ORDERS
    ========================== -->

    <h2>Orders</h2>

    <table class="admin-table">

        <tr>

            <th>ID</th>

            <th>Customer</th>

            <th>Total</th>

            <th>Status</th>

        </tr>


        <?php while ($o = $orders->fetch_assoc()): ?>

            <tr>

                <td>
                    <?php echo $o["order_id"]; ?>
                </td>


                <td>
                    <?php echo htmlspecialchars($o["name"]); ?>
                </td>


                <td>
                    ৳<?php echo number_format($o["total_amount"], 2); ?>
                </td>


                <td>

                    <form
                        action="update_order.php"
                        method="POST"
                        class="status-form"
                    >

                        <input
                            type="hidden"
                            name="order_id"
                            value="<?php echo $o["order_id"]; ?>"
                        >


                        <select
                            name="order_status"
                            class="status-select"
                        >

                            <option
                                value="Pending"
                                <?php
                                echo ($o["order_status"] === "Pending")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Pending
                            </option>


                            <option
                                value="Confirmed"
                                <?php
                                echo ($o["order_status"] === "Confirmed")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Confirmed
                            </option>


                            <option
                                value="Preparing"
                                <?php
                                echo ($o["order_status"] === "Preparing")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Preparing
                            </option>


                            <option
                                value="Out for Delivery"
                                <?php
                                echo ($o["order_status"] === "Out for Delivery")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Out for Delivery
                            </option>


                            <option
                                value="Delivered"
                                <?php
                                echo ($o["order_status"] === "Delivered")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Delivered
                            </option>


                            <option
                                value="Cancelled"
                                <?php
                                echo ($o["order_status"] === "Cancelled")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Cancelled
                            </option>

                        </select>


                        <button
                            type="submit"
                            class="update-btn"
                        >
                            Update
                        </button>

                    </form>

                </td>

            </tr>

        <?php endwhile; ?>

    </table>


    <!-- =========================
         FOODS
    ========================== -->

    <h2>Foods</h2>

    <table class="admin-table">

        <tr>

            <th>ID</th>

            <th>Name</th>

            <th>Price</th>

            <th>Status</th>

        </tr>


        <?php while ($f = $foods->fetch_assoc()): ?>

            <tr>

                <td>
                    <?php echo $f["food_id"]; ?>
                </td>


                <td>
                    <?php echo htmlspecialchars($f["food_name"]); ?>
                </td>


                <td>
                    ৳<?php echo number_format($f["price"], 2); ?>
                </td>


                <td>
                    <?php echo htmlspecialchars($f["status"]); ?>
                </td>

            </tr>

        <?php endwhile; ?>

    </table>

</main>

</body>

</html>