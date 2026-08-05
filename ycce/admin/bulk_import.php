<?php
require_once '../includes/header.php';

$valid_branches = [
    "Computer Science & Design", "Information Technology", "Computer Technology", 
    "Computer Science & Engineering", "Electronics Engineering (VLSI Design and Technology)", 
    "Computer Science and Engineering (IoT)", "Artificial Intelligence and Data Science", 
    "Computer Science and Engineering (AIML)", "Electronics Engineering", 
    "Mechanical Engineering", "Civil Engineering", "Electronics and Telecommunication Engg", 
    "Electrical Engg (Electronics and Power)"
];

$import_data = [];

if (isset($_POST['upload_csv']) && $_FILES['csv_file']['error'] == 0) {
    $file = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($file, "r");
    fgetcsv($handle); 

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if(empty($data[0])) continue;
        $import_data[] = [
            'name' => trim($data[0]),
            'email' => trim($data[1]),
            'reg_no' => trim($data[2]),
            'department' => trim($data[3]),
            'role' => strtolower(trim($data[4]))
        ];
    }
    fclose($handle);
    $_SESSION['temp_import'] = $import_data;
}
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800;900&display=swap');

    body { 
        background-color: #f4f5f7; 
        font-family: 'Inter', sans-serif; 
        color: #0f172a;
    }
    
    .brutal-card { 
        background: #ffffff; 
        border: 2px solid #0f172a; 
        box-shadow: 4px 4px 0px #0f172a; 
    }

    .brutal-btn { 
        background: #0f172a; 
        color: #ffffff; 
        border: 2px solid #0f172a; 
        box-shadow: 4px 4px 0px #6366f1;
        transition: transform 0.05s, box-shadow 0.05s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        cursor: pointer;
    }

    .brutal-btn:active { 
        transform: translate(2px, 2px); 
        box-shadow: 2px 2px 0px #6366f1; 
    }

    .brutal-btn-outline {
        background: #ffffff;
        color: #0f172a;
        border: 2px solid #0f172a;
        font-weight: 800;
        text-transform: uppercase;
        transition: transform 0.05s, box-shadow 0.05s;
        box-shadow: 3px 3px 0px #0f172a;
    }

    .brutal-btn-outline:active {
        transform: translate(1px, 1px);
        box-shadow: 2px 2px 0px #0f172a;
    }

    .brutal-label { 
        font-size: 11px; 
        font-weight: 900; 
        color: #475569; 
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
    }
    
    .branch-badge { 
        background: #f1f5f9; 
        border: 1px solid #0f172a; 
        padding: 4px 8px; 
        font-size: 10px; 
        font-weight: 800; 
        text-transform: uppercase; 
    }

    .badge-error { 
        background: #fee2e2; 
        border-color: #ef4444; 
        color: #b91c1c; 
    }

    #sampleModal { display: none; }
    #sampleModal:target { display: flex; }
</style>

