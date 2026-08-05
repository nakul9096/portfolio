<?php
$host = '127.0.0.1';
$db   = 'ycce';
$user = 'root';
$pass = 'nakul9096'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

/**
 * AUTO-CLOSE EXPIRED TESTS
 * This function automatically closes tests when end_time has passed.
 * Call this at the top of dashboards and test-related pages.
 */
function auto_close_expired_tests($pdo) {
    try {
        $pdo->exec("SET time_zone = '+05:30'");
        
        $stmt = $pdo->prepare("
            UPDATE tests 
            SET status = 'closed'
            WHERE status = 'published' 
              AND end_time IS NOT NULL 
              AND end_time <= NOW()
        ");
        $stmt->execute();
    } catch (Exception $e) {
        error_log("Auto-close error: " . $e->getMessage());
    }
}
?>