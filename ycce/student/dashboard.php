<?php 
require_once '../includes/header.php'; 

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT reg_no, department FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$meta = $stmt->fetch();

$reg = !empty($meta['reg_no']) ? $meta['reg_no'] : "YCCE-" . str_pad($user_id, 4, '0', STR_PAD_LEFT);
$dept = !empty($meta['department']) ? $meta['department'] : "Engineering & Technology";

$stmt_active = $pdo->prepare("
    SELECT t.*, u.name as teacher_name,
           TIMESTAMPDIFF(SECOND, NOW(), t.start_time) as seconds_until_start,
           TIMESTAMPDIFF(SECOND, NOW(), t.end_time) as remaining_seconds
    FROM tests t 
    JOIN test_candidates tc ON t.id = tc.test_id 
    JOIN users u ON t.teacher_id = u.id 
    WHERE tc.student_id = ? 
      AND t.status = 'published' 
      AND (t.end_time >= NOW() OR t.end_time IS NULL)
      AND NOT EXISTS (SELECT 1 FROM test_results WHERE test_id = t.id AND student_id = ?) 
    ORDER BY t.created_at DESC
");
$stmt_active->execute([$user_id, $user_id]);
$active_exams = $stmt_active->fetchAll();

$stmt_ended = $pdo->prepare("
    SELECT t.*, u.name as teacher_name 
    FROM tests t 
    JOIN test_candidates tc ON t.id = tc.test_id 
    JOIN users u ON t.teacher_id = u.id 
    WHERE tc.student_id = ? 
      AND (t.status = 'published' OR t.status = 'completed') 
      AND (t.end_time < NOW() AND t.end_time IS NOT NULL)
      AND NOT EXISTS (SELECT 1 FROM test_results WHERE test_id = t.id AND student_id = ?) 
    ORDER BY t.end_time DESC
");
$stmt_ended->execute([$user_id, $user_id]);
$ended_exams = $stmt_ended->fetchAll();

$stmt_locked = $pdo->prepare("SELECT t.* FROM tests t WHERE t.status = 'published' AND t.id NOT IN (SELECT test_id FROM test_candidates WHERE student_id = ?) AND t.id NOT IN (SELECT test_id FROM test_results WHERE student_id = ?)");
$stmt_locked->execute([$user_id, $user_id]);
$restricted = $stmt_locked->fetchAll();

$stmt_history = $pdo->prepare("SELECT t.title, tr.score, tr.total_marks, tr.percentage, tr.submitted_at FROM test_results tr JOIN tests t ON tr.test_id = t.id WHERE tr.student_id = ? ORDER BY tr.submitted_at DESC");
$stmt_history->execute([$user_id]);
$history = $stmt_history->fetchAll();
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght=400;500;700;800&display=swap');
    
    html, body { 
        height: 100%; 
        margin: 0; 
        padding: 0;
    }
    
    body { 
        font-family: 'Inter', sans-serif; 
        background-color: #f4f5f7; 
        color: #1e293b; 
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .main-content { 
        flex: 1 0 auto; 
        width: 100%;
    }

    .dashboard-card { 
        background: #ffffff; 
        border: 2px solid #0f172a; 
        box-shadow: 4px 4px 0px #0f172a; 
    }

    .btn-action { 
        background: #2563eb;
        color: #ffffff; 
        font-weight: 700;
        border: 2px solid #0f172a; 
        box-shadow: 3px 3px 0px #0f172a; 
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.1s ease;
        text-decoration: none;
    }

    .btn-action:not(:disabled):not(.btn-started-grey):hover { 
        transform: translate(1px, 1px);
        box-shadow: 2px 2px 0px #0f172a;
        background: #1d4ed8;
    }

    .btn-action:disabled {
        background: #cbd5e1 !important;
        border-color: #94a3b8 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        transform: none !important;
        color: #64748b !important;
    }

    .btn-started-grey {
        background: #cbd5e1 !important;
        border-color: #0f172a !important;
        color: #475569 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        transform: none !important;
    }

    .label-badge { 
        font-size: 10px; 
        font-weight: 800; 
        text-transform: uppercase; 
        letter-spacing: 0.1em; 
    }

    .history-item.hidden { display: none; }

    .dashboard-footer-divider {
        border: 0;
        height: 2px;
        background-color: #0f172a;
        margin-top: auto;
        margin-bottom: 0;
        flex-shrink: 0;
    }

    footer, .footer, #footer {
        flex-shrink: 0 !important;
        margin-top: 0 !important;
    }

    .modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(2px);
        display: flex; align-items: center; justify-content: center;
        z-index: 1000; opacity: 0; pointer-events: none;
        transition: opacity 0.2s ease-in-out;
    }
    .modal-overlay.active { opacity: 1; pointer-events: auto; }
    .modal-container {
        width: 100%; max-width: 550px;
        transform: scale(0.95); transition: transform 0.2s ease-in-out;
    }
    .modal-overlay.active .modal-container { transform: scale(1); }

    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-thumb { background: #0f172a; }
</style>

<div class="main-content">
    <main class="max-w-6xl mx-auto px-6 py-10">
        
        <div class="dashboard-card p-6 mb-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-[#e2e8f0] border-2 border-[#0f172a] flex items-center justify-center text-[#0f172a] font-extrabold text-2xl shadow-[3px_3px_0px_#0f172a] flex-shrink-0 relative overflow-hidden">
                    <svg class="w-14 h-14 text-slate-400 absolute bottom-0" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12c0 2.624 1.035 5.006 2.715 6.753.303.313.738.446 1.148.367A13.233 13.233 0 0112 18c2.47 0 4.793.676 6.785 1.865a.75.75 0 00.75-.768zM12 5.25a3.75 3.75 0 100 7.5 3.75 3.75 0 000-7.5z" clip-rule="evenodd" />
                    </svg>
                    <span class="relative z-10 text-[#0f172a] font-extrabold"><?= strtoupper(substr($_SESSION['name'], 0, 1)) ?></span>
                </div>

                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 uppercase"><?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <div class="flex flex-wrap items-center gap-3 mt-1">
                        <span class="label-badge bg-slate-900 text-white px-2 py-0.5 text-[9px]"><?= $reg ?></span>
                        <span class="label-badge text-slate-500 font-bold"><?= htmlspecialchars($dept, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
            </div>
            
            <div class="px-3 py-1 border-2 border-slate-900 bg-emerald-50 text-emerald-800 flex items-center gap-2">
                <div class="w-2 h-2 bg-emerald-600 rounded-full"></div>
                <span class="text-[9px] font-extrabold uppercase tracking-wider">Logged In</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <div class="lg:col-span-2 space-y-10">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <h3 class="label-badge text-slate-900 text-xs font-extrabold border-2 border-slate-900 bg-white px-2 py-0.5">Your Exams</h3>
                        <div class="h-[2px] flex-grow bg-slate-900"></div>
                    </div>
                    
                    <div class="space-y-4">
                        <?php if (empty($active_exams)): ?>
                            <div class="dashboard-card border-dashed p-8 text-center text-slate-400">
                                <p class="label-badge">No active exams found for you.</p>
                            </div>
                        <?php else: foreach ($active_exams as $test): 
                            $is_upcoming = ((int)$test['seconds_until_start'] > 0);
                        ?>
                            <div class="dashboard-card p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4" id="exam-card-<?= $test['id'] ?>">
                                <div>
                                    <h4 class="font-extrabold text-lg uppercase text-slate-900 tracking-tight"><?= htmlspecialchars($test['title'], ENT_QUOTES, 'UTF-8') ?></h4>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="label-badge text-[9px] text-slate-400">FA:</span>
                                        <span class="text-xs font-bold uppercase text-slate-700"><?= htmlspecialchars($test['teacher_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="text-slate-300">•</span>
                                        <span class="text-xs font-mono font-bold bg-slate-100 px-1.5 py-0.5 border border-slate-300"><?= (int)$test['duration_minutes'] ?> MINS</span>
                                        <span class="text-slate-300">•</span>
                                        <?php if ($is_upcoming): ?>
                                            <span class="text-[10px] font-mono font-extrabold bg-blue-50 text-blue-800 border border-blue-300 px-1.5 py-0.5 uppercase tracking-tight live-countdown" id="countdown-el-<?= $test['id'] ?>" data-start-seconds="<?= (int)$test['seconds_until_start'] ?>" data-rem-seconds="<?= max(0, (int)$test['remaining_seconds']) ?>">
                                                Starts In: Calculating...
                                            </span>
                                        <?php elseif (!empty($test['end_time'])): ?>
                                            <span class="text-[10px] font-mono font-extrabold bg-amber-50 text-amber-800 border border-amber-300 px-1.5 py-0.5 uppercase tracking-tight live-countdown" id="countdown-el-<?= $test['id'] ?>" data-start-seconds="0" data-rem-seconds="<?= max(0, (int)$test['remaining_seconds']) ?>">
                                                Ends In: Calculating...
                                            </span>
                                        <?php else: ?>
                                            <span class="text-[10px] font-mono font-extrabold bg-blue-50 text-blue-800 border border-blue-300 px-1.5 py-0.5 uppercase tracking-tight">
                                                No Fixed End Time
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <button type="button" id="btn-trigger-<?= $test['id'] ?>"
                                    <?= $is_upcoming ? 'disabled' : '' ?>
                                    onclick="openRulesModal('<?= $test['id'] ?>', '<?= htmlspecialchars($test['title'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($test['teacher_name'], ENT_QUOTES, 'UTF-8') ?>', '<?= (int)$test['duration_minutes'] ?>')" 
                                    class="btn-action px-5 py-2.5 text-[10px] uppercase tracking-wider w-full sm:w-auto justify-center">
                                     Start Test <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                </button>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <h3 class="label-badge text-red-600 text-xs font-extrabold border-2 border-red-600 bg-red-50 px-2 py-0.5">Expired Exams</h3>
                        <div class="h-[2px] flex-grow bg-slate-900"></div>
                    </div>
                    
                    <div class="overflow-x-auto border-2 border-slate-900 shadow-[4px_4px_0px_#0f172a]">
                        <table class="w-full text-left bg-white border-collapse">
                            <thead>
                                <tr class="bg-slate-100 border-b-2 border-slate-900">
                                    <th class="p-3 text-[10px] font-black uppercase text-slate-900 tracking-wider">Exam Title</th>
                                    <th class="p-3 text-[10px] font-black uppercase text-slate-900 tracking-wider">Teacher</th>
                                    <th class="p-3 text-[10px] font-black uppercase text-slate-900 tracking-wider">End Time</th>
                                    <th class="p-3 text-[10px] font-black uppercase text-slate-900 tracking-wider text-center">Status</th>
                                </tr>
                            </thead>
                        </table>
                        <div style="max-height: 280px; overflow-y: auto;">
                            <table class="w-full text-left bg-white border-collapse">
                                <tbody class="divide-y-2 divide-slate-200 font-medium text-sm">
                                    <?php if (empty($ended_exams)): ?>
                                        <tr>
                                            <td colspan="4" class="p-6 text-center text-slate-400 label-badge tracking-wide">
                                                No expired exams found.
                                            </td>
                                        </tr>
                                    <?php else: foreach ($ended_exams as $ended): ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="p-3 font-extrabold uppercase text-slate-900 truncate max-w-[200px]">
                                                <?= htmlspecialchars($ended['title'], ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="p-3 font-bold uppercase text-slate-700 text-xs">
                                                <?= htmlspecialchars($ended['teacher_name'], ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="p-3 font-mono text-slate-500 text-xs">
                                                <?= date('d-m-Y H:i', strtotime($ended['end_time'])) ?>
                                            </td>
                                            <td class="p-3 text-center">
                                                <span class="label-badge bg-red-100 text-red-700 border border-red-400 px-2.5 py-0.5 font-bold whitespace-nowrap">
                                                    Ended
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <h3 class="label-badge text-slate-900 text-xs font-extrabold">Locked Exams</h3>
                        <div class="h-[1px] flex-grow bg-slate-300"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php if (empty($restricted)): ?>
                            <div class="col-span-full py-4 text-center border-2 border-dashed border-slate-300 text-slate-300 label-badge">
                                No locked exams.
                            </div>
                        <?php else: foreach ($restricted as $r): ?>
                            <div class="dashboard-card p-4 flex items-center justify-between opacity-60 bg-slate-50 border-slate-300 shadow-none">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-lock text-xs text-slate-400"></i>
                                    <div>
                                        <h4 class="font-bold text-slate-600 text-xs uppercase truncate max-w-[180px]"><?= htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8') ?></h4>
                                        <p class="text-[8px] font-mono uppercase text-slate-400">No Access</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center gap-3 mb-6">
                    <h3 class="label-badge text-slate-900 text-xs font-extrabold border-2 border-slate-900 bg-white px-2 py-0.5">Past Results</h3>
                    <div class="h-[2px] flex-grow bg-slate-900"></div>
                </div>
                
                <div class="dashboard-card divide-y-2 divide-slate-900 bg-white">
                    <?php if (empty($history)): ?>
                        <div class="p-8 text-center label-badge text-slate-400">No results found.</div>
                    <?php else: foreach ($history as $index => $c): ?>
                    <div class="history-item p-4 flex items-center justify-between <?= $index >= 5 ? 'hidden' : '' ?>">
                        <div class="min-w-0 pr-2">
                            <div class="font-extrabold text-xs uppercase tracking-tight truncate text-slate-800"><?= htmlspecialchars($c['title'], ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="text-[9px] font-mono text-slate-400 mt-0.5"><?= date('d-m-Y', strtotime($c['submitted_at'])) ?></div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="font-mono font-extrabold text-xs border-2 border-slate-900 bg-amber-100 px-2 py-0.5 shadow-[2px_2px_0px_#0f172a]">
                                <?= round($c['percentage']) ?>%
                            </span>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                    
                    <?php if (count($history) > 5): ?>
                    <button onclick="toggleHistory(this)" class="w-full py-3 bg-slate-50 text-[9px] font-extrabold text-slate-900 uppercase tracking-wider hover:bg-slate-100 transition-colors border-t-2 border-slate-900">
                        View All Results (<?= count($history) - 5 ?>)
                    </button>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>
</div>

<div id="rulesModal" class="modal-overlay" onclick="closeRulesModal(event)">
    <div class="modal-container px-4" onclick="event.stopPropagation()">
        <div class="dashboard-card bg-white overflow-hidden shadow-[6px_6px_0px_#0f172a]">
            
            <div class="bg-slate-900 text-white p-4 flex justify-between items-center border-b-2 border-slate-900">
                <div>
                    <span class="label-badge bg-amber-400 text-slate-900 px-2 py-0.5 border border-slate-900 rounded-none text-[8px] font-black">Verification Setup</span>
                    <h2 class="text-sm font-extrabold uppercase tracking-tight mt-1" id="modalTestTitle">Exam Rules</h2>
                </div>
                <button type="button" onclick="hideModalDirect()" class="text-white hover:text-red-400 font-extrabold text-xs uppercase font-mono bg-slate-800 border border-slate-700 px-2 py-1">Esc ✕</button>
            </div>

            <div class="p-5 border-b-2 border-slate-100 bg-slate-50 flex flex-wrap gap-x-6 gap-y-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <span>Teacher: <strong class="text-slate-700" id="modalTeacherName">-</strong></span>
                <span>•</span>
                <span>Duration: <strong class="text-slate-700" id="modalDuration">- Minutes</strong></span>
            </div>

            <div class="p-5 space-y-4 max-h-[250px] overflow-y-auto">
                <div class="flex gap-3">
                    <span class="font-extrabold text-blue-600 text-sm">01.</span>
                    <p class="text-xs font-medium text-slate-700 leading-relaxed">
                        Do <span class="bg-red-100 text-red-700 font-bold px-1 border border-red-200">NOT refresh</span> the page or change window configurations once the test starts. Any system disconnection will log timeout metrics immediately.
                    </p>
                </div>
                <div class="flex gap-3 border-t border-slate-100 pt-3">
                    <span class="font-extrabold text-blue-600 text-sm">02.</span>
                    <p class="text-xs font-medium text-slate-700 leading-relaxed">Do not open external tabs or navigate away from the test window. The platform monitors tab focus and log visibility flags.</p>
                </div>
                <div class="flex gap-3 border-t border-slate-100 pt-3">
                    <span class="font-extrabold text-blue-600 text-sm">03.</span>
                    <p class="text-xs font-medium text-slate-700 leading-relaxed">When the test window loads, entering the core area triggers full-screen window parameters automatically.</p>
                </div>
            </div>

            <div class="p-5 bg-slate-50 border-t-2 border-slate-900 flex flex-col items-center gap-4">
                <label class="flex items-center gap-3 cursor-pointer group w-full">
                    <input type="checkbox" id="agree-checkbox" class="w-4 h-4 border-2 border-slate-900 rounded-none checked:bg-blue-600 cursor-pointer">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 group-hover:text-slate-900 transition-colors">
                        I understand and agree to adhere to these rules.
                    </span>
                </label>

                <button id="start-test-btn" disabled onclick="confirmLaunchExam()" 
                        class="w-full btn-action py-3.5 font-extrabold uppercase tracking-widest justify-center">
                    Launch Exam Tab
                </button>
            </div>

        </div>
    </div>
</div>

<hr class="dashboard-footer-divider">

<script>
let targetedTestId = null;

function toggleHistory(btn) {
    const items = document.querySelectorAll('.history-item.hidden');
    items.forEach(item => {
        item.style.display = 'flex';
        item.classList.remove('hidden');
    });
    btn.remove();
}

const clientTimeAtLoad = Date.now();
const examTimeouts = {};

function openRulesModal(testId, title, teacherName, duration) {
    if (examTimeouts[testId] !== undefined && examTimeouts[testId] <= 0) {
        alert("Test Ended");
        window.location.reload();
        return;
    }
    targetedTestId = testId;
    document.getElementById('modalTestTitle').innerText = title;
    document.getElementById('modalTeacherName').innerText = teacherName;
    document.getElementById('modalDuration').innerText = duration + " Minutes";
    
    document.getElementById('agree-checkbox').checked = false;
    document.getElementById('start-test-btn').disabled = true;
    
    document.getElementById('rulesModal').classList.add('active');
}

function closeRulesModal(e) {
    if(e.target.id === 'rulesModal') hideModalDirect();
}

function hideModalDirect() {
    document.getElementById('rulesModal').classList.remove('active');
    targetedTestId = null;
}

document.getElementById('agree-checkbox').addEventListener('change', function() {
    document.getElementById('start-test-btn').disabled = !this.checked;
});

function confirmLaunchExam() {
    if(!targetedTestId) return;
    
    const triggerBtn = document.getElementById('btn-trigger-' + targetedTestId);
    if (triggerBtn) {
        triggerBtn.disabled = true;
        triggerBtn.classList.add('btn-started-grey');
        triggerBtn.innerHTML = 'Exam Started';
        triggerBtn.removeAttribute('onclick');
    }
    
    window.open("take-test.php?id=" + targetedTestId, "_blank", "noopener,noreferrer");
    hideModalDirect();
}

document.addEventListener('keydown', function(e) {
    if(e.key === 'Escape') hideModalDirect();
});

function initCountdowns() {
    const targets = document.querySelectorAll('.live-countdown');
    
    function updateTimers() {
        const elapsedMs = Date.now() - clientTimeAtLoad;
        
        targets.forEach(el => {
            const idMatch = el.id.match(/countdown-el-(.+)$/);
            if (!idMatch) return;
            const testId = idMatch[1];
            
            const initialStartSeconds = parseInt(el.getAttribute('data-start-seconds'), 10);
            const initialRemSeconds = parseInt(el.getAttribute('data-rem-seconds'), 10);
            
            const triggerBtn = document.getElementById('btn-trigger-' + testId);
            const currentStartDiff = (initialStartSeconds * 1000) - elapsedMs;
            
            if (currentStartDiff > 0) {
                if (triggerBtn) triggerBtn.disabled = true;
                
                el.classList.remove('bg-amber-50', 'text-amber-800', 'border-amber-300', 'bg-red-50', 'text-red-800', 'border-red-300');
                el.classList.add('bg-blue-50', 'text-blue-800', 'border-blue-300');
                
                const hours = Math.floor(currentStartDiff / (1000 * 60 * 60));
                const mins = Math.floor((currentStartDiff % (1000 * 60 * 60)) / (1000 * 60));
                const secs = Math.floor((currentStartDiff % (1000 * 60)) / 1000);
                
                let displayStr = "Starts In: ";
                if (hours > 0) displayStr += hours + "h ";
                displayStr += mins + "m " + secs + "s";
                el.textContent = displayStr;
                return;
            }
            
            if (triggerBtn && triggerBtn.disabled && !triggerBtn.classList.contains('btn-started-grey') && isNaN(examTimeouts[testId])) {
                triggerBtn.disabled = false;
            }
            
            if (isNaN(initialRemSeconds)) {
                el.textContent = "Live";
                return;
            }
            
            const currentEndDiff = (initialRemSeconds * 1000) - elapsedMs;
            examTimeouts[testId] = currentEndDiff;
            
            if (currentEndDiff <= 0) {
                el.textContent = "Ends In: Closed";
                el.classList.remove('bg-amber-50', 'text-amber-800', 'border-amber-300', 'bg-blue-50', 'text-blue-800', 'border-blue-300');
                el.classList.add('bg-red-50', 'text-red-800', 'border-red-300');
                
                if (triggerBtn && !triggerBtn.classList.contains('btn-started-grey')) {
                    triggerBtn.disabled = true;
                }
                return;
            }
            
            el.classList.remove('bg-blue-50', 'text-blue-800', 'border-blue-300', 'bg-red-50', 'text-red-800', 'border-red-300');
            el.classList.add('bg-amber-50', 'text-amber-800', 'border-amber-300');
            
            const hours = Math.floor(currentEndDiff / (1000 * 60 * 60));
            const mins = Math.floor((currentEndDiff % (1000 * 60 * 60)) / (1000 * 60));
            const secs = Math.floor((currentEndDiff % (1000 * 60)) / 1000);
            
            let displayStr = "Ends In: ";
            if (hours > 0) displayStr += hours + "h ";
            displayStr += mins + "m " + secs + "s";
            el.textContent = displayStr;
        });
    }
    
    updateTimers();
    setInterval(updateTimers, 1000);
}

document.addEventListener('DOMContentLoaded', initCountdowns);

</script>

<?php require_once '../includes/footer.php'; ?>