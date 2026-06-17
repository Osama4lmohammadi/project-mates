<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) header('Location: login.php');
$uid = $_SESSION['user_id'];

// Fetch user profile data
$stmt = mysqli_prepare($conn, 'SELECT u.*, p.interest_ai, p.interest_web, p.interest_cyber, p.skill_python, p.skill_react, p.skill_java, p.work_pace, p.work_time, p.bio FROM student u LEFT JOIN user_profile p ON u.id=p.user_id WHERE u.id = ?');
mysqli_stmt_bind_param($stmt, 'i', $uid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Handle form submission to update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $interest_ai = isset($_POST['interest_ai']) ? 1 : 0;
    $interest_web = isset($_POST['interest_web']) ? 1 : 0;
    $interest_cyber = isset($_POST['interest_cyber']) ? 1 : 0;
    $skill_python = isset($_POST['skill_python']) ? 1 : 0;
    $skill_react = isset($_POST['skill_react']) ? 1 : 0;
    $skill_java = isset($_POST['skill_java']) ? 1 : 0;
    $work_pace = $_POST['work_pace'] ?? 'normal';
    $work_time = $_POST['work_time'] ?? 'flexible';
    $bio = trim($_POST['bio'] ?? '');

    $stmt2 = mysqli_prepare($conn, 'REPLACE INTO user_profile (user_id, interest_ai, interest_web, interest_cyber, skill_python, skill_react, skill_java, work_pace, work_time, bio) VALUES (?,?,?,?,?,?,?,?,?,?)');
    mysqli_stmt_bind_param($stmt2, 'iiiiiiiiss', $uid, $interest_ai, $interest_web, $interest_cyber, $skill_python, $skill_react, $skill_java, $work_pace, $work_time, $bio);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Profile - Project Mates</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
include 'header.html';
?>
<main class="container">
  <h3 style="color: black;">Insert your Profile</h3>
  <form method="POST">
    <fieldset>
      <legend>Academic Interests</legend>
      <label><input type="checkbox" name="interest_ai" <?php if(!empty($data['interest_ai'])) echo 'checked'; ?>> Artificial Intelligence</label><br>
      <label><input type="checkbox" name="interest_web" <?php if(!empty($data['interest_web'])) echo 'checked'; ?>> Web Development</label><br>
      <label><input type="checkbox" name="interest_cyber" <?php if(!empty($data['interest_cyber'])) echo 'checked'; ?>> Cybersecurity</label>
    </fieldset>

    <fieldset>
      <legend>Skills / Tools</legend>
      <label><input type="checkbox" name="skill_python" <?php if(!empty($data['skill_python'])) echo 'checked'; ?>> Python</label><br>
      <label><input type="checkbox" name="skill_react" <?php if(!empty($data['skill_react'])) echo 'checked'; ?>> React</label><br>
      <label><input type="checkbox" name="skill_java" <?php if(!empty($data['skill_java'])) echo 'checked'; ?>> Java</label>
    </fieldset>

    <fieldset>
      <legend>Working Habits</legend>
      <label>Work pace:
        <select name="work_pace">
          <option value="slow" <?php if(($data['work_pace'] ?? '')=='slow') echo 'selected'; ?>>Slow</option>
          <option value="normal" <?php if(($data['work_pace'] ?? '')=='normal') echo 'selected'; ?>>Normal</option>
          <option value="fast" <?php if(($data['work_pace'] ?? '')=='fast') echo 'selected'; ?>>Fast</option>
        </select>
      </label><br>
      <label>Preferred time:
        <select name="work_time">
          <option value="morning" <?php if(($data['work_time'] ?? '')=='morning') echo 'selected'; ?>>Morning</option>
          <option value="night" <?php if(($data['work_time'] ?? '')=='night') echo 'selected'; ?>>Night</option>
          <option value="flexible" <?php if(($data['work_time'] ?? '')=='flexible') echo 'selected'; ?>>Flexible</option>
        </select>
      </label>
    </fieldset>

    <label>Short bio / description (optional)</label>
    <textarea name="bio" rows="4"><?php echo htmlspecialchars($data['bio'] ?? ''); ?></textarea>

    <button type="submit">Save Profile</button>
  </form>
</main>
</body>
</html>