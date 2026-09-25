<?php

session_start();

require_once "config/db.php";

/* =========================================================
   CHECK LOGIN
========================================================= */

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$id = (int) $_SESSION["user_id"];


/* =========================================================
   FETCH USER INFORMATION
========================================================= */

$stmt = $conn->prepare("
    SELECT
        name,
        email,
        mobile,
        address,
        role,
        created_at
    FROM users
    WHERE user_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();


/* =========================================================
   USER NOT FOUND
========================================================= */

if (!$user) {
    session_destroy();
    header("Location: login.html");
    exit;
}


/* =========================================================
   USER DATA
========================================================= */

$name = $user["name"] ?? "N/A";
$email = $user["email"] ?? "N/A";
$mobile = $user["mobile"] ?? "";
$address = $user["address"] ?? "";
$role = $user["role"] ?? "customer";
$createdAt = $user["created_at"] ?? "N/A";


/* =========================================================
   FIRST / LAST NAME
========================================================= */

$nameParts = explode(" ", trim($name), 2);

$firstName = $nameParts[0] ?? "N/A";
$lastName = $nameParts[1] ?? "N/A";


/* =========================================================
   AVATAR LETTER
========================================================= */

$avatarLetter = strtoupper(
    substr($firstName, 0, 1)
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Profile | FoodieHub</title>


    <!-- MAIN CSS -->
    <link
        rel="stylesheet"
        href="style.css"
    >


    <!-- FONT AWESOME -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>


<body>


<!-- =========================================================
     LOCATION BAR
========================================================= -->

<div class="location-bar">

    <span class="location-name">
        <i class="fa-solid fa-location-dot"></i>
        Select Location
    </span>

    <span
        class="opening-time"
        id="openingTime"
    >
        <i class="fa-regular fa-clock"></i>

        <span id="restaurantStatus">
            Checking...
        </span>
    </span>

</div>



<!-- =========================================================
     HEADER
========================================================= -->

<header class="food-header">

    <a
        href="index.html"
        class="food-logo"
    >
        FoodieHub
    </a>


    <div class="header-icons">

        <!-- Wishlist -->
        <a
            href="#"
            title="Wishlist"
        >
            <i class="fa-regular fa-heart"></i>
        </a>


        <!-- Notification -->
        <a
            href="#"
            title="Notifications"
        >
            <i class="fa-regular fa-bell"></i>
        </a>


        <!-- Cart -->
        <a
            href="cart.html"
            title="Cart"
        >
            <i class="fa-solid fa-cart-shopping"></i>
        </a>


        <!-- Profile -->
        <div
            class="header-profile"
            title="Profile"
        >
            <?php echo htmlspecialchars($avatarLetter); ?>
        </div>

    </div>

</header>



<!-- =========================================================
     PROFILE MAIN
========================================================= -->

<main class="profile-wrapper">


    <!-- =====================================================
         LEFT SIDEBAR
    ====================================================== -->

    <aside class="profile-sidebar">


        <!-- COVER -->
        <div class="cover-photo">

            <div class="food-cover"></div>


            <button
                class="edit-cover"
                type="button"
            >
                <i class="fa-solid fa-camera"></i>
                Edit
            </button>


            <!-- AVATAR -->
            <div class="profile-avatar">

                <?php echo htmlspecialchars($avatarLetter); ?>

            </div>

        </div>



        <!-- USER BASIC INFO -->

        <div class="profile-user">

            <h2>
                <?php echo htmlspecialchars($name); ?>
            </h2>


            <p class="profile-email">

                <?php echo htmlspecialchars($email); ?>

            </p>


            <p class="level">

                Level:

                <span>
                    Bronze
                </span>

                &nbsp;&nbsp;

                Points:

                <span>
                    0
                </span>

            </p>


            <small class="user-role">

                <?php
                echo htmlspecialchars(
                    ucfirst($role)
                );
                ?>

            </small>

        </div>



        <!-- =================================================
             PROFILE STATS
        ================================================== -->

        <div class="profile-stats">


            <div>

                <strong>
                    0
                </strong>

                <span>
                    Total
                </span>

            </div>


            <div>

                <strong>
                    0
                </strong>

                <span>
                    Delivery
                </span>

            </div>


            <div>

                <strong>
                    0
                </strong>

                <span>
                    Pick-up
                </span>

            </div>


            <div>

                <strong>
                    0
                </strong>

                <span>
                    Points
                </span>

            </div>


        </div>



        <!-- =================================================
             MEMBERSHIP CARD
        ================================================== -->

        <div class="member-card">

            <div class="crown">

                <i class="fa-solid fa-crown"></i>

            </div>


            <h3>
                Become a Member
            </h3>


            <p>
                Enjoy exclusive FoodieHub benefits.
            </p>


            <button
                class="member-btn"
                type="button"
            >
                Join Now
            </button>

        </div>

    </aside>



    <!-- =====================================================
         RIGHT PROFILE CONTENT
    ====================================================== -->

    <section class="profile-content">


        <!-- =================================================
             PROFILE TABS
        ================================================== -->

        <div class="profile-tabs">


            <a
                href="profile.php"
                class="active"
            >

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


            <a href="change_password.php">

                <i class="fa-solid fa-lock"></i>

                Change Password

            </a>


        </div>



        <!-- =================================================
             USER INFORMATION
        ================================================== -->

        <div class="user-info-box">


            <!-- ROW 1 -->

            <div class="info-row">


                <!-- FIRST NAME -->

                <div class="info-item">

                    <i class="fa-regular fa-user"></i>

                    <div>

                        <strong>
                            First Name
                        </strong>

                        <p>

                            <?php
                            echo htmlspecialchars($firstName);
                            ?>

                        </p>

                    </div>

                </div>



                <!-- MOBILE -->

                <div class="info-item">

                    <i class="fa-solid fa-phone"></i>

                    <div>

                        <strong>
                            Mobile
                        </strong>

                        <p>

                            <?php

                            if (!empty($mobile)) {

                                echo htmlspecialchars($mobile);

                            } else {

                                echo "N/A";

                            }

                            ?>

                        </p>

                    </div>

                </div>


            </div>



            <!-- ROW 2 -->

            <div class="info-row">


                <!-- LAST NAME -->

                <div class="info-item">

                    <i class="fa-regular fa-user"></i>

                    <div>

                        <strong>
                            Last Name
                        </strong>

                        <p>

                            <?php
                            echo htmlspecialchars($lastName);
                            ?>

                        </p>

                    </div>

                </div>



                <!-- EMAIL -->

                <div class="info-item">

                    <i class="fa-regular fa-envelope"></i>

                    <div>

                        <strong>
                            Email
                        </strong>

                        <p>

                            <?php
                            echo htmlspecialchars($email);
                            ?>

                        </p>

                    </div>

                </div>


            </div>



            <!-- ROW 3 -->

            <div class="info-row">


                <!-- ACCOUNT TYPE -->

                <div class="info-item">

                    <i class="fa-solid fa-shield-halved"></i>

                    <div>

                        <strong>
                            Account Type
                        </strong>

                        <p>

                            <?php

                            echo htmlspecialchars(
                                ucfirst($role)
                            );

                            ?>

                        </p>

                    </div>

                </div>



                <!-- JOINED DATE -->

                <div class="info-item">

                    <i class="fa-regular fa-calendar"></i>

                    <div>

                        <strong>
                            Joined Date
                        </strong>

                        <p>

                            <?php
                            echo htmlspecialchars($createdAt);
                            ?>

                        </p>

                    </div>

                </div>


            </div>


        </div>



        <!-- =================================================
             SAVED ADDRESS
        ================================================== -->

        <div class="address-section">


            <h3>

                <i class="fa-solid fa-location-dot"></i>

                Saved Address

            </h3>


            <div class="address-box">


                <?php if (!empty(trim($address))): ?>


                    <i class="fa-solid fa-location-dot"></i>


                    <span>

                        <?php

                        echo nl2br(
                            htmlspecialchars($address)
                        );

                        ?>

                    </span>


                <?php else: ?>


                    <i class="fa-regular fa-map"></i>


                    <span class="no-address">

                        Address not added yet!

                    </span>


                <?php endif; ?>


            </div>

        </div>



        <!-- =================================================
             ACTION BUTTONS
        ================================================== -->

        <div class="profile-actions">


            <a href="index.html">

                <i class="fa-solid fa-house"></i>

                Home

            </a>


            <a href="order_history.php">

                <i class="fa-solid fa-bag-shopping"></i>

                My Orders

            </a>


            <a
                href="logout.php"
                class="logout-btn"
            >

                <i class="fa-solid fa-right-from-bracket"></i>

                Logout

            </a>


        </div>


    </section>

</main>



<!-- =========================================================
     OPENING TIME JAVASCRIPT
========================================================= -->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const status =
            document.getElementById(
                "restaurantStatus"
            );

        const openingTime =
            document.getElementById(
                "openingTime"
            );


        function updateOpeningTime() {

            const now = new Date();

            const hour =
                now.getHours();

            const minute =
                now.getMinutes();


            const currentMinutes =
                (hour * 60) + minute;


            /*
                FoodieHub Opening Time
                10:00 AM
            */

            const openMinutes =
                10 * 60;


            /*
                FoodieHub Closing Time
                11:00 PM
            */

            const closeMinutes =
                23 * 60;


            if (
                currentMinutes >= openMinutes &&
                currentMinutes < closeMinutes
            ) {

                status.textContent =
                    "Open Now · 10:00 AM – 11:00 PM";


                openingTime.classList.remove(
                    "closed"
                );

                openingTime.classList.add(
                    "open"
                );


            } else {

                status.textContent =
                    "Closed · Opens at 10:00 AM";


                openingTime.classList.remove(
                    "open"
                );

                openingTime.classList.add(
                    "closed"
                );

            }

        }


        updateOpeningTime();


        /*
            Update status every minute
        */

        setInterval(
            updateOpeningTime,
            60000
        );

    }

);

</script>


</body>

</html>