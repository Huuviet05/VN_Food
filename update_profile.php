<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:home.php');
};

if (isset($_POST['submit'])) {

   $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
   $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
   $number = htmlspecialchars($_POST['number'], ENT_QUOTES, 'UTF-8');
   $old_pass = htmlspecialchars($_POST['old_pass'], ENT_QUOTES, 'UTF-8');
   $new_pass = htmlspecialchars($_POST['new_pass'], ENT_QUOTES, 'UTF-8');
   $confirm_pass = htmlspecialchars($_POST['confirm_pass'], ENT_QUOTES, 'UTF-8');

   // Cập nhật thông tin người dùng
   if (!empty($name)) {
      $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE userID = ?");
      $update_name->execute([$name, $user_id]);
      $message[] = 'Cập nhật tên người dùng thành công';
   }

   if (!empty($email)) {
      $select_email = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND userID = ?");
      $select_email->execute([$email, $user_id]);
      if ($select_email->rowCount() > 0) {
         $message[] = 'Email đã được sử dụng!';
      } else {
         $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE userID = ?");
         $update_email->execute([$email, $user_id]);
         $message[] = 'Cập nhật email thành công';
      }
   }

   if (!empty($number)) {
      $select_number = $conn->prepare("SELECT * FROM `users` WHERE phoneNumber = ? AND userID = ?");
      $select_number->execute([$number, $user_id]);
      if ($select_number->rowCount() > 0) {
         $message[] = 'Số điện thoại đã được sử dụng!';
      } else {
         $update_number = $conn->prepare("UPDATE `users` SET phoneNumber = ? WHERE userID = ?");
         $update_number->execute([$number, $user_id]);
         $message[] = 'Cập nhật số điện thoại thành công';
      }
   }

   // Xử lý cập nhật mật khẩu
   if (!empty($old_pass)) {
      $select_prev_pass = $conn->prepare("SELECT password FROM `users` WHERE userID = ?");
      $select_prev_pass->execute([$user_id]);
      $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);
      $prev_pass = $fetch_prev_pass['password'];

      if (!password_verify($old_pass, $prev_pass)) {
         $message[] = 'Mật khẩu hiện tại không chính xác!';
      } elseif ($new_pass !== $confirm_pass) {
         $message[] = 'Xác nhận mật khẩu không khớp!';
      } elseif (!empty($new_pass)) {
         $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
         $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE userID = ?");
         $update_pass->execute([$hashed_new_pass, $user_id]);
         $message[] = 'Cập nhật mật khẩu thành công!';
      } else {
         $message[] = 'Vui lòng nhập mật khẩu mới!';
      }
   } else {
      $message[] = 'Vui lòng nhập mật khẩu!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>update profile</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <section class="form-container update-form" style="margin-top: 100px;">

        <form action="" method="post">
            <h3>cập nhật hồ sơ</h3>
            <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" class="box" maxlength="50">
            <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" class="box" maxlength="50"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="number" name="number" placeholder="<?= $fetch_profile['phoneNumber']; ?>"" class=" box" min="0"
                max="9999999999" maxlength="10">
            <input type="password" name="old_pass" placeholder="Nhập mật khẩu hiện tại" class="box" maxlength="50"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="new_pass" placeholder="Nhập mật khẩu mới" class="box" maxlength="50"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="confirm_pass" placeholder="Xác nhận mật khẩu mới của bạn" class="box"
                maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="Cập nhật ngay" name="submit" class="btn">
        </form>

    </section>










    <?php include 'components/footer.php'; ?>






    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>