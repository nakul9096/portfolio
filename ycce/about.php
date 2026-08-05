<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | YCCE Portal</title>
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

        /* Neo-Brutalist Components */
        .bb-card { background: white; border: 2px solid #0f172a; box-shadow: 4px 4px 0px #0f172a; }
        .btn-black { background: #0f172a; color: white; border: 2px solid #0f172a; box-shadow: 3px 3px 0px #6366f1; transition: 0.2s; cursor: pointer; }
        .btn-black:hover { transform: translate(-2px, -2px); box-shadow: 5px 5px 0px #6366f1; }
        
        .college-frame { border: 2px solid #0f172a; box-shadow: 6px 6px 0px #0f172a; overflow: hidden; background: #e2e8f0; transition: 0.3s; }
        .college-frame:hover { transform: translateY(-5px); box-shadow: 10px 10px 0px #6366f1; }
        
        .profile-img { border: 3px solid #0f172a; box-shadow: 4px 4px 0px #6366f1; transition: 0.3s; }
        
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
            <a href="about.php" class="text-indigo-600">About</a>
        </nav>
    </div>
</header>

<main>
    <div class="max-w-7xl mx-auto px-6 py-12 md:py-20">
        <section class="grid lg:grid-cols-2 gap-12 mb-24 items-start">
            <div class="space-y-6">
                <span class="bg-indigo-600 text-white px-3 py-1 border-2 border-slate-900 font-black text-[10px] uppercase tracking-widest inline-block shadow-[2px_2px_0px_#000]">Official Portal Info</span>
                <h2 class="text-6xl md:text-8xl font-black tracking-tighter leading-[0.85] uppercase">YCCE <br><span class="text-indigo-600 italic">NAGPUR.</span></h2>
                <p class="text-lg font-bold text-slate-600 max-w-md border-l-4 border-indigo-600 pl-6 leading-tight">
                    Yeshwantrao Chavan College of Engineering is a premier autonomous institution in Central India, driving technological innovation since 1984.
                </p>
                <div class="flex gap-4 pt-4">
                    <div class="bb-card px-4 py-2 font-black text-[10px] uppercase tracking-widest bg-yellow-400">Autonomous</div>
                    <div class="bb-card px-4 py-2 font-black text-[10px] uppercase tracking-widest bg-cyan-400">Grade A++</div>
                </div>
            </div>

            <div class="bb-card p-8 relative overflow-hidden bg-white">
                <div class="absolute top-0 right-0 bg-indigo-600 text-white px-4 py-1 font-black text-[10px] uppercase tracking-tighter">Developer</div>
                
                <div class="flex flex-col sm:flex-row items-center gap-8">
                    <div class="shrink-0">
                        <img src="https://ui-avatars.com/api/?name=Nakul+Dhande&background=6366f1&color=fff&size=200" 
                             alt="Nakul Dhande" 
                             class="w-32 h-32 profile-img object-cover rotate-[-3deg] hover:rotate-0">
                    </div>

                    <div class="text-center sm:text-left">
                        <h3 class="text-3xl font-black tracking-tighter uppercase mb-1">Nakul Dhande</h3>
                        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] mb-4">Computer Science & Design (CSD)</p>
                        <p class="text-xs font-bold text-slate-500 mb-6 leading-relaxed">
                            YCCE'27
                        </p>
                        
                        <div class="flex flex-wrap gap-3 justify-center sm:justify-start">
                            <a href="https://linkedin.com/in/nakuldhande" target="_blank" class="btn-black px-4 py-2 text-[10px] font-black uppercase flex items-center gap-2">
                                <i class="fa-brands fa-linkedin"></i> LinkedIn
                            </a>
                            <a href="mailto:nakulnetworkz@gmail.com" class="btn-black px-4 py-2 text-[10px] font-black uppercase flex items-center gap-2">
                                <i class="fa-solid fa-envelope"></i> Email
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-24">
            <div class="flex items-center gap-4 mb-10">
                <h3 class="text-sm font-black uppercase tracking-[0.3em] text-slate-400">System Capabilities</h3>
                <div class="h-[2px] flex-grow bg-slate-200"></div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bb-card p-8 group hover:bg-indigo-50 transition-colors">
                    <div class="w-12 h-12 bg-indigo-600 border-2 border-slate-900 flex items-center justify-center text-white mb-6 group-hover:rotate-12 transition-transform">
                        <i class="fa-solid fa-users-gear text-xl"></i>
                    </div>
                    <h4 class="text-xl font-black uppercase mb-3">Unified Dashboard</h4>
                    <p class="text-sm font-bold text-slate-500 leading-relaxed">
                        A centralized PHP-driven ecosystem with distinct portals for Admins, Teachers, and Students to manage assessments seamlessly.
                    </p>
                </div>
                
                <div class="bb-card p-8 group hover:bg-indigo-50 transition-colors">
                    <div class="w-12 h-12 bg-indigo-600 border-2 border-slate-900 flex items-center justify-center text-white mb-6 group-hover:rotate-12 transition-transform">
                        <i class="fa-solid fa-chart-line text-xl"></i>
                    </div>
                    <h4 class="text-xl font-black uppercase mb-3">Rapid Results</h4>
                    <p class="text-sm font-bold text-slate-500 leading-relaxed">
                        Instant marks calculation and percentage profiling with professional, print-ready academic reports generated immediately.
                    </p>
                </div>
            </div>
        </section>

        <section class="pb-20">
            <div class="flex items-center gap-4 mb-10">
                <h3 class="text-sm font-black uppercase tracking-[0.3em] text-slate-400">Campus Gallery</h3>
                <div class="h-[2px] flex-grow bg-slate-200"></div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="college-frame aspect-[4/3]">
                    <img src="./images/college1.jpg" alt="Campus 1" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-500">
                </div>
                <div class="college-frame aspect-[4/3]">
                    <img src="./images/college2.jpg" alt="Campus 2" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-500">
                </div>
                <div class="college-frame aspect-[4/3]">
                    <img src="./images/college3.jpg" alt="Campus 3" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-500">
                </div>
                <div class="college-frame aspect-[4/3]">
                    <img src="./images/college4.jpg" alt="Campus 4" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-500">
                </div>
            </div>
        </section>
    </div>
</main>

<footer class="bg-white border-t-2 border-slate-900 py-6">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] font-black uppercase tracking-widest text-slate-400">
        <p>© 2026 Nakul Dhande • YCCE Nagpur</p>
        <p>Port 8080 • System Active</p>
    </div>
</footer>

</body>
</html>