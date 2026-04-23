<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | Skillify</title>
    <style>
        :root {
            --bg: #04070f;
            --panel: rgba(12, 18, 34, 0.72);
            --line: rgba(160, 181, 222, 0.24);
            --text: #eef4ff;
            --muted: #9aadd3;
            --primary: #79f0d4;
            --shadow: 0 24px 56px rgba(0, 0, 0, 0.45);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--text);
            font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif;
            background:
                radial-gradient(980px 540px at 20% -10%, rgba(48, 84, 159, 0.35), transparent 62%),
                radial-gradient(980px 540px at 90% -20%, rgba(121, 240, 212, 0.2), transparent 62%),
                linear-gradient(180deg, #03050b 0%, #060a14 100%);
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 24px 0 44px;
        }

        .nav,
        .hero,
        .lower {
            width: min(1180px, 94vw);
            margin-left: auto;
            margin-right: auto;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .brand {
            font-size: 22px;
            letter-spacing: 0.2px;
            font-weight: 700;
        }

        .brand span {
            display: inline-block;
            width: 24px;
            height: 2px;
            background: var(--text);
            margin-left: 8px;
            opacity: 0.7;
            transform: translateY(-6px);
        }

        .menu {
            display: flex;
            gap: 22px;
            flex-wrap: wrap;
            align-items: center;
        }

        .menu a, .menu button {
            background: none;
            border: 0;
            color: #d4e3ff;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            padding: 7px 5px;
        }

        .menu a:hover, .menu button:hover { color: #ffffff; }

        .courses-menu,
        .progress-menu,
        .home-menu {
            position: relative;
        }

        .courses-trigger,
        .progress-trigger,
        .home-trigger {
            background: none;
            border: 0;
            color: #d4e3ff;
            font-size: 16px;
            cursor: pointer;
            padding: 7px 5px;
            display: inline-flex;
            align-items: center;
        }

        .courses-trigger:hover,
        .progress-trigger:hover,
        .home-trigger:hover { color: #ffffff; }

        .courses-dropdown,
        .progress-dropdown,
        .home-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            min-width: 230px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(8, 14, 28, 0.98);
            box-shadow: var(--shadow);
            padding: 8px;
            display: none;
            z-index: 26;
        }

        .courses-menu.open .courses-dropdown,
        .progress-menu.open .progress-dropdown,
        .home-menu.open .home-dropdown { display: block; }

        .courses-item,
        .progress-item,
        .home-item {
            display: block;
            color: #dbe6ff;
            text-decoration: none;
            border-radius: 9px;
            padding: 8px 10px;
            font-size: 13px;
            width: 100%;
            text-align: left;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .courses-item:hover,
        .progress-item:hover,
        .home-item:hover { background: rgba(121, 240, 212, 0.13); }

        .courses-empty,
        .progress-empty,
        .home-empty {
            color: var(--muted);
            font-size: 12px;
            padding: 8px 10px;
        }

        .my-courses-menu {
            position: relative;
        }

        .my-courses-trigger {
            background: none;
            border: 0;
            color: #d4e3ff;
            font-size: 16px;
            cursor: pointer;
            padding: 7px 5px;
            display: inline-flex;
            align-items: center;
        }

        .my-courses-trigger:hover { color: #ffffff; }

        .my-courses-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            min-width: 220px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(8, 14, 28, 0.98);
            box-shadow: var(--shadow);
            padding: 8px;
            display: none;
            z-index: 25;
        }

        .my-courses-menu.open .my-courses-dropdown { display: block; }

        .my-courses-item {
            display: block;
            text-decoration: none;
            color: #dbe6ff;
            font-size: 13px;
            border-radius: 9px;
            padding: 8px 10px;
        }

        .my-courses-item:hover { background: rgba(121, 240, 212, 0.13); }

        .my-courses-empty {
            color: var(--muted);
            font-size: 12px;
            padding: 8px 10px;
        }

        .mentors-menu {
            position: relative;
        }

        .mentors-trigger {
            background: none;
            border: 0;
            color: #d4e3ff;
            font-size: 16px;
            cursor: pointer;
            padding: 7px 5px;
            display: inline-flex;
            align-items: center;
        }

        .mentors-trigger:hover { color: #ffffff; }

        .mentors-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            min-width: 230px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(8, 14, 28, 0.98);
            box-shadow: var(--shadow);
            padding: 8px;
            display: none;
            z-index: 26;
        }

        .mentors-menu.open .mentors-dropdown { display: block; }

        .mentor-item {
            display: block;
            color: #dbe6ff;
            text-decoration: none;
            border-radius: 9px;
            padding: 8px 10px;
            font-size: 13px;
        }

        .mentor-item:hover { background: rgba(121, 240, 212, 0.13); }

        .mentor-empty {
            color: var(--muted);
            font-size: 12px;
            padding: 8px 10px;
        }

        .profile-menu {
            position: relative;
            margin-left: 2px;
        }

        .profile-trigger {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            border: 2px solid rgba(121, 240, 212, 0.38);
            background: #0f172b;
            color: #f3f7ff;
            font-weight: 700;
            font-size: 16px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 0 0 4px rgba(121, 240, 212, 0.13);
            overflow: hidden;
            line-height: 1;
        }

        .profile-trigger:hover { border-color: rgba(121, 240, 212, 0.65); }

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
            box-shadow: var(--shadow);
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

        .hero {
            text-align: center;
            margin: 64px auto 30px;
            max-width: 780px;
        }

        .hero h1 {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(34px, 6vw, 62px);
            line-height: 1.06;
            letter-spacing: -0.6px;
            font-weight: 500;
        }

        .hero p {
            margin: 14px 0 0;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        .slider-shell {
            margin-top: 26px;
            position: relative;
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            padding-bottom: 34px;
        }

        .slider-controls {
            position: absolute;
            right: clamp(16px, 4vw, 48px);
            top: -54px;
            display: flex;
            gap: 8px;
        }

        .ctrl {
            border: 1px solid var(--line);
            width: 40px;
            height: 40px;
            border-radius: 999px;
            background: rgba(11, 18, 34, 0.86);
            color: #e6f0ff;
            cursor: pointer;
        }

        .ctrl:hover { border-color: rgba(121, 240, 212, 0.7); }

        .slider {
            display: flex;
            gap: 18px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            padding: 16px calc((100vw - 276px) / 2) 86px;
            scrollbar-width: none;
        }

        .slider::-webkit-scrollbar { display: none; }

        .card {
            flex: 0 0 276px;
            height: 384px;
            border-radius: 26px;
            border: 1px solid rgba(215, 229, 255, 0.22);
            box-shadow: var(--shadow);
            position: relative;
            overflow: visible;
            scroll-snap-align: center;
            transition: transform 0.35s ease, opacity 0.35s ease;
            transform: scale(0.88) rotateY(8deg);
            opacity: 0.5;
            display: flex;
            align-items: flex-end;
            padding: 18px;
            cursor: pointer;
        }

        .card.has-image {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .card::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(to top, rgba(5, 7, 13, 0.86), rgba(5, 7, 13, 0.08));
        }

        .card-content {
            position: relative;
            z-index: 1;
        }

        .card small {
            display: block;
            color: #c8d8fa;
            margin-bottom: 6px;
            font-size: 12px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .card h3 {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 36px;
            line-height: 1.05;
            font-weight: 500;
        }

        .card p {
            margin: 8px 0 0;
            font-size: 14px;
            color: #d7e5ff;
            line-height: 1.55;
        }

        .card.active {
            transform: scale(1) rotateY(0deg);
            opacity: 1;
        }

        .card-link {
            color: inherit;
            text-decoration: none;
        }

        .c1 { background: radial-gradient(circle at 25% 20%, #ff5d4f 0%, #731610 46%, #18090a 100%); }
        .c2 { background: radial-gradient(circle at 62% 24%, #ffe87e 0%, #8c6224 44%, #17130e 100%); }
        .c3 { background: radial-gradient(circle at 35% 26%, #5fd9ff 0%, #1e607f 46%, #0c1219 100%); }
        .c4 { background: radial-gradient(circle at 60% 18%, #ec84ff 0%, #633878 48%, #100b14 100%); }
        .c5 { background: radial-gradient(circle at 40% 28%, #7dffbe 0%, #1c7050 48%, #0a1310 100%); }
        .c6 { background: radial-gradient(circle at 45% 25%, #ff9f8a 0%, #7d3f32 48%, #130d0b 100%); }

        .lower {
            margin-top: 34px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .panel {
            border: 1px solid var(--line);
            background: var(--panel);
            border-radius: 16px;
            padding: 16px;
        }

        .panel h4 {
            margin: 0 0 10px;
            font-size: 15px;
        }

        .panel p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .course-list {
            margin: 0;
            padding-left: 18px;
            display: grid;
            gap: 8px;
        }

        .course-list a {
            color: #dbe6ff;
            text-decoration: none;
            font-size: 13px;
        }

        .course-list a:hover { color: #ffffff; }

        .progress-track {
            margin-top: 10px;
            height: 8px;
            border-radius: 99px;
            background: rgba(145, 170, 219, 0.24);
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 64%;
            background: linear-gradient(120deg, #67d7ff, #79f0d4);
        }

        @media (max-width: 980px) {
            .lower { grid-template-columns: 1fr; }
            .slider-controls {
                position: static;
                justify-content: flex-end;
                margin-bottom: 8px;
                padding-right: clamp(10px, 3vw, 22px);
            }
        }

        @media (max-width: 700px) {
            .menu { display: none; }
            .slider { padding-inline: calc((100vw - 78vw) / 2); }
            .card { flex-basis: 78vw; height: 320px; }
            .hero { margin-top: 40px; }
        }

        .site-footer {
            width: min(1180px, 94vw);
            margin: 26px auto 0;
            border-top: 1px solid var(--line);
            padding-top: 16px;
            color: var(--muted);
            font-size: 13px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    @if(session('success'))
        <div style="width:min(1180px,94vw);margin:0 auto 12px;border:1px solid rgba(73,214,139,.4);border-radius:10px;background:rgba(12,42,24,.5);color:#b4f2cc;padding:10px 12px;font-size:13px;">
            {{ session('success') }}
        </div>
    @endif
    <header class="nav">
        <div class="brand">Skillify<span></span></div>
        <nav class="menu">
            <div class="courses-menu" id="coursesMenu">
                <button class="courses-trigger" id="coursesTrigger" type="button" aria-expanded="false">Courses</button>
                <div class="courses-dropdown">
                    @forelse(($carouselCourses ?? []) as $course)
                        <a class="courses-item" href="{{ $course['href'] }}">{{ $course['title'] }}</a>
                    @empty
                        <div class="courses-empty">Belum ada course.</div>
                    @endforelse
                </div>
            </div>
            <div class="mentors-menu" id="mentorsMenu">
                <button class="mentors-trigger" id="mentorsTrigger" type="button" aria-expanded="false">Mentors</button>
                <div class="mentors-dropdown">
                    @forelse(($mentors ?? []) as $mentor)
                        <a class="mentor-item" href="{{ route('mentors.show', ['mentor' => $mentor->id]) }}">
                            {{ $mentor->name }} ({{ $mentor->courses_count }})
                        </a>
                    @empty
                        <div class="mentor-empty">Belum ada mentor dengan course.</div>
                    @endforelse
                </div>
            </div>
            <div class="progress-menu" id="progressMenu">
                <button class="progress-trigger" id="progressTrigger" type="button" aria-expanded="false">Progress</button>
                <div class="progress-dropdown">
                    <button class="progress-item" type="button">Weekly Progress: 64%</button>
                    <button class="progress-item" type="button">Lessons Completed: 8</button>
                    <button class="progress-item" type="button">Badges: 3</button>
                </div>
            </div>
            <div class="my-courses-menu" id="myCoursesMenu">
                <button class="my-courses-trigger" id="myCoursesTrigger" type="button" aria-expanded="false">My Courses</button>
                <div class="my-courses-dropdown">
                    @forelse(($enrolledCourses ?? []) as $course)
                        <a class="my-courses-item" href="{{ $course['roadmap_route'] }}">{{ $course['title'] }}</a>
                    @empty
                        <div class="my-courses-empty">Belum ada course terdaftar.</div>
                    @endforelse
                </div>
            </div>
            <div class="home-menu" id="homeMenu">
                <button class="home-trigger" id="homeTrigger" type="button" aria-expanded="false">Home</button>
                <div class="home-dropdown">
                    <a class="home-item" href="{{ route('landing') }}">Go To Landing</a>
                    <a class="home-item" href="{{ route('student.dashboard') }}">Student Dashboard</a>
                    <a class="home-item" href="{{ route('profile.show') }}">Profile</a>
                </div>
            </div>
            @php
                $nameParts = preg_split('/\s+/', trim(auth()->user()->name));
                $initials = strtoupper(substr($nameParts[0] ?? '', 0, 1).substr($nameParts[1] ?? '', 0, 1));
                $initials = $initials !== '' ? $initials : 'U';
                $profileImageUrl = auth()->user()->profile_image ? asset('storage/'.auth()->user()->profile_image) : null;
            @endphp
            <div class="profile-menu" id="profileMenuStudent">
                <a class="profile-trigger" href="{{ route('profile.show') }}" id="profileTriggerStudent" aria-expanded="false">
                    @if($profileImageUrl)
                        <img src="{{ $profileImageUrl }}" alt="Profile picture" class="profile-image">
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
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="profile-item">Logout</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <section class="hero">
        <h1>Selected and popular courses to grow your digital skills</h1>
        <p>Hi {{ auth()->user()->name }}, pilih jalur belajarmu dan lanjutkan progressmu hari ini. Belajar terasa lebih visual, lebih fokus, dan lebih seru.</p>
    </section>

    <section class="slider-shell">
        <div class="slider-controls">
            <button class="ctrl" id="prevBtn" aria-label="Previous">&#8592;</button>
            <button class="ctrl" id="nextBtn" aria-label="Next">&#8594;</button>
        </div>

        <div class="slider" id="courseSlider">
            @php
                $cardPalette = ['c1', 'c2', 'c3', 'c4', 'c5', 'c6'];
            @endphp
            @foreach(($carouselCourses ?? []) as $index => $course)
                <article
                    class="card {{ empty($course['image']) ? $cardPalette[$index % count($cardPalette)] : 'has-image' }} {{ $index === 0 ? 'active' : '' }}"
                    data-href="{{ $course['href'] }}"
                    @if(!empty($course['image']))
                        style="background-image: url('{{ $course['image'] }}');"
                    @endif
                >
                    <div class="card-content">
                        <small>{{ $course['category'] }}</small>
                        <h3>{{ $course['title'] }}</h3>
                        <p>{{ $course['description'] }}</p>
                        <p><a class="card-link" href="{{ $course['href'] }}"><strong>View Course -&gt;</strong></a></p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    
    <section class="lower">
        <article class="panel">
            <h4>Continue Learning</h4>
            <p>You are currently on track. Keep your momentum with the next module from Frontend Craft.</p>
            <div class="progress-track"><div class="progress-fill"></div></div>
            <p style="margin-top: 8px;">Progress: 64% complete</p>
        </article>

        <article class="panel">
            <h4>Weekly Focus</h4>
            <p>Complete 2 lessons and 1 mini-quiz this week to unlock your next achievement badge.</p>
        </article>
    </section>

    <footer class="site-footer">
        <p>&copy; {{ date('Y') }} Skillify. Keep learning, keep growing.</p>
    </footer>
</div>

<script>
    const slider = document.getElementById('courseSlider');
    const cards = Array.from(slider.querySelectorAll('.card'));
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    let activeIndex = 0;

    function setActiveCard() {
        const center = slider.scrollLeft + slider.clientWidth / 2;
        let closest = cards[0];
        let minDistance = Number.POSITIVE_INFINITY;

        cards.forEach((card) => {
            const cardCenter = card.offsetLeft + card.clientWidth / 2;
            const distance = Math.abs(center - cardCenter);
            if (distance < minDistance) {
                minDistance = distance;
                closest = card;
            }
        });

        cards.forEach((card) => card.classList.remove('active'));
        closest.classList.add('active');
        activeIndex = cards.indexOf(closest);
    }

    function goToCard(index) {
        if (index < 0) {
            index = cards.length - 1;
        }
        if (index >= cards.length) {
            index = 0;
        }

        activeIndex = index;
        cards.forEach((card) => card.classList.remove('active'));
        const target = cards[index];
        target.classList.add('active');
        target.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }

    prevBtn.addEventListener('click', () => goToCard(activeIndex - 1));
    nextBtn.addEventListener('click', () => goToCard(activeIndex + 1));
    slider.addEventListener('scroll', () => window.requestAnimationFrame(setActiveCard));
    window.addEventListener('resize', setActiveCard);

    cards.forEach((card) => {
        card.addEventListener('mouseenter', () => {
            card.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            cards.forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
        });

        card.addEventListener('click', (event) => {
            if (event.target.closest('a, button, input, select, textarea, form')) {
                return;
            }

            const href = card.getAttribute('data-href');
            if (href) {
                window.location.href = href;
            }
        });
    });

    const profileMenu = document.getElementById('profileMenuStudent');
    const profileTrigger = document.getElementById('profileTriggerStudent');
    const myCoursesMenu = document.getElementById('myCoursesMenu');
    const myCoursesTrigger = document.getElementById('myCoursesTrigger');
    const mentorsMenu = document.getElementById('mentorsMenu');
    const mentorsTrigger = document.getElementById('mentorsTrigger');
    const coursesMenu = document.getElementById('coursesMenu');
    const coursesTrigger = document.getElementById('coursesTrigger');
    const progressMenu = document.getElementById('progressMenu');
    const progressTrigger = document.getElementById('progressTrigger');
    const homeMenu = document.getElementById('homeMenu');
    const homeTrigger = document.getElementById('homeTrigger');

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

    if (myCoursesMenu && myCoursesTrigger) {
        myCoursesTrigger.addEventListener('click', function (event) {
            event.stopPropagation();
            myCoursesMenu.classList.toggle('open');
            myCoursesTrigger.setAttribute('aria-expanded', String(myCoursesMenu.classList.contains('open')));
        });

        document.addEventListener('click', function (event) {
            if (!myCoursesMenu.contains(event.target)) {
                myCoursesMenu.classList.remove('open');
                myCoursesTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    if (mentorsMenu && mentorsTrigger) {
        mentorsTrigger.addEventListener('click', function (event) {
            event.stopPropagation();
            mentorsMenu.classList.toggle('open');
            mentorsTrigger.setAttribute('aria-expanded', String(mentorsMenu.classList.contains('open')));
        });

        document.addEventListener('click', function (event) {
            if (!mentorsMenu.contains(event.target)) {
                mentorsMenu.classList.remove('open');
                mentorsTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    if (coursesMenu && coursesTrigger) {
        coursesTrigger.addEventListener('click', function (event) {
            event.stopPropagation();
            coursesMenu.classList.toggle('open');
            coursesTrigger.setAttribute('aria-expanded', String(coursesMenu.classList.contains('open')));
        });

        document.addEventListener('click', function (event) {
            if (!coursesMenu.contains(event.target)) {
                coursesMenu.classList.remove('open');
                coursesTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    if (progressMenu && progressTrigger) {
        progressTrigger.addEventListener('click', function (event) {
            event.stopPropagation();
            progressMenu.classList.toggle('open');
            progressTrigger.setAttribute('aria-expanded', String(progressMenu.classList.contains('open')));
        });

        document.addEventListener('click', function (event) {
            if (!progressMenu.contains(event.target)) {
                progressMenu.classList.remove('open');
                progressTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    if (homeMenu && homeTrigger) {
        homeTrigger.addEventListener('click', function (event) {
            event.stopPropagation();
            homeMenu.classList.toggle('open');
            homeTrigger.setAttribute('aria-expanded', String(homeMenu.classList.contains('open')));
        });

        document.addEventListener('click', function (event) {
            if (!homeMenu.contains(event.target)) {
                homeMenu.classList.remove('open');
                homeTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    setActiveCard();
</script>
</body>
</html>



