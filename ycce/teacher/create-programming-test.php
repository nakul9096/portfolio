<?php 
require_once '../includes/header.php'; 

// Auto close expired tests
auto_close_expired_tests($pdo);
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap');
    
    html, body { height: 100%; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background-color: #f4f5f7; color: #1e293b; display: flex; flex-direction: column; min-h-screen; }

    .main-content { flex: 1 0 auto; width: 100%; }
    .dashboard-card { background: #ffffff; border: 2px solid #0f172a; box-shadow: 4px 4px 0px #0f172a; }
    .btn-action, .btn-primary-brutal { /* your existing button styles */ }
    .label-sharp { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px; display: block; color: #475569; }
    .input-field-brutal { width: 100%; background: #ffffff; border: 2px solid #0f172a; padding: 0.75rem; font-weight: 600; color: #0f172a; outline: none; }
    .input-field-brutal:focus { background: #f8fafc; }
    .test-case { border: 2px solid #0f172a; padding: 1rem; margin-bottom: 1rem; background: #f8fafc; }
</style>

<div class="main-content">
    <main class="max-w-5xl mx-auto px-6 py-10">
        
        <div class="mb-4 flex justify-start">
            <a href="dashboard.php" class="btn-action px-4 py-2 text-[10px] uppercase tracking-wider">
                <i class="fa-solid fa-arrow-left text-[9px]"></i> Back to Dashboard
            </a>
        </div>
        
        <div class="dashboard-card p-6 mb-10">
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 uppercase">Create Programming Test</h2>
        </div>

        <form action="save-programming-test.php" method="POST" id="progTestForm">
            
            <!-- Basic Info -->
            <div class="dashboard-card p-6 mb-8 bg-white">
                <div class="space-y-6">
                    <div>
                        <label class="label-sharp">Test Title</label>
                        <input type="text" name="title" placeholder="e.g., Two Sum - Array Challenge" required class="input-field-brutal text-lg font-bold">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="label-sharp">End Date</label>
                            <input type="date" name="end_date" required class="input-field-brutal">
                        </div>
                        <div>
                            <label class="label-sharp">Deadline Time</label>
                            <div class="flex gap-1">
                                <select name="deadline_hr" class="input-field-brutal font-bold"><?php for($i=1;$i<=12;$i++) echo "<option>".sprintf("%02d",$i)."</option>"; ?></select>
                                <select name="deadline_min" class="input-field-brutal font-bold"><?php for($i=0;$i<=59;$i+=5) echo "<option>".sprintf("%02d",$i)."</option>"; ?></select>
                                <select name="deadline_period" class="input-field-brutal font-bold"><option>AM</option><option selected>PM</option></select>
                            </div>
                        </div>
                        <div>
                            <label class="label-sharp">Duration (Minutes)</label>
                            <input type="number" name="duration" value="90" required class="input-field-brutal font-bold">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Problem Statement -->
            <div class="dashboard-card p-6 mb-8 bg-white">
                <label class="label-sharp">Problem Description</label>
                <textarea name="problem_description" rows="10" placeholder="Write the full problem statement..." required class="input-field-brutal w-full"></textarea>
            </div>

            <!-- Test Cases -->
            <div class="dashboard-card p-6 mb-8 bg-white">
                <div class="flex justify-between items-center mb-6">
                    <span class="label-badge text-slate-900 text-xs font-extrabold border-2 border-slate-900 bg-violet-300 px-2 py-0.5">TEST CASES</span>
                    <button type="button" onclick="addTestCase()" class="btn-action px-4 py-2 text-xs">+ Add Test Case</button>
                </div>
                <div id="testCasesContainer"></div>
            </div>

            <!-- Student Assignment -->
            <div class="dashboard-card p-6 mb-8 bg-white">
                <div class="flex justify-between items-center mb-4">
                    <span class="label-badge text-slate-900 text-xs font-extrabold border-2 border-slate-900 bg-emerald-300 px-2 py-0.5">ASSIGN TO STUDENTS</span>
                    <button type="button" onclick="selectAllStudents()" class="text-blue-600 font-extrabold text-xs uppercase hover:underline">Select All</button>
                </div>
                <div id="studentList" class="grid grid-cols-1 md:grid-cols-3 gap-3 max-h-[300px] overflow-y-auto p-4 border-2 border-slate-900"></div>
            </div>

            <button type="submit" class="w-full btn-primary-brutal py-5 uppercase text-lg tracking-widest shadow-[4px_4px_0px_#0f172a]">
                PUBLISH PROGRAMMING TEST
            </button>
        </form>
    </main>
</div>

<script>
fetch('../fetch-students.php')
    .then(r => r.text())
    .then(html => document.getElementById('studentList').innerHTML = html);

function selectAllStudents() {
    document.querySelectorAll('#studentList input[type="checkbox"]').forEach(c => c.checked = true);
}

let testCaseCount = 0;

function addTestCase() {
    testCaseCount++;
    const container = document.getElementById('testCasesContainer');
    const div = document.createElement('div');
    div.className = 'test-case';
    div.innerHTML = `
        <div class="flex justify-between mb-3">
            <span class="font-bold">Test Case #${testCaseCount}</span>
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-600 font-bold">Remove</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label-sharp">Input</label>
                <textarea name="test_input[]" rows="3" class="input-field-brutal w-full font-mono" placeholder="Sample Input"></textarea>
            </div>
            <div>
                <label class="label-sharp">Expected Output</label>
                <textarea name="test_output[]" rows="3" class="input-field-brutal w-full font-mono" placeholder="Expected Output"></textarea>
            </div>
        </div>
    `;
    container.appendChild(div);
}

// Add one default test case
window.onload = () => { addTestCase(); };
</script>

<?php require_once '../includes/footer.php'; ?>