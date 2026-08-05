<?php
require_once '../includes/header.php';
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
    .input-field-brutal {
        width: 100%;
        background: #ffffff;
        border: 2px solid #0f172a;
        padding: 0.75rem;
        font-weight: 600;
        color: #0f172a;
        outline: none;
    }
    .input-field-brutal:focus {
        background: #f8fafc;
    }
    .draggable-question {
        background: white;
        border: 2px solid #0f172a;
        box-shadow: 4px 4px 0px #0f172a;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .q-text-input {
        width: 100%;
        resize: none;
        overflow: hidden;
        border: none;
        background: transparent;
        font-weight: 800;
        font-size: 1.25rem;
        color: #0f172a;
        line-height: 1.5;
        margin-top: 0.5rem;
        outline: none;
    }
    .opt-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }
    .opt-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background-color: #ffffff;
        padding: 0.75rem;
        border: 2px solid #0f172a;
        box-shadow: 2px 2px 0px #0f172a;
    }
    .opt-text {
        flex: 1;
        min-width: 0;
        background: transparent;
        border: none;
        resize: none;
        font-size: 0.95rem;
        line-height: 1.5;
        font-weight: 600;
        color: #0f172a;
        overflow: hidden;
        outline: none;
    }
    .tab-container {
        display: flex;
        border-bottom: 2px solid #0f172a;
        margin-bottom: 1rem;
    }
    .tab {
        padding: 0.75rem 1.5rem;
        font-weight: 700;
        cursor: pointer;
        border: 2px solid transparent;
        background: #ffffff;
        margin-right: 0.5rem;
        border-bottom: none;
    }
    .tab.active {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }
    #studentList {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
        max-height: 250px;
        overflow-y: auto;
        padding: 15px;
        background: #ffffff;
        border: 2px solid #0f172a;
    }
    .student-card {
        border: 2px solid #0f172a;
        padding: 12px;
        background: #fff;
        box-shadow: 3px 3px 0px #0f172a;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .student-card.valid {
        border-color: #166534;
        background: #f0fdf4;
    }
    .student-card.invalid {
        border-color: #991b1b;
        background: #fef2f2;
    }
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
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-thumb { background: #0f172a; }
</style>
<div class="main-content">
    <main class="max-w-5xl mx-auto px-6 py-10">
       
        <div class="mb-4 flex justify-start">
            <a href="dashboard.php" class="btn-action px-4 py-2 text-[10px] uppercase tracking-wider">
                <i class="fa-solid fa-arrow-left text-[9px]"></i> Back to Dashboard
            </a>
        </div>
       
        <div class="dashboard-card p-6 mb-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 uppercase">Create Assessment</h2>
                <div class="flex flex-wrap items-center gap-3 mt-1">
                    <span class="label-badge bg-slate-900 text-white px-2 py-0.5 text-[9px]">Authoring Mode</span>
                    <span class="label-badge text-slate-500 font-bold">YCCE Examination System</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 w-full lg:w-auto">
                <a href="dashboard.php" class="btn-action px-4 py-2 text-[10px] uppercase tracking-wider justify-center grow sm:grow-0">
                    <i class="fa-solid fa-arrow-left text-[9px]"></i> Dashboard
                </a>
                <button type="button" onclick="downloadTemplate()" class="btn-action px-4 py-2 text-[10px] uppercase tracking-wider justify-center grow sm:grow-0">
                    <i class="fa-solid fa-download text-[9px]"></i> Template
                </button>
                <input type="file" id="csvImport" accept=".csv" class="hidden" onchange="handleCSVImport(this)">
                <button type="button" onclick="document.getElementById('csvImport').click()" class="btn-primary-brutal px-4 py-2 text-[10px] uppercase tracking-wider justify-center grow sm:grow-0">
                    <i class="fa-solid fa-file-csv text-[9px]"></i> Import CSV
                </button>
            </div>
        </div>
        
        <form action="save-test.php" method="POST" id="testForm" class="mb-12" enctype="multipart/form-data">
           
            <div class="dashboard-card p-6 mb-8 bg-white">
                <div class="flex items-center gap-2 mb-6">
                    <span class="label-badge text-slate-900 text-xs font-extrabold border-2 border-slate-900 bg-amber-300 px-2 py-0.5">1. Configuration Settings</span>
                </div>
               
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-900 uppercase mb-2">Assessment Title</label>
                        <input type="text" name="title" placeholder="e.g., Mid-Term Exam - DBMS" required class="input-field-brutal text-lg font-bold">
                    </div>
                   
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-extrabold text-emerald-700 uppercase mb-2">🟢 Start Date</label>
                            <input type="date" name="start_date" required class="input-field-brutal border-emerald-700 focus:bg-emerald-50">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-emerald-700 uppercase mb-2">🟢 Activation Time</label>
                            <div class="flex gap-1">
                                <select name="start_hr" class="input-field-brutal font-bold border-emerald-700"><?php for($i=1;$i<=12;$i++) echo "<option>".sprintf("%02d",$i)."</option>"; ?></select>
                                <select name="start_min" class="input-field-brutal font-bold border-emerald-700"><?php for($i=0;$i<=59;$i+=1) echo "<option>".sprintf("%02d",$i)."</option>"; ?></select>
                                <select name="start_period" class="input-field-brutal font-bold border-emerald-700"><option selected>AM</option><option>PM</option></select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-900 uppercase mb-2">Duration (Minutes)</label>
                            <input type="number" name="duration" value="60" required class="input-field-brutal font-bold">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-extrabold text-rose-700 uppercase mb-2">🛑 End Date</label>
                            <input type="date" name="end_date" required class="input-field-brutal border-rose-700 focus:bg-rose-50">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-rose-700 uppercase mb-2">🛑 Deadline Time</label>
                            <div class="flex gap-1">
                                <select name="deadline_hr" class="input-field-brutal font-bold border-rose-700"><?php for($i=1;$i<=12;$i++) echo "<option>".sprintf("%02d",$i)."</option>"; ?></select>
                                <select name="deadline_min" class="input-field-brutal font-bold border-rose-700"><?php for($i=0;$i<=59;$i+=1) echo "<option>".sprintf("%02d",$i)."</option>"; ?></select>
                                <select name="deadline_period" class="input-field-brutal font-bold border-rose-700"><option>AM</option><option selected>PM</option></select>
                            </div>
                        </div>
                        <div>
                            </div>
                    </div>

                    <div class="p-4 border-2 border-slate-900 bg-blue-50 shadow-[2px_2px_0px_#0f172a] flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="font-extrabold text-sm uppercase text-slate-900 block">Anti-Cheating Randomization Engine</span>
                            <p class="text-[11px] font-bold text-slate-600 uppercase tracking-tight mt-0.5">Specify pool execution size delivered randomly per student cluster.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-extrabold uppercase">Show</span>
                                <input type="number" name="questions_to_show" value="25" required class="w-16 text-center p-1.5 border-2 border-slate-900 text-black font-extrabold bg-white shadow-[1px_1px_0px_#0f172a]">
                                <span class="text-xs font-extrabold uppercase">Per Candidate</span>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer select-none border-2 border-slate-900 bg-white px-2 py-1.5 shadow-[1px_1px_0px_#0f172a]">
                                <input type="checkbox" name="shuffle_enabled" checked class="w-4 h-4 text-slate-900 focus:ring-0 cursor-pointer accent-slate-900">
                                <span class="text-xs font-black uppercase">Shuffle</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-card p-6 mb-8 bg-white">
                <div class="flex justify-between items-center mb-4">
                    <span class="label-badge text-slate-900 text-xs font-extrabold border-2 border-slate-900 bg-emerald-300 px-2 py-0.5">2. Target Candidate Assignment</span>
                </div>

                <div class="tab-container">
                    <div class="tab active" onclick="switchTab(0)">Select via Registration</div>
                    <div class="tab" onclick="switchTab(1)">Select via CSV</div>
                </div>

                <div id="tab-content-0">
                    <div id="studentList" class="font-bold text-xs uppercase tracking-wide">
                        <p class="text-slate-400 font-bold">Loading synchronized rosters...</p>
                    </div>
                    <button type="button" onclick="selectAll()" class="mt-3 text-blue-600 font-extrabold text-xs uppercase hover:underline tracking-wider">Select All Students</button>
                </div>

                <div id="tab-content-1" class="hidden">
                    <div class="flex justify-between items-center mb-3">
                        <span class="font-bold text-sm">Imported Students</span>
                        <button type="button" onclick="selectAllCSVStudents()" class="text-blue-600 font-extrabold text-xs uppercase hover:underline tracking-wider">Select All Valid</button>
                    </div>
                    <div id="csvStudentResult" class="min-h-[200px] max-h-[320px] overflow-auto"></div>
                    <input type="hidden" name="csv_selected_emails" id="csvSelectedEmails">
                    
                    <div class="p-6 border-2 border-dashed border-slate-900 bg-slate-50 text-center mt-4">
                        <button type="button" onclick="document.getElementById('csvStudentImport').click()" class="btn-primary-brutal px-6 py-3 text-sm uppercase tracking-wider">
                            <i class="fa-solid fa-file-csv"></i> Import Student CSV
                        </button>
                        <input type="file" id="csvStudentImport" accept=".csv" class="hidden" onchange="handleStudentCSVImport(this)">
                        <p class="text-xs text-slate-500 mt-4">Download template first to ensure correct format</p>
                        <button type="button" onclick="downloadStudentTemplate()" class="mt-3 btn-action px-4 py-2 text-xs uppercase tracking-wider">
                            Download Student Template
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center mb-4 px-1">
                <span class="label-badge text-slate-900 text-xs font-extrabold border-2 border-slate-900 bg-purple-300 px-2 py-0.5">3. Component Pool Elements</span>
                <span id="poolCounter" class="bg-slate-900 text-white border-2 border-slate-900 px-3 py-0.5 font-extrabold text-xs uppercase shadow-[2px_2px_0px_#0f172a]">0 Questions</span>
            </div>
            <div id="questionsContainer"></div>
            <button type="button" onclick="addQuestion()" class="mt-2 w-full py-5 border-2 border-dashed border-slate-400 bg-white text-slate-500 font-extrabold uppercase tracking-wider hover:bg-slate-50 hover:border-slate-900 hover:text-slate-900 shadow-[3px_3px_0px_#cbd5e1] transition-all">
                + Add new Question
            </button>
            <div class="mt-12 sticky bottom-6 z-10">
                <button type="submit" class="w-full btn-primary-brutal py-4 uppercase text-md tracking-widest shadow-[4px_4px_0px_#0f172a]">
                    💾 Finalize and Publish Assessment
                </button>
            </div>
        </form>
    </main>
</div>
<hr class="dashboard-footer-divider">
<script>
const container = document.getElementById('questionsContainer');
fetch('../fetch-students.php')
    .then(r => r.ok ? r.text() : Promise.reject())
    .then(h => { document.getElementById('studentList').innerHTML = h; })
    .catch(() => { document.getElementById('studentList').innerHTML = '<p class="text-red-500 font-bold p-2">Roster sync processing unexpected breakdown.</p>'; });

function switchTab(n) {
    document.querySelectorAll('.tab').forEach((t,i) => t.classList.toggle('active', i===n));
    document.getElementById('tab-content-0').classList.toggle('hidden', n!==0);
    document.getElementById('tab-content-1').classList.toggle('hidden', n===0);
}

function selectAll() {
    document.querySelectorAll('#studentList input[type="checkbox"]').forEach(c => c.checked = true);
}

function selectAllCSVStudents() {
    document.querySelectorAll('#csvStudentResult input[type="checkbox"]').forEach(c => c.checked = true);
    updateSelectedEmails();
}

function downloadTemplate() {
    const headers = ["Question", "Option 1", "Option 2", "Option 3", "Option 4", "Correct Index", "Points"];
    const sampleRow = ["How to initialize an array in JavaScript?", "var items = ◇item1◇,", "var items=(1:item1, 2:item2,", "var items=(1:item1, 2:item2, 3:item3)", "var items=[Momok, item2, item3]", "4", "1"];
    let csvContent = headers.map(h => `"${h}"`).join(",") + "\n";
    csvContent += sampleRow.map(v => `"${v.replace(/"/g, '""')}"`).join(",");
    const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "ycce_assessment_template.csv";
    link.click();
}

function downloadStudentTemplate() {
    const headers = ["Registration_No", "Student_Name", "Email"];
    const sampleRow = ["2023001", "John Doe", "john@example.com"];
    let csvContent = headers.map(h => `"${h}"`).join(",") + "\n";
    csvContent += sampleRow.map(v => `"${v.replace(/"/g, '""')}"`).join(",");
    const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "student_import_template.csv";
    link.click();
}

function handleCSVImport(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        let text = e.target.result;
        text = fixEncoding(text);
        const rows = parseCSV(text);
        if (container.children.length === 1 && container.querySelector('.q-text-input').value.trim() === "") {
            container.innerHTML = '';
        }
        rows.forEach((cols, index) => {
            if (index === 0 || cols.length < 7 || !cols[0].trim()) return;
            addQuestion({
                text: cols[0].trim(),
                options: [cols[1]?cols[1].trim():'', cols[2]?cols[2].trim():'', cols[3]?cols[3].trim():'', cols[4]?cols[4].trim():''],
                correct: parseInt(cols[5] || 1),
                points: cols[6] || '2'
            });
        });
        updateIndices();
        input.value = '';
        alert("✅ Questions CSV imported successfully!");
    };
    reader.readAsText(file, 'UTF-8');
}

function handleStudentCSVImport(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        let text = fixEncoding(e.target.result);
        const rows = parseCSV(text);
        const emails = [];
        rows.forEach((row, i) => {
            if (i === 0 || row.length < 3) return;
            const email = row[2] ? row[2].trim() : '';
            if (email) emails.push(email);
        });
        if (emails.length === 0) {
            alert("No valid emails found in CSV.");
            return;
        }
        validateStudents(emails);
        input.value = '';
    };
    reader.readAsText(file, 'UTF-8');
}

