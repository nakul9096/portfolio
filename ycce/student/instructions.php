<?php 
require_once '../includes/header.php'; 

$user_id = $_SESSION['user_id'] ?? 0; 
$test_id = $_GET['id'] ?? null;

if (!$test_id) {
    header("Location: dashboard.php");
    exit;
}

$stmt = $pdo->prepare("SELECT t.*, u.name as teacher_name FROM tests t JOIN users u ON t.teacher_id = u.id WHERE t.id = ?");
$stmt->execute([$test_id]);
$test = $stmt->fetch();

if (!$test) {
    die("Assessment not found.");
}
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

    .btn-action:disabled, .btn-action.disabled-link {
        background: #cbd5e1 !important;
        border-color: #94a3b8 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        transform: none !important;
        color: #64748b !important;
        pointer-events: none;
    }

    .btn-action:not(.disabled-link):hover { 
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
    <nav class="bg-white border-b-2 border-slate-900 px-6 py-3 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-blue-600 border-2 border-slate-900 flex items-center justify-center text-white font-extrabold text-xs shadow-[1px_1px_0px_#000]">i</div>
                <span class="font-extrabold text-slate-900 text-xs uppercase tracking-tight">Exam Rules</span>
            </div>
            <a href="dashboard.php" class="text-slate-500 hover:text-red-600 text-[10px] font-extrabold uppercase tracking-wider">Back to Dashboard</a>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto px-6 mt-6 mb-10">
        <div class="dashboard-card p-5 mb-6 bg-white">
            <span class="label-badge bg-amber-100 text-amber-900 border border-amber-400 px-2 py-0.5 rounded-none text-[9px]">Ready</span>
            <h1 class="text-2xl font-extrabold text-slate-900 uppercase tracking-tight mt-2 mb-2">
                <?= htmlspecialchars($test['title'], ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <span>Teacher: <strong class="text-slate-700"><?= htmlspecialchars($test['teacher_name'], ENT_QUOTES, 'UTF-8') ?></strong></span>
                <span>•</span>
                <span>Time: <strong class="text-slate-700"><?= (int)$test['duration_minutes'] ?> Minutes</strong></span>
            </div>
        </div>

        <div class="dashboard-card overflow-hidden mb-6">
            <div class="bg-slate-900 text-white px-5 py-2.5 border-b-2 border-slate-900">
                <h2 class="text-[10px] font-extrabold uppercase tracking-wider">Important Rules</h2>
            </div>
            <div class="p-5 space-y-4">
                <div class="flex gap-3">
                    <span class="font-extrabold text-blue-600 text-sm">01.</span>
                    <p class="text-xs font-medium text-slate-700 leading-relaxed">
                        Do <span class="bg-red-100 text-red-700 font-bold px-1 border border-red-200">NOT refresh</span> the page or close the assessment window once the test begins. This will submit your exam automatically.
                    </p>
                </div>
                
                <div class="flex gap-3 border-t border-slate-100 pt-4">
                    <span class="font-extrabold text-blue-600 text-sm">02.</span>
                    <p class="text-xs font-medium text-slate-700 leading-relaxed">Do not change tabs or open other windows during the test. The system will detect it and register a violation.</p>
                </div>

                <div class="flex gap-3 border-t border-slate-100 pt-4">
                    <span class="font-extrabold text-blue-600 text-sm">03.</span>
                    <p class="text-xs font-medium text-slate-700 leading-relaxed">When the exam screen loads, clicking anywhere on the workspace will activate <span class="underline decoration-blue-500 font-bold">Full Screen Mode</span>.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center gap-4">
            <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" id="agree-checkbox" class="w-4 h-4 border-2 border-slate-900 rounded-none checked:bg-blue-600 cursor-pointer">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 group-hover:text-slate-900 transition-colors">
                    I agree to follow the exam rules
                </span>
            </label>

            <a id="start-test-btn" href="take-test.php?id=<?= (int)$test['id'] ?>" target="_blank" rel="noopener noreferrer"
               class="w-full btn-action disabled-link py-3.5 font-extrabold uppercase tracking-widest justify-center">
                Start Exam
            </a>
            
            <p class="text-[8px] font-mono font-bold text-slate-400 uppercase tracking-widest text-center">
                ID:  <?= md5($user_id . time()) ?>
            </p>
        </div>
    </div>
</div>

<hr class="dashboard-footer-divider">

<script>
    const checkbox = document.getElementById('agree-checkbox');
    const startBtn = document.getElementById('start-test-btn');

    checkbox.addEventListener('change', function() {
        if (this.checked) {
            startBtn.classList.remove('disabled-link');
        } else {
            startBtn.classList.add('disabled-link');
        }
    });

    startBtn.addEventListener('click', function() {
        setTimeout(() => {
            window.location.replace("dashboard.php");
        }, 1000);
    });
</script>

<?php 
require_once '../includes/footer.php'; 
?>