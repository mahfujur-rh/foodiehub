<?php

session_start();

require_once "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$user_id = (int) $_SESSION["user_id"];

$message = "";
$messageType = "";

// Save address
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $address = trim($_POST["address"] ?? "");

    if ($address === "") {
        $message = "Please enter your address.";
        $messageType = "error";
    } else {

        $stmt = $conn->prepare(
            "UPDATE users SET address = ? WHERE user_id = ?"
        );

        $stmt->bind_param("si", $address, $user_id);

        if ($stmt->execute()) {
            $message = "Address updated successfully!";
            $messageType = "success";
        } else {
            $message = "Failed to update address.";
            $messageType = "error";
        }

        $stmt->close();
    }
}

// Get current address
$stmt = $conn->prepare(
    "SELECT name, email, address FROM users WHERE user_id = ?"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: login.html");
    exit;
}

$name = $user["name"] ?? "";
$email = $user["email"] ?? "";
$address = $user["address"] ?? "";

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Update Address | FoodieHub</title>

    <link rel="stylesheet" href="style.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >
</head>

<body>

<div class="location-bar">
    <span>
        <i class="fa-solid fa-location-dot"></i>
        Update Delivery Address
    </span>

    <i class="fa-solid fa-chevron-down"></i>
</div>


<header class="food-header">

    <a href="index.html" class="food-logo">
        FoodieHub
    </a>

    <div class="header-icons">

        <a href="#" title="Wishlist">
            <i class="fa-regular fa-heart"></i>
        </a>

        <a href="#" title="Notifications">
            <i class="fa-regular fa-bell"></i>
        </a>

        <a href="cart.html" title="Cart">
            <i class="fa-solid fa-cart-shopping"></i>
        </a>

        <a href="profile.php" class="header-profile">
            <?php
            echo htmlspecialchars(
                strtoupper(substr($name, 0, 1))
            );
            ?>
        </a>

    </div>

</header>


<main class="profile-wrapper">

    <aside class="profile-sidebar">

        <div class="cover-photo">

            <div class="food-cover"></div>

            <div class="profile-avatar">
                <?php
                echo htmlspecialchars(
                    strtoupper(substr($name, 0, 1))
                );
                ?>
            </div>

        </div>


        <div class="profile-user">

            <h2>
                <?php echo htmlspecialchars($name); ?>
            </h2>

            <p class="profile-email">
                <?php echo htmlspecialchars($email); ?>
            </p>

            <p class="level">
                Level: <span>Bronze</span>
                &nbsp;&nbsp;
                Points: <span>0</span>
            </p>

        </div>


        <div class="profile-stats">

            <div>
                <strong>0</strong>
                <span>Total</span>
            </div>

            <div>
                <strong>0</strong>
                <span>Delivery</span>
            </div>

            <div>
                <strong>0</strong>
                <span>Pick-up</span>
            </div>

            <div>
                <strong>0</strong>
                <span>Points</span>
            </div>

        </div>


        <div class="member-card">

            <div class="crown">
                <i class="fa-solid fa-crown"></i>
            </div>

            <h3>Become a Member</h3>

            <p>
                Enjoy exclusive FoodieHub benefits.
            </p>

            <button class="member-btn" type="button">
                Join Now
            </button>

        </div>

    </aside>


    <section class="profile-content">

        <div class="profile-tabs">

            <a href="profile.php">
                <i class="fa-regular fa-user"></i>
                User Profile
            </a>

            <a href="update_profile.php">
                <i class="fa-solid fa-pen"></i>
                Update Profile
            </a>

            <a href="update_address.php" class="active">
                <i class="fa-solid fa-location-dot"></i>
                Update Address
            </a>

            <a href="change_password.php">
                <i class="fa-solid fa-lock"></i>
                Change Password
            </a>

        </div>


        <div class="user-info-box form-box">

            <h2>
                <i class="fa-solid fa-location-dot"></i>
                Update Delivery Address
            </h2>

            <p class="form-description">
                Add or update your address for food delivery.
            </p>


            <?php if ($message !== ""): ?>

                <div class="<?php echo $messageType; ?>-message">

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </div>

            <?php endif; ?>


            <form method="POST" action="">

                <label for="address">
                    Delivery Address
                </label>

                <textarea
                    id="address"
                    name="address"
                    rows="6"
                    placeholder="Enter your full delivery address..."
                    required
                ><?php echo htmlspecialchars($address); ?></textarea>


                <button type="submit" class="member-btn save-address-btn">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Save Address
                </button>

            </form>


            <div class="form-links">

                <a href="profile.php">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Profile
                </a>

            </div>

        </div>

    </section>

</main>

</body>
</html>