<div class="max-w-6xl mx-auto px-6 py-12">
    <div class="mb-8">
        <a href="dashboard.php" class="text-[11px] font-black uppercase tracking-wider text-slate-500 hover:text-slate-900 transition-colors flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 pb-6 border-b-2 border-dashed border-slate-300">
        <div>
            <h1 class="text-4xl font-black tracking-tight text-slate-900 uppercase">Bulk System Registry</h1>
            <p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 border border-indigo-200 inline-block mt-2">YCCE IT Examination System</p>
        </div>
        <div class="flex flex-wrap gap-4">
            <a href="#sampleModal" class="brutal-btn-outline px-5 py-2.5 text-xs">
                <i class="fa-solid fa-eye mr-2"></i> View Template Guide
            </a>
            <a href="download_template.php" class="brutal-btn-outline bg-slate-900 text-white px-5 py-2.5 text-xs">
                <i class="fa-solid fa-file-excel mr-2"></i> Download CSV Template
            </a>
        </div>
    </div>

    <div class="brutal-card p-12 mb-12 text-center border-dashed bg-slate-50/50">
        <form method="POST" enctype="multipart/form-data" id="importForm">
            <i class="fa-solid fa-file-csv text-6xl mb-4 text-slate-300"></i>
            <h3 class="font-extrabold uppercase text-sm mb-6 text-slate-600">Upload Pre-formatted Student Data CSV File</h3>
            
            <label class="brutal-btn px-10 py-4 text-xs">
                <i class="fa-solid fa-plus mr-2"></i> Select CSV File
                <input type="file" name="csv_file" class="hidden" accept=".csv" onchange="document.getElementById('importForm').submit()">
            </label>
            
            <input type="hidden" name="upload_csv" value="1">
            <p class="mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Acceptable formats: Standard Comma Separated .csv</p>
        </form>
    </div>

    <?php if (!empty($import_data)): ?>
    <div class="brutal-card overflow-hidden">
        <div class="p-4 bg-slate-900 text-white flex justify-between items-center border-b-2 border-slate-900">
            <span class="text-[11px] font-black uppercase tracking-wider text-slate-300">Data Import Preview & Verification Table</span>
            <span class="text-[10px] font-black bg-indigo-600 text-white px-3 py-1 border border-indigo-400 uppercase tracking-wider"><?= count($import_data) ?> Rows Loaded</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b-2 border-slate-900">
                    <tr>
                        <th class="p-4 brutal-label">Identity / Information</th>
                        <th class="p-4 brutal-label">Registration ID</th>
                        <th class="p-4 brutal-label">Branch Mapping</th>
                        <th class="p-4 brutal-label">Assigned Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-slate-900 bg-white">
                    <?php foreach ($import_data as $row): 
                        $valid_dept = in_array($row['department'], $valid_branches);
                        $valid_role = in_array($row['role'], ['student', 'teacher']);
                    ?>
                    <tr>
                        <td class="p-4">
                            <div class="font-black text-slate-900 text-sm"><?= htmlspecialchars($row['name']) ?></div>
                            <div class="text-xs font-bold text-slate-400 font-mono"><?= htmlspecialchars($row['email']) ?></div>
                        </td>
                        <td class="p-4 text-xs font-black italic text-indigo-600 font-mono"><?= htmlspecialchars($row['reg_no']) ?></td>
                        <td class="p-4">
                            <span class="branch-badge <?= !$valid_dept ? 'badge-error' : '' ?>">
                                <?= htmlspecialchars($row['department']) ?>
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="text-xs font-black uppercase tracking-wide <?= !$valid_role ? 'text-red-600' : 'text-slate-500' ?>">
                                <?= htmlspecialchars($row['role']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t-2 border-slate-900 flex justify-end bg-slate-50/50">
            <form method="POST" action="process_import.php">
                <button type="submit" class="brutal-btn px-12 py-4 text-xs">
                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Confirm Verification & Import Rows
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<div id="sampleModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 items-center justify-center p-6">
    <div class="bg-white border-2 border-slate-900 shadow-[6px_6px_0px_#000] max-w-4xl w-full p-8 relative">
        <a href="#" class="absolute -top-3 -right-3 bg-red-500 text-white w-9 h-9 flex items-center justify-center border-2 border-slate-900 font-black hover:bg-red-600 transition-colors shadow-[2px_2px_0px_#000]">X</a>
        <h2 class="text-xl font-black uppercase mb-6 tracking-tight">CSV Column Structure Reference</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-2 border-slate-900 text-xs font-bold bg-white">
                <thead>
                    <tr class="bg-slate-100 border-b-2 border-slate-900">
                        <th class="p-3 border-r-2 border-slate-900 brutal-label">name</th>
                        <th class="p-3 border-r-2 border-slate-900 brutal-label">email</th>
                        <th class="p-3 border-r-2 border-slate-900 brutal-label">reg_no</th>
                        <th class="p-3 border-r-2 border-slate-900 brutal-label">department</th>
                        <th class="p-3 brutal-label">role</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-slate-900">
                    <tr>
                        <td class="p-3 border-r-2 border-slate-900">Nakul Dhande</td>
                        <td class="p-3 border-r-2 border-slate-900 font-mono text-slate-500">240300XX@ycce.edu</td>
                        <td class="p-3 border-r-2 border-slate-900 font-mono text-indigo-600">240300XX</td>
                        <td class="p-3 border-r-2 border-slate-900">Computer Science & Design</td>
                        <td class="p-3 uppercase text-slate-500">student</td>
                    </tr>
                    <tr class="bg-slate-50">
                        <td class="p-3 border-r-2 border-slate-900">Dr. Smith</td>
                        <td class="p-3 border-r-2 border-slate-900 font-mono text-slate-500">smith@ycce.edu</td>
                        <td class="p-3 border-r-2 border-slate-900 font-mono text-indigo-600">EMP001</td>
                        <td class="p-3 border-r-2 border-slate-900">Mechanical Engineering</td>
                        <td class="p-3 uppercase text-slate-500">teacher</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-6 p-4 bg-amber-50 border-2 border-slate-900 shadow-[2px_2px_0px_#000]">
            <p class="text-[10px] font-black uppercase text-amber-700 mb-1 tracking-wider">Validation Integrity Guard</p>
            <p class="text-xs font-bold text-slate-800 leading-relaxed">The content inside the <span class="underline">department</span> column must strictly match predefined institutional academic names. Rows containing unmatched branches or typo metrics will automatically highlight in red on the verification grid.</p>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>