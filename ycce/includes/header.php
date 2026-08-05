<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ycce/login.php");
    exit;
}
// Bulletproof path logic: Up one level from 'includes' to find db.php
require_once dirname(__DIR__) . '/includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examify • YCCE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc; /* Subtle grey background */
            color: #0f172a; 
        }
        /* Global Blackish Border Utility */
        .border-blackish { border: 2px solid #94a3b8; } /* Slate-400 */
        .border-blackish-thick { border: 3px solid #64748b; } /* Slate-500 */
        
        .card-container {
            background: #ffffff;
            border: 2px solid #94a3b8;
            border-radius: 2.5rem;
            box-shadow: 0 4px 10px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

<nav class="sticky top-0 z-[100] bg-white border-b-2 border-slate-400 px-8 py-2 flex justify-between items-center shadow-sm">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-slate-900 rounded-xl flex items-center justify-center font-black text-white">YCCE</div>
        <span class="text-1xl font-black tracking-tighter text-slate-900 uppercase">YCCE IT EXAMINATION SYSTEM</span>
    </div>

    <div class="flex items-center gap-6">
        <div class="text-right hidden sm:block">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Status: Active</p>
            <p class="text-sm font-black text-slate-800"><?= htmlspecialchars($_SESSION['name']) ?></p>
        </div>
        <div class="h-8 w-px bg-slate-300"></div>
        <a href="/ycce/logout.php" class="w-10 h-10 border-2 border-slate-400 rounded-xl flex items-center justify-center text-slate-400 hover:text-red-600 hover:border-red-600 transition-all">
            <i class="fa-solid fa-power-off"></i>
        </a>
    </div>
</nav>