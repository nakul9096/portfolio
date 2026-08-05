<?php
require_once '../includes/header.php';

// Ensure session is active to securely manage test timing state
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$test_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// URL Cleanup mitigation: fallback to an active session ID if URL params were dynamically replaced
if ($test_id > 0) {
    $_SESSION['active_test_id'] = $test_id;
} elseif (isset($_SESSION['active_test_id'])) {
    $test_id = $_SESSION['active_test_id'];
}

if ($test_id <= 0) exit("Invalid Test ID");

$stmt = $pdo->prepare("SELECT * FROM tests WHERE id = ? AND status = 'published'");
$stmt->execute([$test_id]);
$test = $stmt->fetch();
if (!$test) exit("Test not found");

// --- SECURE SERVER SIDE TIMER CONTROL ---
$session_timer_key = "test_end_time_" . $test_id;
if (!isset($_SESSION[$session_timer_key])) {
    // Set absolute timestamp deadline on the server only once
    $_SESSION[$session_timer_key] = time() + ((int)$test['duration_minutes'] * 60);
}

// Compute accurate remaining seconds from server clock anchor
$remaining_seconds = $_SESSION[$session_timer_key] - time();
if ($remaining_seconds <= 0) {
    $remaining_seconds = 0;
}
// ----------------------------------------

$order_by = ($test['is_random_questions'] == 1) ? "RAND()" : "id ASC";
$limit = ($test['questions_to_show'] > 0) ? (int)$test['questions_to_show'] : 9999;

$id_stmt = $pdo->prepare("SELECT id FROM questions WHERE test_id = ? ORDER BY $order_by LIMIT $limit");
$id_stmt->execute([$test_id]);
$question_ids = $id_stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($question_ids)) {
    $questions = [];
    $shown_ids = '';
} else {
    $id_list = implode(',', array_map('intval', $question_ids));

    $query = "SELECT q.id as q_id, q.question_text, q.image_path, q.points, o.id as opt_id, o.option_text 
              FROM questions q
              LEFT JOIN options o ON q.id = o.question_id 
              WHERE q.id IN ($id_list)
              ORDER BY FIELD(q.id, $id_list) ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll();
}

$questions = [];
$actual_total_marks = 0;

if (!empty($results)) {
    foreach ($results as $row) {
        $qid = $row['q_id'];
        if (!isset($questions[$qid])) {
            $questions[$qid] = [
                'id' => $qid, 
                'text' => $row['question_text'], 
                'image_path' => $row['image_path'], 
                'points' => $row['points'],
                'options' => []
            ];
            $actual_total_marks += (int)$row['points'];
        }
        if ($row['opt_id']) {
            $questions[$qid]['options'][] = ['id' => $row['opt_id'], 'text' => $row['option_text']];
        }
    }
}

