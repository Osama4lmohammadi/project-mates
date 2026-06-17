<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) header('Location: login.php');
$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invite_id = intval($_POST['invite_id']);
    $action = $_POST['action'] ?? '';

    if (in_array($action, ['accept','reject'])) {
        $status = $action === 'accept' ? 'accepted' : 'rejected';
        $stmt = mysqli_prepare($conn, "UPDATE invitations SET status=? WHERE id=? AND receiver_id=?");
        mysqli_stmt_bind_param($stmt, 'sii', $status, $invite_id, $uid);
        mysqli_stmt_execute($stmt);

        if ($status === 'accepted') {
            // Update both student to "in_team"
            $stmt2 = mysqli_prepare($conn, "UPDATE student u 
                JOIN invitations i ON (u.id=i.sender_id OR u.id=i.receiver_id)
                SET u.team_status='in_team'
                WHERE i.id=?");
            mysqli_stmt_bind_param($stmt2, 'i', $invite_id);
            mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);
        }

        mysqli_stmt_close($stmt);
    }
}

header('Location: invitations.php');
exit;
?>