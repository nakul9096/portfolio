<?php
require_once 'includes/db.php';
$stmt = $pdo->query("SELECT id, name, email FROM users WHERE role = 'student' ORDER BY name");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($students as $s) {
    echo '
    <div class="glass rounded-3xl p-4 flex items-center gap-x-3">
        <input type="checkbox" name="students[]" value="' . $s['id'] . '" class="w-5 h-5 accent-indigo-600">
        <div>
            <div class="font-medium">' . htmlspecialchars($s['name']) . '</div>
            <div class="text-xs text-zinc-400">' . htmlspecialchars($s['email']) . '</div>
        </div>
    </div>';
}
?>