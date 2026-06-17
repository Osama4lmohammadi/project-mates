<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $from = $_SESSION['user_id'];
    $to = intval($_POST['to_id']);
    $stmt = mysqli_prepare($conn, 'SELECT id FROM invitations WHERE sender_id=? AND receiver_id=? AND status="pending"');
    mysqli_stmt_bind_param($stmt, 'ii', $from, $to);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) === 0) {
        $stmt2 = mysqli_prepare($conn, 'INSERT INTO invitations (sender_id,receiver_id) VALUES (?,?)');
        mysqli_stmt_bind_param($stmt2, 'ii', $from, $to);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
    }
    mysqli_stmt_close($stmt);
}
header('Location: recommend.php');
exit;
?>