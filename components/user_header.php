<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
      <div class="message">
         <span>' . $msg . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
    }
}

?>

<head>
    <!-- Thêm Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
    .header {
        font-family: 'Poppins', sans-serif;
        /* Sử dụng font Poppins */
        background: linear-gradient(90deg, #ff7e5f, #feb47b);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 10px 5%;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 999;
    }

    .header .logo {
        font-size: 34px;
        /* Tăng kích thước chữ */
        font-weight: 700;
        /* Đậm hơn để nổi bật */
        color: white;
        text-transform: uppercase;
        text-decoration: none;
        letter-spacing: 1px;
        /* Khoảng cách giữa các ký tự */
        transition: all 0.3s ease;
    }

    .header .logo:hover {
        color: #ffe6d8;
    }

    .navbar a {
        font-size: 16px;
        font-weight: 400;
        color: white;
        margin: 0 12px;
        text-transform: capitalize;
        text-decoration: none;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }

    .navbar a::before {
        content: "";
        position: absolute;
        bottom: -3px;
        left: 50%;
        width: 0;
        height: 3px;
        background: linear-gradient(90deg, #ffe6d8, #ffff);
        transition: 0.4s ease;
        transform: translateX(-50%);
    }

    .navbar a:hover::before {
        width: 100%;
    }

    .navbar a:hover {
        color: #ffe6d8;
    }

    /* Active link styling */
    .navbar a.active {
        color: #ffff !important;
        /* Đảm bảo màu trắng được áp dụng */
    }

    .navbar a.active::before {
        width: 100%;
        /* Show underline for active link */
    }


    .icons a {
        color: white;
        font-size: 20px;
        /* Điều chỉnh kích thước icon */
        margin: 0 10px;
        position: relative;
        transition: transform 0.3s ease;
    }

    .icons a:hover {
        transform: scale(1.2);
    }

    #user-btn img {
        border: 2px solid white;
        border-radius: 50%;
        transition: transform 0.3s ease;
    }

    #user-btn img:hover {
        transform: scale(1.1);
    }

    #menu-btn {
        color: white;
        font-size: 24px;
        /* Tăng kích thước icon menu */
        cursor: pointer;
    }
    </style>

</head>
<header class="header">

    <section class="flex">

        <a href="home.php" class="logo">VN-Food</a>

        <nav class="navbar">
            <a href="home.php">Trang chủ</a>
            <a href="about.php">Giới thiệu</a>
            <a href="menu.php">Thực đơn</a>
            <a href="orders.php">Đơn hàng</a>
            <a href="contact.php">Liên hệ</a>
        </nav>


        <div class="icons">
            <?php
            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE userID = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_items = $count_cart_items->rowCount();
            ?>
            <a href="search.php"><i class="fas fa-search"></i></a>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $total_cart_items; ?>)</span></a>

            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE userID = ?");
            $select_profile->execute([$user_id]);

            $avatar = "uploaded_img/user-icon.png"; // Mặc định avatar
            if ($select_profile->rowCount() > 0) {
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC); // Lấy dữ liệu
                $avatar = "uploaded_img/{$fetch_profile['avatar']}"; // Sử dụng avatar của người dùng
            }
            ?>

            <div id="user-btn" style="display: inline-block;"><img src="<?= $avatar; ?>" alt="Avatar"
                    style="width: 27px; height: 27px; border-radius: 50%; object-fit: cover;">
            </div>

            <div id="menu-btn" class="fas fa-bars" style="color: black;"></div>
        </div>

        <div class="profile">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE userID = ?");
            $select_profile->execute([$user_id]);
            if ($select_profile->rowCount() > 0) {
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <p class="name"><?= $fetch_profile['name']; ?></p>
            <div class="flex">
                <a href="profile.php" class="btn">Hồ sơ</a>
                <a href="components/user_logout.php" onclick="return confirm(' đăng xuất khỏi trang web này?');"
                    class="delete-btn">Đăng xuất</a>
            </div>
            <p class="account">
                <a href="login.php">Đăng nhập</a> or
                <a href="register.php">đăng ký</a>
            </p>
            <?php
            } else {
            ?>
            <p class="name">vui lòng đăng nhập trước!</p>
            <a href="login.php" class="btn">Đăng nhập</a>
            <?php
            }
            ?>
        </div>

    </section>

</header>

<script>
// Lấy URL đầy đủ hiện tại
const currentUrl = window.location.href;

// Lấy tất cả các link trong navbar
const navLinks = document.querySelectorAll('.navbar a');

// Lặp qua các link và kiểm tra URL của từng link
navLinks.forEach((link) => {
    // So sánh phần cuối của URL hiện tại với href của link
    if (currentUrl.includes(link.getAttribute('href'))) {
        link.classList.add('active'); // Thêm class "active" nếu URL khớp
    }
});
</script>