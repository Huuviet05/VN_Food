<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php'; // Import thư viện Google API Client

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:home.php');
}

// Xử lý khi người dùng nhấn nút "Đã nhận hàng"
if (isset($_POST['confirm_received'])) {
   $order_id = $_POST['order_id'];

   // Kiểm tra đơn hàng thuộc về khách hàng hiện tại
   $check_order = $conn->prepare("SELECT * FROM `orders` WHERE id = ? AND userID = ?");
   $check_order->execute([$order_id, $user_id]);

   if ($check_order->rowCount() > 0) {
      $order = $check_order->fetch(PDO::FETCH_ASSOC);

      // Cập nhật trạng thái đơn hàng
      $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
      $update_status->execute(['đã giao hàng', $order_id]);

      // Tạo hóa đơn PDF bằng mPDF
      $mpdf = new \Mpdf\Mpdf();
      $html = '
      <div style="font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd;">
          <h1 style="text-align: center; color: #ff7e5f;">VN-Food</h1>
          <p style="text-align: center;">Hóa Đơn Thanh Toán</p>
          <hr>
          <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
              <tr>
                  <td style="padding: 8px; border: 1px solid #ddd;"><strong>ID Khách Hàng</strong></td>
                  <td style="padding: 8px; border: 1px solid #ddd;">' . $order['userID'] . '</td>
              </tr>
              <tr>
                  <td style="padding: 8px; border: 1px solid #ddd;"><strong>Ngày Đặt Hàng</strong></td>
                  <td style="padding: 8px; border: 1px solid #ddd;">' . $order['placed_on'] . '</td>
              </tr>
              <tr>
                  <td style="padding: 8px; border: 1px solid #ddd;"><strong>Tên</strong></td>
                  <td style="padding: 8px; border: 1px solid #ddd;">' . $order['name'] . '</td>
              </tr>
              <tr>
                  <td style="padding: 8px; border: 1px solid #ddd;"><strong>Email</strong></td>
                  <td style="padding: 8px; border: 1px solid #ddd;">' . $order['email'] . '</td>
              </tr>
              <tr>
                  <td style="padding: 8px; border: 1px solid #ddd;"><strong>Số Điện Thoại</strong></td>
                  <td style="padding: 8px; border: 1px solid #ddd;">' . $order['phoneNumber'] . '</td>
              </tr>
              <tr>
                  <td style="padding: 8px; border: 1px solid #ddd;"><strong>Địa Chỉ</strong></td>
                  <td style="padding: 8px; border: 1px solid #ddd;">' . $order['address'] . '</td>
              </tr>
          </table>

          <h3 style="text-align: left;">Chi Tiết Đơn Hàng</h3>
          <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
              <tr>
                  <td style="padding: 8px; border: 1px solid #ddd;"><strong>Tên Sản Phẩm</strong></td>
                  <td style="padding: 8px; border: 1px solid #ddd;">' . $order['total_products'] . '</td>
              </tr>
              <tr>
                  <td style="padding: 8px; border: 1px solid #ddd;"><strong>Tổng Tiền</strong></td>
                  <td style="padding: 8px; border: 1px solid #ddd;">' . $order['total_price'] . 'k</td>
              </tr>
              <tr>
                  <td style="padding: 8px; border: 1px solid #ddd;"><strong>Phương Thức Thanh Toán</strong></td>
                  <td style="padding: 8px; border: 1px solid #ddd;">' . $order['method'] . '</td>
              </tr>
              <tr>
                  <td style="padding: 8px; border: 1px solid #ddd;"><strong>Trạng Thái Thanh Toán</strong></td>
                  <td style="padding: 8px; border: 1px solid #ddd;">' . $order['payment_status'] . '</td>
              </tr>
          </table>
          <p style="text-align: center; font-size: 14px; color: #666;">Cảm ơn bạn đã đặt hàng tại VN-Food!</p>
      </div>
      ';

      $mpdf->WriteHTML($html);
      $pdf_file = 'invoice_' . $order_id . '.pdf';
      $mpdf->Output($pdf_file, 'F'); // Lưu file PDF vào server

      // Gửi email với tệp đính kèm
      $mail = new PHPMailer(true);

      try {
         // Cấu hình SMTP
         $mail->isSMTP();
         $mail->Host = 'smtp.gmail.com';  // Sử dụng Gmail SMTP
         $mail->SMTPAuth = true;
         $mail->Username = 'huuviet19905@gmail.com';  // Địa chỉ email của bạn
         $mail->Password = 'vhabuiyfyxenxqqx';  // Mật khẩu email của bạn
         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
         $mail->Port = 587;

         // Thông tin người gửi và người nhận
         $mail->setFrom('huuviet19905@gmail.com', 'VN-Food');
         $mail->addAddress($order['email'], $order['name']);

         // Cấu hình charset và mã hóa cho subject và body
         $mail->CharSet = 'UTF-8'; // Đặt mã hóa UTF-8

         // Tệp đính kèm
         $mail->addAttachment($pdf_file);

         // Nội dung email
         $mail->isHTML(true);
         $mail->Subject = 'Cảm ơn bạn đã đặt hàng tại VN-Food';
         $mail->Body    = '<p>Xin chào ' . htmlspecialchars($order['name']) . ',</p>
                           <p>Cảm ơn bạn đã đặt hàng tại VN-Food. Vui lòng kiểm tra hóa đơn đính kèm.</p>
                           <p>Trân trọng,<br>VN-Food</p>';

         $mail->send();

         $message[] = 'Kiểm tra hóa đơn ở email của bạn!';
      } catch (Exception $e) {
         $message[] = 'Không thể gửi email. Lỗi: ' . $mail->ErrorInfo;
      }

      // Xóa file PDF sau khi gửi
      if (file_exists($pdf_file)) {
         unlink($pdf_file);
      }
   } else {
      $message[] = 'Đơn hàng không tồn tại hoặc không thuộc về bạn!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>orders</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

    <style>
    .btn {
        padding: 10px 15px;
        background-color: #28a745;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: 0.3s ease;
    }

    .btn:hover {
        background-color: #218838;
    }

    .box {
        border: 1px solid #ddd;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    </style>

</head>

<body>

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <!-- <div class="heading">
        <h3>đơn đặt hàng</h3>
        <p><a href="html.php">trang chủ</a> <span> / đơn đặt hàng</span></p>
    </div> -->

    <section class="orders">

        <h1 class="title" style="margin-top:100px">đơn hàng của bạn</h1>

        <div class="box-container">

            <?php
         if ($user_id == '') {
            echo '<p class="empty">vui lòng đăng nhập để xem đơn hàng của bạn</p>';
         } else {
            $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE userID = ? ORDER BY `id` DESC");

            $select_orders->execute([$user_id]);
            if ($select_orders->rowCount() > 0) {
               while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
         ?>
            <div class="box">
                <p>ngày đặt hàng : <span><?= $fetch_orders['placed_on']; ?></span></p>
                <p>Tên : <span><?= $fetch_orders['name']; ?></span></p>
                <p>Email : <span><?= $fetch_orders['email']; ?></span></p>
                <p>Số điện thoại : <span><?= $fetch_orders['phoneNumber']; ?></span></p>
                <p>Địa chỉ : <span><?= $fetch_orders['address']; ?></span></p>
                <p>phương thức thanh toán : <span><?= $fetch_orders['method']; ?></span></p>
                <p>Chi tiết đơn hàng: <span><?= $fetch_orders['total_products']; ?></span></p>
                <p>Tổng giá đơn : <span><?= $fetch_orders['total_price']; ?>k</span></p>
                <p>Tình trạng đơn hàng :
                    <span style="color:<?php
                                             if ($fetch_orders['payment_status'] == 'chờ giao hàng') {
                                                echo 'orange';
                                             } elseif ($fetch_orders['payment_status'] == 'đang giao hàng') {
                                                echo 'blue';
                                             } else {
                                                echo 'green';
                                             }; ?>">
                        <?= $fetch_orders['payment_status']; ?>
                    </span>
                </p>

                <?php if ($fetch_orders['payment_status'] === 'đang giao hàng') { ?>
                <form action="" method="POST">
                    <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                    <button type="submit" name="confirm_received" class="btn">Đã nhận được hàng</button>
                </form>
                <?php } ?>
            </div>
            <?php
               }
            } else {
               echo '<p class="empty">chưa có đơn hàng nào được đặt!</p>';
            }
         }
         ?>

        </div>

    </section>

    <!-- footer section starts  -->
    <?php include 'components/footer.php'; ?>
    <!-- footer section ends -->

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>