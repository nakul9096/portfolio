<?php
// 1. Force start session explicitly before header loading to preserve data matrix state
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/header.php';

// Access Control Layer
if ($_SESSION['role'] !== 'teacher') {
    header("Location: ./dashboard.php");
    exit;
}

// 2. BULLETPROOF PARAMETER PARSING ENGINE
// Tries URL parameter first, fallbacks to POST body if form is submitted, or falls back to tracking session
$test_id = 0;
if (isset($_GET['id']) && (int)$_GET['id'] > 0) {
    $test_id = (int)$_GET['id'];
    $_SESSION['last_active_test_node'] = $test_id; // Set backup tracker
} elseif (isset($_POST['backup_test_id']) && (int)$POST['backup_test_id'] > 0) {
    $test_id = (int)$_POST['backup_test_id'];
} elseif (isset($_SESSION['last_active_test_node']) && (int)$_SESSION['last_active_test_node'] > 0) {
    $test_id = (int)$_SESSION['last_active_test_node'];
}

// Absolute strict exit boundary check
if ($test_id <= 0) {
    echo "<script>alert('Invalid Assessment Node Parameter'); window.location.href='./dashboard.php';</script>";
    exit;
}

// Fetch Master Test Record Context securely to verify ownership
$stmt = $pdo->prepare("SELECT * FROM tests WHERE id = ? AND teacher_id = ?");
$stmt->execute([$test_id, $_SESSION['user_id']]);
$test = $stmt->fetch();

if (!$test) {
    echo "<script>alert('Assessment entry not found or unauthorized access attempt.'); window.location.href='./dashboard.php';</script>";
    exit;
}

// =========================================================================
// BACKEND PROCESSING ENGINE: Update student roster if form is submitted
// =========================================================================
$success_message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Clear out previously assigned entries for this specific test node
        $delete_stmt = $pdo->prepare("DELETE FROM test_candidates WHERE test_id = ?");
        $delete_stmt->execute([$test_id]);

        // Map and bind newly selected candidates tracking rows if array is parsed
        if (isset($_POST['student_ids']) && !empty($_POST['student_ids'])) {
            $insert_stmt = $pdo->prepare("INSERT INTO test_candidates (test_id, student_id) VALUES (?, ?)");
            foreach ($_POST['student_ids'] as $student_id) {
                $insert_stmt->execute([$test_id, (int)$student_id]);
            }
        }

        $pdo->commit();
        $success_message = "Enrolled candidate roster updated successfully.";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Error processing structural matrix update: " . addslashes($e->getMessage()) . "');</script>";
    }
}
// =========================================================================

// Fetch all system users for student enrollment pool mapping
$user_stmt = $pdo->prepare("SELECT id, name, email, reg_no, department FROM users WHERE role = 'student' ORDER BY name ASC");
$user_stmt->execute();
$all_students = $user_stmt->fetchAll();

