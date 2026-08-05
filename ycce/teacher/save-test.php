<?php
require_once '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

if (isset($_POST['submit_token'])) {
    $currentToken = $_POST['submit_token'];
    if (isset($_SESSION['last_used_token']) && $_SESSION['last_used_token'] === $currentToken) {
        header("Location: dashboard.php?success=1");
        exit;
    }
    $_SESSION['last_used_token'] = $currentToken;
}

$pdo->exec("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
$pdo->exec("SET CHARACTER SET utf8mb4");

date_default_timezone_set('Asia/Kolkata');
$pdo->exec("SET time_zone = '+05:30'");

$s_hr = (int)$_POST['start_hr'];
$s_min = (int)$_POST['start_min'];
$s_per = $_POST['start_period'];
if ($s_per == 'PM' && $s_hr < 12) $s_hr += 12;
if ($s_per == 'AM' && $s_hr == 12) $s_hr = 0;
$start_date = $_POST['start_date'] ?? date('Y-m-d');
$created_at = $start_date . ' ' . sprintf("%02d:%02d:00", $s_hr, $s_min);

$hr = (int)$_POST['deadline_hr'];
$min = (int)$_POST['deadline_min'];
$per = $_POST['deadline_period'];
if ($per == 'PM' && $hr < 12) $hr += 12;
if ($per == 'AM' && $hr == 12) $hr = 0;
$deadline_date = $_POST['end_date'] ?? $_POST['deadline_date'] ?? date('Y-m-d');
$end_time = $deadline_date . ' ' . sprintf("%02d:%02d:00", $hr, $min);

function get_raw_string($input) {
    if ($input === null) return '';
    return (string)$input;
}

$sql = "INSERT INTO tests (
            title, description, teacher_id, duration_minutes, end_time, 
            questions_to_show, is_random_questions, shuffle_enabled, is_random_options, 
            status, test_type, start_time, start_hr, start_min, start_period,
            deadline_hr, deadline_min, deadline_period, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'published', ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    get_raw_string($_POST['title'] ?? ''),
    get_raw_string($_POST['description'] ?? ''),
    $_SESSION['user_id'],
    (int)($_POST['duration'] ?? 0),
    $end_time,
    (int)($_POST['questions_to_show'] ?? 0),
    isset($_POST['is_random_questions']) ? 1 : 0,
    isset($_POST['shuffle_enabled']) ? 1 : 0,
    isset($_POST['is_random_options']) ? 1 : 0,
    get_raw_string($_POST['test_type'] ?? 'mcq'),
    $created_at,
    $_POST['start_hr'],
    $_POST['start_min'],
    $_POST['start_period'],
    $_POST['deadline_hr'],
    $_POST['deadline_min'],
    $_POST['deadline_period'],
    $created_at
]);

$tid = $pdo->lastInsertId();

$student_ids = isset($_POST['students']) && is_array($_POST['students']) ? $_POST['students'] : [];

if (!empty($_POST['csv_selected_emails'])) {
    $emails = explode(',', $_POST['csv_selected_emails']);
    $emails = array_filter(array_map('trim', $emails));
    if (!empty($emails)) {
        $placeholders = implode(',', array_fill(0, count($emails), '?'));
        $stmt_emails = $pdo->prepare("SELECT id FROM users WHERE email IN ($placeholders)");
        $stmt_emails->execute($emails);
        $csv_ids = $stmt_emails->fetchAll(PDO::FETCH_COLUMN);
        $student_ids = array_merge($student_ids, $csv_ids);
    }
}

$student_ids = array_unique($student_ids);

if (!empty($student_ids)) {
    $st = $pdo->prepare("INSERT INTO test_candidates (test_id, student_id) VALUES (?, ?)");
    foreach ($student_ids as $sid) {
        $st->execute([$tid, $sid]);
    }
}

$total_marks = 0;
$i = 1;

$uploadDir = '../uploads/questions/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

while (isset($_POST["q_text_$i"])) {
    $txt = get_raw_string($_POST["q_text_$i"]);
    $pts = (int)$_POST["q_points_$i"];
    if ($txt === '') {
        $i++;
        continue;
    }

    $imagePath = null;
    if (isset($_FILES["q_image_$i"]) && $_FILES["q_image_$i"]['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES["q_image_$i"]['tmp_name'];
        $fileName = $_FILES["q_image_$i"]['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = 'test_' . $tid . '_q_' . $i . '_' . time() . '.' . $fileExtension;
        $targetFileDir = $uploadDir . $newFileName;
        
        if (move_uploaded_file($fileTmpPath, $targetFileDir)) {
            $imagePath = 'uploads/questions/' . $newFileName;
        }
    }

    $sq = $pdo->prepare("INSERT INTO questions (test_id, question_text, points, image_path) VALUES (?, ?, ?, ?)");
    $sq->execute([$tid, $txt, $pts, $imagePath]);
    $qid = $pdo->lastInsertId();
    $total_marks += $pts;

    $o = 1;
    while (isset($_POST["opt_text_{$i}_{$o}"])) {
        $otxt = get_raw_string($_POST["opt_text_{$i}_{$o}"]);
        if ($otxt !== '') {
            $isC = (isset($_POST["correct_$i"]) && in_array($o, (array)$_POST["correct_$i"])) ? 1 : 0;
            $so = $pdo->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
            $so->execute([$qid, $otxt, $isC]);
        }
        $o++;
    }
    $i++;
}

$pdo->prepare("UPDATE tests SET total_marks = ? WHERE id = ?")->execute([$total_marks, $tid]);

header("Location: dashboard.php?success=1");
exit;
?>