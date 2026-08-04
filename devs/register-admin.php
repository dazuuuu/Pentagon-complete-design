<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Models\Admin;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $message = 'All fields are required.';
    } else {
        $adminModel = new Admin();

        if ($adminModel->findByEmail($email)) {
            $message = 'An admin with that email already exists.';
        } else {
            $adminModel->create($name, $email, password_hash($password, PASSWORD_DEFAULT));
            $message = 'Admin registered successfully.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register Admin</title>
</head>
<body>
<h3>Register Admin</h3>
<?php if ($message): ?>
  <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>
<form method="POST">
  <label>Name</label><br>
  <input type="text" name="name" required><br><br>
  <label>Email</label><br>
  <input type="email" name="email" required><br><br>
  <label>Password</label><br>
  <input type="password" name="password" required><br><br>
  <button type="submit">Register</button>
</form>
</body>
</html>
