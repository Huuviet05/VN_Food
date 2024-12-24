<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_users = $conn->prepare("DELETE FROM `users` WHERE userID = ?");
    $delete_users->execute([$delete_id]);
    $delete_order = $conn->prepare("DELETE FROM `orders` WHERE user_id = ?");
    $delete_order->execute([$delete_id]);
    header('location:users_accounts.php');
}

// Phân trang
$users_per_page = 5; // Số người dùng trên mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Trang hiện tại
$start_from = ($page - 1) * $users_per_page;

// Truy vấn dữ liệu
$select_account = $conn->prepare("SELECT * FROM `users` WHERE role = ? LIMIT ?, ?");
$select_account->bindParam(1, $role, PDO::PARAM_STR);
$role = 'client';
$select_account->bindParam(2, $start_from, PDO::PARAM_INT);
$select_account->bindParam(3, $users_per_page, PDO::PARAM_INT);
$select_account->execute();

// Tính tổng số người dùng
$total_users = $conn->query("SELECT COUNT(*) FROM `users` WHERE role = 'client'")->fetchColumn();
$total_pages = ceil($total_users / $users_per_page); // Tổng số trang

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản người dùng</title>

    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Custom CSS File Link -->
    <link rel="stylesheet" href="../css/admin_style.css">

    <style>
    /* CSS cho bảng tài khoản người dùng */
    .accounts-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 16px;
        text-align: left;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #f8f8f8;
        animation: fadeIn 1s ease-in-out;
    }

    .accounts-table th,
    .accounts-table td {
        padding: 14px 20px;
        border: 1px solid #ddd;
        font-family: 'Arial', sans-serif;
    }

    .accounts-table th {
        background-color: #ecb901;
        color: white;
        font-weight: bold;
        text-align: center;
        text-transform: uppercase;
    }

    .accounts-table td {
        text-align: center;
        font-size: 14px;
        color: #333;
    }

    .accounts-table tr:hover {
        background-color: #f1f1f1;
    }

    .accounts-table tr:nth-child(even) {
        background-color: #fafafa;
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

    /* Cải thiện nút hành động */
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .action-buttons a {
        padding: 8px 16px;
        border-radius: 5px;
        color: #fff;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: bold;
    }

    .action-buttons a.delete-btn {
        background-color: #dc3545;
    }

    .action-buttons a.delete-btn:hover {
        background-color: #c82333;
    }

    .action-buttons a:hover {
        transform: scale(1.1);
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

    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #ddd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    </style>

</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <!-- user accounts section starts -->
    <section class="accounts">
        <h1 class="heading">Tài khoản người dùng</h1>

        <table class="accounts-table">
            <thead>
                <tr>
                    <th>Mã người dùng</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Ảnh đại diện</th>
                    <th style="width: 50px;">Hành động</th>
                </tr>
            </thead>
            <tbody>

                <?php
                if ($select_account->rowCount() > 0) {
                    while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                    <td><?= $fetch_accounts['userID']; ?></td>
                    <td><?= $fetch_accounts['name']; ?></td>
                    <td><?= $fetch_accounts['email']; ?></td>
                    <td><?= $fetch_accounts['phoneNumber']; ?></td>
                    <td><?= $fetch_accounts['address']; ?></td>
                    <td>
                        <img src="../uploaded_img/<?= $fetch_accounts['avatar']; ?>" alt="Avatar" class="user-avatar">
                    </td>
                    <td class="actions">
                        <a href="users_accounts.php?delete=<?= $fetch_accounts['userID']; ?>" class="delete-btn"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này không?');"><i
                                class='bx bx-trash'></i></a>
                    </td>
                </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="6" class="empty">Không có tài khoản nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <!-- Hiển thị phân trang -->
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="users_accounts.php?page=<?= $page - 1; ?>">&laquo; Trước</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="users_accounts.php?page=<?= $i; ?>" class="<?= $i == $page ? 'active' : ''; ?>"><?= $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
            <a href="users_accounts.php?page=<?= $page + 1; ?>">Tiếp &raquo;</a>
            <?php endif; ?>
        </div>

    </section>
    <!-- user accounts section ends -->

    <!-- Custom JS File Link -->
    <script src="../js/admin_script.js"></script>

</body>

</html>