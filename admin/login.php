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
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Poppins', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
      background: linear-gradient(135deg, #1b1622 0%, #2b1736 100%);
    }
    .login-box {
      background: #ffffff;
      border-radius: 16px;
      padding: 40px 36px;
      max-width: 420px;
      width: 100%;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
      text-align: center;
    }
    .login-logo {
      height: 58px;
      max-width: 100%;
      object-fit: contain;
      display: block;
      margin: 0 auto 20px auto;
      filter: none;
    }
    .login-form {
      text-align: left;
      margin-top: 24px;
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
    .input-wrap {
      position: relative;
    }
    .login-form input {
      width: 100%;
      padding: 12px 14px;
      border-radius: 8px;
      border: 1px solid #cbd5e1;
      font-family: inherit;
      font-size: 14px;
      color: #1e293b;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .login-form input[type="password"],
    .login-form input.has-toggle {
      padding-right: 42px;
    }
    .login-form input:focus {
      outline: none;
      border-color: #852e70;
      box-shadow: 0 0 0 3px rgba(133, 46, 112, 0.15);
    }
    .toggle-pw {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #64748b;
      cursor: pointer;
      padding: 4px;
      font-size: 15px;
    }
    .toggle-pw:hover {
      color: #852e70;
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
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .login-btn:hover:not(:disabled) {
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(133, 46, 112, 0.3);
    }
    .login-btn:disabled {
      opacity: 0.75;
      cursor: wait;
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
    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-top: 22px;
      font-size: 13px;
      color: #64748b;
      text-decoration: none;
      transition: color 0.2s;
    }
    .back-link:hover {
      color: #852e70;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <img src="../assets/images/logo.png" alt="Roppets Logo" class="login-logo">
    <h3 style="font-size: 22px; color: #1e293b; margin-bottom: 4px; font-weight: 700;">Staff Production Portal</h3>
    <p style="color: #64748b; font-size: 13px;">Sign in with your staff credentials to manage events.</p>

    <?php if (!empty($error)): ?>
      <div class="error-alert">
        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php" class="login-form" id="loginForm">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" placeholder="Enter your username" autocomplete="username" required autofocus>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrap">
          <input type="password" id="password" name="password" class="has-toggle" placeholder="••••••••" autocomplete="current-password" required>
          <button type="button" class="toggle-pw" id="togglePw" aria-label="Toggle password visibility">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="login-btn" id="loginBtn">
        <i class="fa-solid fa-lock"></i> <span>Sign In</span>
      </button>
    </form>

    <a href="../index.php" class="back-link">
      <i class="fa-solid fa-arrow-left"></i> Back to Public Website
    </a>
  </div>

  <script>
    (function() {
      var pwInput = document.getElementById('password');
      var toggleBtn = document.getElementById('togglePw');
      if (toggleBtn && pwInput) {
        toggleBtn.addEventListener('click', function() {
          var isPw = pwInput.type === 'password';
          pwInput.type = isPw ? 'text' : 'password';
          toggleBtn.innerHTML = isPw ? '<i class="fa-regular fa-eye-slash"></i>' : '<i class="fa-regular fa-eye"></i>';
        });
      }
      var form = document.getElementById('loginForm');
      var btn = document.getElementById('loginBtn');
      if (form && btn) {
        form.addEventListener('submit', function() {
          btn.disabled = true;
          btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> <span>Signing In...</span>';
        });
      }
    })();
  </script>
</body>
</html>
