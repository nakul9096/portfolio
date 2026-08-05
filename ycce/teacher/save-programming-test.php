<?php
require_once '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_SESSION['role'] !== 'teacher') {
    header("Location: dashboard.php");
    exit;
}

// Set timezone
date_default_timezone_set('Asia/Kolkata');
$pdo->exec("SET time_zone = '+05:30'");

// Clean function
function clean_text($text) {
    return $text !== null ? trim($text) : '';
}

// 1. Insert Test Header
$title = clean_text($_POST['title']);
$end_date = $_POST['end_date'];
$hr = (int)$_POST['deadline_hr'];
$min = (int)$_POST['deadline_min'];
$per = $_POST['deadline_period'];

if ($per == 'PM' && $hr < 12) $hr += 12;
if ($per == 'AM' && $hr == 12) $hr = 0;

$end_time = $end_date . ' ' . sprintf("%02d:%02d:00", $hr, $min);

$stmt = $pdo->prepare("INSERT INTO tests (
    title, description, teacher_id, duration_minutes, end_time, 
    status, test_type
) VALUES (?, ?, ?, ?, ?, 'published', 'programming')");

$stmt->execute([
    $title,
    clean_text($_POST['problem_description']),
    $_SESSION['user_id'],
    (int)$_POST['duration'],
    $end_time
]);

$test_id = $pdo->lastInsertId();

// 2. Save Test Cases
if (isset($_POST['test_input']) && is_array($_POST['test_input'])) {
    $insert_case = $pdo->prepare("INSERT INTO programming_test_cases 
        (test_id, input, expected_output) VALUES (?, ?, ?)");
    
    for ($i = 0; $i < count($_POST['test_input']); $i++) {
        $input = clean_text($_POST['test_input'][$i]);
        $output = clean_text($_POST['test_output'][$i] ?? '');
        
        if (!empty($input) || !empty($output)) {
            $insert_case->execute([$test_id, $input, $output]);
        }
    }
}

// 3. Assign Students
if (isset($_POST['students']) && is_array($_POST['students'])) {
    $st = $pdo->prepare("INSERT INTO test_candidates (test_id, student_id) VALUES (?, ?)");
    foreach ($_POST['students'] as $sid) {
        $st->execute([$test_id, (int)$sid]);
    }
}

header("Location: dashboard.php?success=1&test_id=$test_id");
exit;
?>