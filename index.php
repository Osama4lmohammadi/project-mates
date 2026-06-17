<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) header('Location: login.php');

$stmt = mysqli_prepare($conn, 'SELECT id,name,department,team_status FROM student WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id, $name, $department, $team_status);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard - Project Mates</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
include 'header.html';
?>
<main class="container">
  <section class="card">
    <h3 style="color: black;">Welcome, <?php echo htmlspecialchars($name); ?></h3>
    <p>Department: <?php echo htmlspecialchars($department); ?></p>
    <p>Team status: <?php echo htmlspecialchars($team_status); ?></p>
    <p>Use the "Find Teammates" page to get recommendations based on your questionnaire.</p>
  </section>
</main>
</body>
</html>
