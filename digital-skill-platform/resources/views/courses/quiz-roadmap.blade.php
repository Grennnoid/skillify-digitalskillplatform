<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $roadmapTitle }} Roadmap | Skillify</title>
    <style>
        :root {
            --bg: #050914;
            --text: #e9f1ff;
            --muted: #9db1d6;
            --accent: #45d0ff;
            --accent-deep: #28b8ea;
            --line: rgba(154, 178, 225, 0.24);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(1200px 620px at 15% -18%, rgba(69, 208, 255, 0.2), transparent 60%),
                radial-gradient(900px 480px at 85% -20%, rgba(124, 246, 214, 0.16), transparent 56%),
                var(--bg);
            overflow: hidden;
        }

        .page {
            height: 100vh;
            padding: 14px 0 12px;
            display: grid;
            grid-template-rows: auto auto 1fr auto;
        }

        .topbar, .hero, .controls, .actions {
            width: min(1240px, 95vw);
            margin-left: auto;
            margin-right: auto;
        }

        .topbar {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 8px 2px 10px;
            border-bottom: 1px solid var(--line);
        }

        .brand-wrap { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .brand-dot {
            width: 11px; height: 11px; border-radius: 50%;
            background: linear-gradient(120deg, var(--accent), #7cf6d6);
            box-shadow: 0 0 12px rgba(69, 208, 255, 0.65);
            flex-shrink: 0;
        }
        .brand { font-weight: 700; color: #eef4ff; }
        .course-chip {
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 6px 11px;
            font-size: 12px;
            color: #cde0ff;
            background: rgba(8, 14, 27, 0.86);
            white-space: nowrap;
        }

        .top-actions { display: flex; align-items: center; gap: 18px; }
        .link {
            text-decoration: none;
            color: #cfe0ff;
            padding: 4px 0;
            font-size: 16px;
            border-bottom: 1px solid transparent;
        }
        .link:hover { color: #eef5ff; border-color: rgba(143, 194, 255, 0.55); }

        .favorite-btn {
            border: 0;
            background: transparent;
            color: #cfe0ff;
            padding: 0;
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
            transition: color 0.18s ease, transform 0.18s ease;
        }

        .favorite-btn.active {
            color: #ffd86d;
        }

        .favorite-btn:hover {
            transform: translateY(-1px);
        }

        .profile-menu {
            position: relative;
            margin-left: 2px;
        }

        .profile-trigger {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 1px solid var(--line);
            overflow: hidden;
            display: grid;
            place-items: center;
            background: rgba(11, 20, 38, 0.86);
            color: #f0f6ff;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            flex-shrink: 0;
        }

        .profile-trigger:hover { border-color: rgba(143, 194, 255, 0.72); }

        .profile-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 50%;
        }

        .profile-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            min-width: 260px;
            border: 1px solid var(--line);
            background: rgba(8, 14, 28, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            padding: 8px;
            display: none;
            box-shadow: 0 24px 56px rgba(0, 0, 0, 0.45);
            z-index: 20;
        }

        .profile-menu.open .profile-dropdown { display: block; }

        .profile-head {
            padding: 10px;
            border-bottom: 1px solid var(--line);
            margin-bottom: 6px;
        }

        .profile-head strong {
            display: block;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .profile-head span {
            color: var(--muted);
            font-size: 12px;
        }

        .profile-item {
            width: 100%;
            text-align: left;
            border: 0;
            background: transparent;
            color: #dbe6ff;
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 14px;
            display: block;
            cursor: pointer;
        }

        .profile-item:hover { background: rgba(121, 240, 212, 0.13); }
        .profile-form { margin: 0; }
        .profile-form button { font-family: inherit; }

        .hero {
            margin-bottom: 8px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: rgba(11, 19, 35, 0.82);
            padding: 12px 16px;
            box-shadow: 0 18px 42px rgba(1, 7, 21, 0.46);
        }
        .hero h1 { margin: 0; font-size: clamp(24px, 3.3vw, 38px); }
        .hero p { margin: 6px 0 0; color: var(--muted); font-size: 14px; }

        .timeline {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            scroll-behavior: smooth;
            padding: 8px 0 0;
            cursor: grab;
            scrollbar-width: none;
        }
        .timeline::-webkit-scrollbar { display: none; }
        .timeline:active { cursor: grabbing; }

        .road { position: relative; min-width: 1760px; height: 360px; padding: 0 max(3vw, 20px); }
        .steps {
            list-style: none;
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            align-items: center;
            gap: 48px;
        }

        .step { width: 182px; position: relative; display: flex; justify-content: center; }
        .step-link { text-decoration: none; color: inherit; display: contents; }
        .step .node {
            width: 156px; height: 156px; border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, #ffffff 0%, #f7f7f7 100%);
            border: 6px solid var(--accent);
            box-shadow: 0 16px 26px rgba(5, 10, 20, 0.3);
            display: grid; place-items: center; text-align: center; z-index: 2;
        }
        .node small { display: block; color: #958d99; letter-spacing: 1px; font-size: 12px; margin-bottom: 3px; }
        .node strong { font-size: 54px; line-height: 1; color: #7a7280; font-weight: 700; }
        .meta { position: absolute; width: 186px; text-align: center; }
        .meta .tag { display: block; color: #9eb2d7; font-size: 11px; margin-bottom: 3px; text-transform: uppercase; font-weight: 700; }
        .meta h3 { margin: 0 0 4px; font-size: 18px; color: #edf3ff; }
        .meta p { margin: 0; font-size: 12px; color: var(--muted); line-height: 1.45; }
        .step.up { transform: translateY(-42px); }
        .step.down { transform: translateY(42px); }
        .step.up .meta { top: 162px; }
        .step.down .meta { bottom: 162px; }
        .step:hover .node { transform: scale(1.03); border-color: var(--accent-deep); }

        .controls {
            margin-top: 4px;
            margin-bottom: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 14px;
        }
        .control-btn {
            width: 42px; height: 42px; border-radius: 999px;
            border: 1px solid rgba(116, 170, 255, 0.42);
            background: linear-gradient(180deg, #0c1b34 0%, #081225 100%);
            color: #e7f0ff; font-size: 16px; cursor: pointer;
        }

        .actions { margin-top: 10px; display: flex; justify-content: flex-end; }
        .start-btn {
            text-decoration: none;
            color: #34250c;
            font-weight: 700;
            border-radius: 12px;
            padding: 10px 16px;
            background: linear-gradient(120deg, #ffdf88, #f2ad18);
        }

        .qa-drawer {
            position: fixed;
            right: 18px;
            top: 82px;
            width: min(420px, calc(100vw - 36px));
            max-height: calc(100vh - 110px);
            overflow: auto;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: rgba(8, 14, 28, 0.97);
            box-shadow: 0 24px 56px rgba(0, 0, 0, 0.45);
            z-index: 30;
            padding: 12px;
            display: none;
        }

        .qa-drawer.open { display: block; }

        .qa-list {
            display: grid;
            gap: 8px;
            margin-top: 10px;
        }

        .qa-item {
            border: 1px solid var(--line);
            border-radius: 10px;
            background: rgba(9, 15, 29, 0.82);
            padding: 10px;
        }

        .qa-item-head {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 6px;
            color: #9db1d6;
            font-size: 12px;
        }
    </style>
</head>
<body>
@php
    $nameParts = preg_split('/\s+/', trim(auth()->user()->name));
    $initials = strtoupper(substr($nameParts[0] ?? '', 0, 1).substr($nameParts[1] ?? '', 0, 1));
    $initials = $initials !== '' ? $initials : 'U';
@endphp
<div class="page">
    <div class="topbar">
        <div class="brand-wrap">
            <span class="brand-dot" aria-hidden="true"></span>
            <span class="brand">Skillify</span>
        </div>
        <div class="top-actions">
            <a class="link" href="{{ route('courses.quiz.info', ['quiz' => $course->id]) }}">Course Info</a>
            <a class="link" href="{{ route('student.dashboard') }}">Dashboard</a>
            <button class="favorite-btn {{ !empty($isFavorite) ? 'active' : '' }}" id="favoriteBtn" type="button" aria-pressed="{{ !empty($isFavorite) ? 'true' : 'false' }}" aria-label="Favorite course">{{ !empty($isFavorite) ? '★' : '☆' }}</button>
            <a class="link" href="{{ route('courses.quiz.info', ['quiz' => $course->id]) }}#review">Review</a>
            <a class="link" href="#qa" id="qaToggleLink">Q&A</a>
            <div class="profile-menu" id="profileMenuRoadmapQuiz">
                <a class="profile-trigger" href="{{ route('profile.show') }}" id="profileTriggerRoadmapQuiz" aria-expanded="false" aria-label="User profile">
                    @if(auth()->user()->profile_image)
                        <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="{{ auth()->user()->name }}" class="profile-image">
                    @else
                        {{ $initials }}
                    @endif
                </a>
                <div class="profile-dropdown">
                    <div class="profile-head">
                        <strong>{{ auth()->user()->name }}</strong>
                        <span>{{ auth()->user()->email }}</span>
                    </div>
                    <a class="profile-item" href="{{ route('profile.show') }}">Profile Settings</a>
                    <a class="profile-item" href="{{ route('login', ['switch' => 1]) }}">Switch Account</a>
                    <form action="{{ route('logout') }}" method="POST" class="profile-form">
                        @csrf
                        <button type="submit" class="profile-item">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <section class="hero">
        <h1>{{ $roadmapTitle }}</h1>
    </section>

    <div class="controls">
        <button class="control-btn" type="button" id="scrollLeftBtn" aria-label="Geser roadmap ke kiri">&larr;</button>
        <button class="control-btn" type="button" id="scrollRightBtn" aria-label="Geser roadmap ke kanan">&rarr;</button>
    </div>

    <div class="timeline" id="timeline">
        <div class="road">
            <ol class="steps">
                @foreach(($chapters ?? []) as $chapter)
                    <li class="step {{ $chapter['position'] }}">
                        <a class="step-link" href="{{ $chapter['href'] }}">
                            <div class="node"><div><small>STEP</small><strong>{{ str_pad((string) $chapter['number'], 2, '0', STR_PAD_LEFT) }}</strong></div></div>
                            <article class="meta">
                                <span class="tag">{{ $chapter['video_ready'] ? 'Video Ready' : 'Chapter' }}</span>
                                <h3>{{ $chapter['title'] }}</h3>
                                <p>{{ $chapter['description'] }}</p>
                            </article>
                        </a>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>

    <div class="actions">
        <a class="start-btn" href="{{ route('courses.quiz.chapter', ['quiz' => $course->id, 'chapter' => 1]) }}">Start Chapter 1</a>
    </div>
</div>

<aside class="qa-drawer" id="qaDrawer">
    <h3 style="margin:0;">Q&A Course</h3>
    <p style="margin:6px 0 0;color:#9db1d6;font-size:13px;">Semua pertanyaan untuk {{ $roadmapTitle }} akan muncul di sini.</p>
    <div class="qa-list">
        @forelse(($roadmapQuestions ?? []) as $qa)
            <article class="qa-item">
                <div class="qa-item-head">
                    <span>{{ $roadmapQuestionUsers[$qa->user_id] ?? 'Student' }}</span>
                    <span>{{ $qa->chapter_number ? 'Chapter '.$qa->chapter_number : 'General' }}</span>
                </div>
                <div style="font-size:14px;line-height:1.55;">{{ $qa->question_text }}</div>
                @if(!empty($qa->answer_text))
                    <div style="margin-top:6px;color:#c9e6ff;font-size:13px;"><strong>Jawaban dosen:</strong> {{ $qa->answer_text }}</div>
                @endif
            </article>
        @empty
            <article class="qa-item">Belum ada pertanyaan.</article>
        @endforelse
    </div>
</aside>

<script>
    const timeline = document.getElementById('timeline');
    const scrollLeftBtn = document.getElementById('scrollLeftBtn');
    const scrollRightBtn = document.getElementById('scrollRightBtn');
    const favoriteBtn = document.getElementById('favoriteBtn');
    const profileMenu = document.getElementById('profileMenuRoadmapQuiz');
    const profileTrigger = document.getElementById('profileTriggerRoadmapQuiz');
    const qaDrawer = document.getElementById('qaDrawer');
    const qaToggleLink = document.getElementById('qaToggleLink');
    let holdTimer = null;

    timeline.addEventListener('wheel', (event) => {
        if (Math.abs(event.deltaY) > Math.abs(event.deltaX)) {
            event.preventDefault();
            timeline.scrollLeft += event.deltaY;
        }
    }, { passive: false });

    function startHoldScroll(direction) {
        stopHoldScroll();
        holdTimer = setInterval(() => { timeline.scrollLeft += 14 * direction; }, 16);
    }
    function stopHoldScroll() {
        if (holdTimer) { clearInterval(holdTimer); holdTimer = null; }
    }

    scrollLeftBtn.addEventListener('mousedown', () => startHoldScroll(-1));
    scrollRightBtn.addEventListener('mousedown', () => startHoldScroll(1));
    window.addEventListener('mouseup', stopHoldScroll);

    favoriteBtn.addEventListener('click', async () => {
        try {
            const response = await fetch('{{ route('courses.favorite', 'quiz-'.$course->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            const active = !!data.is_favorite;
            favoriteBtn.classList.toggle('active', active);
            favoriteBtn.setAttribute('aria-pressed', String(active));
            favoriteBtn.textContent = active ? '★' : '☆';
        } catch (error) {
            // Keep UI stable if request fails.
        }
    });

    if (profileMenu && profileTrigger) {
        profileTrigger.addEventListener('click', function (event) {
            if (!profileMenu.classList.contains('open')) {
                event.preventDefault();
                profileMenu.classList.add('open');
                profileTrigger.setAttribute('aria-expanded', 'true');
            }
        });

        document.addEventListener('click', function (event) {
            if (!profileMenu.contains(event.target)) {
                profileMenu.classList.remove('open');
                profileTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    if (qaDrawer && qaToggleLink) {
        qaToggleLink.addEventListener('click', function (event) {
            event.preventDefault();
            qaDrawer.classList.toggle('open');
        });

        document.addEventListener('click', function (event) {
            if (!qaDrawer.contains(event.target) && event.target !== qaToggleLink) {
                qaDrawer.classList.remove('open');
            }
        });
    }
</script>
</body>
</html>
