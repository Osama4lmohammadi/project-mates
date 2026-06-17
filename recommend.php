<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) header('Location: login.php'); 
$uid = $_SESSION['user_id'];

// Functions for encoding work time and pace
function encodeWorkTime($v) {
    return $v === 'morning' ? 1 : ($v === 'night' ? 2 : 3);
}

function encodeWorkPace($v) {
    return $v === 'slow' ? 1 : ($v === 'normal' ? 2 : 3);
}

// Function to get user vector
function getUserVector($row) {
    return [
        intval($row['interest_ai']),
        intval($row['interest_web']),
        intval($row['interest_cyber']),
        intval($row['skill_python']),
        intval($row['skill_react']),
        intval($row['skill_java']),
        encodeWorkTime($row['work_time'] ?? 'flexible'),
        encodeWorkPace($row['work_pace'] ?? 'normal')
    ];
}

// Similarity calculation functions
function cosineSimilarity($a, $b) {
    $dot = 0; $m1 = 0; $m2 = 0;
    foreach ($a as $i => $value) {
        $dot += $value * $b[$i];
        $m1 += $value * $value;
        $m2 += $b[$i] * $b[$i];
    }
    return ($m1 == 0 || $m2 == 0) ? 0 : $dot / (sqrt($m1) * sqrt($m2));
}

function euclideanDistance($a, $b) {
    $s = 0;
    foreach ($a as $i => $value) {
        $s += pow($value - $b[$i], 2);
    }
    return sqrt($s);
}

function hybridScore($a, $b) {
    $cos = cosineSimilarity($a, $b);
    $euc = euclideanDistance($a, $b);
    $normE = 1 / (1 + $euc);
    return ($cos * 0.6) + ($normE * 0.4);
}

// Get current user vector
$meQuery = "SELECT u.*, p.* FROM student u LEFT JOIN user_profile p ON u.id=p.user_id WHERE u.id = $uid";
$meResult = mysqli_query($conn, $meQuery);
$me = mysqli_fetch_assoc($meResult);
$myVec = getUserVector($me);

// Handle filter options
$skillFilter = $_POST['skill'] ?? 'Any Skill';
$interestFilter = $_POST['interest'] ?? 'Any Interest';
$nameFilter = $_POST['name'] ?? '';

// Build the SQL query
$sql = "SELECT u.id, u.name, u.department, u.team_status, p.* 
        FROM student u 
        LEFT JOIN user_profile p ON u.id=p.user_id 
        WHERE u.id != $uid AND u.team_status = 'available'"; // Ensure current user is excluded

if ($skillFilter !== 'Any Skill') {
    $sql .= " AND p.skill_" . strtolower($skillFilter) . " = 1"; // Assuming skills are stored as binary flags
}
if ($interestFilter !== 'Any Interest') {
    $interestColumn = strtolower(str_replace(' ', '_', $interestFilter)); // Replace spaces with underscores
    $sql .= " AND p.interest_" . $interestColumn . " = 1"; // Ensure the column exists
}
if (!empty($nameFilter)) {
    $nameFilter = mysqli_real_escape_string($conn, $nameFilter); // Sanitize input
    $sql .= " AND u.name LIKE '%$nameFilter%'";
}

// Execute the statement
$res2 = mysqli_query($conn, $sql);
$candidates = mysqli_fetch_all($res2, MYSQLI_ASSOC);

// Calculate hybrid scores for candidates
$recs = [];
foreach ($candidates as $c) {
    $vec = getUserVector($c);
    $score = hybridScore($myVec, $vec);
    $recs[] = ['user' => $c, 'score' => $score];
}

// Sort recommendations by score
usort($recs, fn($a, $b) => $b['score'] <=> $a['score']);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Recommendations - Project Mates</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php

include 'header.html';
?>
<main class="container">
  <section class="dashboard">
    <h3>Find Teammates</h3>
    <form method="post" action="">
      <div style="display: flex; justify-content: space-between;">
        <div>
          <label for="name">Search by Name:</label>
          <input type="text" name="name" id="name" placeholder="Enter name..." value="<?= htmlspecialchars($nameFilter) ?>">
        </div>
        <div>
          <label for="skill">Filter by Skill:</label>
          <select name="skill" id="skill">
            <option value="Any Skill">Any Skill</option>
            <option value="Python">Python</option>
            <option value="React">React</option>
            <option value="Java">Java</option>
          </select>
        </div>
        <div>
          <label for="interest">Filter by Interest:</label>
          <select name="interest" id="interest">
            <option value="Any Interest">Any Interest</option>
            <option value="AI">AI</option>
            <option value="Web Development">Web Development</option>
            <option value="Cybersecurity">Cybersecurity</option>
          </select>
        </div>
        <div>
          <button type="submit">Search</button>
        </div>
      </div>
    </form>

    <h4>Suggested Teammates</h4>
    <?php if (empty($recs)): ?>
      <p>No available recommendations right now.</p>
    <?php else: ?>
      <div class="teammate-suggestions">
        <?php foreach ($recs as $r): ?>
          <div class="teammate-card">
            <h5><?= htmlspecialchars($r['user']['name']) ?></h5>
            <p>Department: <?= htmlspecialchars($r['user']['department']) ?></p>
            <p>Compatibility: <?= round($r['score'] * 100, 2) ?>%</p>
            <div style="display:flex; justify-content: space-between;">
            <a href="viewprofile.php?id=<?= intval($r['user']['id']) ?>" class="view-profile">View Profile</a>
            <form method="post" action="invite_send.php">
                 <input type="hidden" name="to_id" value="<?= intval($r['user']['id']) ?>">
              <button type="submit" class="invite-button">Send Invitation</button>
              </div>
             
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>
</body>
</html>