<?php

// vnpay_payment.php

$vnp_TmnCode = "CGXZLS0Z"; // Mã website VNPay cung cấp
$vnp_HashSecret = "XNBCJFAKAZQSGTARRLGCHVZWCIOIGSHN"; // Chuỗi bí mật VNPay cung cấp
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html"; // URL thanh toán VNPay (sandbox hoặc production)
$vnp_ReturnUrl = "http://localhost:9001/vn_food_2/vnpay_return.php"; // URL trả về sau khi thanh toán

session_start();

$_SESSION['order_data'] = [
    'user_id' => $user_id,
    'name' => $name,
    'number' => $number,
    'email' => $email,
    'address' => $address,
    'total_products' => $total_products,
    'total_price' => $total_price,
    'method' => $method,
    'payment_status' => 'chờ giao hàng',
];

// Lấy các thông tin từ request
$order_id = uniqid(); // Mã đơn hàng
$order_info = "Thanh toán đơn hàng tại website của bạn";
$order_type = "billpayment";
$amount = $_POST['total_price'] * 100 * 1000; // Số tiền cần thanh toán (VNĐ) nhân 100 (theo yêu cầu của VNPay)
$locale = "vn"; // Ngôn ngữ (vn/en)
$bank_code = ""; // Không bắt buộc
$ip_address = $_SERVER['REMOTE_ADDR']; // Lấy IP của khách hàng

// Tạo mảng dữ liệu gửi tới VNPay
$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $ip_address,
    "vnp_Locale" => $locale,
    "vnp_OrderInfo" => $order_info,
    "vnp_OrderType" => $order_type,
    "vnp_ReturnUrl" => $vnp_ReturnUrl,
    "vnp_TxnRef" => $order_id,
);

if (!empty($bank_code)) {
    $inputData['vnp_BankCode'] = $bank_code;
}

// Sắp xếp dữ liệu theo thứ tự alphabet
ksort($inputData);
$query = "";
$i = 0;
$hashdata = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$vnp_Url = $vnp_Url . "?" . $query;
if (isset($vnp_HashSecret)) {
    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret); // Tạo hash
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
}

header('Location: ' . $vnp_Url); // Chuyển hướng khách hàng tới VNPay
exit();