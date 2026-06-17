<?php
require 'config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $department = trim($_POST['department'] ?? '');

    if (!$name) {
        $errors[] = 'Name is required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email required.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (empty($errors)) {
        // Check existing email
        $result = mysqli_query($conn, "SELECT id FROM student WHERE email = '$email'");
        if (mysqli_num_rows($result) > 0) {
            $errors[] = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT); // one-way hashing
            $insertUserQuery = "INSERT INTO student (name, email, password, department) VALUES ('$name', '$email', '$hash', '$department')";
            
            if (mysqli_query($conn, $insertUserQuery)) {
                $userId = mysqli_insert_id($conn);
                // Create empty profile
                $insertProfileQuery = "INSERT INTO user_profile (user_id) VALUES ($userId)";
                mysqli_query($conn, $insertProfileQuery);
                header('Location: login.php');
                exit;
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Signup </title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="card center">
  <h1>Signup</h1>
  <?php if (!empty($errors)): ?>
    <div class="alert"><?php echo implode('<br>', $errors); ?></div>
  <?php endif; ?>
  <form method="POST">
    <label>Name</label>
    <input name="name" required>
    <label>Email</label>
    <input name="email" type="email" required>
    <label>Department</label>
    <input name="department">
    <label>Password</label>
    <input name="password" type="password" required>
    <button type="submit">Register</button>
  </form>
  <p>Already have an account? <a href="login.php">Login</a></p>
</main>
</body>
</html>