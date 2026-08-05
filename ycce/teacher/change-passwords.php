<?php
require_once '../includes/header.php';

// Check if the user is a teacher
if ($_SESSION['role'] !== 'teacher') {
    header("Location: ./dashboard.php");
    exit;
}

$test_id = (int)($_GET['test_id'] ?? 0);
if ($test_id <= 0) {
    header("Location: ./dashboard.php");
    exit;
}

// Handle Generate Request
if (isset($_POST['shuffle_all'])) {
    $stmt = $pdo->prepare("SELECT u.id, u.name FROM test_candidates tc JOIN users u ON tc.student_id = u.id WHERE tc.test_id = ?");
    $stmt->execute([$test_id]);
    $candidates = $stmt->fetchAll();

    foreach ($candidates as $c) {
        // Clean name: keep only letters and make uppercase
        $cleanName = strtoupper(preg_replace('/[^A-Za-z]/', '', $c['name']));
        $prefix = substr($cleanName, 0, 3);
        
        // Pad with 'X' if name is shorter than 3 letters
        $prefix = str_pad($prefix, 3, "X"); 
        
        // 6-digit random number
        $new_pass = $prefix . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update password in the database
        $update = $pdo->prepare("UPDATE users SET password = ?, last_password_reset = NOW() WHERE id = ?");
        $update->execute([$new_pass, $c['id']]);
    }
    header("Location: change-passwords.php?test_id=$test_id&success=1");
    exit;
}

// Fetch Quiz & Student details
$stmt = $pdo->prepare("SELECT title FROM tests WHERE id = ?");
$stmt->execute([$test_id]);
$test = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT u.id, u.name, u.email, u.password, u.last_password_reset, u.reg_no 
    FROM test_candidates tc 
    JOIN users u ON tc.student_id = u.id 
    WHERE tc.test_id = ? 
    ORDER BY u.name ASC