function validateStudents(emails) {
    const resultDiv = document.getElementById('csvStudentResult');
    resultDiv.innerHTML = '<p class="font-bold text-slate-600 p-4">Validating students...</p>';
    
    fetch('validate-students.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ emails: emails })
    })
    .then(r => r.json())
    .then(data => {
        let html = `<div class="grid grid-cols-1 md:grid-cols-2 gap-3">`;
        data.forEach(student => {
            const isValid = student.exists;
            html += `
                <div class="student-card ${isValid ? 'valid' : 'invalid'}">
                    ${isValid ? `<input type="checkbox" class="csv-student-chk w-4 h-4 accent-emerald-700" value="${student.email}" onchange="updateSelectedEmails()">` : ''}
                    <div class="flex-1">
                        <div class="font-bold">${student.email}</div>
                        ${!isValid ? '<div class="text-red-600 text-xs">Student not registered!</div>' : ''}
                    </div>
                </div>`;
        });
        html += `</div>`;
        resultDiv.innerHTML = html;
        updateSelectedEmails();
    })
    .catch(() => {
        resultDiv.innerHTML = '<p class="text-red-600 font-bold p-4">Validation failed. Please try again.</p>';
    });
}

function updateSelectedEmails() {
    const selected = Array.from(document.querySelectorAll('.csv-student-chk:checked')).map(chk => chk.value);
    document.getElementById('csvSelectedEmails').value = selected.join(',');
}

