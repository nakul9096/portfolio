<?php
session_start();
require_once 'includes/db.php';

$error = null;

if (isset($_POST['login_action'])) {
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['name']    = $user['name'];

            $path = ($user['role'] === 'admin') ? 'admin/dashboard.php' : (($user['role'] === 'teacher') ? 'teacher/dashboard.php' : 'student/dashboard.php');
            header("Location: $path");
            exit;
        } else {
            $error = "Incorrect Access Key.";
        }
    } else {
        $error = "Identity not found. Check your email.";
    }
}

if (isset($_SESSION['user_id'])) {
    $path = ($_SESSION['role'] === 'admin') ? 'admin/dashboard.php' : (($_SESSION['role'] === 'teacher') ? 'teacher/dashboard.php' : 'student/dashboard.php');
    header("Location: $path");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YCCE EXAMINATION PORTAL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc; 
            color: #0f172a; 
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        header { flex-shrink: 0; }

        main { 
            flex-grow: 1; 
            overflow-y: auto; 
            scrollbar-width: thin;
        }

        footer { 
            flex-shrink: 0; 
            z-index: 50;
        }

        .bb-card { background: white; border: 2px solid #0f172a; box-shadow: 4px 4px 0px #0f172a; }
        .btn-black { background: #0f172a; color: white; border: 2px solid #0f172a; box-shadow: 3px 3px 0px #6366f1; transition: 0.2s; cursor: pointer; }
        .btn-black:hover { transform: translate(-2px, -2px); box-shadow: 5px 5px 0px #6366f1; }
        .input-bb { border: 2px solid #0f172a; transition: all 0.2s; }
        .input-bb:focus { box-shadow: 4px 4px 0px #6366f1; outline: none; }
        .college-frame { border: 2px solid #0f172a; box-shadow: 6px 6px 0px #0f172a; overflow: hidden; transition: 0.3s; background: #e2e8f0; }
        .college-frame:hover { transform: translateY(-5px); box-shadow: 10px 10px 0px #6366f1; }
    </style>
</head>
<body class="antialiased">

<header class="bg-white border-b-2 border-slate-900 z-50">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 border-2 border-slate-900 rounded-lg flex items-center justify-center text-white font-black text-xl">Y</div>
            <h1 class="text-xl font-black uppercase italic tracking-tighter">YCCE IT EXAMINATION SYSTEM</h1>
        </div>

        <div class="hidden md:flex items-center gap-8 text-[10px] font-black uppercase tracking-widest">
            <a href="index.php" class="hover:text-indigo-600 text-slate-900 transition-colors">Home</a>
            <a href="about.php" class="hover:text-indigo-600 text-slate-900 transition-colors">About</a>
            <a href="user-guide.php" class="hover:text-indigo-600 text-slate-900 transition-colors">User Guide</a>
            <div class="h-4 w-[2px] bg-slate-900"></div>
            <a href="https://ycce.edu" class="hover:text-indigo-600 text-slate-900">Official YCCE Portal</a>
            <a href="help.php" class="hover:text-indigo-600 text-slate-900">Help</a>
        </div>
    </div>
</header>

<main>
    <div class="max-w-7xl mx-auto px-6">
        <section class="py-16 grid md:grid-cols-2 gap-16 items-center">
            <div>
                <span class="bg-indigo-600 text-white px-3 py-1 border-2 border-slate-900 font-black text-[10px] uppercase tracking-widest mb-6 inline-block shadow-[2px_2px_0px_#000]">Official Assessment Engine</span>
                <h1 class="text-6xl md:text-8xl font-black leading-none mb-8 tracking-tighter">SECURE<br><span class="text-indigo-600 italic">TESTING.</span></h1>
                <p class="text-lg font-bold text-slate-600 max-w-md">The unified proctoring and assessment platform for Yeshwantrao Chavan College of Engineering, Nagpur.</p>
            </div>

            <div>
                <div class="bb-card p-10 rounded-2xl">
                    <div class="mb-8">
                        <h2 class="text-2xl font-black uppercase italic tracking-tight">Login</h2>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Enter Portal Credentials</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="mb-6 p-4 border-2 border-red-500 bg-red-50 text-red-600 font-black text-xs uppercase italic flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation"></i> <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form action="index.php" method="POST" class="space-y-6">
                        <input type="hidden" name="login_action" value="1">
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest block mb-2">Institutional Email</label>
                            <input type="email" name="email" placeholder="reg@ycce.in" required 
                                   class="w-full p-4 input-bb font-bold text-sm">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest block mb-2">Access Key</label>
                            <input type="password" name="password" placeholder="••••••••" required 
                                   class="w-full p-4 input-bb font-bold text-sm">
                        </div>
                        <button type="submit" class="w-full py-5 btn-black font-black uppercase tracking-widest text-sm">
                            Verify & Enter
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section class="pb-24">
            <div class="flex items-center gap-4 mb-10">
                <h3 class="text-sm font-black uppercase tracking-[0.3em] text-slate-400">Campus Gallery</h3>
                <div class="h-[2px] flex-grow bg-slate-200"></div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="college-frame aspect-[4/3]">
                    <img src="./images/college1.jpg" alt="YCCE Campus 1" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all">
                </div>
                <div class="college-frame aspect-[4/3]">
                    <img src="./images/college2.jpg" alt="YCCE Campus 2" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all">
                </div>
                <div class="college-frame aspect-[4/3]">
                    <img src="./images/college3.jpg" alt="YCCE Campus 3" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all">
                </div>
                <div class="college-frame aspect-[4/3]">
                    <img src="./images/college4.jpg" alt="YCCE Campus 4" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all">
                </div>
            </div>
        </section>
    </div>
</main>

<footer class="bg-white border-t-2 border-slate-900 py-6">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] font-black uppercase tracking-widest">
        <p class="flex items-center gap-2">
            © 2026 
            <a href="https://linkedin.com/in/nakuldhande" 
               target="_blank" 
               rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 text-slate-900 hover:text-indigo-600 transition-all duration-200 border-b-2 border-dashed border-slate-900 hover:border-indigo-600 pb-px">
                Nakul Dhande
                <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
            </a> 
            • YCCE Nagpur
        </p>
        <p>Port 8080 • System Active</p>
    </div>
</footer>

</body>
</html>