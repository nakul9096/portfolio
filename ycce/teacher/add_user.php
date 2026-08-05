<?php 
require_once '../includes/db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ./dashboard.php");
    exit;
}

$user = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $user = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name']);
    $email      = trim($_POST['email']);
    $dept       = $_POST['department'];
    $role       = $_POST['role'];
    $reg_no     = trim($_POST['reg_no']);
    $user_id    = $_POST['user_id'] ?? null;

    $error = '';

    if (!$user_id) { 
        if (empty($name)) {
            $error = "Name is required";
        } else {
            if (!empty($_POST['generated_password'])) {
                $password = trim($_POST['generated_password']);
            } else {
                $cleanName = strtoupper(preg_replace('/[^A-Za-z]/', '', $name));
                $prefix = str_pad(substr($cleanName, 0, 3), 3, 'X'); 
                $password = $prefix . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            }
        }
    } else {
        $password = $_POST['password'] ?? '';
    }

    if (empty($error)) {
        if ($user_id) {
            if (!empty($password)) {
                $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, department=?, role=?, reg_no=?, password=? WHERE id=?");
                $stmt->execute([$name, $email, $dept, $role, $reg_no, $password, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, department=?, role=?, reg_no=? WHERE id=?");
                $stmt->execute([$name, $email, $dept, $role, $reg_no, $user_id]);
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, department, role, reg_no, password) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$name, $email, $dept, $role, $reg_no, $password]);
        }
        header("Location: manage_users.php");
        exit;
    }
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

    .input-sharp {
        border: 2px solid #0f172a;
        font-weight: 700;
        border-radius: 0;
        transition: all 0.1s ease;
        background: #ffffff;
        box-shadow: 2px 2px 0px #0f172a;
    }

    .input-sharp:focus {
        background: #f8fafc;
        box-shadow: 2px 2px 0px #0f172a;
        outline: none;
        border-color: #2563eb;
    }

    .label-sharp {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 6px;
        display: block;
        color: #475569;
    }

    .btn-action-sharp {
        background: #2563eb;
        color: #ffffff;
        border: 2px solid #0f172a;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        transition: all 0.1s ease;
        cursor: pointer;
        box-shadow: 3px 3px 0px #0f172a;
    }

    .btn-action-sharp:hover {
        background: #1d4ed8;
        transform: translate(1px, 1px);
        box-shadow: 2px 2px 0px #0f172a;
    }

    .preview-container {
        border: 2px solid #0f172a;
        background: #fffbeb;
        border-style: dashed;
    }

    .pass-font {
        font-family: monospace;
        font-weight: 800;
        color: #b45309;
    }

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
</style>

