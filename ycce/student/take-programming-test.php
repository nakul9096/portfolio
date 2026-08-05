<?php 
require_once '../includes/header.php'; 

$test_id = (int)($_GET['id'] ?? 0);
if ($test_id <= 0) {
    header("Location: dashboard.php");
    exit;
}

// Fetch test details
$stmt = $pdo->prepare("SELECT * FROM tests WHERE id = ? AND status = 'published'");
$stmt->execute([$test_id]);
$test = $stmt->fetch();

if (!$test || $test['test_type'] !== 'programming') {
    echo "<script>alert('Invalid Programming Test'); window.location.href='dashboard.php';</script>";
    exit;
}

// Check if student is enrolled
$enroll_check = $pdo->prepare("SELECT 1 FROM test_candidates WHERE test_id = ? AND student_id = ?");
$enroll_check->execute([$test_id, $_SESSION['user_id']]);
if (!$enroll_check->fetch()) {
    echo "<script>alert('You are not authorized for this test'); window.location.href='dashboard.php';</script>";
    exit;
}
?>

<div class="min-h-screen bg-zinc-950 text-white">
    <div class="flex h-screen">
        <!-- Problem Description Panel -->
        <div class="w-2/5 border-r border-zinc-800 overflow-auto p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold"><?= htmlspecialchars($test['title']) ?></h1>
                <div class="text-emerald-400 text-sm mt-1">Time Left: <span id="timer" class="font-mono">45:00</span></div>
            </div>
            
            <div id="problemDescription" class="prose prose-invert max-w-none">
                <?= nl2br(htmlspecialchars($test['description'])) ?>
            </div>

            <div class="mt-8">
                <h3 class="font-semibold mb-3 text-lg">Test Cases</h3>
                <div id="testCases" class="space-y-4"></div>
            </div>
        </div>

        <!-- Code Editor Panel -->
        <div class="flex-1 flex flex-col">
            <div class="bg-zinc-900 border-b border-zinc-800 p-3 flex items-center justify-between">
                <select id="language" class="bg-zinc-800 border border-zinc-700 px-4 py-2 rounded text-sm">
                    <option value="cpp">C++</option>
                    <option value="python">Python</option>
                    <option value="java">Java</option>
                </select>
                <div class="flex gap-3">
                    <button onclick="runCode()" class="bg-emerald-600 hover:bg-emerald-500 px-6 py-2 rounded font-medium">Run Code</button>
                    <button onclick="submitCode()" class="bg-blue-600 hover:bg-blue-500 px-6 py-2 rounded font-medium">Submit</button>
                </div>
            </div>

            <!-- CodeMirror or Monaco-like Editor -->
            <div class="flex-1 bg-[#1e1e1e] p-4 font-mono text-sm overflow-auto" id="editorContainer">
                <textarea id="codeEditor" class="w-full h-full bg-transparent text-white resize-none focus:outline-none font-mono" spellcheck="false"> 
// Write your code here
#include <bits/stdc++.h>
using namespace std;

int main() {
    // Your solution
    return 0;
}
                </textarea>
            </div>

            <!-- Output Console -->
            <div class="h-80 bg-zinc-900 border-t border-zinc-800 p-4 overflow-auto">
                <div class="flex justify-between text-xs mb-2">
                    <span class="text-emerald-400">Output</span>
                    <span id="status" class="font-mono"></span>
                </div>
                <pre id="output" class="text-zinc-300 font-mono text-sm whitespace-pre-wrap"></pre>
            </div>
        </div>
    </div>
</div>

<script>
// Timer
let timeLeft = <?= $test['duration_minutes'] * 60 ?>;
const timerEl = document.getElementById('timer');

setInterval(() => {
    timeLeft--;
    let min = Math.floor(timeLeft / 60);
    let sec = timeLeft % 60;
    timerEl.textContent = `${min}:${sec < 10 ? '0' : ''}${sec}`;
    
    if (timeLeft <= 0) {
        submitCode(true);
    }
}, 1000);

// Fetch Test Cases
fetch(`get-test-cases.php?id=<?= $test_id ?>`)
    .then(r => r.json())
    .then(cases => {
        const container = document.getElementById('testCases');
        cases.forEach((tc, i) => {
            container.innerHTML += `
                <div class="border border-zinc-700 p-4 rounded">
                    <div class="text-xs text-zinc-500 mb-2">Test Case ${i+1}</div>
                    <div><strong>Input:</strong> <code class="block bg-black p-2 mt-1">${tc.input}</code></div>
                    <div class="mt-3"><strong>Expected:</strong> <code class="block bg-black p-2 mt-1">${tc.expected_output}</code></div>
                </div>`;
        });
    });

// Run & Submit (Mock for now - integrate real compiler later)
function runCode() {
    document.getElementById('output').textContent = "Running...\n";
    setTimeout(() => {
        document.getElementById('output').textContent = "Sample Output\n6\n";
        document.getElementById('status').innerHTML = '<span class="text-emerald-400">✓ Passed</span>';
    }, 800);
}

function submitCode(auto = false) {
    if (auto) alert("Time's up! Auto-submitted.");
    else alert("Solution submitted successfully! Score will be calculated by compiler.");
    // TODO: Send code + test cases to compiler backend
}
</script>

<?php require_once '../includes/footer.php'; ?>