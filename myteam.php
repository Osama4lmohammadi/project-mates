<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) header('Location: login.php');
$uid = $_SESSION['user_id'];

// Fetch accepted invitations involving this user
$sql = "SELECT 
          CASE WHEN i.sender_id = $uid THEN i.receiver_id ELSE i.sender_id END AS teammate_id
        FROM invitations i
        WHERE (i.sender_id = $uid OR i.receiver_id = $uid) AND i.status = 'accepted'";
$res = mysqli_query($conn, $sql);

$team_ids = [];
while ($row = mysqli_fetch_assoc($res)) {
  $team_ids[] = $row['teammate_id'];
}

$teammates = [];
if (!empty($team_ids)) {
  $ids = implode(',', array_map('intval', $team_ids));
  $result = mysqli_query($conn, "SELECT id, name, email, department FROM student WHERE id IN ($ids)");
  while ($r = mysqli_fetch_assoc($result)) {
    $teammates[] = $r;
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Team - Project Mates</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
include 'header.html';
?>
<main class="container">
  <section class="card">
    <h3 style="color: black;">My Team Members</h3>
    <?php if (empty($teammates)): ?>
      <p>You currently don't have any accepted teammates yet.</p>
    <?php else: ?>
      <div class="grid">
        <?php foreach ($teammates as $t): ?>
          <div class="user-card">
            <h4><?=htmlspecialchars($t['name'])?></h4>
            <p>Email: <?=htmlspecialchars($t['email'])?></p>
            <p>Department: <?=htmlspecialchars($t['department'])?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>
</body>
</html>