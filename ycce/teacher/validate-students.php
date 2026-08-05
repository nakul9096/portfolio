<?php
header('Content-Type: application/json');
require_once '../includes/db.php'; // Adjust your DB connection

$data = json_decode(file_get_contents('php://input'), true);
$emails = $data['emails'] ?? [];

$result = [];

foreach ($emails as $email) {
    $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $exists = $stmt->rowCount() > 0;
    $result[] = ['email' => $email, 'exists' => $exists];
}

echo json_encode($result);
?>