<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

if (isset($_POST['update_payment'])) {
    $order_id = $_POST['order_id'];
    $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ? AND payment_status = 'chờ giao hàng'");
    $update_status->execute(['đang giao hàng', $order_id]);
    if ($update_status->rowCount() > 0) {
        $message[] = 'Đơn hàng đang được giao!';
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
    $delete_order->execute([$delete_id]);
    header('location:placed_orders.php');
}

// Phân trang
$orders_per_page = 6; // Số đơn hàng trên mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Trang hiện tại
$start_from = ($page - 1) * $orders_per_page; // Vị trí bắt đầu truy vấn

// Truy vấn đơn hàng có trạng thái "chờ giao hàng" cho trang hiện tại
$select_orders = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = 'chờ giao hàng' LIMIT ?, ?");
$select_orders->bindParam(1, $start_from, PDO::PARAM_INT);
$select_orders->bindParam(2, $orders_per_page, PDO::PARAM_INT);
$select_orders->execute();

// Tính tổng số đơn hàng và số trang
$total_orders = $conn->query("SELECT COUNT(*) FROM `orders` WHERE payment_status = 'chờ giao hàng'")->fetchColumn();
$total_pages = ceil($total_orders / $orders_per_page); // Tính tổng số trang
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placed Orders</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- custom css file link -->
    <link rel="stylesheet" href="../css/admin_style.css">

    <style>
    /* CSS cho bảng orders */
    .orders-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 16px;
        text-align: left;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #f8f8f8;
    }

    .orders-table th,
    .orders-table td {
        padding: 14px 20px;
        border: 1px solid #ddd;
        font-family: 'Arial', sans-serif;
    }

    .orders-table th {
        background-color: #ecb901;
        color: white;
        font-weight: bold;
        text-align: center;
        text-transform: uppercase;
    }

    .orders-table td {
        text-align: center;
        font-size: 14px;
        color: #333;
    }

    .orders-table tr:hover {
        background-color: #f1f1f1;
    }

    .orders-table tr:nth-child(even) {
        background-color: #fafafa;
    }

    /* Style cho badge trạng thái */
    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        color: #fff;
        font-weight: bold;
    }

    .badge-success {
        background-color: #28a745;
        /* Xanh lá */
    }

    .badge-warning {
        background-color: #ffc107;
        /* Vàng */
    }

    .badge-danger {
        background-color: #dc3545;
        /* Đỏ */
    }

    /* Cải thiện nút hành động */
    .btn {
        padding: 8px 16px;
        border-radius: 5px;
        color: #fff;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        display: inline-block;
        background-color: #007bff;
    }

    .btn-success {
        background-color: #28a745;
    }

    .btn-primary {
        background-color: #007bff;
    }

    .btn-danger {
        background-color: #dc3545;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    /* Hiệu ứng khi di chuột qua các ô */
    .orders-table td:hover {
        background-color: #f8f8f8;
    }

    /* Chỉnh sửa phân trang */
    .pagination {
        text-align: center;
        margin-top: 20px;
    }

    .pagination a {
        padding: 8px 16px;
        margin: 0 5px;
        text-decoration: none;
        border: 1px solid #ddd;
        color: #ecb901;
        font-size: 14px;
        border-radius: 5px;
    }

    .pagination a:hover {
        background-color: #f1f1f1;
    }

    /* Tạo hiệu ứng chuyển động cho bảng */
    .orders-table {
        animation: fadeIn 1s ease-in-out;
    }

    /* Hiệu ứng fade-in cho bảng */
    @keyframes fadeIn {
        0% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .action-buttons a,
    .action-buttons button {
        transition: transform 0.3s ease-in-out;
    }

    .action-buttons a:hover,
    .action-buttons button:hover {
        transform: scale(1.1);
    }

    /* Header */
    .heading {
        text-align: center;
        margin: 30px 0;
        font-size: 24px;
        color: #333;
        font-family: 'Arial', sans-serif;
        text-transform: uppercase;
    }

    /* Chỉnh sửa phân trang */
    .pagination {
        text-align: center;
        margin-top: 20px;
    }

    .pagination a {
        padding: 8px 16px;
        margin: 0 5px;
        text-decoration: none;
        border: 1px solid #ddd;
        color: #ecb901;
        font-size: 14px;
        border-radius: 5px;
        transition: background-color 0.3s, transform 0.3s;
    }

    .pagination a:hover {
        background-color: #f1f1f1;
        transform: scale(1.1);
    }

    .pagination a.active {
        background-color: #ecb901;
        color: #fff;
        font-weight: bold;
    }
    </style>
</head>

<body>
    <?php include '../components/admin_header.php' ?>

    <h1 class="heading">Đơn đặt hàng</h1>

    <table class="orders-table">
        <thead>
            <tr>
                <th>Ngày đặt hàng</th>
                <th>Tên</th>
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th>Chi tiết đơn hàng</th>
                <th>Giá đơn</th>
                <th>Phương thức thanh toán</th>
                <th>Trạng thái đơn hàng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($select_orders->rowCount() > 0) {
                while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <tr>
                <td><?= $fetch_orders['placed_on']; ?></td>
                <td><?= $fetch_orders['name']; ?></td>
                <td><?= $fetch_orders['phoneNumber']; ?></td>
                <td><?= $fetch_orders['address']; ?></td>
                <td><?= $fetch_orders['total_products']; ?></td>
                <td><?= $fetch_orders['total_price']; ?>k</td>
                <td><?= $fetch_orders['method']; ?></td>
                <td>
                    <span class="badge badge-danger">
                        <?= $fetch_orders['payment_status']; ?>
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <form action="" method="POST">
                            <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                            <button type="submit" class="btn btn-success" name="update_payment"><i
                                    class="fa-solid fa-truck-fast"></i></button>
                        </form>
                        <a href="generate_invoice.php?order_id=<?= $fetch_orders['id']; ?>" class="btn btn-primary"
                            target="_blank"><i class="fa-solid fa-print"></i></a>
                        <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="btn btn-danger"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa đơn này không?');"><i
                                class='bx bx-trash'></i></a>
                    </div>
                </td>
            </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="9" style="text-align: center;">Chưa có đơn hàng nào đang chờ giao hàng!</td></tr>';
            }
            ?>
        </tbody>
    </table>

    <!-- Hiển thị phân trang -->
    <div class="pagination">
        <?php if ($page > 1): ?>
        <a href="placed_orders.php?page=<?= $page - 1; ?>">&laquo; Trước</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="placed_orders.php?page=<?= $i; ?>" class="<?= $i == $page ? 'active' : ''; ?>"><?= $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
        <a href="placed_orders.php?page=<?= $page + 1; ?>">Tiếp &raquo;</a>
        <?php endif; ?>
    </div>

    <!-- custom js file link -->
    <script src="../js/admin_script.js"></script>
</body>

</html>