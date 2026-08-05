<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support | YCCE Portal</title>
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

        header, footer { flex-shrink: 0; }

        main { 
            flex-grow: 1; 
            overflow-y: auto; 
            scrollbar-width: thin;
        }

        .bb-card { background: white; border: 2px solid #0f172a; box-shadow: 4px 4px 0px #0f172a; transition: 0.3s; }
        .bb-card:hover { transform: translate(-2px, -2px); box-shadow: 8px 8px 0px #6366f1; }
        
        .btn-black { background: #0f172a; color: white; border: 2px solid #0f172a; box-shadow: 3px 3px 0px #6366f1; transition: 0.2s; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
        .btn-black:hover { transform: translate(-1px, -1px); box-shadow: 5px 5px 0px #6366f1; }
        
        .icon-box { width: 50px; height: 50px; background: #6366f1; border: 2px solid #0f172a; display: flex; align-items: center; justify-content: center; color: white; transform: rotate(-5deg); }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #0f172a; border: 1px solid #f1f1f1; }
    </style>
</head>
<body class="antialiased">

<header class="bg-white border-b-2 border-slate-900 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 border-2 border-slate-900 rounded-lg flex items-center justify-center text-white font-black text-xl">Y</div>
            <h1 class="text-xl font-black uppercase italic tracking-tighter">YCCE IT EXAMINATION SYSTEM</h1>
        </div>
        <nav class="hidden md:flex items-center gap-8 text-[10px] font-black uppercase tracking-widest">
            <a href="index.php" class="hover:text-indigo-600 text-slate-900 transition-colors">Home</a>
            <a href="about.php" class="hover:text-indigo-600 text-slate-900 transition-colors">About</a>
            <a href="help.php" class="text-indigo-600">Help</a>
        </nav>
    </div>
</header>

<main>
    <div class="max-w-5xl mx-auto px-6 py-16">
        <div class="text-center mb-16">
            <span class="bg-yellow-400 text-slate-900 px-3 py-1 border-2 border-slate-900 font-black text-[10px] uppercase tracking-widest mb-4 inline-block shadow-[2px_2px_0px_#000]">Support Desk</span>
            <h2 class="text-5xl md:text-6xl font-black tracking-tighter uppercase leading-none">Need <span class="text-indigo-600 italic">Assistance?</span></h2>
            <p class="mt-4 text-slate-500 font-bold max-w-lg mx-auto">Having trouble with your access key or assessment reports? Reach out to the authorized personnel below.</p>
        </div>

        <div class="flex justify-center">
            <div class="bb-card p-8 flex flex-col items-center text-center w-full max-w-md">
                <div class="icon-box mb-6">
                    <i class="fa-solid fa-user-tie text-2xl"></i>
                </div>
                <h3 class="text-2xl font-black uppercase tracking-tight mb-1">Dr. Sushil Chavan</h3>
                <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-6">Institutional Head / Exam Dept.</p>
                
                <p class="text-sm font-bold text-slate-500 mb-8 leading-relaxed">
                    For queries regarding examination scheduling, academic eligibility, or institutional access keys.
                </p>
                
                <div class="w-full space-y-3">
                    <a href="mailto:Infotechtpc@gmail.com" class="btn-black w-full justify-center py-3 text-[10px] font-black uppercase tracking-widest">
                        <i class="fa-solid fa-envelope"></i> Contact Dept
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-16 bb-card p-6 border-l-8 border-l-yellow-400 bg-slate-50">
            <p class="text-xs font-bold text-slate-600 italic">
                <i class="fa-solid fa-circle-info mr-2 text-indigo-600"></i> Note: Please ensure you are using your official <strong>@ycce.edu</strong> email before contacting.
            </p>
        </div>
    </div>
</main>

<footer class="bg-white border-t-2 border-slate-900 py-6">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] font-black uppercase tracking-widest text-slate-400">
        <p>© 2026 Nakul Dhande • YCCE Nagpur</p>
        <p>Port 8080 • Support System Active</p>
    </div>
</footer>

</body>
</html>