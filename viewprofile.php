<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) header('Location: login.php');
$uid = $_SESSION['user_id'];

// تأكد من أن معرف المستخدم موجود في الرابط
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$profile_id = intval($_GET['id']);

// Fetch user profile data
$stmt = mysqli_prepare($conn, 'SELECT u.*, p.interest_ai, p.interest_web, p.interest_cyber, p.skill_python, p.skill_react, p.skill_java, p.work_pace, p.work_time, p.bio FROM student u LEFT JOIN user_profile p ON u.id=p.user_id WHERE u.id = ?');
mysqli_stmt_bind_param($stmt, 'i', $profile_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// إذا لم يتم العثور على المستخدم
if (!$data) {
    header('Location: index.php');
    exit;
}

// Handle invitation submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invitation_stmt = mysqli_prepare($conn, 'INSERT INTO invitations (sender_id, receiver_id, status, created_at) VALUES (?, ?, ?, NOW())');
    $status = 'pending'; // حالة الدعوة
    mysqli_stmt_bind_param($invitation_stmt, 'iis', $uid, $profile_id, $status);
    if (mysqli_stmt_execute($invitation_stmt)) {
        echo "<script>alert('Invitation sent successfully!');</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    mysqli_stmt_close($invitation_stmt);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>View Profile - Project Mates</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
include 'header.html';
?>
<main class="container">
  <section class="profile-header">
    <h3><?php echo htmlspecialchars($data['name']); ?></h3>
    <p>Department: <?php echo htmlspecialchars($data['department']); ?></p>
    <p>Email: <?php echo htmlspecialchars($data['email']); ?></p>
    <p>Team Status: <?php echo htmlspecialchars($data['team_status']); ?></p>
  </section>

  <section class="profile-info">
    <h4>About Me</h4>
    <p><?php echo nl2br(htmlspecialchars($data['bio'])); ?></p>
  </section>

  <section class="section">
    <h4>Academic Interests</h4>
    <ul>
      <?php if ($data['interest_ai']) echo '<li>Artificial Intelligence</li>'; ?>
      <?php if ($data['interest_web']) echo '<li>Web Development</li>'; ?>
      <?php if ($data['interest_cyber']) echo '<li>Cybersecurity</li>'; ?>
    </ul>
  </section>

  <section class="section">
    <h4>Skills / Tools</h4>
    <ul>
      <?php if ($data['skill_python']) echo '<li>Python</li>'; ?>
      <?php if ($data['skill_react']) echo '<li>React</li>'; ?>
      <?php if ($data['skill_java']) echo '<li>Java</li>'; ?>
    </ul>
  </section>

  <section class="section">
    <h4>Working Habits</h4>
    <p>Work Pace: <?php echo htmlspecialchars($data['work_pace']); ?></p>
    <p>Preferred Time: <?php echo htmlspecialchars($data['work_time']); ?></p>
  </section>

  <form method="post">
    <input type="hidden" name="to_id" value="<?php echo htmlspecialchars($profile_id); ?>">
    <button type="submit" class="invite-button">Send Invitation</button>
  </form>
</main>
</body>
</html>