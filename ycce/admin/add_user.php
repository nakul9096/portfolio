<?php
require_once '../includes/header.php';

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
            $cleanName = preg_replace('/[^A-Za-z]/', '', $name);
            $firstThree = strtoupper(substr($cleanName, 0, 3));
            $firstThree = str_pad($firstThree, 3, 'X');
            $randomDigits = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $password = $firstThree . $randomDigits;
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

    .brutal-input {
        border: 2px solid #0f172a;
        border-radius: 0;
        font-weight: 700;
        transition: transform 0.05s, box-shadow 0.05s;
    }

    .brutal-input:focus {
        box-shadow: 3px 3px 0px #6366f1;
        outline: none;
        transform: translate(-1px, -1px);
    }

    .brutal-label {
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #0f172a;
        margin-bottom: 6px;
        display: block;
    }

    .brutal-btn { 
        background: #0f172a;
        color: #ffffff; 
        font-weight: 800;
        border: 2px solid #0f172a; 
        box-shadow: 4px 4px 0px #6366f1; 
        cursor: pointer;
        transition: transform 0.05s, box-shadow 0.05s;
    }

    .brutal-btn:active { 
        transform: translate(2px, 2px);
        box-shadow: 2px 2px 0px #6366f1;
    }

    .brutal-alert {
        background: #eff6ff;
        border: 2px solid #0f172a;
        padding: 16px;
    }
</style>

<div class="min-h-screen py-12 px-6">
    <div class="max-w-5xl mx-auto">
        
        <div class="mb-8">
            <a href="manage_users.php" class="text-[11px] font-black uppercase tracking-wider text-slate-500 hover:text-slate-900 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Go Back To List
            </a>
        </div>

        <div class="flex flex-col justify-between items-start gap-2 mb-12 pb-6 border-b-2 border-dashed border-slate-300">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 border border-indigo-200">System Registry</span>
            </div>
            <h1 class="text-4xl font-black tracking-tight text-slate-900 uppercase">
                <?= $user ? 'Edit User Profile' : 'Add New User Account' ?>
            </h1>
            <p class="text-slate-500 font-bold text-xs uppercase mt-1 tracking-wide">Setup or update application login credentials</p>
        </div>

        <div class="brutal-card p-8 md:p-10">
            <form method="POST" class="space-y-8">
                <input type="hidden" name="user_id" value="<?= $user ? $user['id'] : '' ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="brutal-label">Full Name</label>
                        <input type="text" name="name" id="name" 
                               value="<?= $user ? htmlspecialchars($user['name'] ?? '') : '' ?>" 
                               class="brutal-input w-full px-4 py-3 text-sm" required>
                    </div>

                    <div>
                        <label class="brutal-label">Email Address</label>
                        <input type="email" name="email" 
                               value="<?= $user ? htmlspecialchars($user['email'] ?? '') : '' ?>" 
                               class="brutal-input w-full px-4 py-3 text-sm" required>
                    </div>

                    <div>
                        <label class="brutal-label">ID Number</label>
                        <input type="text" name="reg_no" 
                               value="<?= $user ? htmlspecialchars($user['reg_no'] ?? '') : '' ?>" 
                               class="brutal-input w-full px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label class="brutal-label">Department / Branch</label>
                        <select name="department" class="brutal-input w-full px-4 py-3 text-sm appearance-none bg-white">
                            <?php
                            $all_branches = [
                                "Computer Science & Design", "Information Technology", "Computer Technology", 
                                "Computer Science & Engineering", "Electronics Engineering (VLSI Design and Technology)", 
                                "Computer Science and Engineering (IoT)", "Artificial Intelligence and Data Science", 
                                "Computer Science and Engineering (AIML)", "Electronics Engineering", 
                                "Mechanical Engineering", "Civil Engineering", "Electronics and Telecommunication Engg", 
                                "Electrical Engg (Electronics and Power)"
                            ];
                            foreach($all_branches as $branch):
                                $selected = ($user && $user['department'] == $branch) ? 'selected' : '';
                                echo "<option value=\"$branch\" $selected>$branch</option>";
                            endforeach;
                            ?>
                        </select>
                    </div>

                    <div>
                        <label class="brutal-label">System Role</label>
                        <select name="role" class="brutal-input w-full px-4 py-3 text-sm appearance-none bg-white uppercase font-black">
                            <option value="student" <?= ($user && $user['role'] == 'student') ? 'selected' : '' ?>>Student</option>
                            <option value="teacher" <?= ($user && $user['role'] == 'teacher') ? 'selected' : '' ?>>Teacher</option>
                        </select>
                    </div>

                    <?php if (!$user): ?>
                    <div class="brutal-alert md:col-span-2">
                        <p class="text-[10px] font-black uppercase mb-1 tracking-wider text-slate-400">Automatic Password Preview</p>
                        <p class="text-xs font-bold text-slate-700 mb-3">Pattern: First 3 Letters + 6 Random Numbers</p>
                        <div class="inline-block bg-white border-2 border-slate-900 px-4 py-1.5 font-mono text-xs font-black shadow-[2px_2px_0px_#000]" id="generated-pass-preview">
                            WAITING...
                        </div>
                    </div>
                    <?php else: ?>
                    <div>
                        <label class="brutal-label">Set New Password</label>
                        <input type="text" name="password" class="brutal-input w-full px-4 py-3 text-sm bg-amber-50/50" placeholder="Leave this empty to keep old password">
                    </div>
                    <?php endif; ?>
                </div>

                <div class="pt-6 border-t-2 border-dashed border-slate-200 flex justify-end">
                    <button type="submit" class="brutal-btn px-10 py-3.5 text-xs uppercase tracking-wider">
                        <?= $user ? 'Save Changes' : 'Create User Account' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const nameInput = document.getElementById('name');
if (nameInput) {
    nameInput.addEventListener('input', function() {
        if (<?= $user ? 'false' : 'true' ?>) {
            let name = this.value.trim();
            if (name.length >= 1) {
                let cleaned = name.replace(/[^A-Za-z]/g, '').substring(0, 3).toUpperCase();
                let firstThree = cleaned.padEnd(3, 'X');
                document.getElementById('generated-pass-preview').textContent = firstThree + "######";
            } else {
                document.getElementById('generated-pass-preview').textContent = "WAITING...";
            }
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>