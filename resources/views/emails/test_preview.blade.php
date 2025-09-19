<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template Previews</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* --- CSS Variables for Easy Customization --- */
        :root {
            --accent-color: #c026d3; /* Vibrant Magenta */
            --accent-glow: rgba(192, 38, 211, 0.3);
            --sidebar-bg: rgba(22, 27, 34, 0.6); /* Slightly more opaque for better readability */
            --sidebar-border: rgba(255, 255, 255, 0.08);
            --text-primary: #e6edf3;
            --text-secondary: #7d8590;
            --preview-card-bg: #161b22;
            --preview-card-border: #30363d;
            --base-bg: #0d1117;
        }

        /* --- Aurora Background & Body Styling --- */
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            background-color: var(--base-bg);
            position: relative;
        }

        body::before, body::after {
            content: '';
            position: absolute;
            z-index: -1;
            filter: blur(120px);
            opacity: 0.25;
        }
        body::before {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, #3a00ff, transparent 60%);
            top: -15%;
            left: -15%;
            animation: move-glow1 18s infinite alternate ease-in-out;
        }
        body::after {
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, var(--accent-color), transparent 60%);
            bottom: -20%;
            right: -20%;
            animation: move-glow2 22s infinite alternate ease-in-out;
        }

        @keyframes move-glow1 {
            from { transform: translate(-10%, -15%) rotate(0deg); }
            to { transform: translate(15%, 10%) rotate(45deg); }
        }
        @keyframes move-glow2 {
            from { transform: translate(10%, 15%) rotate(0deg); }
            to { transform: translate(-15%, -10%) rotate(-45deg); }
        }

        /* ENHANCEMENT: Custom Scrollbar for a polished look */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* --- Glassmorphism Sidebar --- */
        .sidebar {
            width: 280px;
            flex-shrink: 0;
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid var(--sidebar-border);
            padding: 25px;
            display: flex;
            flex-direction: column;
            z-index: 10;
            /* ENHANCEMENT: Subtle entry animation */
            animation: slide-in 0.6s ease-out;
        }
        
        @keyframes slide-in {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid var(--sidebar-border);
        }
        .sidebar-header .logo-icon { width: 36px; height: 36px; flex-shrink: 0; }
        .sidebar-header h1 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        /* --- Navigation List --- */
        .email-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
            overflow-y: auto;
        }
        
        /* ENHANCEMENT: Style for category titles in the list */
        .nav-category {
            padding: 20px 5px 10px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .email-list li a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 15px;
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.95rem;
            border-radius: 8px;
            transition: all 0.2s ease-out;
            position: relative;
            margin-bottom: 5px;
        }
        .email-list li a:hover {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.04);
        }

        /* ENHANCEMENT: Refined active state with an animating vertical bar */
        .email-list li a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            height: 0; /* Start with no height */
            width: 4px;
            background: var(--accent-color);
            border-radius: 2px;
            transform: translateY(-50%);
            transition: height 0.25s ease-out;
        }
        .email-list li.active a {
            color: var(--text-primary);
            background: linear-gradient(90deg, rgba(192, 38, 211, 0.1), transparent);
        }
        .email-list li.active a::before {
            height: 60%; /* Animate to full height */
        }
        .email-list .icon { width: 18px; height: 18px; flex-shrink: 0; }

        /* --- Main Content Area --- */
        .main-content {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
             /* ENHANCEMENT: Subtle entry animation */
            animation: fade-in 0.8s ease-out;
        }
        
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .preview-container {
            background-color: var(--preview-card-bg);
            border-radius: 12px;
            border: 1px solid var(--preview-card-border);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            min-height: 100%;
            padding-top: 20px;
            padding-bottom: 20px;
        }
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 100px 40px;
            height: 100%;
            box-sizing: border-box;
        }
        .empty-state h2 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0 0 10px;
        }
        .empty-state p {
            font-size: 1.1rem;
            color: var(--text-secondary);
            max-width: 400px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <svg class="logo-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 13.04v-2.08c0-4.03.94-4.97 4.97-4.97h2.08M22 10.96v2.08c0 4.03-.94 4.97-4.97 4.97h-2.08M17.03 2H9.05C5.02 2 4.08 2.94 4.08 6.97v2.08M19.92 14.95v2.08c0 4.03-.94 4.97-4.97 4.97" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M15.91 9.38 13.11 11.53c-.62.48-1.6.48-2.22 0l-2.8-2.15" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            <h1>Email Previewer</h1>
        </div>

        <ol class="email-list">
            <li class="nav-category">Transactional Emails</li>

            @foreach ($testEmailTypes as $i => $type)
            <li class="{{ request()->get('view') == $type ? 'active' : '' }}">
                <a href="{{ route('test-email-previews', ['view' => $type]) }}">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 20.5H7C4 20.5 2 19 2 15.5V8.5C2 5 4 3.5 7 3.5H17C20 3.5 22 5 22 8.5V15.5C22 19 20 20.5 17 20.5Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path><path d="M17 9L13.87 11.5C12.84 12.32 11.15 12.32 10.12 11.5L7 9" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                    <span>{{ $type }}</span>
                </a>
            </li>
            @endforeach
            
            </ol>
    </div>

    <div class="main-content">
        <div class="preview-container">
             @if(isset($view))
                @include($view)
            @else
                <div class="empty-state">
                    <h2>Select a Template</h2>
                    <p>Choose an email template from the sidebar to see its preview here.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>