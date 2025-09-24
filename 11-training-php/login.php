<?php
// Start the session
session_start();

require_once 'models/UserModel.php';
$userModel = new UserModel();
require_once 'redis_connection.php'; // Đảm bảo file này tồn tại và kết nối được

if (!empty($_POST['submit'])) {
    $users = [
        'username' => $_POST['username'],
        'password' => $_POST['password']
    ];

    // Gọi phương thức xác thực người dùng một lần duy nhất
    $user = $userModel->auth($users['username'], $users['password']);


    if ($user) {
        // Đăng nhập thành công
        $_SESSION['id'] = $user[0]['id'];
        $_SESSION['message'] = 'Login successful';

        // Lưu thông tin người dùng vào một biến
        $user_info = [
            'id' => $user[0]['id'],
            'username' => $user[0]['name'],
            // Bạn có thể thêm các thông tin khác ở đây
        ];

        // LƯU THÔNG TIN VÀO REDIS
        // Tạo một key duy nhất cho người dùng, ví dụ: 'user:1', 'user:2',...
        $redis_key = 'user:' . $user_info['id'];
        $user_json_for_redis = json_encode($user_info);

        // Lưu chuỗi JSON vào Redis
        $redis->set($redis_key, $user_json_for_redis);

        // Đặt thời gian hết hạn cho key (ví dụ: 1 giờ = 3600 giây)
        $redis->expire($redis_key, 3600);

        // Lưu thông tin người dùng vào một biến cho localStorage
        $user_json_for_ls = json_encode($user_info);

        // SỬ DỤNG JAVASCRIPT ĐỂ LƯU VÀO LOCAL STORAGE VÀ SAU ĐÓ CHUYỂN HƯỚNG
        echo "<script>";
        echo "localStorage.setItem('currentUser', '" . addslashes($user_json_for_ls) . "');";
        echo "window.location.href = 'list_users.php';";
        echo "</script>";
        exit();
    } else {
        // Đăng nhập thất bại
        $_SESSION['message'] = 'Login failed';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User form</title>
    <?php include 'views/meta.php' ?>
</head>
<body>
<?php include 'views/header.php'?>

<div class="container">
    <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info" >
            <div class="panel-heading">
                <div class="panel-title">Login</div>
                <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Forgot password?</a></div>
            </div>

            <div style="padding-top:30px" class="panel-body" >
                <form method="post" class="form-horizontal" role="form">

                    <div class="margin-bottom-25 input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="login-username" type="text" class="form-control" name="username" value="" placeholder="username or email">
                    </div>

                    <div class="margin-bottom-25 input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
                    </div>

                    <div class="margin-bottom-25">
                        <input type="checkbox" tabindex="3" class="" name="remember" id="remember">
                        <label for="remember"> Remember Me</label>
                    </div>

                    <div class="margin-bottom-25 input-group">
                        <div class="col-sm-12 controls">
                            <button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
                            <a id="btn-fblogin" href="#" class="btn btn-primary">Login with Facebook</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12 control">
                            Don't have an account!
                            <a href="form_user.php">
                                Sign Up Here
                            </a>
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