function fixEncoding(str) {
    return str
        .replace(/â€™/g, '’')
        .replace(/â€œ/g, '“')
        .replace(/â€/g, '”')
        .replace(/â€¢/g, '•')
        .replace(/âˆ’/g, '–')
        .replace(/â€‹/g, '')
        .replace(/\uFFFD/g, '')
        .replace(/Ã¢â‚¬â„¢/g, '’')
        .replace(/Ã¢â‚¬Å“/g, '“')
        .replace(/Ã¢â‚¬Â/g, '”');
}

function parseCSV(text) {
    const lines = [];
    let row = [];
    let entry = '';
    let insideQuote = false;
    let i = 0;
    while (i < text.length) {
        let char = text[i];
        let nextChar = text[i + 1];
        if (char === '"') {
            if (insideQuote && nextChar === '"') {
                entry += '"';
                i += 2;
                continue;
            } else {
                insideQuote = !insideQuote;
            }
        } else if (char === ',' && !insideQuote) {
            row.push(entry);
            entry = '';
        } else if ((char === '\n' || char === '\r') && !insideQuote) {
            if (char === '\r' && nextChar === '\n') i++;
            row.push(entry);
            lines.push(row);
            row = [];
            entry = '';
        } else {
            entry += char;
        }
        i++;
    }
    if (entry || row.length > 0) {
        row.push(entry);
        lines.push(row);
    }
    return lines;
}

