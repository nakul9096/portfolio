<?php 
require_once '../includes/header.php'; 

// Logic to change all student passwords at once
if (isset($_POST['global_shuffle'])) {
    // Select only users who are students
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'student'");
    $stmt->execute();
    $students = $stmt->fetchAll();

    foreach ($students as $s) {
        // Keep only letters from the name and make them capital
        $cleanName = strtoupper(preg_replace('/[^A-Za-z]/', '', $s['name']));
        // Get the first 3 letters (use 'XXX' if the name is too short)
        $prefix = str_pad(substr($cleanName, 0, 3), 3, 'X'); 
        // Create a 6-digit random number
        $new_pass = $prefix . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update the password in the database
        $update = $pdo->prepare("UPDATE users SET password = ?, last_password_reset = NOW() WHERE id = ?");
        $update->execute([$new_pass, $s['id']]);
    }
    header("Location: dashboard.php?shuffle_success=1");
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
    
    .neubrutalism-card { 
        background: #ffffff; 
        border: 2px solid #0f172a; 
        box-shadow: 4px 4px 0px #0f172a; 
    }

    .neubrutalism-card-interactive {
        background: #ffffff; 
        border: 2px solid #0f172a; 
        box-shadow: 4px 4px 0px #0f172a;
        transition: all 0.1s ease;
    }

    .neubrutalism-card-interactive:hover {
        transform: translate(1px, 1px);
        box-shadow: 2px 2px 0px #0f172a;
    }
    
    .stat-container {
        background: #ffffff;
        border: 2px solid #0f172a;
        box-shadow: 3px 3px 0px #0f172a;
        padding: 24px;
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

    .btn-danger-brutal {
        background: #ef4444;
        color: #ffffff;
        font-weight: 900;
        border: 2px solid #0f172a;
        box-shadow: 3px 3px 0px #0f172a;
        transition: all 0.1s ease;
    }

    .btn-danger-brutal:hover {
        background: #dc2626;
        transform: translate(1px, 1px);
        box-shadow: 2px 2px 0px #0f172a;
    }
</style>

<div class="min-h-screen py-12 px-6">
    <div class="max-w-7xl mx-auto">
        
        <?php if(isset($_GET['shuffle_success'])): ?>
            <div class="mb-8 p-4 bg-amber-50 border-2 border-black text-amber-950 shadow-[4px_4px_0px_#000] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 border-2 border-black bg-amber-400 flex items-center justify-center text-black text-xs font-black shrink-0">!</div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wide leading-tight">
                        Success: Passwords for all students have been reset to a new pattern.
                    </span>
                </div>
                <a href="dashboard.php" class="text-xs font-black p-1 hover:bg-amber-100 border border-transparent hover:border-black transition-all">✕</a>
            </div>
        <?php endif; ?>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6 pb-6 border-b-2 border-dashed border-slate-300">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 border border-indigo-200">System Admin</span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-slate-900 uppercase">Control Center</h1>
                <p class="text-slate-500 font-bold text-xs uppercase mt-1 tracking-wide">YCCE IT Examination System</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <a href="manage_users.php" class="btn-action btn-primary-dark px-6 py-3 text-xs uppercase tracking-wider text-center flex items-center gap-2">
                    <i class="fa-solid fa-users-gear text-xs"></i> Manage Users
                </a>
                <a href="view_grades.php" class="btn-action px-6 py-3 text-xs uppercase tracking-wider text-center flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-xs"></i> Grade Reports
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="stat-container">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Users</h3>
                    <div class="w-6 h-6 bg-slate-100 border border-black flex items-center justify-center text-slate-900 text-xs">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                </div>
                <?php $total = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(); ?>
                <div class="text-5xl font-black text-slate-900 tracking-tighter"><?= $total ?></div>
                <div class="mt-4 pt-3 border-t-2 border-dashed border-slate-100 flex items-center text-emerald-600 gap-2 font-black uppercase text-[9px] tracking-wide">
                    <span class="w-2 h-2 bg-emerald-500 border border-black rounded-full"></span> Database Connected
                </div>
            </div>

            <div class="stat-container">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Tests</h3>
                    <div class="w-6 h-6 bg-slate-100 border border-black flex items-center justify-center text-slate-900 text-xs">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                </div>
                <?php $total_tests = $pdo->query("SELECT COUNT(*) FROM tests")->fetchColumn(); ?>
                <div class="text-5xl font-black text-slate-900 tracking-tighter"><?= $total_tests ?></div>
                <div class="mt-4 pt-3 border-t-2 border-dashed border-slate-100 flex items-center text-indigo-600 gap-2 font-black uppercase text-[9px] tracking-wide">
                    <i class="fa-solid fa-clock"></i> Active Test Sessions
                </div>
            </div>

            <div class="stat-container bg-slate-900 text-white">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Server Status</h3>
                    <div class="w-6 h-6 bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-300 text-xs">
                        <i class="fa-solid fa-server"></i>
                    </div>
                </div>
                <div class="text-4xl font-black text-white uppercase tracking-tight py-1">Online</div>
                <div class="mt-5 pt-3 border-t-2 border-slate-800 flex items-center text-slate-400 gap-2 font-black uppercase text-[9px] tracking-wide">
                    <i class="fa-solid fa-microchip"></i> Running on Port 8080
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-[10px] font-black text-slate-900 uppercase tracking-[0.2em] mb-6 flex items-center gap-4">
                System Tools <span class="h-[2px] bg-black flex-grow"></span>
            </h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="neubrutalism-card p-8 bg-amber-50 lg:col-span-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-10 h-10 bg-amber-400 border-2 border-black flex items-center justify-center text-black shrink-0">
                                <i class="fa-solid fa-key text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-slate-900 uppercase tracking-tight text-sm">Reset Passwords</h4>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Security Tool</p>
                            </div>
                        </div>
                        
                        <p class="text-xs text-slate-700 font-medium mb-6 leading-relaxed">
                            This creates brand new automatic passwords for <strong>every single student</strong> in the system. The new pattern will be: <span class="font-bold text-slate-900">First 3 Letters + 6 Random Numbers</span>. You cannot undo this step.
                        </p>
                    </div>

                    <form method="POST" onsubmit="return confirm('WARNING: Are you completely sure you want to reset ALL student passwords?')">
                        <button type="submit" name="global_shuffle" class="btn-danger-brutal w-full py-4 uppercase tracking-wider text-xs flex items-center justify-center gap-2">
                            <i class="fa-solid fa-bolt text-xs"></i> Change All Student Passwords
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <a href="add_user.php" class="neubrutalism-card-interactive p-6 flex flex-col justify-between group">
                        <div class="flex justify-between items-start">
                            <div class="w-10 h-10 border-2 border-black bg-white flex items-center justify-center text-slate-900 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                <i class="fa-solid fa-user-plus text-sm"></i>
                            </div>
                            <span class="text-[9px] uppercase font-black px-2 py-0.5 bg-neutral-100 border border-neutral-300 text-neutral-600">Manual Entry</span>
                        </div>
                        <div class="mt-8">
                            <span class="text-xs font-black uppercase tracking-wider block text-slate-900">Add New User</span>
                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Create a single account by hand</p>
                        </div>
                    </a>

                    <a href="bulk_import.php" class="neubrutalism-card-interactive p-6 flex flex-col justify-between group bg-slate-50">
                        <div class="flex justify-between items-start">
                            <div class="w-10 h-10 border-2 border-black bg-white flex items-center justify-center text-slate-900 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                <i class="fa-solid fa-file-csv text-sm"></i>
                            </div>
                            <span class="text-[9px] uppercase font-black px-2 py-0.5 bg-indigo-50 border border-indigo-200 text-indigo-600">File upload</span>
                        </div>
                        <div class="mt-8">
                            <span class="text-xs font-black uppercase tracking-wider block text-slate-900">Bulk Import CSV</span>
                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Add many users from a spread sheet file</p>
                        </div>
                    </a>

                    <a href="manage_users.php" class="neubrutalism-card-interactive p-6 flex flex-col justify-between group">
                        <div class="flex justify-between items-start">
                            <div class="w-10 h-10 border-2 border-black bg-white flex items-center justify-center text-slate-900 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <i class="fa-solid fa-users text-sm"></i>
                            </div>
                            <span class="text-[9px] uppercase font-black px-2 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-600">List View</span>
                        </div>
                        <div class="mt-8">
                            <span class="text-xs font-black uppercase tracking-wider block text-slate-900">Manage Students</span>
                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Edit, update, or remove student accounts</p>
                        </div>
                    </a>

                    <a href="view_grades.php" class="neubrutalism-card-interactive p-6 flex flex-col justify-between group">
                        <div class="flex justify-between items-start">
                            <div class="w-10 h-10 border-2 border-black bg-white flex items-center justify-center text-slate-900 group-hover:bg-slate-900 group-hover:text-white transition-colors">
                                <i class="fa-solid fa-terminal text-sm"></i>
                            </div>
                            <span class="text-[9px] uppercase font-black px-2 py-0.5 bg-neutral-100 border border-neutral-300 text-neutral-600">Grades log</span>
                        </div>
                        <div class="mt-8">
                            <span class="text-xs font-black uppercase tracking-wider block text-slate-900">View Grade List</span>
                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">See completed marks and test sheets</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>