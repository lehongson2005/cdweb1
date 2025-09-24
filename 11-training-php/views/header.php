<?php
// Đảm bảo session đã được start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lấy id từ session (user đã đăng nhập)
$id = '';
if (!empty($_SESSION['id'])) {
    $id = (int)$_SESSION['id']; // ép kiểu int để an toàn
}

// Lấy keyword từ GET (search)
$keyword = '';
if (!empty($_GET['keyword'])) {
    // Escape trước khi hiển thị (chống XSS)
    $keyword = htmlspecialchars($_GET['keyword'], ENT_QUOTES, 'UTF-8');
}

// Lấy message từ session (nếu có)
$message = '';
if (!empty($_SESSION['message'])) {
    $message = htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8');
    unset($_SESSION['message']); // hiển thị 1 lần rồi xóa
}
?>
<div class="container">
    <nav class="navbar navbar-icon-top navbar-default">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" 
                    data-toggle="collapse" 
                    data-target="#bs-example-navbar-collapse-1" 
                    aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="list_users.php">App Web 1</a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="form_user.php">Add new user</a></li>
            </ul>

            <form class="navbar-form navbar-left" method="get" action="list_users.php">
                <div class="form-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Search users"
                           value="<?php echo $keyword; ?>">
                </div>
                <button type="submit" class="btn btn-default">Search</button>
            </form>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" 
                       role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user-circle-o"></i>
                        Account <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (!empty($id)) { ?>
                            <li><a href="view_user.php?id=<?php echo $id; ?>">Profile</a></li>
                            <li role="separator" class="divider"></li>
                        <?php } ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <?php if (!empty($message)) { ?>
        <div class="alert alert-warning" role="alert">
            <?php echo $message; ?>
        </div>
    <?php } ?>
</div>