// Updated addQuestion function supporting image field addition & preview logic
function addQuestion(data = null) {
    const div = document.createElement('div');
    div.className = "draggable-question";
    const rawText = data ? data.text : '';
    const marksVal = data ? data.points : '2';
    div.innerHTML = `
        <div class="flex justify-between items-start gap-4">
            <div class="flex-grow min-w-0">
                <span class="q-num label-badge text-slate-900 border border-slate-900 bg-slate-100 px-1.5 py-0.5"></span>
                <textarea class="q-text-input" placeholder="Type structural question question payload here..." oninput="autoResize(this)"></textarea>
            </div>
            <button type="button" onclick="this.closest('.draggable-question').remove(); updateIndices();" class="w-8 h-8 border-2 border-slate-900 flex items-center justify-center font-extrabold text-lg bg-white shadow-[1px_1px_0px_#0f172a] hover:bg-red-500 hover:text-white transition-colors">&times;</button>
        </div>
        
        <div class="mt-4 p-3 bg-slate-50 border-2 border-dashed border-slate-300 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex flex-col gap-1">
                <span class="text-[10px] font-extrabold uppercase text-slate-700">Question Image (Optional)</span>
                <input type="file" class="q-img-file text-xs font-bold text-slate-600" accept="image/*" onchange="previewSelectedImage(this)">
            </div>
            <div class="q-img-preview-container hidden border-2 border-slate-900 bg-white p-1 max-w-[140px] shadow-[1px_1px_0px_#0f172a]">
                <img src="" class="q-preview-tag max-h-20 w-auto object-contain">
            </div>
        </div>

        <div class="opt-grid"></div>
        <div class="flex justify-between items-center mt-6 pt-4 border-t-2 border-slate-900">
            <button type="button" onclick="addOption(this.closest('.draggable-question'))" class="text-blue-600 font-extrabold text-xs uppercase hover:underline tracking-wider">+ Append Option Slot</button>
            <div class="flex items-center gap-2 bg-slate-100 px-3 py-1 border-2 border-slate-900 shadow-[1px_1px_0px_#0f172a]">
                <span class="text-[9px] font-black text-slate-600 uppercase">Weightage Marks</span>
                <input type="number" class="q-pts-input w-8 text-center font-extrabold bg-transparent border-none p-0 focus:ring-0 text-slate-900" value="${marksVal}">
            </div>
        </div>
    `;
    div.querySelector('.q-text-input').value = rawText;
    container.appendChild(div);
    autoResize(div.querySelector('.q-text-input'));
    const gridBlock = div.querySelector('.opt-grid');
    if (data && data.options) {
        data.options.forEach((optValue, i) => {
            addOption(div, optValue, (i + 1) === data.correct);
        });
    } else {
        for(let j = 0; j < 4; j++) {
            addOption(div);
        }
    }
    updateIndices();
}

