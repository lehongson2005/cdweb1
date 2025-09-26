<?php
session_start();

require_once 'models/UserModel.php';
require_once 'redis_connection.php'; // Kết nối Redis

$userModel = new UserModel();

// Tạo CSRF token nếu chưa có
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Biến hiển thị thông báo
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Kiểm tra CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Yêu cầu không hợp lệ.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $message = 'Vui lòng nhập username và password.';
        } else {
            // Gọi auth an toàn
            $user = $userModel->auth($username, $password);

            if ($user) {
                // Login thành công
                session_regenerate_id(true); // chống session fixation
                $_SESSION['id'] = $user[0]['id'];
                $_SESSION['message'] = 'Login successful';

                // Lưu thông tin không nhạy cảm vào Redis
                $user_info = [
                    'id' => $user[0]['id'],
                    'username' => $user[0]['name'],
                ];
                $redis_key = 'user:' . $user_info['id'];
                $redis->set($redis_key, json_encode($user_info, JSON_UNESCAPED_UNICODE));
                $redis->expire($redis_key, 3600);

                // Lưu vào localStorage an toàn
                $user_json = json_encode($user_info, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
                echo "<script>
                        localStorage.setItem('currentUser', JSON.stringify($user_json));
                        window.location.href = 'list_users.php';
                      </script>";
                exit();
            } else {
                $message = 'Login failed';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
    <?php include 'views/meta.php' ?>
</head>
<body>
<?php include 'views/header.php' ?>

<div class="container">
    <?php if ($message): ?>
        <div class="alert alert-warning"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="panel-title">Login</div>
                <div style="float:right; font-size: 80%; position: relative; top:-10px">
                    <a href="#">Forgot password?</a>
                </div>
            </div>
            <div style="padding-top:30px" class="panel-body">
                <form method="post" class="form-horizontal">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <div class="margin-bottom-25 input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="login-username" type="text" class="form-control" name="username" placeholder="username or email">
                    </div>
                    <div class="margin-bottom-25 input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
                    </div>
                    <div class="margin-bottom-25">
                        <input type="checkbox" name="remember" id="remember"><label for="remember"> Remember Me</label>
                    </div>
                    <div class="margin-bottom-25 input-group">
                        <div class="col-sm-12 controls">
                            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                            <a id="btn-fblogin" href="#" class="btn btn-primary">Login with Facebook</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 control">
                            Don't have an account! <a href="form_user.php">Sign Up Here</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="public/js/jquery-2.1.4.min.js"></script>
<script src="public/js/bootstrap.min-3.3.7.js"></script>
<script src="public/js/app.js"></script>
</body>
</html>
