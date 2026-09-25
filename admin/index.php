<?php

require_once __DIR__ . "/auth.php";

start_admin_session();

if (!empty($_SESSION['admin_authenticated'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (admin_login($email, $password)) {
        header("Location: dashboard.php");
        exit();
    }

    $error = "Invalid email or password.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-login-page">
    <main class="admin-login-card">
        <div class="admin-login-brand">
            <img
                src="../img/Central_Philippines_State_University_Official_Logo.png"
                alt="Central Philippines State University logo"
            >
            <div>
                <p>Central Philippines State University</p>
                <h1>Admin Login</h1>
            </div>
        </div>

        <?php if ($error !== ''): ?>
            <div class="admin-login-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <label for="admin-email">Email</label>
            <input type="email" id="admin-email" name="email" required autofocus>

            <label for="admin-password">Password</label>
            <input type="password" id="admin-password" name="password" required>

            <button type="submit">Log In</button>
        </form>
    </main>
</body>
</html>
