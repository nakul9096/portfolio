<?php
require_once '../includes/header.php';

$test_id = (int)($_GET['test_id'] ?? 0);
$student_id = (int)($_GET['student_id'] ?? 0);

if ($test_id <= 0 || $_SESSION['role'] !== 'teacher') {
    header("Location: ./dashboard.php");
    exit;
}

// Create table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS email_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_id INT NOT NULL,
    student_id INT NOT NULL,
    teacher_id INT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Log the email
$stmt = $pdo->prepare("INSERT INTO email_logs (test_id, student_id, teacher_id) VALUES (?, ?, ?)");
$stmt->execute([$test_id, $student_id, $_SESSION['user_id']]);

// Increment total count
$stmt = $pdo->prepare("UPDATE tests SET email_sent_count = COALESCE(email_sent_count, 0) + 1 WHERE id = ?");
$stmt->execute([$test_id]);

// Redirect back to send-email page
header("Location: send-email.php?test_id=" . $test_id);
exit;
?>