<div class="main-content">
    <main class="max-w-4xl mx-auto px-6 py-10">
        
        <div class="mb-6">
            <a href="manage_users.php" class="btn-back-link px-4 py-2">
                <i class="fa-solid fa-arrow-left text-[9px]"></i> Back to Registry
            </a>
        </div>

        <div class="bb-card-sharp p-6 mb-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 uppercase">
                    <?= $user ? 'Edit Enrollment' : 'New Candidate' ?>
                </h1>
                <div class="flex flex-wrap items-center gap-3 mt-1">
                    <span class="label-badge bg-slate-900 text-white px-2 py-0.5 text-[9px]">YCCE Registration Module</span>
                    <span class="label-badge text-slate-500 font-bold">Authorized Faculty Desk</span>
                </div>
            </div>
        </div>

        <div class="bb-card-sharp p-6 sm:p-10 bg-white">
            <form method="POST" class="space-y-8">
                <input type="hidden" name="user_id" value="<?= $user ? $user['id'] : '' ?>">
                
                <?php if (!$user): ?>
                    <input type="hidden" name="generated_password" id="generated-pass-hidden" value="">
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    
                    <div class="md:col-span-2">
                        <label class="label-sharp">Full Name</label>
                        <input type="text" name="name" id="name" 
                               value="<?= $user ? htmlspecialchars($user['name'] ?? '') : '' ?>" 
                               class="input-sharp w-full px-4 py-3 text-sm uppercase font-bold text-slate-800" required>
                    </div>

                    <div>
                        <label class="label-sharp">Email Address</label>
                        <input type="email" name="email" 
                               value="<?= $user ? htmlspecialchars($user['email'] ?? '') : '' ?>" 
                               class="input-sharp w-full px-4 py-3 text-sm" placeholder="2403XXXX@ycce.in" required>
                    </div>

                    <div>
                        <label class="label-sharp">Reg No</label>
                        <input type="text" name="reg_no" 
                               value="<?= $user ? htmlspecialchars($user['reg_no'] ?? '') : '' ?>" 
                               class="input-sharp w-full px-4 py-3 text-sm" placeholder="e.g. 2403XXXX">
                    </div>

                    <div>
                        <label class="label-sharp">Department</label>
                        <div class="relative">
                            <select name="department" class="input-sharp w-full px-4 py-3 text-sm appearance-none cursor-pointer bg-white pr-10">
                                <option value="Computer Science & Design (CSD)" <?= ($user && $user['department'] == 'Computer Science & Design (CSD)') ? 'selected' : '' ?>>CSD - Computer Science & Design</option>
                                <option value="Information Technology (IT)" <?= ($user && $user['department'] == 'Information Technology (IT)') ? 'selected' : '' ?>>IT - Information Technology</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-700">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="label-sharp">Role</label>
                        <div class="relative">
                            <select name="role" class="input-sharp w-full px-4 py-3 text-sm appearance-none cursor-pointer bg-white pr-10">
                                <option value="student" <?= ($user && $user['role'] == 'student') ? 'selected' : '' ?>>CANDIDATE (STUDENT)</option>
                                <option value="teacher" <?= ($user && $user['role'] == 'teacher') ? 'selected' : '' ?>>AUTHORIZED (TEACHER)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-700">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <?php if (!$user): ?>
                    <div class="md:col-span-2 preview-container p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <label class="label-sharp !text-amber-700 font-bold">Password</label>
                                <div class="pass-font text-2xl tracking-tight uppercase mt-0.5" id="generated-pass-preview">
                                    ---XXXXXX
                                </div>
                            </div>
                            <div class="text-right">
                                <i class="fa-solid fa-key text-amber-200 text-3xl"></i>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="md:col-span-2 border-t-2 border-slate-100 pt-6">
                        <label class="label-sharp">Manual Password Override</label>
                        <input type="text" name="password" 
                               class="input-sharp w-full px-4 py-3 text-sm bg-slate-50" 
                               placeholder="Leave blank to keep current credential">
                    </div>
                    <?php endif; ?>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="btn-action-sharp px-10 py-3 text-xs w-full sm:w-auto">
                        <?= $user ? 'Update Profile' : 'Confirm Enrollment' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<hr class="dashboard-footer-divider">

<script>
document.getElementById('name').addEventListener('input', function() {
    if (<?= $user ? 'false' : 'true' ?>) {
        let name = this.value.trim();
        let previewEl = document.getElementById('generated-pass-preview');
        let hiddenEl = document.getElementById('generated-pass-hidden');
        
        if (name.length >= 1) {
            let cleaned = name.replace(/[^A-Za-z]/g, '').toUpperCase();
            let prefix = cleaned.substring(0, 3).padEnd(3, 'X'); 
            
            if (!window.currentRand) {
                window.currentRand = String(Math.floor(Math.random() * 1000000)).padStart(6, '0');
            }
            
            let fullPassword = prefix + window.currentRand;
            previewEl.textContent = fullPassword;
            hiddenEl.value = fullPassword;
        } else {
            previewEl.textContent = "---XXXXXX";
            hiddenEl.value = "";
        }
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>