// Fetch currently enrolled student IDs for checkbox checks
$enrolled_stmt = $pdo->prepare("SELECT student_id FROM test_candidates WHERE test_id = ?");
$enrolled_stmt->execute([$test_id]);
$enrolled_student_ids = $enrolled_stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch existing questions for Quiz Update panel context matching
$q_stmt = $pdo->prepare("SELECT * FROM questions WHERE test_id = ? ORDER BY id ASC");
$q_stmt->execute([$test_id]);
$questions = $q_stmt->fetchAll();
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

    /* Tab Engine Styles */
    .tab-trigger {
        background: #ffffff;
        color: #475569;
        border: 2px solid #0f172a;
        border-bottom-width: 4px;
        transition: all 0.1s ease;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .tab-trigger.active-tab-state {
        background: #6366f1;
        color: white;
        transform: translateY(2px);
        border-bottom-width: 2px;
        box-shadow: inset 2px 2px 0px rgba(0,0,0,0.2);
    }
    
    .input-bb-sharp {
        border: 2px solid #0f172a;
        box-shadow: 3px 3px 0px #0f172a;
        transition: all 0.2s ease;
    }
    .input-bb-sharp:focus {
        box-shadow: 4px 4px 0px #6366f1;
        outline: none;
    }
</style>

<div class="min-h-screen py-12 px-6 bg-slate-50">
    <div class="max-w-6xl mx-auto">
        
        <div class="mb-6 flex justify-between items-center">
            <a href="./dashboard.php" class="btn-outline-bb px-5 py-3 text-xs uppercase flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-[11px]"></i> Back to Dashboard
            </a>
            <a href="send-credentials.php?test_id=<?= $test_id ?>" class="btn-black-sharp px-5 py-3 text-xs flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-[11px]"></i> Dispatcher Hub
            </a>
        </div>

        <?php if ($success_message): ?>
            <div class="mb-6 p-4 border-3 border-emerald-600 bg-emerald-50 text-emerald-800 font-black text-xs uppercase italic flex items-center gap-2 shadow-[4px_4px_0px_#059669]">
                <i class="fa-solid fa-circle-check text-sm"></i> <?= $success_message ?>
            </div>
        <?php endif; ?>

        <div class="bb-card-sharp p-8 mb-8 bg-white">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] bg-indigo-600 text-white px-2 py-0.5 border border-slate-900 shadow-[1px_1px_0px_#000]">Configuration Center</span>
                <span class="text-[10px] font-mono font-bold text-slate-400">Node Ref ID: #00<?= $test_id ?></span>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight uppercase italic">Modify Assessment Ecosystem</h1>
            <p class="text-xs font-bold text-slate-400 uppercase mt-1">Current Active Pointer: <span class="text-indigo-600"><?= htmlspecialchars($test['title']) ?></span></p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <button type="button" onclick="switchControlView('tab-panel-info', this)" class="tab-trigger active-tab-state py-4 text-xs px-4 flex items-center justify-center gap-2">
                <i class="fa-solid fa-sliders text-[13px]"></i> Quiz Information
            </button>
            <button type="button" id="trigger-tab-students" onclick="switchControlView('tab-panel-students', this)" class="tab-trigger py-4 text-xs px-4 flex items-center justify-center gap-2">
                <i class="fa-solid fa-user-plus text-[13px]"></i> Update Students
            </button>
            <button type="button" onclick="switchControlView('tab-panel-quiz', this)" class="tab-trigger py-4 text-xs px-4 flex items-center justify-center gap-2">
                <i class="fa-solid fa-layer-group text-[13px]"></i> Update Quiz/Assessment
            </button>
        </div>

        <div id="tab-panel-info" class="tab-content-view block">
            <div class="bb-card-sharp p-8 bg-white">
                
                <div class="border-3 border-red-600 bg-red-50 p-5 mb-8 shadow-[4px_4px_0px_#dc2626]">
                    <div class="flex items-start gap-3">
                        <div class="text-red-600 text-xl pt-0.5">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-wider text-red-700">Quiz Update Option is not available at this moment</h3>
                            <p class="text-[11px] font-bold text-red-600 uppercase tracking-wide mt-1">
                                Notice: Configuration parameters are restricted within this instance to avoid data structure conflicts on running quizzes. Content structural updates can only be executed prior to dispatching access tokens.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-b-2 border-slate-900 pb-4 mb-6">
                    <h2 class="text-lg font-black uppercase tracking-tight text-slate-900">Core Parameters Overview</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Structural time constraints and operational reference names for this active record context.</p>
                </div>

                <div class="space-y-6 max-w-2xl">
                    <div class="border-2 border-slate-900 p-4 bg-slate-50 shadow-[3px_3px_0px_#000]">
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-1">Assessment Title / Reference Name</span>
                        <div class="text-sm font-black text-slate-900 uppercase italic"><?= htmlspecialchars($test['title']) ?></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="border-2 border-slate-900 p-4 bg-slate-50 shadow-[3px_3px_0px_#000]">
                            <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-1">Duration Parameters</span>
                            <div class="text-sm font-mono font-black text-slate-900"><?= (int)$test['duration_minutes'] ?> Minutes</div>
                        </div>
                        <div class="border-2 border-slate-900 p-4 bg-slate-50 shadow-[3px_3px_0px_#000]">
                            <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-1">Closing Lock Expiry Date/Time</span>
                            <div class="text-sm font-mono font-black text-slate-900">
                                <?= $test['end_time'] ? date('d M, Y \a\t h:i A', strtotime($test['end_time'])) : 'No Fixed Deadline' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="tab-panel-students" class="tab-content-view hidden">
            <div class="bb-card-sharp p-8 bg-white">
                <div class="border-b-2 border-slate-900 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-black uppercase tracking-tight text-slate-900">Enrolled Candidates Array Pipeline</h2>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Select or remove student users authorized to enter this specific testing terminal instance.</p>
                    </div>
                    <input type="text" id="studentRosterSearch" onkeyup="filterStudentSelectionGrid()" placeholder="Quick filter profiles..." class="p-2.5 text-xs font-bold input-bb-sharp max-w-xs">
                </div>

                <form method="POST" action="update-test-students.php?id=<?= $test_id ?>" id="studentEnrollmentMasterForm">
                    <input type="hidden" name="backup_test_id" value="<?= $test_id ?>">

                    <div class="max-w-full overflow-y-auto max-h-[450px] border-2 border-slate-900 p-2 bg-slate-50 mb-6 scrollbar-width-thin">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest">
                                    <th class="p-3 text-center border border-slate-900 w-12">Grant</th>
                                    <th class="p-3 border border-slate-900">Full Name</th>
                                    <th class="p-3 border border-slate-900">Enrolment ID</th>
                                    <th class="p-3 border border-slate-900">Department</th>
                                    <th class="p-3 border border-slate-900">Email Address</th>
                                </tr>
                            </thead>
                            <tbody id="studentTargetTableBody">
                                <?php foreach($all_students as $student): 
                                    $isChecked = in_array($student['id'], $enrolled_student_ids);
                                ?>
                                    <tr class="student-row-item bg-white text-xs font-bold text-slate-800 border-b border-slate-200 hover:bg-indigo-50/40"
                                        data-search-blob="<?= strtolower(htmlspecialchars($student['name'] . ' ' . $student['reg_no'] . ' ' . $student['department'])) ?>">
                                        <td class="p-3 border border-slate-200 text-center">
                                            <input type="checkbox" name="student_ids[]" value="<?= $student['id'] ?>" <?= $isChecked ? 'checked' : '' ?> class="w-4 h-4 accent-indigo-600 border-2 border-slate-900">
                                        </td>
                                        <td class="p-3 border border-slate-200 font-black text-slate-900 uppercase italic"><?= htmlspecialchars($student['name']) ?></td>
                                        <td class="p-3 border border-slate-200 font-mono text-[11px] text-indigo-600"><?= htmlspecialchars($student['reg_no']) ?></td>
                                        <td class="p-3 border border-slate-200 text-[10px] uppercase font-black tracking-wider text-slate-500"><?= htmlspecialchars($student['department']) ?></td>
                                        <td class="p-3 border border-slate-200 text-slate-400 font-medium font-mono lowercase"><?= htmlspecialchars($student['email']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total System Pool Size: <?= count($all_students) ?> Profiles</p>
                        <button type="submit" class="btn-black-sharp px-8 py-4 text-xs tracking-widest">
                            <i class="fa-solid fa-user-check mr-2"></i> Save Enrolled Student Roster
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="tab-panel-quiz" class="tab-content-view hidden">
            <div class="bb-card-sharp p-8 bg-white">
                
                <div class="border-3 border-red-600 bg-red-50 p-5 mb-8 shadow-[4px_4px_0px_#dc2626]">
                    <div class="flex items-start gap-3">
                        <div class="text-red-600 text-xl pt-0.5">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-wider text-red-700">Quiz Update Option is not available at this moment</h3>
                            <p class="text-[11px] font-bold text-red-600 uppercase tracking-wide mt-1">
                                Notice: Question modifications are restricted within this instance to avoid data structure conflicts on running quizzes. Content structural updates can only be executed prior to dispatching access tokens.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-b-2 border-slate-900 pb-4 mb-6">
                    <h2 class="text-lg font-black uppercase tracking-tight text-slate-900">Linked Question Manifest</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Reviewing active questionnaires assigned to candidate session parameters.</p>
                </div>

                <?php if (count($questions) === 0): ?>
                    <div class="p-12 text-center border-2 border-dashed border-slate-300 bg-slate-50">
                        <div class="w-12 h-12 bg-white border-2 border-slate-900 rounded flex items-center justify-center text-xl font-black mx-auto mb-3 shadow-[2px_2px_0px_#000]">
                            <i class="fa-solid fa-folder-open text-slate-400"></i>
                        </div>
                        <h4 class="text-xs font-black uppercase text-slate-800 tracking-wider">Question Bank Empty</h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">No questions mapped to this resource node framework.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach($questions as $index => $q): ?>
                            <div class="border-2 border-slate-900 p-5 bg-white relative shadow-[3px_3px_0px_#0f172a]">
                                <div class="flex justify-between items-center mb-3 border-b border-slate-100 pb-2">
                                    <span class="text-[10px] font-black uppercase tracking-widest font-mono text-indigo-600">Question Schema Matrix #<?= ($index + 1) ?></span>
                                    <span class="text-[9px] font-black uppercase bg-slate-900 text-white px-2 py-0.5 border border-slate-900 shadow-[1px_1px_0px_#000] tracking-widest">Read Only</span>
                                </div>
                                <h4 class="text-xs font-black text-slate-900 uppercase tracking-tight mb-3"><?= htmlspecialchars($q['question_text']) ?></h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] font-bold text-slate-500">
                                    <div class="p-2 border border-slate-100 bg-slate-50 rounded <?= isset($q['correct_option']) && $q['correct_option'] === 'a' ? 'border-emerald-500 !bg-emerald-50 text-emerald-800 font-extrabold' : '' ?>">A: <?= htmlspecialchars($q['a'] ?? $q['option_a'] ?? '') ?></div>
                                    <div class="p-2 border border-slate-100 bg-slate-50 rounded <?= isset($q['correct_option']) && $q['correct_option'] === 'b' ? 'border-emerald-500 !bg-emerald-50 text-emerald-800 font-extrabold' : '' ?>">B: <?= htmlspecialchars($q['b'] ?? $q['option_b'] ?? '') ?></div>
                                    <div class="p-2 border border-slate-100 bg-slate-50 rounded <?= isset($q['correct_option']) && $q['correct_option'] === 'c' ? 'border-emerald-500 !bg-emerald-50 text-emerald-800 font-extrabold' : '' ?>">C: <?= htmlspecialchars($q['c'] ?? $q['option_c'] ?? '') ?></div>
                                    <div class="p-2 border border-slate-100 bg-slate-50 rounded <?= isset($q['correct_option']) && $q['correct_option'] === 'd' ? 'border-emerald-500 !bg-emerald-50 text-emerald-800 font-extrabold' : '' ?>">D: <?= htmlspecialchars($q['d'] ?? $q['option_d'] ?? '') ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
// Keep the "Update Students" tab open automatically if they just saved a roster entry
window.addEventListener('DOMContentLoaded', () => {
    <?php if ($success_message): ?>
        const studentTabBtn = document.getElementById('trigger-tab-students');
        if (studentTabBtn) {
            switchControlView('tab-panel-students', studentTabBtn);
        }
    <?php endif; ?>
});

// Non-Reloading Tab Control Layout Pipeline Engine
function switchControlView(targetPanelId, activeTriggerButton) {
    // Hide all panels safely
    const panels = document.querySelectorAll('.tab-content-view');
    panels.forEach(p => {
        p.classList.remove('block');
        p.classList.add('hidden');
    });

    // Display active target panel container
    const activePanel = document.getElementById(targetPanelId);
    if(activePanel) {
        activePanel.classList.remove('hidden');
        activePanel.classList.add('block');
    }

    // Standardize all state trigger buttons
    const triggers = document.querySelectorAll('.tab-trigger');
    triggers.forEach(t => {
        t.classList.remove('active-tab-state');
    });

    // Elevate target click state visually
    activeTriggerButton.classList.add('active-tab-state');
}

// Client Side Table Filter Pipeline for Student Selection Panel Matrix
function filterStudentSelectionGrid() {
    const inputVal = document.getElementById('studentRosterSearch').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.student-row-item');

    rows.forEach(row => {
        const textBlob = row.getAttribute('data-search-blob');
        if (textBlob.includes(inputVal)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>