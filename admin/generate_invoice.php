<?php
require '../vendor/autoload.php'; // Đường dẫn tới autoload của Composer
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Lấy dữ liệu đơn hàng từ cơ sở dữ liệu
    $select_order = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
    $select_order->execute([$order_id]);

    if ($select_order->rowCount() > 0) {
        $order = $select_order->fetch(PDO::FETCH_ASSOC);

        // Khởi tạo mPDF
        $mpdf = new \Mpdf\Mpdf();

        // Nội dung hóa đơn được thiết kế
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

        // Viết nội dung vào PDF
        $mpdf->WriteHTML($html);

        // Xuất file PDF
        // Xuất file PDF với tên file được mã hóa theo kiểu Vn-Food-<order_id>.pdf
        // $mpdf->Output('Vn-Food-' . $order_id . '.pdf', 'D');

        $mpdf->Output();
    } else {
        echo "Đơn hàng không tồn tại!";
    }
} else {
    echo "Không có ID đơn hàng!";
}