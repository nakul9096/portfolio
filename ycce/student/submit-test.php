<?php
require_once '../includes/header.php';

if ($_SESSION['role'] !== 'student' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /ycce/student/dashboard.php");
    exit;
}

$test_id = isset($_POST['test_id']) ? (int)$_POST['test_id'] : 0;
$student_id = $_SESSION['user_id'];

// The 'shown_ids' hidden input is critical to grade only what the student saw
$shown_ids_raw = isset($_POST['shown_ids']) ? $_POST['shown_ids'] : '';
$shown_ids = !empty($shown_ids_raw) ? explode(',', $shown_ids_raw) : [];

if (empty($shown_ids)) {
    echo "<div class='p-20 text-center text-red-500 font-extrabold border-4 border-black bg-white max-w-xl mx-auto my-12 shadow-[4px_4px_0px_#000]'>Session Error: Question data missing.</div>";
    require_once '../includes/footer.php';
    exit;
}

// --- 1. FETCH TEST DETAILS ---
$stmt = $pdo->prepare("SELECT t.id, t.title 
                       FROM tests t 
                       JOIN test_candidates tc ON t.id = tc.test_id 
                       WHERE t.id = ? AND tc.student_id = ? AND t.status = 'published'");
$stmt->execute([$test_id, $student_id]);
$test = $stmt->fetch();

if (!$test) {
    echo "<div class='p-20 text-center text-red-500 font-extrabold border-4 border-black bg-white max-w-xl mx-auto my-12 shadow-[4px_4px_0px_#000]'>Access Denied or Test Not Found</div>";
    require_once '../includes/footer.php';
    exit;
}

// --- 2. SCORING LOGIC (GRADE ONLY SHOWN QUESTIONS) ---
$placeholders = implode(',', array_fill(0, count($shown_ids), '?'));
$stmt = $pdo->prepare("
    SELECT q.id as question_id, q.points, q.type,
           GROUP_CONCAT(o.id) as correct_option_ids
    FROM questions q
    JOIN options o ON q.id = o.question_id
    WHERE q.id IN ($placeholders) AND o.is_correct = 1
    GROUP BY q.id
");
$stmt->execute($shown_ids);
$correctAnswers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$score = 0;
$total_available_marks = 0;
$total_questions = count($correctAnswers);

foreach ($correctAnswers as $correct) {
    $qid = $correct['question_id'];
    $points = $correct['points'];
    $total_available_marks += $points;
    $student_answer = $_POST["q_$qid"] ?? null;

    if ($correct['type'] === 'single') {
        $correct_ids = explode(',', $correct['correct_option_ids']);
        $correct_id = (int)$correct_ids[0];
        if ($student_answer && (int)$student_answer === $correct_id) {
            $score += $points;
        }
    } else {
        $selected = is_array($student_answer) ? array_map('intval', $student_answer) : [];
        $correct_ids = array_map('intval', explode(',', $correct['correct_option_ids']));
        sort($selected);
        sort($correct_ids);
        if ($selected === $correct_ids && !empty($selected)) {
            $score += $points;
        }
    }
}

// --- 3. CALCULATIONS & PROCESS SUBMISSION TYPE ---
$percentage = $total_available_marks > 0 ? round(($score / $total_available_marks) * 100, 1) : 0;

$raw_reason = isset($_POST['submission_type']) ? $_POST['submission_type'] : '';
$submission_type = 'Submitted By Candidate';
if ($raw_reason === 'violation') {
    $submission_type = 'Maximum Violation Threshold Breached';
} elseif ($raw_reason === 'timeout') {
    $submission_type = 'Allotted Time Interval Completed';
}

$stmt = $pdo->prepare("INSERT INTO test_results 
    (test_id, student_id, score, total_marks, percentage, submission_type, submitted_at) 
    VALUES (?, ?, ?, ?, ?, ?, NOW()) 
    ON DUPLICATE KEY UPDATE 
    score = VALUES(score), 
    total_marks = VALUES(total_marks),
    percentage = VALUES(percentage), 
    submission_type = VALUES(submission_type),
    submitted_at = NOW()");
$stmt->execute([$test_id, $student_id, $score, $total_available_marks, $percentage, $submission_type]);

// Fixed classes here directly to prevent dynamic styling breakages
if ($percentage >= 70) {
    $badgeBg = 'bg-emerald-100 text-emerald-900 border-emerald-400';
    $alertBox = 'bg-emerald-50 border-emerald-900 text-emerald-950';
    $statusText = 'Outstanding';
} elseif ($percentage >= 50) {
    $badgeBg = 'bg-amber-100 text-amber-900 border-amber-400';
    $alertBox = 'bg-amber-50 border-amber-900 text-amber-950';
    $statusText = 'Passed';
} else {
    $badgeBg = 'bg-red-100 text-red-900 border-red-400';
    $alertBox = 'bg-red-50 border-red-900 text-red-950';
    $statusText = 'Needs Review';
}
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800;900&display=swap');
    
    body { background-color: #f4f5f7; font-family: 'Inter', sans-serif; color: #0f172a; }
    
    .neubrutalism-card { 
        background: #ffffff; 
        border: 2px solid #0f172a; 
        box-shadow: 4px 4px 0px #0f172a; 
    }
    
    .stat-container {
        background: #ffffff;
        border: 2px solid #0f172a;
        box-shadow: 3px 3px 0px #0f172a;
        padding: 18px;
    }

    .btn-action { 
        background: #ffffff;
        color: #0f172a; 
        font-weight: 800;
        border: 2px solid #0f172a; 
        box-shadow: 3px 3px 0px #0f172a; 
        cursor: pointer;
        transition: all 0.1s ease;
    }

    .btn-action:hover { 
        transform: translate(1px, 1px);
        box-shadow: 2px 2px 0px #0f172a;
    }

    .btn-primary-dark {
        background: #0f172a;
        color: #ffffff;
    }
    .btn-primary-dark:hover {
        background: #1e293b;
    }

    .label-badge { 
        font-size: 10px; 
        font-weight: 800; 
        text-transform: uppercase; 
        letter-spacing: 0.1em; 
    }
</style>

<div class="min-h-screen py-12 px-6">
    <div class="max-w-3xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 pb-6 border-b-2 border-dashed border-slate-300">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase">Assessment Complete</h1>
                <p class="text-slate-500 font-bold text-xs uppercase mt-1 tracking-wide"><?= htmlspecialchars($test['title']) ?></p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Submitted On</span>
                <div class="bg-white border-2 border-black px-4 py-2 font-black text-xs text-slate-900 shadow-[2px_2px_0px_#000] uppercase">
                    <?= date('d M, Y') ?> <span class="text-slate-400 mx-2">|</span> <?= date('h:i A') ?>
                </div>
            </div>
        </div>

        <div class="neubrutalism-card overflow-hidden mb-6">
            <div class="flex flex-col md:flex-row">
                
                <div class="md:w-1/3 bg-slate-900 p-10 text-center flex flex-col justify-center items-center border-b-2 md:border-b-0 md:border-r-2 border-black">
                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Final Percentage</div>
                    <div class="text-6xl font-black text-white tracking-tighter"><?= round($percentage) ?><span class="text-2xl text-slate-400 font-bold">%</span></div>
                    
                    <div class="mt-5 px-3 py-1 text-[9px] font-black uppercase border rounded-none <?= $badgeBg ?>">
                        <?= $statusText ?>
                    </div>
                </div>

                <div class="md:w-2/3 p-8 bg-white flex flex-col justify-center">
                    <div class="mb-4 p-4 border-2 border-black bg-neutral-50 text-slate-900 flex flex-col gap-1">
                        <span class="text-[9px] uppercase font-black tracking-wider text-slate-400">Submission Trigger Reason</span>
                        <p class="text-xs font-black uppercase italic tracking-wide"><?= htmlspecialchars($submission_type) ?></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="stat-container">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider mb-1">Score Obtained</p>
                            <p class="text-2xl font-black text-slate-900"><?= $score ?> <span class="text-xs font-bold text-slate-400">/ <?= $total_available_marks ?></span></p>
                        </div>
                        <div class="stat-container">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider mb-1">Total Questions</p>
                            <p class="text-2xl font-black text-slate-900"><?= $total_questions ?></p>
                        </div>
                    </div>

                    <div class="mt-6 p-4 border-2 border-black flex items-center gap-3 <?= $alertBox ?>">
                        <div class="w-6 h-6 border-2 border-black bg-white flex items-center justify-center text-black text-xs font-black shrink-0">i</div>
                        <p class="text-[11px] font-extrabold uppercase tracking-wide leading-tight">
                            <?php 
                                if($percentage >= 70) echo "Congratulations! You've mastered this topic successfully.";
                                elseif($percentage >= 50) echo "Good effort! Review the incorrect answers to improve.";
                                else echo "We recommend reviewing the course material carefully again.";
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-end mt-8">
            <button onclick="window.print()" class="btn-action px-6 py-3 text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                Print Result Card
            </button>
            <a href="/ycce/student/dashboard.php" class="btn-action btn-primary-dark px-8 py-3 text-xs uppercase tracking-wider text-center">
                Return to Dashboard
            </a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>