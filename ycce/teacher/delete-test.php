<?php
require_once '../includes/db.php';
session_start();

if ($_SESSION['role'] !== 'teacher' || !isset($_GET['id'])) {
    header("Location: /ycce/teacher/dashboard.php");
    exit;
}

$test_id = (int)$_GET['id'];

$stmt = $pdo->prepare("DELETE FROM tests WHERE id = ? AND teacher_id = ?");
$stmt->execute([$test_id, $_SESSION['user_id']]);

header("Location: /ycce/teacher/dashboard.php?deleted=1");
exit;
?>