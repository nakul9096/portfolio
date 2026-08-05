<?php
require_once '../includes/header.php';

// Access Guard - Enforce clear authority structure
if ($_SESSION['role'] !== 'teacher') {
    header("Location: ./dashboard.php");
    exit;
}

$test_id = (int)($_GET['test_id'] ?? 0);
if ($test_id <= 0) {
    echo "<script>alert('Invalid test ID'); window.location.href='./dashboard.php';</script>";
    exit;
}

// Fetch Master Test Record Context
$stmt = $pdo->prepare("SELECT title, end_time, duration_minutes FROM tests WHERE id = ? AND teacher_id = ?");
$stmt->execute([$test_id, $_SESSION['user_id']]);
$test = $stmt->fetch();

if (!$test) {
    echo "<script>alert('Test not found.'); window.location.href='./dashboard.php';</script>";
    exit;
}

// Extract Enrolled Candidate Manifest Matrix
$stmt = $pdo->prepare("
    SELECT 
        u.id, u.name, u.email, u.reg_no, u.department, u.password,
        COALESCE((SELECT COUNT(*) FROM email_logs 
                  WHERE test_id = ? AND student_id = u.id), 0) as sent_count
    FROM test_candidates tc 
    JOIN users u ON tc.student_id = u.id 
    WHERE tc.test_id = ?
    ORDER BY u.name ASC
");
$stmt->execute([$test_id, $test_id]);
$students = $stmt->fetchAll();

// Dynamic URL Detection Core Engine
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
$portalUrl = $protocol . $domainName . "/ycce";
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
    
    body { background-color: #f8fafc; font-family: 'Inter', sans-serif; color: #0f172a; }
    
    /* Neo-Brutalist Layout Components */
    .bb-card-sharp { 
        background: #ffffff; 
        border: 3px solid #0f172a; 
        box-shadow: 6px 6px 0px #0f172a; 
    }
    
    .btn-send-sharp { 
        background: #0f172a; 
        color: #ffffff; 
        border: 2px solid #0f172a; 
        box-shadow: 3px 3px 0px #6366f1; 
        transition: all 0.15s ease; 
        cursor: pointer; 
    }
    .btn-send-sharp:hover { 
        background: #6366f1; 
        transform: translate(-2px, -2px); 
        box-shadow: 5px 5px 0px #0f172a; 
    }
    
    .btn-sent-sharp { 
        background: #f0fdf4; 
        color: #166534; 
        border: 2px solid #15803d; 
        box-shadow: 3px 3px 0px #15803d; 
        transition: all 0.15s ease; 
        cursor: pointer; 
    }
    .btn-sent-sharp:hover {
        background: #dcfce7;
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
    
    .badge-sharp { 
        border: 2px solid #0f172a; 
        background: #ffffff; 
        font-weight: 900; 
        text-transform: uppercase; 
        box-shadow: 3px 3px 0px #0f172a;
    }
    
    .label-badge {
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    .input-bb-sharp {
        border: 3px solid #0f172a;
        box-shadow: 4px 4px 0px #0f172a;
        transition: all 0.2s ease;
    }
    .input-bb-sharp:focus {
        box-shadow: 5px 5px 0px #6366f1;
        outline: none;
    }
</style>

<div class="min-h-screen py-12 px-6 bg-slate-50">
    <div class="max-w-7xl mx-auto">
        
        <div class="mb-6 flex justify-start">
            <a href="./dashboard.php" class="btn-outline-bb px-5 py-3 text-xs uppercase flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-[11px]"></i> Back to Dashboard
            </a>
        </div>
        
        <div class="bb-card-sharp p-8 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-white">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="label-badge bg-indigo-600 text-white px-2 py-0.5 border border-slate-900 shadow-[1px_1px_0px_#000]">Secure Dispatcher</span>
                    <span class="label-badge text-slate-400 font-bold">Node Base: <?= htmlspecialchars($portalUrl) ?></span>
                </div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight uppercase italic">Send Credentials Hub</h1>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">
                    TARGET ASSESSMENT: <span class="text-indigo-600 font-black"><?= htmlspecialchars($test['title']) ?></span>
                </p>
            </div>
            
            <div class="badge-sharp flex items-center gap-3 py-3.5 px-6 text-xs text-slate-900">
                <i class="fa-solid fa-users-viewfinder text-sm text-indigo-600"></i>
                <span><span id="renderedCount"><?= count($students) ?></span> / <?= count($students) ?> Candidates Enrolled</span>
            </div>
        </div>

        <div class="mb-10 max-w-md">
            <div class="relative">
                <input type="text" 
                       id="candidateSearchInput" 
                       onkeyup="executeLiveFilter()" 
                       placeholder="Search by name, email, enrolment ID or department..." 
                       class="w-full p-4 pl-12 font-bold text-sm bg-white input-bb-sharp text-slate-900 placeholder-slate-400">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>
        </div>

        <div id="emptySearchState" class="hidden bb-card-sharp p-12 text-center bg-white mb-10">
            <div class="w-16 h-16 border-2 border-slate-900 bg-amber-50 text-slate-900 flex items-center justify-center text-2xl font-black mx-auto mb-4 shadow-[3px_3px_0px_#0f172a]">
                <i class="fa-solid fa-user-slash"></i>
            </div>
            <h3 class="text-lg font-black uppercase tracking-tight text-slate-900">No Candidates Matched</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Refine your active search filters and parameter arrays.</p>
        </div>

        <div id="candidatesGridContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($students as $student): 
                $sentCount = (int)$student['sent_count'];
                $isSent = $sentCount >= 1;

                $endTimeText = $test['end_time'] 
                    ? date('d M, Y \a\t h:i A', strtotime($test['end_time'])) 
                    : 'Session Active';

                // Assemble message payload details
                $body = "OFFICIAL EXAMINATION NOTICE - YCCE NAGPUR\n";
                $body .= "--------------------------------------------------\n";
                $body .= "Hello " . htmlspecialchars($student['name']) . ",\n\n";

                $body .= "You have been officially enrolled in the following assessment:\n";
                $body .= "ASSESSMENT TITLE : " . htmlspecialchars($test['title']) . "\n";
                $body .= "DURATION         : " . $test['duration_minutes'] . " Minutes\n";
                $body .= "BRANCH/DEPT      : " . htmlspecialchars($student['department']) . "\n";
                $body .= "END DATE/TIME    : " . $endTimeText . "\n\n";

                $body .= "SECURE ACCESS CREDENTIALS:\n";
                $body .= "--------------------------------------------------\n";
                $body .= "PORTAL URL       : " . $portalUrl . "\n";
                $body .= "ENROLMENT ID     : " . htmlspecialchars($student['reg_no']) . "\n";
                $body .= "USERNAME (MAIL)  : " . htmlspecialchars($student['email']) . "\n";
                $body .= "SECURITY PASS    : " . htmlspecialchars($student['password']) . "\n\n";

                $body .= "IMPORTANT INSTRUCTIONS FOR STUDENTS:\n";
                $body .= "• Ensure You are connected to the college's LAN / Wifi Connection.\n";
                $body .= "• Do not refresh or close the browser during the test.\n";
                $body .= "• The timer cannot be paused under any circumstances.\n";
                $body .= "• Once the test is submitted, answers cannot be changed.\n\n";

                $body .= "VIOLATION POLICY:\n";
                $body .= "--------------------------------------------------\n";
                $body .= "Any refresh, tab switch, or moving outside the active test window \n";
                $body .= "will be considered a violation. You are allowed up to 3 violations; \n";
                $body .= "after the third violation, the test will be AUTOMATICALLY SUBMITTED.\n\n";

                $body .= "SYSTEM CONTROL UNIT\n";
                $body .= "YCCE IT EXAMINATION SYSTEM";

                $gmailLink = "https://mail.google.com/mail/?view=cm&fs=1&to=" . 
                             rawurlencode($student['email']) . 
                             "&su=" . rawurlencode("CRITICAL: Examination Credentials - " . $test['title']) . 
                             "&body=" . rawurlencode($body);
            ?>
                <div class="candidate-card bb-card-sharp p-6 flex flex-col justify-between bg-white relative overflow-hidden transition-all group hover:scale-[1.01]"
                     data-name="<?= strtolower(htmlspecialchars($student['name'])) ?>"
                     data-email="<?= strtolower(htmlspecialchars($student['email'])) ?>"
                     data-reg="<?= strtolower(htmlspecialchars($student['reg_no'])) ?>"
                     data-dept="<?= strtolower(htmlspecialchars($student['department'])) ?>">
                    
                    <?php if ($isSent): ?>
                        <div class="absolute top-0 right-0 bg-emerald-500 text-white border-b-2 border-l-2 border-slate-900 px-3 py-1 text-[9px] font-black tracking-widest uppercase shadow-sm">
                            <i class="fa-solid fa-square-check mr-1"></i> Dispatched
                        </div>
                    <?php endif; ?>

                    <div>
                        <div class="mb-4">
                            <span class="text-[9px] font-mono font-bold text-indigo-600 bg-indigo-50 border border-indigo-200 px-1.5 py-0.5 rounded uppercase">Candidate Profile</span>
                            <h3 class="font-black text-slate-900 uppercase tracking-tight text-md mt-1 truncate target-search-name">
                                <?= htmlspecialchars($student['name']) ?>
                            </h3>
                            <p class="text-[10px] font-bold text-slate-400 lowercase tracking-tight truncate mt-0.5">
                                <?= htmlspecialchars($student['email']) ?>
                            </p>
                        </div>

                        <div class="border-t-2 border-dashed border-slate-200 pt-4 mb-6 space-y-2.5">
                            <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-wider">
                                <span class="text-slate-400">Enrolment ID</span>
                                <span class="text-slate-900 bg-slate-100 border border-slate-300 px-1.5 py-0.5 font-mono text-[9px] font-black"><?= htmlspecialchars($student['reg_no']) ?></span>
                            </div>
                            <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-wider">
                                <span class="text-slate-400">Department</span>
                                <span class="text-slate-900 font-extrabold"><?= htmlspecialchars($student['department']) ?></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <a href="<?= $gmailLink ?>" 
                           target="_blank"
                           onclick="handleEmailClick(this, <?= $test_id ?>, <?= $student['id'] ?>)"
                           class="block w-full text-center py-3.5 text-xs font-black uppercase tracking-wider <?= $isSent ? 'btn-sent-sharp' : 'btn-send-sharp' ?> flex items-center justify-center gap-2">
                            <i class="fa-solid <?= $isSent ? 'fa-share-from-square' : 'fa-paper-plane' ?> text-[11px]"></i>
                            <?= $isSent ? 'Dispatch Again' : 'Send Credentials' ?>
                        </a>

                        <?php if ($isSent): ?>
                            <div class="flex justify-center items-center gap-1.5 mt-3">
                                <span class="h-1 w-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                <p class="text-[9px] font-black uppercase tracking-widest text-emerald-600 font-mono">
                                    Logs Counter: [ 0<?= $sentCount ?> ]
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="flex justify-center items-center gap-1.5 mt-3">
                                <span class="h-1 w-1 rounded-full bg-amber-500"></span>
                                <p class="text-[9px] font-black uppercase tracking-widest text-amber-600 font-mono">
                                    Status: Pending Transmission
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-16">
            <a href="./dashboard.php" class="inline-flex items-center gap-2 border-b-2 border-slate-900 text-slate-900 font-black text-xs uppercase tracking-[0.2em] pb-1 hover:text-indigo-600 hover:border-indigo-600 transition-all">
                <i class="fa-solid fa-arrow-left-long text-[10px]"></i> Return to Operational Dashboard
            </a>
        </div>
    </div>
</div>

<script>
function executeLiveFilter() {
    const query = document.getElementById('candidateSearchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.candidate-card');
    const emptyState = document.getElementById('emptySearchState');
    const displayCounter = document.getElementById('renderedCount');
    
    let matchCounter = 0;

    cards.forEach(card => {
        const name = card.getAttribute('data-name');
        const email = card.getAttribute('data-email');
        const reg = card.getAttribute('data-reg');
        const dept = card.getAttribute('data-dept');

        if (name.includes(query) || email.includes(query) || reg.includes(query) || dept.includes(query)) {
            card.style.display = 'flex';
            matchCounter++;
        } else {
            card.style.display = 'none';
        }
    });

    displayCounter.textContent = matchCounter;

    if (matchCounter === 0 && cards.length > 0) {
        emptyState.style.display = 'block';
    } else {
        emptyState.style.display = 'none';
    }
}

function handleEmailClick(btn, testId, studentId) {
    btn.style.transform = "translate(1px, 1px)";
    btn.style.boxShadow = "none";
    
    setTimeout(() => {
        window.location.href = `update-email-count.php?test_id=${testId}&student_id=${studentId}`;
    }, 500);
}
</script>

<?php require_once '../includes/footer.php'; ?>