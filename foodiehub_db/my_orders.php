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


/*
|--------------------------------------------------------------------------
| Get Customer Orders
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        order_id,
        total_amount,
        order_status,
        created_at
    FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$stmt->bind_param("i", $user_id);

$stmt->execute();

$orders = $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Orders - FoodieHub</title>

    <link
        rel="stylesheet"
        href="style.css"
    >


    <style>

        .orders-page {

            max-width: 1100px;

            margin: 50px auto;

            padding: 20px;

        }


        .orders-page h1 {

            margin-bottom: 30px;

        }


        .order-card {

            background: #fff;

            border-radius: 12px;

            padding: 20px;

            margin-bottom: 20px;

            box-shadow:
                0 5px 20px rgba(0,0,0,0.08);

        }


        .order-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            flex-wrap: wrap;

        }


        .order-header h3 {

            margin: 0;

        }


        .order-link {

            color: #ff6b35;

            text-decoration: none;

            transition: 0.3s;

        }


        .order-link:hover {

            text-decoration: underline;

        }


        .order-info {

            margin-top: 15px;

            line-height: 1.8;

        }


        .status {

            display: inline-block;

            padding: 7px 14px;

            border-radius: 20px;

            background: #ff6b35;

            color: white;

            font-size: 14px;

            font-weight: 600;

        }


        .no-orders {

            text-align: center;

            padding: 50px 20px;

        }


        .back-btn {

            display: inline-block;

            margin-top: 20px;

            padding: 10px 18px;

            background: #ff6b35;

            color: white;

            text-decoration: none;

            border-radius: 8px;

        }


        .details-btn {

            display: inline-block;

            margin-top: 10px;

            padding: 9px 15px;

            background: #ff6b35;

            color: white;

            text-decoration: none;

            border-radius: 7px;

            font-size: 14px;

        }


        .details-btn:hover {

            opacity: 0.9;

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
     ORDERS
========================= -->

<main class="orders-page">

    <h1>
        My Orders
    </h1>


    <?php if ($orders->num_rows > 0): ?>


        <?php while ($order = $orders->fetch_assoc()): ?>


            <div class="order-card">


                <div class="order-header">


                    <h3>

                        <a
                            class="order-link"
                            href="order_details.php?order_id=<?php echo (int)$order["order_id"]; ?>"
                        >

                            Order #
                            <?php
                            echo (int)$order["order_id"];
                            ?>

                        </a>

                    </h3>


                    <span class="status">

                        <?php
                        echo htmlspecialchars(
                            $order["order_status"]
                        );
                        ?>

                    </span>


                </div>



                <div class="order-info">


                    <p>

                        <strong>
                            Total:
                        </strong>

                        ৳<?php
                        echo number_format(
                            $order["total_amount"],
                            2
                        );
                        ?>

                    </p>


                    <p>

                        <strong>
                            Order Date:
                        </strong>

                        <?php
                        echo htmlspecialchars(
                            $order["created_at"]
                        );
                        ?>

                    </p>


                    <a
                        href="order_details.php?order_id=<?php echo (int)$order["order_id"]; ?>"
                        class="details-btn"
                    >
                        View Order Details
                    </a>


                </div>


            </div>


        <?php endwhile; ?>


    <?php else: ?>


        <div class="no-orders">

            <h2>
                No Orders Yet
            </h2>


            <p>
                You haven't placed any orders yet.
            </p>


            <a
                href="index.html"
                class="back-btn"
            >
                Browse Foods
            </a>

        </div>


    <?php endif; ?>


</main>


</body>

</html>