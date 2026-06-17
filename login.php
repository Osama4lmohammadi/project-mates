<?php
require 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $query = "SELECT * FROM student WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $id = $row['id'];
        $hash = $row['password'];
        $name=$row['name'];
        
        if (password_verify($password, $hash)) { // verify password
            $_SESSION['user_id'] = $id;
            $_SESSION['name']=  $name;
            header('Location: profile.php');
            exit;
        } else {
            $error = 'Invalid credentials.';
        }
    } else {
        $error = 'Invalid credentials.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="card center">
  <h1>Login</h1>
  <?php if ($error): ?><div class="alert"><?php echo $error; ?></div><?php endif; ?>
  <form method="POST">
    <label>Email</label>
    <input name="email" type="email" required>
    <label>Password</label>
    <input name="password" type="password" required>
    <button type="submit">Login</button>
  </form>
  <p>Don't have an account? <a href="signup.php">Register</a></p>
</main>
</body>
</html>