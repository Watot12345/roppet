<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Please enter your username and password.';
    } else {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = :user LIMIT 1");
            $stmt->execute([':user' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_name'] = $user['full_name'];
                $_SESSION['admin_role'] = $user['role'];

                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (Exception $e) {
            $error = 'Database connection error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Login - Roppets Edutainment Production</title>
  
  <link rel="icon" type="image/png" href="../assets/images/logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/admin.css">
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background: linear-gradient(135deg, #1b1622 0%, #2b1736 100%);
    }
    .login-box {
      background: #ffffff;
      border-radius: 16px;
      padding: 40px;
      max-width: 420px;
      width: 100%;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
      text-align: center;
    }
    .login-box img {
      height: 60px;
      margin: 0 auto 20px auto;
    }
    .login-form {
      text-align: left;
      margin-top: 25px;
    }
    .login-form .form-group {
      margin-bottom: 18px;
    }
    .login-form label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #334155;
      margin-bottom: 6px;
    }
    .login-form input {
      width: 100%;
      padding: 12px 14px;
      border-radius: 8px;
      border: 1px solid #cbd5e1;
      font-family: inherit;
      font-size: 14px;
    }
    .login-form input:focus {
      outline: none;
      border-color: #852e70;
      box-shadow: 0 0 0 3px rgba(133, 46, 112, 0.15);
    }
    .login-btn {
      width: 100%;
      padding: 12px;
      background: linear-gradient(135deg, #852e70 0%, #b83f8e 100%);
      color: #fff;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 15px;
      cursor: pointer;
      margin-top: 10px;
      transition: all 0.2s;
    }
    .login-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(133, 46, 112, 0.3);
    }
    .error-alert {
      background: #fee2e2;
      border: 1px solid #fca5a5;
      color: #991b1b;
      padding: 10px 14px;
      border-radius: 8px;
      font-size: 13px;
      margin-top: 15px;
      text-align: left;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <img src="../assets/images/logo.png" alt="Roppets Logo">
    <h3 style="font-size: 22px; color: #1e293b; margin-bottom: 4px;">Staff Production Portal</h3>
    <p style="color: #64748b; font-size: 13px;">Sign in with your staff credentials to manage events.</p>

    <?php if (!empty($error)): ?>
      <div class="error-alert">
        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php" class="login-form">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="e.g. admin" required autofocus>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>

      <button type="submit" class="login-btn">
        <i class="fa-solid fa-lock" style="margin-right: 6px;"></i> Sign In
      </button>
    </form>

    <div style="margin-top: 24px; font-size: 12.5px; color: #94a3b8;">
      Default login: <code>admin</code> / <code>roppets2026!</code>
    </div>
  </div>
</body>
</html>
