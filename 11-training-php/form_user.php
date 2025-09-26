<?php
// form_user.php
declare(strict_types=1);
session_start();

require_once 'models/UserModel.php';
$userModel = new UserModel();

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ID user (update) nếu có
$_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$user = null;

if ($_id) {
    $user = $userModel->findUserById($_id); // Phải dùng prepared statement trong UserModel
}

// Xử lý POST
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // CSRF check
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = 'Yêu cầu không hợp lệ (CSRF).';
    } else {
        // Validate name
        $name = trim((string)($_POST['name'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($name === '') {
            $errors[] = 'Tên không được để trống.';
        }

        // Nếu không có lỗi
        if (empty($errors)) {
            $data = ['name' => $name];
            
            // Chỉ truyền mật khẩu thô, việc băm sẽ được xử lý trong UserModel
            if ($password !== '') {
                $data['password'] = $password;
            }

            if ($_id) {
                // Thêm ID vào mảng dữ liệu để phương thức updateUser xử lý
                $data['id'] = $_id;
                $userModel->updateUser($data);
            } else {
                $userModel->insertUser($data);
            }

            header('Location: list_users.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>User Form</title>
    <?php include 'views/meta.php'; ?>
    <link rel="stylesheet" href="public/css/bootstrap.min.css">
</head>
<body>
<?php include 'views/header.php'; ?>

<div class="container" style="margin-top:30px; max-width:720px;">
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $err) echo htmlspecialchars($err) . '<br>'; ?>
        </div>
    <?php endif; ?>

    <?php if ($user || !$_id): ?>
        <div class="alert alert-info">User form</div>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <?php if ($_id): ?>
                <input type="hidden" name="id" value="<?php echo (int)$_id; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="name">Name</label>
                <input class="form-control" name="name" placeholder="Name"
                        value="<?php echo htmlspecialchars($user[0]['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password <?php echo $_id ? '(để trống nếu không đổi)' : ''; ?></label>
                <input type="password" name="password" class="form-control" placeholder="Password">
            </div>

            <button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
        </form>
    <?php else: ?>
        <div class="alert alert-warning">User not found!</div>
    <?php endif; ?>
</div>
</body>
</html>
