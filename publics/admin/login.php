<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;

Auth::startSession();

if (Auth::check()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminModel = new App\Models\Admin();
    $admin = $adminModel->findByEmail(trim($_POST['email'] ?? ''));

    if ($admin && password_verify($_POST['password'] ?? '', $admin['password_hash'])) {
        Auth::login((int) $admin['id'], $admin['name']);
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid email or password.';
}

$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | Pentagon Quest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
            <h3 class="mb-3">Admin Login</h3>
            <?php if ($error): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-success w-100">Sign In</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
