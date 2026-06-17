
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    margin: auto;
    overflow: hidden;
}

.topbar {
    background: #4CAF50;
    color: #fff;
    padding: 10px 0;
}

.topbar h2 {
    margin: 0;
    display: inline;
}

.topbar nav {
    float: right;
}

.topbar nav a {
    color: #fff;
    text-decoration: none;
    padding: 0 15px;
}

.dashboard {
    background: #fff;
    padding: 20px;
    margin-top: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.invitation-cards {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.invitation-card {
    background: #e9ecef;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
}

.invitation-card h4 {
    margin: 0 0 10px;
}

.accept-button, .reject-button {
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-right: 10px;
}

.accept-button {
    background: green;
    color: white;
}

.reject-button {
    background: red;
    color: white;
}

.accept-button:hover {
    background: darkgreen;
}

.reject-button:hover {
    background: darkred;
}
  </style>
<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) header('Location: login.php');
$uid = $_SESSION['user_id'];

// Fetch pending invitations where current user is the receiver
$sql = "SELECT i.id, u.name AS sender_name, u.department, i.status 
        FROM invitations i 
        JOIN student u ON u.id = i.sender_id 
        WHERE i.receiver_id = ? AND i.status = 'pending'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $uid);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$requests = mysqli_fetch_all($res, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Invitations </title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php
include 'header.html';
?>
<main class="container">
  <section class="dashboard">
    <h3>Incoming Invitations</h3>
    <?php if (empty($requests)): ?>
      <p>No new invitations.</p>
    <?php else: ?>
      <div class="invitation-cards">
        <?php foreach ($requests as $r): ?>
          <div class="invitation-card">
            <h4><?= htmlspecialchars($r['sender_name']) ?></h4>
            <p>Department: <?= htmlspecialchars($r['department']) ?></p>
            <form method="post" action="invite_action.php">
              <input type="hidden" name="invite_id" value="<?= $r['id'] ?>">
              <button name="action" value="accept" class="accept-button">Accept</button>
              <button name="action" value="reject" class="reject-button">Reject</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>
</body>
</html>