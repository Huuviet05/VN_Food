<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
};

if (isset($_POST['submit'])) {

   $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
   $pass = htmlspecialchars($_POST['pass'], ENT_QUOTES, 'UTF-8');

   // Kiểm tra xem email có hợp lệ không
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      die("Email không hợp lệ.");
   }


   // Lấy thông tin người dùng từ cơ sở dữ liệu
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if ($select_user->rowCount() > 0) {
      // Kiểm tra role
      if ($row['role'] === 'client') {
         // Kiểm tra mật khẩu
         if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['userID'];
            header('location:home.php');
         } else {
            $message[] = 'Mật khẩu không chính xác!';
         }
      } else {
         $message[] = 'Người dùng không phải là khách hàng!';
      }
   } else {
      $message[] = 'Email không tồn tại!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <section class="form-container" style="margin-top: 100px;">
        <form action="" method="post">
            <h3>Đăng nhập tài khoản</h3>
            <input type="email" name="email" required placeholder="Nhập email của bạn" class="box" maxlength="50"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="pass" required placeholder="Nhập mật khẩu của bạn" class="box" maxlength="50"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="Đăng nhập" name="submit" class="btn">
            <p>Không có tài khoản? <a href="register.php">Đăng ký ngay</a></p>

            <!-- Phân cách với đường kẻ -->
            <div class="separator">
                <span>Hoặc</span>
            </div>

            <!-- Google Sign-In -->
            <div id="google-login-container">
                <div id="g_id_onload"
                    data-client_id="884739451917-memhs7bsdnclddn77vi4mp7npbcu7b5l.apps.googleusercontent.com"
                    data-context="signin" data-ux_mode="popup"
                    data-login_uri="http://localhost:9001/VN_food_2/google-callback.php" data-auto_prompt="false">
                </div>
                <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline"
                    data-size="large"></div>
            </div>
        </form>
    </section>













    <?php include 'components/footer.php'; ?>





    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>