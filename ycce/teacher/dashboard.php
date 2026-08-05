<?php 
require_once '../includes/db.php';
session_start();

// ====================== AUTO CLOSE EXPIRED TESTS ======================
auto_close_expired_tests($pdo);
// =====================================================================

if (isset($_GET['action']) && $_GET['action'] === 'end_test' && isset($_GET['end_id'])) {
    date_default_timezone_set('Asia/Kolkata');
    $pdo->exec("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
    $pdo->exec("SET CHARACTER SET utf8mb4");
    $pdo->exec("SET time_zone = '+05:30'");
    
    $end_id = intval($_GET['end_id']);
    $updateStmt = $pdo->prepare("UPDATE tests SET end_time = NOW() WHERE id = ? AND teacher_id = ?");
    $updateStmt->execute([$end_id, $_SESSION['user_id']]);
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

require_once '../includes/header.php'; 
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght=400;500;700;800&display=swap');
    
    html, body { height: 100%; margin: 0; }
    body { 
        font-family: 'Inter', sans-serif; 
        background-color: #f4f5f7; 
        color: #1e293b; 
        display: flex;
        flex-direction: column;
    }

    .main-content { flex: 1 0 auto; }

    .dashboard-card { 
        background: #ffffff; 
        border: 2px solid #0f172a; 
        box-shadow: 4px 4px 0px #0f172a; 
    }

    .btn-action { 
        background: #ffffff;
        color: #0f172a; 
        font-weight: 700;
        border: 2px solid #0f172a; 
        box-shadow: 2px 2px 0px #0f172a; 
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.1s ease;
    }

    .btn-action:hover { 
        transform: translate(1px, 1px);
        box-shadow: 1px 1px 0px #0f172a;
        background: #f8fafc;
    }

    .btn-primary-brutal {
        background: #2563eb;
        color: #ffffff;
        font-weight: 700;
        border: 2px solid #0f172a;
        box-shadow: 3px 3px 0px #0f172a;
        transition: all 0.1s ease;
    }

    .btn-primary-brutal:hover {
        transform: translate(1px, 1px);
        box-shadow: 2px 2px 0px #0f172a;
        background: #1d4ed8;
    }

    .label-badge { 
        font-size: 10px; 
        font-weight: 800; 
        text-transform: uppercase; 
        letter-spacing: 0.1em; 
    }

    .dashboard-footer-divider {
        border: 0;
        height: 2px;
        background-color: #0f172a;
        margin-top: auto;
        margin-bottom: 0;
    }

    footer, .footer, #footer {
        padding-top: 12px !important;
        padding-bottom: 12px !important;
        margin-top: 0 !important;
        border-top: 2px solid #0f172a !important;
    }

    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-thumb { background: #0f172a; }
</style>

<div class="main-content">
    <main class="max-w-6xl mx-auto px-6 py-10">
        
        <div class="dashboard-card p-6 mb-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-[#e2e8f0] border-2 border-[#0f172a] flex items-center justify-center text-[#0f172a] font-all-black font-extrabold text-2xl shadow-[3px_3px_0px_#0f172a] flex-shrink-0 relative overflow-hidden">
                    <svg class="w-14 h-14 text-slate-400 absolute bottom-0" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12c0 2.624 1.035 5.006 2.715 6.753.303.313.738.446 1.148.367A13.233 13.233 0 0112 18c2.47 0 4.793.676 6.785 1.865a.75.75 0 00.75-.768zM12 5.25a3.75 3.75 0 100 7.5 3.75 3.75 0 000-7.5z" clip-rule="evenodd" />
                    </svg>
                    <span class="relative z-10 text-[#0f172a] font-extrabold"><?= strtoupper(substr($_SESSION['name'] ?? 'T', 0, 1)) ?></span>
                </div>

                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 uppercase">Teacher Dashboard</h2>
                    <div class="flex flex-wrap items-center gap-3 mt-1">
                        <span class="label-badge bg-slate-900 text-white px-2 py-0.5 text-[9px]">Faculty Console</span>
                        <span class="label-badge text-slate-500 font-bold">YCCE Examination System</span>
                    </div>
                </div>
            </div>
            
            <div class="relative group">
                <button onclick="toggleDropdown()" class="btn-primary-brutal px-6 py-3 text-[10px] uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-plus text-[9px]"></i> New Test 
                    <i class="fa-solid fa-chevron-down text-xs transition-transform"></i>
                </button>
                
                <div id="testDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white border-2 border-slate-900 shadow-[6px_6px_0px_#0f172a] z-50">
                    <a href="create-test.php" class="block px-6 py-4 hover:bg-slate-100 border-b border-slate-200 flex items-center gap-3">
                        <i class="fa-solid fa-list-check text-indigo-600 w-5"></i>
                        <div>
                            <div class="font-bold">MCQ Assessment</div>
                            <div class="text-xs text-slate-500">Multiple Choice Questions</div>
                        </div>
                    </a>
                    <a href="#" class="block px-6 py-4 hover:bg-slate-100 flex items-center gap-3">
                        <i class="fa-solid fa-code text-emerald-600 w-5"></i>
                        <div>
                            <div class="font-bold">Programming Assessment</div>
                            <div class="text-xs text-slate-500">Under development</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <a href="manage_users.php" class="dashboard-card p-5 flex items-center gap-4 hover:border-blue-600 transition-colors bg-white">
                <div class="w-12 h-12 border-2 border-slate-900 flex items-center justify-center bg-blue-50 text-slate-900 shadow-[2px_2px_0px_#0f172a] shrink-0">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <div>
                    <h3 class="font-extrabold uppercase text-sm text-slate-900">User Management</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Create Students & Teachers</p>
                </div>
            </a>
            <a href="view_grades.php" class="dashboard-card p-5 flex items-center gap-4 hover:border-blue-600 transition-colors bg-white">
                <div class="w-12 h-12 border-2 border-slate-900 flex items-center justify-center bg-emerald-50 text-slate-900 shadow-[2px_2px_0px_#0f172a] shrink-0">
                    <i class="fa-solid fa-chart-simple"></i>
                </div>
                <div>
                    <h3 class="font-extrabold uppercase text-sm text-slate-900">Master Grade List</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Global Performance Tracking</p>
                </div>
            </a>
        </div>

        <?php
        date_default_timezone_set('Asia/Kolkata');
        // Prevent diamond symbols inside question streams fetched from MySQL
        $pdo->exec("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
        $pdo->exec("SET CHARACTER SET utf8mb4");
        $pdo->exec("SET time_zone = '+05:30'");

        // Active tests include those where end_time is in the future
        $activeStmt = $pdo->prepare("SELECT * FROM tests WHERE teacher_id = ? AND status = 'published' AND (end_time IS NULL OR end_time > NOW()) ORDER BY created_at DESC");
        $activeStmt->execute([$_SESSION['user_id']]);
        $active_tests = $activeStmt->fetchAll();

        // Ended tests include manually closed tests or those whose end_time has passed
        $endedStmt = $pdo->prepare("SELECT * FROM tests WHERE teacher_id = ? AND (status = 'closed' OR end_time <= NOW()) ORDER BY end_time DESC");
        $endedStmt->execute([$_SESSION['user_id']]);
        $ended_tests = $endedStmt->fetchAll();
        ?>

        <div class="flex items-center gap-3 mb-6">
            <h3 class="label-badge text-slate-900 text-xs font-extrabold border-2 border-slate-900 bg-white px-2 py-0.5">Active Tests</h3>
            <div class="h-[2px] flex-grow bg-slate-900"></div>
        </div>

        <div class="space-y-4 mb-12">
            <?php if (count($active_tests) > 0): 
                foreach ($active_tests as $test): ?>
                <div class="dashboard-card p-5 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 bg-white">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 border-2 border-slate-900 bg-slate-50 text-slate-900 hidden sm:flex items-center justify-center shadow-[2px_2px_0px_#0f172a] shrink-0">
                            <i class="fa-solid fa-file-lines text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-lg uppercase text-slate-900 tracking-tight"><?= htmlspecialchars($test['title']) ?></h4>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <span class="text-xs font-mono font-bold bg-slate-100 border border-slate-300 px-1.5 py-0.5 text-slate-700">
                                    <i class="fa-solid fa-clock mr-1 text-blue-600"></i> <?= $test['duration_minutes'] ?> MIN
                                </span>
                                <span class="text-xs font-mono font-bold bg-slate-100 border border-slate-300 px-1.5 py-0.5 text-slate-700">
                                    <i class="fa-solid fa-star mr-1 text-blue-600"></i> <?= $test['total_marks'] ?? '0' ?> MARKS
                                </span>
                                <span class="text-xs font-mono font-bold bg-indigo-50 border border-indigo-200 px-1.5 py-0.5 text-indigo-800">
                                    <i class="fa-solid fa-calendar-check mr-1"></i> Starts: <?= date('d-m-Y h:i A', strtotime($test['created_at'])) ?>
                                </span>
                                <?php if (!empty($test['end_time'])): ?>
                                <span class="text-xs font-mono font-bold bg-amber-50 border border-amber-200 px-1.5 py-0.5 text-amber-800">
                                    <i class="fa-solid fa-calendar-days mr-1"></i> Closes: <?= date('d-m-Y h:i A', strtotime($test['end_time'])) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto border-t-2 lg:border-t-0 pt-3 lg:pt-0 border-slate-100">
                        <a href="?action=end_test&end_id=<?= $test['id'] ?>" onclick="return confirm('Are you sure you want to terminate this active test immediately?')" class="btn-action px-3 py-2 text-[10px] uppercase tracking-wider justify-center grow sm:grow-0 bg-red-50 text-red-700 border-red-600 hover:bg-red-100">
                            End Test
                        </a>
                        <a href="edit-test.php?id=<?= $test['id'] ?>" class="btn-action px-3 py-2 text-[10px] uppercase tracking-wider justify-center grow sm:grow-0 bg-slate-900 text-white hover:bg-slate-800">
                            Edit
                        </a>
                        <a href="change-passwords.php?test_id=<?= $test['id'] ?>" class="btn-action px-3 py-2 text-[10px] uppercase tracking-wider justify-center grow sm:grow-0">
                            Passwords
                        </a>
                        <a href="send-email.php?test_id=<?= $test['id'] ?>" class="btn-action px-3 py-2 text-[10px] uppercase tracking-wider justify-center grow sm:grow-0">
                            Invite
                        </a>
                        <a href="view_grades.php?test_id=<?= $test['id'] ?>" class="btn-action px-3 py-2 text-[10px] uppercase tracking-wider justify-center grow sm:grow-0">
                            Grades
                        </a>
                        <a href="delete-test.php?id=<?= $test['id'] ?>" onclick="return confirm('Archive record?')" class="w-9 h-9 border-2 border-slate-900 text-slate-400 hover:text-red-600 hover:border-red-600 flex items-center justify-center transition-colors bg-white shadow-[2px_2px_0px_#0f172a] hover:shadow-[1px_1px_0px_#dc2626] hover:translate-x-[1px] hover:translate-y-[1px] shrink-0 ml-auto lg:ml-2">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach;
            else: ?>
                <div class="dashboard-card border-dashed p-8 text-center text-slate-400 bg-white">
                    <p class="label-badge">No active ongoing examinations running currently.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="flex items-center gap-3 mb-6">
            <h3 class="label-badge text-slate-400 text-xs font-extrabold border-2 border-slate-300 bg-slate-100 px-2 py-0.5">Ended Tests</h3>
            <div class="h-[2px] flex-grow bg-slate-300"></div>
        </div>

        <div class="space-y-4">
            <?php if (count($ended_tests) > 0): 
                foreach ($ended_tests as $test): ?>
                <div class="dashboard-card p-5 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 bg-slate-50/80 border-slate-400 opacity-85 shadow-[2px_2px_0px_#94a3b8]">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 border-2 border-slate-400 bg-slate-200 text-slate-500 hidden sm:flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-file-circle-xmark text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg uppercase text-slate-500 tracking-tight line-through decoration-1"><?= htmlspecialchars($test['title']) ?></h4>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <span class="text-xs font-mono font-bold bg-slate-200/60 border border-slate-300 px-1.5 py-0.5 text-slate-500">
                                    Duration: <?= $test['duration_minutes'] ?> MIN
                                </span>
                                <span class="text-xs font-mono font-bold bg-slate-200/60 border border-slate-300 px-1.5 py-0.5 text-slate-500">
                                    Marks: <?= $test['total_marks'] ?? '0' ?>
                                </span>
                                <span class="text-xs font-mono font-bold bg-slate-100 border border-slate-300 px-1.5 py-0.5 text-slate-500">
                                    Started On: <?= date('d-m-Y h:i A', strtotime($test['created_at'])) ?>
                                </span>
                                <span class="text-xs font-mono font-bold bg-red-100 border border-red-200 px-1.5 py-0.5 text-red-700">
                                    <i class="fa-solid fa-calendar-xmark mr-1"></i> Ended On: <?= date('d-m-Y h:i A', strtotime($test['end_time'])) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto border-t border-slate-200 pt-3 lg:pt-0">
                        <a href="view_grades.php?test_id=<?= $test['id'] ?>" class="btn-action px-3 py-2 text-[10px] uppercase tracking-wider justify-center grow sm:grow-0">
                            Grades
                        </a>
                        <a href="delete-test.php?id=<?= $test['id'] ?>" onclick="return confirm('Archive record permanently?')" class="w-9 h-9 border-2 border-slate-400 text-slate-400 hover:text-red-600 hover:border-red-600 flex items-center justify-center transition-colors bg-white shrink-0 ml-auto lg:ml-2 shadow-[1px_1px_0px_#94a3b8]">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach;
            else: ?>
                <div class="dashboard-card border-dashed p-8 text-center text-slate-400 bg-white/50 border-slate-300">
                    <p class="label-badge">No records found inside terminated session logs.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<hr class="dashboard-footer-divider">

<script>
function toggleDropdown() {
    const dropdown = document.getElementById('testDropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('testDropdown');
    if (!e.target.closest('.group') && !dropdown.classList.contains('hidden')) {
        dropdown.classList.add('hidden');
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>