<?php 
require_once '../includes/header.php'; 

$tests = $pdo->query("SELECT id, title FROM tests ORDER BY created_at DESC")->fetchAll();

$selected_test = $_GET['test_id'] ?? null;
$results = [];
$test_details = null;

if ($selected_test) {
    $stmt_test = $pdo->prepare("SELECT title, total_marks FROM tests WHERE id = ?");
    $stmt_test->execute([$selected_test]);
    $test_details = $stmt_test->fetch();

    $query = "SELECT u.department, tr.*, u.name, u.reg_no, t.title as test_name 
              FROM test_results tr
              JOIN users u ON tr.student_id = u.id
              JOIN tests t ON tr.test_id = t.id
              WHERE tr.test_id = ?
              ORDER BY u.department ASC, tr.score DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$selected_test]);
    $results = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
}
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
    .report-body { font-family: 'Inter', sans-serif; }
    
    .bb-border { border: 2px solid #0f172a; }
    .bb-card { background: white; border: 2px solid #0f172a; box-shadow: 4px 4px 0px #0f172a; }
    .btn-black { background: #0f172a; color: white; border: 2px solid #0f172a; box-shadow: 2px 2px 0px #6366f1; transition: 0.2s; }
    .btn-black:hover { transform: translate(-1px, -1px); box-shadow: 4px 4px 0px #6366f1; }
    
    .btn-back-link {
        display: inline-block;
        align-items: center;
        gap: 8px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        color: #94a3b8;
        transition: color 0.2s;
        margin-bottom: 24px;
        text-decoration: none;
    }
    .btn-back-link:hover { color: #0f172a; }

    #print-header { display: none; }

    @media print {
        body { background: white !important; padding: 0 !important; }
        .no-print { display: none !important; }
        #print-header { 
            display: block !important; 
            margin-bottom: 30px; 
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }
        .bb-card { box-shadow: none !important; border: 1px solid #000 !important; }
        .dept-section { page-break-inside: avoid; }
        table { width: 100% !important; border-collapse: collapse !important; }
        th, td { border: 1px solid #000 !important; padding: 8px !important; }
    }
</style>

<div class="report-body min-h-screen bg-[#f8fafc] py-10 px-6 antialiased">
    <div class="max-w-6xl mx-auto">
        
        <a href="dashboard.php" class="btn-back-link no-print">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>

        <div id="print-header" class="text-center">
            <h1 class="text-2xl font-black uppercase tracking-tighter">Yeshwantrao Chavan College of Engineering</h1>
            <p class="text-sm font-bold uppercase tracking-widest text-slate-600">Official Assessment Report</p>
            <div class="mt-4 flex justify-between items-end italic text-xs font-bold">
                <span>Test: <?= htmlspecialchars($test_details['title'] ?? '') ?></span>
                <span>Date: <?= date('d M, Y') ?></span>
                <span>Max Marks: <?= htmlspecialchars($test_details['total_marks'] ?? '') ?></span>
            </div>
        </div>

        <div class="bb-card p-8 mb-10 no-print flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <span class="bg-indigo-600 text-white px-2 py-0.5 border-2 border-slate-900 font-black text-[10px] uppercase tracking-widest mb-2 inline-block shadow-[2px_2px_0px_#000]">Analytics Engine</span>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase italic">Grade Reports</h1>
            </div>

            <form method="GET" class="flex items-center gap-3 w-full md:w-auto">
                <select name="test_id" onchange="this.form.submit()" class="bg-white border-2 border-slate-900 px-4 py-2.5 font-bold text-sm focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all w-full md:w-72">
                    <option value="">Select Assessment...</option>
                    <?php foreach($tests as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($selected_test == $t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <?php if($selected_test): ?>
                    <button type="button" onclick="window.print()" class="btn-black h-[46px] px-5 flex items-center justify-center" title="Print Report">
                        <i class="fa-solid fa-print"></i>
                    </button>
                <?php endif; ?>
            </form>
        </div>

        <?php if (!$selected_test): ?>
            <div class="border-2 border-dashed border-slate-300 rounded-none py-24 text-center">
                <div class="text-slate-200 text-6xl mb-4"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
                <p class="text-slate-400 font-black uppercase tracking-widest text-xs">Select a test module to generate report</p>
            </div>
        <?php elseif (empty($results)): ?>
            <div class="text-center py-24 bg-white bb-border">
                <p class="text-slate-500 font-bold italic">No candidate submissions found for this assessment.</p>
            </div>
        <?php else: ?>
            
            <div class="flex justify-between items-center mb-6 no-print">
                <h2 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400">Departmental Breakdown</h2>
                <button onclick="exportTableToCSV('YCCE_Report_<?= htmlspecialchars($selected_test) ?>.csv')" class="text-[10px] font-black uppercase tracking-widest text-indigo-600 hover:underline">
                    <i class="fa-solid fa-file-csv mr-1"></i> Download CSV
                </button>
            </div>

            <?php foreach($results as $dept => $students): ?>
                <div class="mb-12 dept-section">
                    <div class="flex items-center gap-4 mb-4">
                        <h3 class="bg-slate-900 text-white px-4 py-1 font-black text-xs uppercase italic tracking-widest">
                            <?= htmlspecialchars($dept) ?>
                        </h3>
                        <span class="text-[10px] font-black text-slate-400 uppercase"><?= count($students) ?> Submissions</span>
                        <div class="h-[2px] flex-grow bg-slate-900"></div>
                    </div>
                    
                    <div class="bb-card overflow-hidden">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 border-b-2 border-slate-900">
                                <tr>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-900 uppercase tracking-widest">Reg No</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-900 uppercase tracking-widest">Candidate Name</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-900 uppercase tracking-widest text-center">Score</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-900 uppercase tracking-widest text-right">Result</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-slate-900">
                                <?php foreach($students as $s): ?>
                                <tr class="hover:bg-indigo-50/50 transition-colors">
                                    <td class="px-6 py-4 font-black text-xs text-indigo-600 italic"><?= htmlspecialchars($s['reg_no'] ?? '') ?></td>
                                    <td class="px-6 py-4 text-slate-900 font-bold uppercase text-xs"><?= htmlspecialchars($s['name'] ?? '') ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-slate-900 font-black text-sm csv-raw-score"><?= htmlspecialchars($s['score'] ?? '0') ?></span>
                                        <span class="text-slate-400 font-bold text-[10px]">/<?= htmlspecialchars($s['total_marks'] ?? '0') ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="inline-block border-2 border-slate-900 px-3 py-1 font-black text-[10px] uppercase <?= ($s['percentage'] ?? 0) >= 40 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>">
                                            <?= number_format(($s['percentage'] ?? 0), 1) ?>%
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function exportTableToCSV(filename) {
    let csv = [];
    let tables = document.querySelectorAll("table");
    csv.push("YCCE Assessment Report - Generated on <?= date('Y-m-d') ?>");
    csv.push(""); 

    tables.forEach((table) => {
        let headers = table.querySelectorAll("thead th");
        let headerRow = [];
        headers.forEach((th) => {
            headerRow.push('"' + th.innerText.trim() + '"');
        });
        csv.push(headerRow.join(","));

        let rows = table.querySelectorAll("tbody tr");
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td");
            for (let j = 0; j < cols.length; j++) {
                let cell = cols[j];
                let data = "";
                let rawScoreEl = cell.querySelector(".csv-raw-score");
                if (rawScoreEl) {
                    data = rawScoreEl.innerText.trim();
                } else {
                    data = cell.innerText.replace(/,/g, '').trim();
                }
                row.push('"' + data + '"');
            }
            csv.push(row.join(","));
        }
        csv.push(""); 
    });
    let csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    let downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.click();
}
</script>

<?php require_once '../includes/footer.php'; ?>