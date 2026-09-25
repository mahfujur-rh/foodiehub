<?php

session_start();

require_once "config/db.php";


/*
|--------------------------------------------------------------------------
| Login Check
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {

    header("Location: login.html");
    exit;

}


$user_id = (int)$_SESSION["user_id"];

$order_id = (int)($_GET["order_id"] ?? 0);


if ($order_id <= 0) {

    die("Invalid order ID.");

}


/*
|--------------------------------------------------------------------------
| Get Order Information
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        o.order_id,
        o.total_amount,
        o.delivery_address,
        o.phone,
        o.order_status,
        o.created_at,
        u.name,
        u.email

    FROM orders o

    JOIN users u
        ON o.user_id = u.user_id

    WHERE o.order_id = ?
    AND o.user_id = ?

    LIMIT 1
");


$stmt->bind_param(
    "ii",
    $order_id,
    $user_id
);

$stmt->execute();

$order = $stmt->get_result()->fetch_assoc();


if (!$order) {

    die("Order not found.");

}


/*
|--------------------------------------------------------------------------
| Get Order Items
|--------------------------------------------------------------------------
*/

$item_stmt = $conn->prepare("
    SELECT
        oi.quantity,
        oi.price,
        f.food_name,
        f.image

    FROM order_items oi

    JOIN foods f
        ON oi.food_id = f.food_id

    WHERE oi.order_id = ?

    ORDER BY oi.order_item_id ASC
");


$item_stmt->bind_param(
    "i",
    $order_id
);

$item_stmt->execute();

$items = $item_stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Order #<?php echo $order["order_id"]; ?>
        | FoodieHub
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

    <style>

        .order-details-page {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }


        .order-details-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }


        .order-details-header h1 {
            margin: 0;
        }


        .status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            background: #ff6b35;
            color: #fff;
            font-weight: 600;
        }


        .info-box {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }


        .info-box h2 {
            margin-top: 0;
        }


        .info-grid {
            display: grid;
            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 15px;
        }


        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;

            padding: 15px 0;

            border-bottom: 1px solid #eee;
        }


        .order-item:last-child {
            border-bottom: none;
        }


        .food-name {
            font-weight: 600;
        }


        .food-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }


        .item-price {
            text-align: right;
        }


        .total-box {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-top: 20px;
            padding-top: 20px;

            border-top: 2px solid #eee;

            font-size: 20px;
            font-weight: 700;
        }


        .back-btn {
            display: inline-block;

            margin-top: 20px;

            padding: 10px 18px;

            background: #ff6b35;
            color: #fff;

            text-decoration: none;

            border-radius: 8px;
        }


        @media (max-width: 600px) {

            .info-grid {
                grid-template-columns: 1fr;
            }


            .order-item {
                align-items: flex-start;
            }

        }

    </style>

</head>


<body>


<!-- =========================
     HEADER
========================= -->

<header class="header">

    <a
        href="index.html"
        class="logo"
    >
        🍔 Foodie<span>Hub</span>
    </a>


    <nav>

        <a href="index.html">
            Home
        </a>

        <a href="my_orders.php">
            My Orders
        </a>

        <a href="profile.php">
            Profile
        </a>

    </nav>

</header>



<!-- =========================
     ORDER DETAILS
========================= -->

<main class="order-details-page">


    <div class="order-details-header">

        <h1>
            Order #<?php echo $order["order_id"]; ?>
        </h1>


        <span class="status">

            <?php
            echo htmlspecialchars(
                $order["order_status"]
            );
            ?>

        </span>

    </div>



    <!-- CUSTOMER INFORMATION -->

    <div class="info-box">

        <h2>
            Customer Information
        </h2>


        <div class="info-grid">

            <div>

                <strong>Name:</strong>

                <?php
                echo htmlspecialchars(
                    $order["name"]
                );
                ?>

            </div>


            <div>

                <strong>Email:</strong>

                <?php
                echo htmlspecialchars(
                    $order["email"]
                );
                ?>

            </div>


            <div>

                <strong>Phone:</strong>

                <?php
                echo htmlspecialchars(
                    $order["phone"]
                );
                ?>

            </div>


            <div>

                <strong>Order Date:</strong>

                <?php
                echo htmlspecialchars(
                    $order["created_at"]
                );
                ?>

            </div>


            <div>

                <strong>Delivery Address:</strong>

                <?php
                echo htmlspecialchars(
                    $order["delivery_address"]
                );
                ?>

            </div>

        </div>

    </div>



    <!-- ORDER ITEMS -->

    <div class="info-box">

        <h2>
            Ordered Items
        </h2>


        <?php if ($items->num_rows > 0): ?>


            <?php while ($item = $items->fetch_assoc()): ?>

                <div class="order-item">

                    <div class="food-info">

                        <span class="food-name">

                            <?php
                            echo htmlspecialchars(
                                $item["food_name"]
                            );
                            ?>

                        </span>


                        <span>

                            Quantity:
                            <?php
                            echo (int)$item["quantity"];
                            ?>

                        </span>

                    </div>


                    <div class="item-price">

                        ৳<?php
                        echo number_format(
                            $item["price"],
                            2
                        );
                        ?>

                    </div>

                </div>

            <?php endwhile; ?>


        <?php else: ?>

            <p>
                No items found for this order.
            </p>

        <?php endif; ?>



        <div class="total-box">

            <span>
                Total
            </span>

            <span>

                ৳<?php
                echo number_format(
                    $order["total_amount"],
                    2
                );
                ?>

            </span>

        </div>

    </div>



    <a
        href="my_orders.php"
        class="back-btn"
    >
        ← Back to My Orders
    </a>


</main>


</body>

</html>