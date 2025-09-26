<?php
session_start();
require_once 'models/UserModel.php';
$userModel = new UserModel();

// Chỉ cho phép POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Phải gửi POST mới được xóa!");
}

// Kiểm tra CSRF token
if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token không hợp lệ!");
}

// Lấy ID từ POST
$id = $_POST['id'] ?? null;
if ($id) {
    $userModel->deleteUserById($id);
}

// Xóa token sau khi dùng
unset($_SESSION['csrf_token']);

// Chuyển hướng về danh sách user
header('Location: list_users.php');
exit;
