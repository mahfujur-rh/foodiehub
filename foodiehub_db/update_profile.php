<?php
session_start();

require_once "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$id = (int) $_SESSION["user_id"];

$message = "";
$error = "";

/* Get current user data */
$stmt = $conn->prepare(
    "SELECT name, email, mobile
     FROM users
     WHERE user_id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

$name = $user["name"] ?? "";
$email = $user["email"] ?? "";
$mobile = $user["mobile"] ?? "";


/* Update profile */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $newName = trim($_POST["name"] ?? "");
    $newMobile = trim($_POST["mobile"] ?? "");

    if ($newName === "") {

        $error = "Name is required.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE users
             SET name = ?, mobile = ?
             WHERE user_id = ?"
        );

        $stmt->bind_param(
            "ssi",
            $newName,
            $newMobile,
            $id
        );

        if ($stmt->execute()) {

            $_SESSION["name"] = $newName;

            $message = "Profile updated successfully!";

            $name = $newName;
            $mobile = $newMobile;

        } else {

            $error = "Failed to update profile.";

        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Update Profile | FoodieHub</title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

<header class="header">

    <div class="container nav">

        <a href="index.html" class="logo">
            <i class="fa-solid fa-utensils"></i>
            Foodie<span>Hub</span>
        </a>

        <nav>

            <a href="index.html">
                Home
            </a>

            <a href="menu.html">
                Menu
            </a>

            <a href="profile.php">
                Profile
            </a>

            <a href="my_orders.php">
                My Orders
            </a>

        </nav>

    </div>

</header>


<main class="form-page">

    <div class="form-box">

        <h2>
            <i class="fa-regular fa-user"></i>
            Update Profile
        </h2>


        <?php if ($message): ?>

            <div class="success-message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <?php if ($error): ?>

            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <label>
                Full Name
            </label>

            <input
                type="text"
                name="name"
                value="<?php echo htmlspecialchars($name); ?>"
                placeholder="Enter your name"
                required
            >


            <label>
                Email
            </label>

            <input
                type="email"
                value="<?php echo htmlspecialchars($email); ?>"
                disabled
            >

            <small class="input-note">
                Email cannot be changed.
            </small>


            <label>
                Mobile Number
            </label>

            <input
                type="text"
                name="mobile"
                value="<?php echo htmlspecialchars($mobile); ?>"
                placeholder="01XXXXXXXXX"
            >


            <button type="submit" class="btn">
                <i class="fa-solid fa-floppy-disk"></i>
                Save Changes
            </button>

        </form>


        <div class="form-links">

            <a href="profile.php">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Profile
            </a>

        </div>

    </div>

</main>


<footer>
    © 2026 FoodieHub | Online Food Ordering System
</footer>

</body>

</html>