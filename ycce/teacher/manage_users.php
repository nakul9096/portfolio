<?php 
require_once '../includes/db.php';
session_start();

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->execute([$_GET['delete']]);
    header("Location: manage_users.php");
    exit;
}

require_once '../includes/header.php'; 
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap');
    
    html, body { height: 100%; margin: 0; }
    body { 
        font-family: 'Inter', sans-serif; 
        background-color: #f4f5f7; 
        color: #1e293b; 
        display: flex;
        flex-direction: column;
    }

    .main-content { flex: 1 0 auto; }

    .bb-card-sharp {
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

    .btn-add-sharp {
        background: #2563eb;
        color: #ffffff;
        font-weight: 700;
        border: 2px solid #0f172a;
        box-shadow: 3px 3px 0px #0f172a;
        transition: all 0.1s ease;
    }

    .btn-add-sharp:hover {
        transform: translate(1px, 1px);
        box-shadow: 2px 2px 0px #0f172a;
        background: #1d4ed8;
    }

    .select-sharp {
        background: white; 
        border: 2px solid #0f172a;
        font-weight: 700; 
        font-size: 11px; 
        text-transform: uppercase;
        outline: none; 
        cursor: pointer;
        box-shadow: 2px 2px 0px #0f172a;
    }

    .role-badge-sharp {
        border: 2px solid #0f172a; 
        background: #ffffff;
        font-size: 9px; 
        font-weight: 800; 
        padding: 2px 8px;
        letter-spacing: 0.05em;
    }
    
    .hidden-row { display: none !important; }

    .btn-back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: #64748b;
        transition: color 0.1s ease;
        text-decoration: none;
        border: 2px solid #0f172a;
        background: #ffffff;
        box-shadow: 2px 2px 0px #0f172a;
    }

    .btn-back-link:hover {
        color: #0f172a;
        transform: translate(1px, 1px);
        box-shadow: 1px 1px 0px #0f172a;
        background: #f8fafc;
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
    <main class="max-w-7xl mx-auto px-6 py-10">
        
        <div class="mb-6">
            <a href="dashboard.php" class="btn-back-link px-4 py-2">
                <i class="fa-solid fa-arrow-left text-[9px]"></i> Back to Dashboard
            </a>
        </div>

        <div class="bb-card-sharp p-6 mb-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-white">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 uppercase">User Registry</h2>
                <div class="flex flex-wrap items-center gap-3 mt-1">
                    <span class="label-badge bg-slate-900 text-white px-2 py-0.5 text-[9px]">Administration</span>
                    <span class="label-badge text-slate-500 font-bold">YCCE Examination System</span>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                <select id="roleFilter" class="select-sharp h-10 px-3 text-[10px] tracking-wider w-full sm:w-auto" onchange="filterTable()">
                    <option value="all">Show All Roles</option>
                    <option value="teacher">Teachers</option>
                    <option value="student">Students</option>
                    <option value="admin">Admins</option>
                </select>

                <button onclick="exportFilteredCSV()" class="btn-action h-10 px-4 text-[10px] uppercase tracking-wider justify-center w-full sm:w-auto">
                    <i class="fa-solid fa-file-export text-[9px]"></i> Export CSV
                </button>
                
                <a href="add_user.php" class="btn-add-sharp h-10 px-5 text-[10px] uppercase tracking-wider flex items-center justify-center gap-2 w-full sm:w-auto">
                    <i class="fa-solid fa-user-plus text-[9px]"></i> Add New User
                </a>
            </div>
        </div>

        <div class="bb-card-sharp bg-white overflow-hidden">
            <div class="overflow-x-auto">
                <table id="usersTable" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b-2 border-slate-900 bg-slate-50">
                            <th class="p-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-900">Identity</th>
                            <th class="p-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-900">Department</th>
                            <th class="p-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-900">Role</th>
                            <th class="p-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-900 text-right">Operations</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-slate-100">
                        <?php
                        $users = $pdo->query("SELECT * FROM users ORDER BY role ASC, name ASC")->fetchAll();
                        if (count($users) > 0):
                            foreach($users as $user): ?>
                            <tr class="hover:bg-slate-50 transition-colors user-row" data-role="<?= strtolower($user['role']) ?>">
                                <td class="p-4">
                                    <div class="font-extrabold text-slate-900 uppercase text-sm user-name"><?= htmlspecialchars($user['name']) ?></div>
                                    <div class="text-[10px] font-bold text-blue-600 uppercase tracking-wide user-email mt-0.5"><?= htmlspecialchars($user['email']) ?></div>
                                </td>
                                <td class="p-4 text-xs font-bold text-slate-600 uppercase tracking-wide user-dept">
                                    <?= !empty($user['department']) ? htmlspecialchars($user['department']) : '<span class="text-slate-300 font-normal">N/A</span>' ?>
                                </td>
                                <td class="p-4">
                                    <span class="role-badge-sharp uppercase user-role text-slate-900 <?= strtolower($user['role']) === 'admin' ? 'bg-amber-100 border-amber-500' : (strtolower($user['role']) === 'teacher' ? 'bg-blue-50' : 'bg-slate-50') ?>">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        <a href="add_user.php?edit=<?= $user['id'] ?>" class="btn-action px-3 py-1.5 text-[10px] uppercase tracking-wider bg-slate-900 text-white hover:bg-slate-800">
                                            Edit
                                        </a>
                                        <?php if(strtolower($user['role']) !== 'admin'): ?>
                                            <a href="?delete=<?= $user['id'] ?>" class="btn-action px-3 py-1.5 text-[10px] uppercase tracking-wider text-red-600 hover:bg-red-50" onclick="return confirm('Are you sure you want to delete this user?')">
                                                Delete
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400">
                                    <p class="label-badge">No system registry records discovered.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<hr class="dashboard-footer-divider">

<script>
function filterTable() {
    const selectedRole = document.getElementById('roleFilter').value;
    const rows = document.querySelectorAll(".user-row");

    rows.forEach(row => {
        const rowRole = row.getAttribute('data-role');
        if (selectedRole === 'all' || selectedRole === rowRole) {
            row.classList.remove('hidden-row');
        } else {
            row.classList.add('hidden-row');
        }
    });
}

function exportFilteredCSV() {
    const selectedRole = document.getElementById('roleFilter').value;
    let csv = [];
    csv.push("Name,Email,Department,Role");

    const visibleRows = document.querySelectorAll(".user-row:not(.hidden-row)");
    
    visibleRows.forEach(row => {
        const name = row.querySelector(".user-name").innerText.trim();
        const email = row.querySelector(".user-email").innerText.trim();
        const dept = row.querySelector(".user-dept").innerText.trim();
        const role = row.querySelector(".user-role").innerText.trim();
        
        csv.push(`"${name}","${email}","${dept}","${role}"`);
    });

    if (visibleRows.length === 0) {
        alert("Nothing to export for the selected filter.");
        return;
    }

    const csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `YCCE_${selectedRole}_List.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<?php require_once '../includes/footer.php'; ?>