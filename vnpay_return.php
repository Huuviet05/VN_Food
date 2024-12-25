<?php

session_start();

$vnp_HashSecret = "XNBCJFAKAZQSGTARRLGCHVZWCIOIGSHN"; // Chuỗi bí mật VNPay cung cấp

// Lấy dữ liệu từ VNPay trả về
$vnp_SecureHash = $_GET['vnp_SecureHash'];
$inputData = array();
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}
unset($inputData['vnp_SecureHash']);
ksort($inputData);
$hashData = "";
$i = 0;
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashData .= '&' . $key . "=" . $value;
    } else {
        $hashData .= $key . "=" . $value;
        $i = 1;
    }
}

// Lấy ID đơn hàng và user_id từ VNPay URL
$order_id = $_GET['order_id'] ?? null;
$user_id = $_GET['user_id'] ?? null;

if (!$order_id || !$user_id) {
    $_SESSION['message'] = 'Không tìm thấy thông tin đơn hàng!';
    header('Location: checkout.php');
    exit();
}

// Kiểm tra hash dữ liệu
$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
if ($secureHash == $vnp_SecureHash) {
    if ($_GET['vnp_ResponseCode'] == '00') {
        // Thanh toán thành công
        try {
            // Kết nối cơ sở dữ liệu
            include 'components/connect.php';
            session_start();

            // Lấy thông tin người dùng từ session
            if (!isset($_SESSION['user_id'])) {
                header('location:home.php');
                exit();
            }

            $user_id = $_SESSION['user_id'];

            if (!isset($_SESSION['order_data'])) {
                $_SESSION['message'] = 'Không tìm thấy thông tin đơn hàng. Vui lòng thử lại!';
                header('Location: checkout.php');
                exit();
            }

            $order_data = $_SESSION['order_data'];

            // Lấy thông tin đơn hàng từ session
            $user_id = $order_data['user_id'];
            $name = $order_data['name'];
            $number = $order_data['number'];
            $email = $order_data['email'];
            $address = $order_data['address'];
            $total_products = $order_data['total_products'];
            $total_price = $order_data['total_price'];
            $method = $order_data['method'];

            // Lưu thông tin đơn hàng vào cơ sở dữ liệu
            $insert_order = $conn->prepare("INSERT INTO `orders` (userID, name, phoneNumber, email, method, address, total_products, total_price, payment_status, placed_on) VALUES (?,?,?,?,?,?,?,?,?,NOW())");
            $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price, 'chờ giao hàng']);

            // Xóa giỏ hàng
            $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE userID = ?");
            $delete_cart->execute([$user_id]);


            // Đặt thông báo thành công
            $_SESSION['message'] = 'Đơn hàng đã được đặt thành công qua VNPay!';
        } catch (Exception $e) {
            // Đặt thông báo lỗi nếu có lỗi xảy ra
            $_SESSION['message'] = 'Có lỗi xảy ra khi xử lý đơn hàng: ' . $e->getMessage();
        }

        // Chuyển hướng về trang checkout
        header('Location: checkout.php');
        exit();
    } else {
        // Thanh toán thất bại
        $_SESSION['message'] = 'Thanh toán không thành công. Vui lòng thử lại!';
        header('Location: checkout.php');
        exit();
    }
} else {
    // Chữ ký không hợp lệ
    $_SESSION['message'] = 'Chữ ký không hợp lệ. Vui lòng thử lại!';
    header('Location: checkout.php');
    exit();
}