// Local preview engine handler matching brutalist container view
function previewSelectedImage(input) {
    const wrapper = input.closest('.draggable-question');
    const previewContainer = wrapper.querySelector('.q-img-preview-container');
    const previewImg = wrapper.querySelector('.q-preview-tag');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        previewImg.src = '';
        previewContainer.classList.add('hidden');
    }
}

function addOption(block, txt = '', isCorrect = false) {
    const row = document.createElement('div');
    row.className = "opt-row";
    row.innerHTML = `
        <textarea class="opt-text" rows="1" oninput="autoResize(this)" placeholder="Provide alternate variant response description..." required></textarea>
        <input type="checkbox" class="opt-chk w-4 h-4 border-2 border-slate-900 text-slate-900 focus:ring-0 cursor-pointer accent-slate-900" ${isCorrect ? 'checked' : ''}>
        <button type="button" onclick="this.parentElement.remove();" class="text-slate-400 hover:text-red-500 font-extrabold px-1 text-md leading-none">&times;</button>
    `;
    row.querySelector('.opt-text').value = txt;
    block.querySelector('.opt-grid').appendChild(row);
    autoResize(row.querySelector('.opt-text'));
}

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = el.scrollHeight + 'px';
}

// Updated indexing mapping binder containing dynamic structural file tracking binds
function updateIndices() {
    container.querySelectorAll('.draggable-question').forEach((block, i) => {
        const n = i + 1;
        block.querySelector('.q-num').innerText = `Question Pool Entry #${n}`;
        block.querySelector('.q-text-input').name = `q_text_${n}`;
        block.querySelector('.q-pts-input').name = `q_points_${n}`;
        
        // Dynamically name individual file slots cleanly
        const imgInput = block.querySelector('.q-img-file');
        if (imgInput) {
            imgInput.name = `q_image_${n}`;
        }

        block.querySelectorAll('.opt-row').forEach((row, oi) => {
            const on = oi + 1;
            row.querySelector('.opt-text').name = `opt_text_${n}_${on}`;
            row.querySelector('.opt-chk').name = `correct_${n}[]`;
            row.querySelector('.opt-chk').value = on;
        });
    });
    document.getElementById('poolCounter').innerText = `${container.children.length} Questions Banked`;
}

window.onload = () => { if(container.children.length === 0) addQuestion(); };
</script>
<?php require_once '../includes/footer.php'; ?>