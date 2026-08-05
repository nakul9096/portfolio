<?php 
require_once '../includes/header.php'; 

// Delete operation logic
if(isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->execute([$_GET['delete']]);
    header("Location: manage_users.php");
    exit;
}
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800;900&display=swap');
    
    body { 
        background-color: #f4f5f7; 
        font-family: 'Inter', sans-serif; 
        color: #0f172a; 
    }
    
    /* Neubrutalism Box Style */
    .brutal-card {
        background: #ffffff; 
        border: 2px solid #0f172a; 
        box-shadow: 4px 4px 0px #0f172a;
    }

    /* Standard Interactive Button Style */
    .brutal-btn { 
        background: #ffffff;
        color: #0f172a; 
        font-weight: 800;
        border: 2px solid #0f172a; 
        box-shadow: 3px 3px 0px #0f172a; 
        cursor: pointer;
        transition: transform 0.05s, box-shadow 0.05s;
    }

    .brutal-btn:active { 
        transform: translate(2px, 2px);
        box-shadow: 1px 1px 0px #0f172a;
    }

    /* Dark Button Variation */
    .brutal-btn-dark {
        background: #0f172a;
        color: #ffffff;
    }
    .brutal-btn-dark:hover {
        background: #1e293b;
    }

    /* Role Badge styling based on type */
    .brutal-badge {
        border: 2px solid #0f172a; 
        background: #ffffff;
        font-size: 10px; 
        font-weight: 900; 
        padding: 3px 8px;
        text-transform: uppercase;
        display: inline-block;
    }
    
    .badge-student { background-color: #e0f2fe; color: #0369a1; }
    .badge-teacher { background-color: #fef3c7; color: #b45309; }
    .badge-admin { background-color: #f3e8ff; color: #6b21a8; }
    
    /* Class helper to quickly hide table items */
    .hidden-row { display: none !important; }
</style>

<div class="min-h-screen py-12 px-6">
    <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
            <a href="dashboard.php" class="text-[11px] font-black uppercase tracking-wider text-slate-500 hover:text-slate-900 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Go Back To Dashboard
            </a>
        </div>

        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-12 pb-6 border-b-2 border-dashed border-slate-300">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 border border-indigo-200">System Directory</span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-slate-900 uppercase">User Registry</h1>
                <p class="text-slate-500 font-bold text-xs uppercase mt-1 tracking-wide">View and edit registered system profiles</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                
                <select id="roleFilter" class="brutal-btn px-4 h-[46px] text-xs font-black uppercase tracking-wider outline-none bg-white min-w-[140px]" onchange="filterTable()">
                    <option value="all">★ All Users</option>
                    <option value="teacher">Teachers</option>
                    <option value="student">Students</option>
                    <option value="admin">Admins</option>
                </select>

                <button onclick="exportFilteredCSV()" class="brutal-btn px-5 h-[46px] text-xs uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-file-export text-xs"></i> Download CSV File
                </button>
                
                <a href="add_user.php" class="brutal-btn brutal-btn-dark px-6 h-[46px] text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i> Add New User
                </a>
            </div>
        </div>

        <div class="brutal-card overflow-hidden">
            <div class="overflow-x-auto">
                <table id="usersTable" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b-2 border-slate-900 bg-slate-50">
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-900">User Details</th>
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-900">Department</th>
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-900">Account Role</th>
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-slate-900 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-slate-200">
                        <?php
                        // Fetch lists filtered clearly by lowest priority role hierarchy
                        $users = $pdo->query("SELECT * FROM users ORDER BY role ASC")->fetchAll();
                        foreach($users as $user): 
                            $roleLower = strtolower($user['role']);
                            
                            // Badge color selection assignments based on current item values
                            $badgeClass = "badge-student";
                            if($roleLower === 'teacher') $badgeClass = "badge-teacher";
                            if($roleLower === 'admin') $badgeClass = "badge-admin";
                        ?>
                        <tr class="hover:bg-indigo-50/20 transition-colors user-row" data-role="<?= $roleLower ?>">
                            
                            <td class="p-5">
                                <div class="font-black text-slate-900 uppercase italic text-sm user-name"><?= htmlspecialchars($user['name']) ?></div>
                                <div class="text-[10px] font-bold text-indigo-600 uppercase user-email mt-0.5"><?= htmlspecialchars($user['email']) ?></div>
                            </td>
                            
                            <td class="p-5 text-xs font-black text-slate-600 uppercase italic user-dept">
                                <?= !empty($user['department']) ? htmlspecialchars($user['department']) : 'Not Assigned' ?>
                            </td>
                            
                            <td class="p-5">
                                <span class="brutal-badge <?= $badgeClass ?> user-role">
                                    <?= htmlspecialchars($user['role']) ?>
                                </span>
                            </td>
                            
                            <td class="p-5 text-right">
                                <div class="flex justify-end items-center gap-4">
                                    <a href="add_user.php?edit=<?= $user['id'] ?>" class="text-slate-900 hover:text-indigo-600 font-black text-[11px] uppercase border-b-2 border-slate-900 transition-colors">
                                        Edit
                                    </a>
                                    <?php if($roleLower !== 'admin'): ?>
                                        <a href="?delete=<?= $user['id'] ?>" class="text-red-600 hover:text-red-800 font-black text-[11px] uppercase border-b-2 border-red-600 transition-colors" onclick="return confirm('Are you sure you want to delete this user completely?')">
                                            Delete
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
// Filter table rows by selection choice standard rules
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

// Convert active row visibility collections safely to CSV text structures
function exportFilteredCSV() {
    const selectedRole = document.getElementById('roleFilter').value;
    let csvRows = [];
    
    // Header columns titles labels values list array
    csvRows.push("Name,Email,Department,Role");

    const visibleRows = document.querySelectorAll(".user-row:not(.hidden-row)");
    
    visibleRows.forEach(row => {
        const name = row.querySelector(".user-name").innerText.trim();
        const email = row.querySelector(".user-email").innerText.trim();
        const dept = row.querySelector(".user-dept").innerText.trim();
        const role = row.querySelector(".user-role").innerText.trim();
        
        csvRows.push(`"${name}","${email}","${dept}","${role}"`);
    });

    if (visibleRows.length === 0) {
        alert("There are no users to download for this group filter choice.");
        return;
    }

    // Process output file mapping content definitions rules securely
    const csvContent = csvRows.join("\n");
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute("download", `YCCE_User_List_${selectedRole}.csv`);
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<?php require_once '../includes/footer.php'; ?>