<?php
// login.php

session_start();
include 'db.php';

// error msg
$error_message = '';
$success_message = '';

// login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $login_field = $_POST['login_field'];
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);

    // find user with name or mail
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $login_field, $login_field);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // pass ath
        if (password_verify($password, $user['password_hash'])) {
            
            // log in success, save pass
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];
// --- LƯU QUYỀN VÀO SESSION ---
            $_SESSION['user_role'] = $user['role']; 

            // cookie remember me
            if ($remember_me) {
                setcookie('remember_login', $login_field, time() + (86400 * 30), "/");
            } else {
                setcookie('remember_login', '', time() - 3600, "/");
            }

            // direct to dashboard
            header('Location: dashboard.php');
            exit;
        }
    }

    // log in fail
    $error_message = 'Invalid username/email or password!';
    
    $stmt->close();
    $conn->close();
}

// load page (get)

// check cookie
$remembered_login = $_COOKIE['remember_login'] ?? '';

// check register status
if (isset($_GET['success']) && $_GET['success'] == 'registered') {
    $success_message = 'Registration successful! Please log in.';
}

// html
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style_simple.css">
</head>
<body>
    <div class="form-box">
        <h2>Login</h2>
        
        <?php
        // Hiển thị thông báo (nếu có)
        if (!empty($error_message)) {
            echo '<p class="error">' . $error_message . '</p>';
        }
        if (!empty($success_message)) {
            echo '<p class="success">' . $success_message . '</p>';
        }
        ?>

        <form action="" method="POST">
            <div class="input-group">
                <label for="login_field">Username or Email:</label>
                <input type="text" id="login_field" name="login_field" value="<?php echo htmlspecialchars($remembered_login); ?>" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-options">
                <input type="checkbox" id="remember_me" name="remember_me">
                <label for="remember_me">Remember me</label>
            </div>

            <button type="submit" class="btn">Login</button>
            <div class="form-link">
                <a href="register.php">Need an account? Register</a>
            </div>
        </form>
    </div>
</body>
</html>
