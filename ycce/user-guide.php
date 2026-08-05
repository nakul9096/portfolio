<?php 
require_once 'includes/db.php';

// Public Student Reference Documents
$student_guides = [
    [
        'title' => 'Student User Manual',
        'desc' => 'Comprehensive platform walkthrough covering account access, profile synchronization, and system settings navigation.',
        'file' => 'GUIDEBOOK YCCE EXAMINATION SYSTEM STUDENTS.pdf',
        'icon' => 'fa-user-graduate'
    ],
    [
        'title' => 'Exam Instructions',
        'desc' => 'Official rules and operational protocols for terminal authentication, proctoring requirements, and online test submissions.',
        'file' => 'GUIDEBOOK_YCCE_IT_EXAMINATION_SYSTEM.pdf',
        'icon' => 'fa-file-invoice'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Guides | YCCE Assessment Portal</title>
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

        footer { flex-shrink: 0; z-index: 50; }

        .bb-card { background: white; border: 2px solid #0f172a; box-shadow: 4px 4px 0px #0f172a; }
        
        .btn-black { 
            background: #0f172a; 
            color: white; 
            border: 2px solid #0f172a; 
            box-shadow: 3px 3px 0px #6366f1; 
            transition: 0.2s; 
            cursor: pointer; 
            text-transform: uppercase;
            font-weight: 900;
            letter-spacing: 0.1em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-black:hover { transform: translate(-2px, -2px); box-shadow: 5px 5px 0px #6366f1; }
        
        .btn-outline-bb {
            background: white;
            color: #0f172a;
            border: 2px solid #0f172a;
            box-shadow: 3px 3px 0px #0f172a;
            transition: 0.2s;
            cursor: pointer;
            text-transform: uppercase;
            font-weight: 900;
            letter-spacing: 0.1em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-outline-bb:hover { transform: translate(-2px, -2px); box-shadow: 5px 5px 0px #6366f1; }

        /* Modal Overlay Windows */
        .viewer-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(4px);
            z-index: 100;
            padding: 24px;
        }

        .modal-box {
            background: #ffffff;
            border: 2px solid #0f172a;
            box-shadow: 6px 6px 0px #0f172a;
            width: 100%;
            max-width: 1100px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
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
            <a href="user-guide.php" class="text-indigo-600 transition-colors">User Guide</a>
            <div class="h-4 w-[2px] bg-slate-900"></div>
            <a href="https://ycce.edu" target="_blank" class="hover:text-indigo-600 text-slate-900">Official YCCE Portal</a>
            <a href="help.php" class="hover:text-indigo-600 text-slate-900">Help</a>
        </div>
    </div>
</header>

<main class="bg-slate-50">
    <div class="max-w-5xl mx-auto px-6 py-12">
        
        <div class="bb-card p-8 mb-12 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
            <div>
                <span class="bg-indigo-600 text-white px-3 py-1 border border-slate-900 font-black text-[9px] uppercase tracking-widest mb-2 inline-block shadow-[2px_2px_0px_#000]">Knowledge Base</span>
                <h2 class="text-3xl font-black uppercase tracking-tight">Student Reference Guides</h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Review guidelines or download system manuals directly prior to authentication.</p>
            </div>
            <a href="index.php" class="btn-outline-bb px-6 py-3.5 text-xs">
                <i class="fa-solid fa-arrow-left text-[11px]"></i> Back to Login
            </a>
        </div>

        <div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php foreach ($student_guides as $guide): ?>
                    <div class="bb-card p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 border-2 border-slate-900 flex items-center justify-center bg-indigo-50 text-slate-900 text-xl font-black shadow-[2px_2px_0px_#0f172a]">
                                    <i class="fa-solid <?= $guide['icon'] ?>"></i>
                                </div>
                                <div>
                                    <h3 class="text-md font-black text-slate-900 uppercase tracking-tight leading-tight"><?= htmlspecialchars($guide['title']) ?></h3>
                                    <span class="text-[9px] font-mono font-bold uppercase text-slate-400">PDF Document</span>
                                </div>
                            </div>
                            <p class="text-xs font-bold text-slate-500 mb-6 leading-relaxed"><?= htmlspecialchars($guide['desc']) ?></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t-2 border-dashed border-slate-100">
                            <button type="button" onclick="openDocViewport('<?= htmlspecialchars($guide['file']) ?>', '<?= htmlspecialchars($guide['title']) ?>')" class="btn-black py-3 text-xs">
                                <i class="fa-solid fa-eye text-[11px]"></i> Open Frame
                            </button>
                            <a href="<?= htmlspecialchars($guide['file']) ?>" download class="btn-outline-bb py-3 text-xs">
                                <i class="fa-solid fa-download text-[11px]"></i> Download
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</main>

<div id="pdfWorkspaceModal" class="viewer-modal items-center justify-center">
    <div class="modal-box">
        <div class="border-b-2 border-slate-900 p-4 bg-slate-50 flex justify-between items-center">
            <div>
                <span class="text-indigo-600 font-black block text-[9px] uppercase tracking-widest mb-0.5">Secure Document Window</span>
                <h3 id="modalDocTitle" class="text-xs font-black text-slate-900 uppercase tracking-tight">Document Viewer</h3>
            </div>
            <button type="button" onclick="closeDocViewport()" class="btn-outline-bb px-4 py-2 text-[10px] !bg-red-50 hover:!bg-red-100 !text-red-600 font-black shadow-[2px_2px_0px_#0f172a]">
                <i class="fa-solid fa-xmark"></i> Close Frame
            </button>
        </div>
        <div class="flex-1 bg-slate-700 relative">
            <iframe id="pdfFrameViewport" class="w-full h-full border-0 absolute inset-0" src="" type="application/pdf"></iframe>
        </div>
    </div>
</div>

<footer class="bg-white border-t-2 border-slate-900 py-6">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] font-black uppercase tracking-widest text-slate-400">
        <p>© 2026 Nakul Dhande • YCCE Nagpur</p>
        <p>Port 8080 • System Active</p>
    </div>
</footer>

<script>
    function openDocViewport(fileUrl, docTitle) {
        const modal = document.getElementById('pdfWorkspaceModal');
        const frame = document.getElementById('pdfFrameViewport');
        const title = document.getElementById('modalDocTitle');

        if (modal && frame && title) {
            title.textContent = docTitle;
            frame.src = fileUrl; 
            
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDocViewport() {
        const modal = document.getElementById('pdfWorkspaceModal');
        const frame = document.getElementById('pdfFrameViewport');

        if (modal && frame) {
            modal.style.display = 'none';
            frame.src = ''; 
            document.body.style.overflow = '';
        }
    }

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDocViewport();
        }
    });
</script>

</body>
</html>