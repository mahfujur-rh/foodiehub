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

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $currentPassword = $_POST["current_password"] ?? "";
    $newPassword = $_POST["new_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($currentPassword === "" || $newPassword === "" || $confirmPassword === "") {

        $message = "Please fill in all fields.";
        $messageType = "error";

    } elseif (strlen($newPassword) < 6) {

        $message = "New password must be at least 6 characters.";
        $messageType = "error";

    } elseif ($newPassword !== $confirmPassword) {

        $message = "New password and confirm password do not match.";
        $messageType = "error";

    } else {

        // Get current password
        $stmt = $conn->prepare(
            "SELECT password FROM users WHERE user_id = ?"
        );

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();

        if (!$user) {

            $message = "User account not found.";
            $messageType = "error";

        } elseif (!password_verify($currentPassword, $user["password"])) {

            $message = "Current password is incorrect.";
            $messageType = "error";

        } else {

            // Hash new password
            $hashedPassword = password_hash(
                $newPassword,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "UPDATE users SET password = ? WHERE user_id = ?"
            );

            $stmt->bind_param(
                "si",
                $hashedPassword,
                $user_id
            );

            if ($stmt->execute()) {

                $message = "Password changed successfully!";
                $messageType = "success";

            } else {

                $message = "Failed to change password.";
                $messageType = "error";
            }

            $stmt->close();
        }
    }
}

// User information
$stmt = $conn->prepare(
    "SELECT name, email FROM users WHERE user_id = ?"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

$name = $user["name"] ?? "User";
$email = $user["email"] ?? "";

$avatarLetter = strtoupper(
    substr($name, 0, 1)
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Change Password | FoodieHub</title>

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
        Change Password
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
            <?php echo htmlspecialchars($avatarLetter); ?>
        </a>

    </div>

</header>


<main class="profile-wrapper">

    <aside class="profile-sidebar">

        <div class="cover-photo">

            <div class="food-cover"></div>

            <div class="profile-avatar">
                <?php echo htmlspecialchars($avatarLetter); ?>
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

            <a href="update_address.php">
                <i class="fa-solid fa-location-dot"></i>
                Update Address
            </a>

            <a href="change_password.php" class="active">
                <i class="fa-solid fa-lock"></i>
                Change Password
            </a>

        </div>


        <div class="user-info-box form-box">

            <h2>
                <i class="fa-solid fa-lock"></i>
                Change Password
            </h2>

            <p class="form-description">
                Update your password to keep your account secure.
            </p>


            <?php if ($message !== ""): ?>

                <div class="<?php echo $messageType; ?>-message">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <form method="POST" action="">

                <label for="current_password">
                    Current Password
                </label>

                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    placeholder="Enter current password"
                    required
                >


                <label for="new_password">
                    New Password
                </label>

                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    placeholder="Enter new password"
                    minlength="6"
                    required
                >


                <label for="confirm_password">
                    Confirm New Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm new password"
                    minlength="6"
                    required
                >


                <button
                    type="submit"
                    class="member-btn save-address-btn"
                >
                    <i class="fa-solid fa-key"></i>
                    Change Password
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