$questions = array_values($questions);
$shown_ids = implode(',', array_column($questions, 'id'));
?>
<style>
    body { background-color: #fff; margin: 0; font-family: 'Inter', sans-serif; height: 100vh; overflow: hidden; user-select: none; }
    .test-navbar { height: 70px; border-bottom: 1px solid #000; display: flex; align-items: center; justify-content: space-between; padding: 0 30px; background: #fff; }
    .app-viewport { display: flex; height: calc(100vh - 70px); }
    .questions-container { flex: 1; padding: 40px; overflow-y: auto; display: flex; flex-direction: column; align-items: center; }
    .question-card { display: none; width: 100%; max-width: 700px; max-height: calc(100vh - 250px); overflow-y: auto; padding-right: 10px; }
    .question-card.active { display: block; }
    .nav-sidebar { width: 320px; border-left: 1px solid #000; padding: 20px; background: #fafafa; display: flex; flex-direction: column; overflow-y: auto; }
    .dot { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid #000; cursor: pointer; font-weight: bold; font-size: 12px; }
    .dot.current { background: #000; color: #fff; border-color: #000; }
    .dot.answered { background: #10b981; color: #fff; border-color: #059669; }
    .dot.marked { background: #3b82f6; color: #fff; border-color: #2563eb; }
    .opt-label { display: flex; border: 1px solid #000; padding: 15px; margin-bottom: 10px; cursor: pointer; align-items: center; word-break: break-word; }
    .opt-label:hover { background: #f5f5f5; }
    .lang-btn { padding: 4px 10px; border: 1px solid #000; font-size: 10px; font-weight: bold; cursor: pointer; background: #fff; }
    .lang-btn.active { background: #000; color: #fff; }
    .legend-item { display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
    .legend-box { width: 16px; height: 16px; border: 1px solid #000; }
    .question-text-area { max-height: 200px; overflow-y: auto; margin-top: 16px; margin-bottom: 16px; padding-right: 8px; word-break: break-word; }
    .options-scroll-area { max-height: 300px; overflow-y: auto; padding-right: 8px; }
    .q-reference-image { max-height: 280px; width: auto; object-fit: contain; border: 2px solid #000; box-shadow: 3px 3px 0px #000; cursor: zoom-in; transition: transform 0.2s ease; }
    
    .zoom-overlay { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.85); z-index: 9999; justify-content: center; align-items: center; cursor: zoom-out; }
    .zoom-overlay.active { display: flex; }
    .zoom-image { max-width: 90%; max-height: 90%; object-fit: contain; border: 3px solid #fff; }

    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #888; border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: #555; }
</style>

<div id="imageZoomOverlay" class="zoom-overlay" onclick="closeImageZoom()">
    <img id="zoomedImageTarget" src="" alt="Zoomed Reference Entry" class="zoom-image">
</div>

<header class="test-navbar">
    <div class="flex items-center gap-4">
        <h1 class="font-bold text-sm uppercase"><?= htmlspecialchars($test['title'], ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="bg-black text-white px-2 py-1 text-[10px] font-bold">TOTAL MARKS: <?= $actual_total_marks ?></div>
        <div class="flex gap-1 ml-4">
            <button class="lang-btn active" onclick="setLanguage('en')">EN</button>
            <button class="lang-btn" onclick="setLanguage('mr')">MR</button>
            <button class="lang-btn" onclick="setLanguage('hi')">HI</button>
        </div>
    </div>
    <div class="flex items-center gap-6">
        <div class="text-right">
            <span class="text-[9px] font-bold uppercase block" id="lbl-time-left">Time Left</span>
            <div id="timer" class="text-xl font-mono font-bold">00:00</div>
        </div>
        <button onclick="submitExam()" class="px-5 py-2 bg-red-600 text-white font-bold text-[10px] uppercase border border-black" id="btn-final-submit">Final Submit</button>
    </div>
</header>

<div class="app-viewport">
    <main class="questions-container">
        <form id="testForm" action="submit-test.php" method="POST" class="w-full flex flex-col items-center">
            <input type="hidden" name="test_id" value="<?= $test_id ?>">
            <input type="hidden" name="shown_ids" value="<?= $shown_ids ?>">
            <input type="hidden" name="submission_type" id="submission_type" value="student">

            <?php foreach ($questions as $i => $q): ?>
            <div id="q_card_<?= $i ?>" class="question-card <?= $i === 0 ? 'active' : '' ?>">
                <div class="mb-2">
                    <div class="flex justify-between items-center mb-2">
                         <span class="text-[10px] font-bold border border-black px-2 py-1 uppercase lbl-question">Question</span>
                         <span class="text-[10px] font-bold text-slate-500">Points: <?= $q['points'] ?></span>
                    </div>
                    <span class="font-bold"><?= $i + 1 ?> / <?= count($questions) ?></span>
                    <div class="question-text-area">
                        <p class="text-lg font-medium text-black"><?= htmlspecialchars($q['text'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>

                <?php if (!empty($q['image_path'])): ?>
                <div class="mb-6 flex justify-center w-full bg-slate-50 p-3 border border-black">
                    <img src="../<?= htmlspecialchars($q['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="Reference Asset Entry" class="q-reference-image" onclick="openImageZoom(this.src)">
                </div>
                <?php endif; ?>
                
                <div class="options-scroll-area space-y-2">
                    <?php foreach ($q['options'] as $opt): ?>
                    <label class="opt-label">
                        <input type="radio" name="q_<?= $q['id'] ?>" value="<?= $opt['id'] ?>" onchange="markAnswered(<?= $i ?>)">
                        <span class="ml-4"><?= htmlspecialchars($opt['text'], ENT_QUOTES, 'UTF-8') ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-6 flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold uppercase select-none text-blue-600">
                        <input type="checkbox" id="review_check_<?= $i ?>" onclick="toggleReviewCheckbox(<?= $i ?>)" class="w-4 h-4 accent-blue-600 border border-black">
                        <span class="btn-review">Review Later</span>
                    </label>
                    
                    <button type="button" onclick="clearChoice(<?= $i ?>, 'q_<?= $q['id'] ?>')" class="text-[10px] font-black uppercase text-red-500 hover:underline btn-clear">Clear Selection</button>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="mt-12 flex gap-4 w-full max-w-[700px]">
                <button type="button" id="prevBtn" onclick="changeQuestion(-1)" class="border border-black px-8 py-3 font-bold text-[10px] uppercase" style="visibility: hidden;">Previous</button>
                <button type="button" id="nextBtn" onclick="handleNext()" class="bg-black text-white px-8 py-3 font-bold text-[10px] uppercase ml-auto">Save & Next</button>
            </div>
        </form>
    </main>

    <aside class="nav-sidebar">
        <div class="mb-6 pb-6 border-b border-slate-200">
            <p class="text-[9px] font-bold uppercase mb-1" id="lbl-violations">Violations</p>
            <p id="violation-count" class="text-sm font-bold text-red-600 mb-2">0 / 3</p>
            <div class="flex gap-2">
                <div id="v_1" class="h-2 flex-1 bg-slate-200"></div>
                <div id="v_2" class="h-2 flex-1 bg-slate-200"></div>
                <div id="v_3" class="h-2 flex-1 bg-slate-200"></div>
            </div>
        </div>

        <h3 class="text-[10px] font-bold uppercase mb-4" id="lbl-navigator">Question Navigator</h3>
        <div class="flex flex-wrap gap-2 mb-6">
            <?php foreach ($questions as $i => $q): ?>
            <div id="dot_<?= $i ?>" onclick="goToQuestion(<?= $i ?>)" class="dot"><?= $i + 1 ?></div>
            <?php endforeach; ?>
        </div>

        <div class="pt-4 border-t border-slate-200 space-y-2.5">
            <p class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Status Legend</p>
            <div class="legend-item">
                <div class="legend-box bg-black"></div>
                <span>Current Question</span>
            </div>
            <div class="legend-item">
                <div class="legend-box bg-emerald-500 border-emerald-600"></div>
                <span>Answered</span>
            </div>
            <div class="legend-item">
                <div class="legend-box bg-blue-500 border-blue-600"></div>
                <span>Marked for Review</span>
            </div>
            <div class="legend-item">
                <div class="legend-box bg-white"></div>
                <span>Unvisited / Unsaved</span>
            </div>
        </div>
    </aside>
</div>

<script>
const TEST_ID = <?= $test_id ?>;
const totalQuestions = <?= count($questions) ?>;
const KEY_VIOLATIONS = 'exam_viol_' + TEST_ID;

// Secured value generated directly from the backend server timeline
let remainingSeconds = <?= $remaining_seconds ?>;
let currentIdx = 0;
let isSubmitting = false;
let violations = parseInt(localStorage.getItem(KEY_VIOLATIONS)) || 0;

// Dynamic check matching your replaceState operation
if (window.location.search.includes('id=')) {
    const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
    window.history.replaceState({path: cleanUrl}, '', cleanUrl);
}

function openImageZoom(src) {
    document.getElementById('zoomedImageTarget').src = src;
    document.getElementById('imageZoomOverlay').classList.add('active');
}

function closeImageZoom() {
    document.getElementById('imageZoomOverlay').classList.remove('active');
}

function triggerFullScreen() {
    const elem = document.documentElement;
    if (elem.requestFullscreen) elem.requestFullscreen().catch(()=>{});
    else if (elem.webkitRequestFullscreen) elem.webkitRequestFullscreen().catch(()=>{});
    else if (elem.msRequestFullscreen) elem.msRequestFullscreen().catch(()=>{});
}

window.onload = () => {
    initNavigationTrap();
    renderViolations();
    updateNavigation();
};

document.addEventListener('click', (e) => {
    if (e.target.closest('#imageZoomOverlay') || e.target.closest('.q-reference-image')) {
        return;
    }
    if (!document.fullscreenElement && !isSubmitting) {
        triggerFullScreen();
    }
}, { once: false });

document.addEventListener('fullscreenchange', () => {
    if (!document.fullscreenElement && !isSubmitting) {
        violations++;
        handleViolation();
    }
});

function initNavigationTrap() {
    window.history.pushState(null, null, window.location.href);
    window.history.pushState(null, null, window.location.href);
    
    window.addEventListener('popstate', function(event) {
        if (!isSubmitting) {
            window.history.pushState(null, null, window.location.href);
            alert("Attention: Navigating back during an ongoing assessment is strictly prohibited. Please use the interface options to complete your test safely.");
        }
    });

    window.addEventListener('beforeunload', function (e) {
        if (!isSubmitting) {
            const msg = "Attention: You are currently attempting to exit an ongoing assessment. Leaving or closing this tab before final submission might result in loss of progress or automatic tracking evaluation.";
            e.preventDefault();
            e.returnValue = msg;
            
            const formData = new FormData(document.getElementById('testForm'));
            formData.set('submission_type', 'timeout');
            navigator.sendBeacon('submit-test.php', formData);
            
            return msg;
        }
    });

    window.addEventListener('unload', function () {
        if (!isSubmitting) {
            isSubmitting = true;
            localStorage.removeItem(KEY_VIOLATIONS);
            
            const formData = new FormData(document.getElementById('testForm'));
            formData.set('submission_type', 'timeout');
            navigator.sendBeacon('submit-test.php', formData);
        }
    });
}

document.addEventListener("visibilitychange", () => {
    if (document.visibilityState === 'hidden' && !isSubmitting) {
        violations++;
        handleViolation();
    }
});

function handleViolation() {
    localStorage.setItem(KEY_VIOLATIONS, violations);
    renderViolations();
    if (violations >= 3) forceSubmit('violation');
}

function renderViolations() {
    for (let i = 1; i <= 3; i++) {
        const el = document.getElementById('v_' + i);
        if (el) el.className = 'h-2 flex-1 ' + (i <= violations ? 'bg-red-600' : 'bg-slate-200');
    }
    document.getElementById('violation-count').innerText = violations + ' / 3';
}

function forceSubmit(type = 'student') {
    isSubmitting = true;
    localStorage.removeItem(KEY_VIOLATIONS);
    if (document.fullscreenElement) document.exitFullscreen().catch(() => {});
    document.getElementById('submission_type').value = type;
    document.getElementById('testForm').submit();
}

// Visual Countdown interval running off server metrics
const timerInterval = setInterval(() => {
    if (remainingSeconds <= 0) {
        clearInterval(timerInterval);
        forceSubmit('timeout');
        return;
    }
    remainingSeconds--;
    const m = Math.floor(remainingSeconds / 60);
    const s = remainingSeconds % 60;
    document.getElementById('timer').textContent = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
}, 1000);

const translations = {
    en: { time: "Time Left", submit: "Final Submit", question: "Question", review: "Review Later", clear: "Clear Selection", nav: "Question Navigator", violations: "Violations", finish: "Finish", next: "Save & Next", prev: "Previous" },
    mr: { time: "उर्वरित वेळ", submit: "परीक्षा जमा करा", question: "प्रश्न", review: "नंतर तपासा", clear: "निवड रद्द करा", nav: "प्रश्न मार्गदर्शक", violations: "नियम मोडले", finish: "पूर्ण करा", next: "पुछचा प्रश्न", prev: "मागील" },
    hi: { time: "शेष समय", submit: "परीक्षा जमा करें", question: "प्रश्न", review: "बाद में समीक्षा", clear: "विकल्प साफ करें", nav: "प्रश्न मार्गदर्शक", violations: "नियम उल्लंघन", finish: "समाप्त करें", next: "अगला प्रश्न", prev: "पिछला" }
};

function setLanguage(lang) {
    document.querySelectorAll('.lang-btn').forEach(b => b.classList.remove('active'));
    if (event && event.target) {
        event.target.classList.add('active');
    }
    document.getElementById('lbl-time-left').innerText = translations[lang].time;
    document.getElementById('btn-final-submit').innerText = translations[lang].submit;
    document.getElementById('lbl-navigator').innerText = translations[lang].nav;
    document.getElementById('lbl-violations').innerText = translations[lang].violations;
    document.querySelectorAll('.lbl-question').forEach(el => el.innerText = translations[lang].question);
    document.querySelectorAll('.btn-review').forEach(el => el.innerText = translations[lang].review);
    document.querySelectorAll('.btn-clear').forEach(el => el.innerText = translations[lang].clear);
    document.getElementById('prevBtn').innerText = translations[lang].prev;
    updateNavigation();
}

function updateNavigation() {
    const activeBtn = document.querySelector('.lang-btn.active');
    const lang = activeBtn ? activeBtn.innerText.toLowerCase() : 'en';
    document.getElementById('prevBtn').style.visibility = currentIdx === 0 ? 'hidden' : 'visible';
    document.getElementById('nextBtn').innerText = currentIdx === totalQuestions - 1 ? translations[lang].finish : translations[lang].next;
    
    document.querySelectorAll('.dot').forEach((dot, idx) => {
        dot.classList.remove('current');
        if (idx === currentIdx) dot.classList.add('current');
    });
}

function handleNext() {
    if (currentIdx === totalQuestions - 1) submitExam();
    else changeQuestion(1);
}

function goToQuestion(index) {
    document.getElementById('q_card_' + currentIdx).classList.remove('active');
    currentIdx = index;
    document.getElementById('q_card_' + currentIdx).classList.add('active');
    updateNavigation();
}

function changeQuestion(step) {
    const target = currentIdx + step;
    if (target >= 0 && target < totalQuestions) goToQuestion(target);
}

function markAnswered(index) {
    const dot = document.getElementById('dot_' + index);
    if (!document.getElementById('review_check_' + index).checked) {
        dot.classList.add('answered');
        dot.classList.remove('marked');
    }
}

function toggleReviewCheckbox(index) {
    const isChecked = document.getElementById('review_check_' + index).checked;
    const dot = document.getElementById('dot_' + index);
    
    if (isChecked) {
        dot.classList.add('marked');
        dot.classList.remove('answered');
    } else {
        dot.classList.remove('marked');
        const formInputs = document.getElementById('q_card_' + index).querySelectorAll('input[type="radio"]');
        let answered = false;
        formInputs.forEach(input => { if(input.checked) answered = true; });
        
        if (answered) {
            dot.classList.add('answered');
        }
    }
}

function clearChoice(index, name) {
    document.getElementsByName(name).forEach(i => i.checked = false);
    document.getElementById('review_check_' + index).checked = false;
    document.getElementById('dot_' + index).classList.remove('answered', 'marked');
}

function submitExam() {
    if (confirm("Are you sure you want to submit the exam?")) forceSubmit('student');
}
</script>
<?php require_once '../includes/footer.php'; ?>