");
$stmt->execute([$test_id]);
$students = $stmt->fetchAll();
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
    
    body { background-color: #f8fafc; font-family: 'Inter', sans-serif; color: #0f172a; }
    
    .bb-card-sharp { 
        background: #ffffff; 
        border: 3px solid #0f172a; 
        box-shadow: 6px 6px 0px #0f172a; 
    }
    
    .btn-black-sharp { 
        background: #0f172a; 
        color: #ffffff; 
        border: 2px solid #0f172a; 
        box-shadow: 3px 3px 0px #6366f1; 
        transition: all 0.15s ease; 
        cursor: pointer; 
        font-weight: 900;
        text-transform: uppercase;
    }
    .btn-black-sharp:hover { 
        background: #6366f1; 
        transform: translate(-2px, -2px); 
        box-shadow: 5px 5px 0px #0f172a; 
    }

    .btn-outline-bb {
        background: white;
        color: #0f172a;
        border: 2px solid #0f172a;
        box-shadow: 3px 3px 0px #0f172a;
        transition: 0.15s ease;
        font-weight: 900;
        letter-spacing: 0.05em;
    }
    .btn-outline-bb:hover {
        transform: translate(-2px, -2px);
        box-shadow: 5px 5px 0px #6366f1;
    }

    .btn-shuffle { 
        background: #f59e0b; 
        color: #0f172a; 
        border: 3px solid #0f172a; 
        box-shadow: 4px 4px 0px #0f172a; 
        font-weight: 900; 
        transition: all 0.1s ease; 
        cursor: pointer; 
    }
    .btn-shuffle:hover { 
        transform: translate(-2px, -2px); 
        box-shadow: 6px 6px 0px #0f172a; 
        background: #fbbf24; 
    }
    
    .pass-box { 
        font-family: 'Courier New', monospace; 
        border: 2px solid #0f172a; 
        background: #fffbeb; 
        padding: 6px 12px; 
        font-weight: 900; 
        color: #92400e; 
        box-shadow: 2px 2px 0px #0f172a;
        display: inline-block;
    }
    
    .status-dot { 
        height: 8px; 
        width: 8px; 
        background: #6366f1; 
        border: 1px solid #0f172a;
        display: inline-block; 
    }
</style>

<div class="min-h-screen py-12 px-6 bg-slate-50">
    <div class="max-w-6xl mx-auto">
        
        <div class="mb-6 flex justify-between items-center">
            <a href="./dashboard.php" class="btn-outline-bb px-5 py-3 text-xs uppercase flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-[11px]"></i> Back to Dashboard
            </a>
            <a href="edit-test.php?id=<?= $test_id ?>" class="btn-outline-bb px-5 py-3 text-xs uppercase flex items-center gap-2">
                <i class="fa-solid fa-sliders text-[11px]"></i> Edit Quiz
            </a>
        </div>

        <div class="bb-card-sharp p-8 mb-8 bg-white flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="status-dot"></span>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] bg-slate-900 text-white px-2 py-0.5 border border-slate-900 shadow-[1px_1px_0px_#000]">YCCE Exam System</span>
                    <span class="text-[10px] font-mono font-bold text-slate-400">ID: #00<?= $test_id ?></span>
                </div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight uppercase italic">Student Passwords</h1>
                <p class="text-xs font-bold text-slate-400 uppercase mt-1">
                    Selected Quiz: <span class="text-indigo-600"><?= htmlspecialchars($test['title']) ?></span>
                </p>
            </div>

            <form method="POST" onsubmit="return confirm('Are you sure you want to create brand new passwords for all students?')">
                <button type="submit" name="shuffle_all" class="btn-shuffle px-6 py-4 text-xs uppercase tracking-widest flex items-center gap-3">
                    <i class="fa-solid fa-bolt-lightning text-sm"></i> Generate New Passwords
                </button>
            </form>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="bb-card-sharp p-4 mb-8 bg-emerald-50 text-emerald-800 border-emerald-600 font-bold text-xs uppercase tracking-wide flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-sm text-emerald-600"></i>
                    <span>Done! All student passwords have been updated successfully.</span>
                </div>
                <i class="fa-solid fa-check-double text-emerald-600"></i>
            </div>
        <?php endif; ?>

        <div class="bb-card-sharp bg-white p-6">
            <div class="border-b-2 border-slate-900 pb-4 mb-6">
                <h2 class="text-lg font-black uppercase tracking-tight text-slate-900">Student List & Login Passwords</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Give these passwords to your students so they can log in and take the quiz.</p>
            </div>

            <div class="max-w-full overflow-x-auto border-2 border-slate-900 bg-slate-50">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest">
                            <th class="p-4 border border-slate-900">Student Info</th>
                            <th class="p-4 border border-slate-900">Login Password</th>
                            <th class="p-4 border border-slate-900 text-right">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-slate-100 bg-white">
                        <?php if (count($students) === 0): ?>
                            <tr>
                                <td colspan="3" class="p-12 text-center bg-slate-50">
                                    <h4 class="text-xs font-black uppercase text-slate-800 tracking-wider">No Students Found</h4>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Please add students to this quiz first using the Edit Quiz page.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                            <tr class="hover:bg-indigo-50/20 transition-colors">
                                <td class="p-4 border-b border-slate-100">
                                    <div class="font-black text-slate-900 uppercase italic text-sm"><?= htmlspecialchars($student['name']) ?></div>
                                    <div class="text-[10px] font-mono font-bold text-indigo-600 uppercase tracking-tight mt-0.5"><?= htmlspecialchars($student['reg_no'] ?? 'No Reg No') ?></div>
                                </td>
                                <td class="p-4 border-b border-slate-100">
                                    <span class="pass-box text-xs">
                                        <?= htmlspecialchars($student['password']) ?>
                                    </span>
                                </td>
                                <td class="p-4 border-b border-slate-100 text-right">
                                    <div class="text-[10px] font-black uppercase text-slate-500 font-mono">
                                        <i class="fa-regular fa-clock mr-1 text-slate-400"></i>
                                        <?= $student['last_password_reset'] ? date('d M, Y | h:i A', strtotime($student['last_password_reset'])) : '<span class="bg-amber-100 text-amber-800 px-1.5 py-0.5 border border-slate-900 shadow-[1px_1px_0px_#000]">NOT CHANGED YET</span>' ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>