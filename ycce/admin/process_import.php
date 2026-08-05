<?php
require_once '../includes/db.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['temp_import'])) {
    $data_to_import = $_SESSION['temp_import'];

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO users (name, email, reg_no, department, role, password, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");

        foreach ($data_to_import as $user) {
            // Neobrutalist Password Logic: 3 letters of name + 6 random digits
            $cleanName = strtoupper(preg_replace('/[^A-Za-z]/', '', $user['name']));
            $prefix = str_pad(substr($cleanName, 0, 3), 3, 'X'); 
            $pass = $prefix . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            $stmt->execute([
                $user['name'],
                $user['email'],
                $user['reg_no'],
                $user['department'],
                $user['role'],
                $pass
            ]);
        }

        $pdo->commit();
        unset($_SESSION['temp_import']);
        header("Location: manage_users.php?import_success=" . count($data_to_import));
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Import Failed. Error: " . $e->getMessage());
    }
} else {
    header("Location: bulk_import.php");
    exit;
}