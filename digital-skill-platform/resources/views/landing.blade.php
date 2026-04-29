<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skillify | Future of Learning</title>
    <style>
        :root {
            --bg: #070b14;
            --panel: rgba(13, 21, 39, 0.72);
            --panel-solid: #101b33;
            --text: #e6efff;
            --muted: #90a3c7;
            --line: rgba(154, 178, 225, 0.25);
            --primary: #45d0ff;
            --primary-2: #7cf6d6;
            --shadow: 0 28px 60px rgba(2, 7, 20, 0.45);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", "Inter", "Helvetica Neue", Arial, sans-serif;
            color: var(--text);
            overflow: hidden;
            background:
                radial-gradient(1100px 580px at 14% -12%, rgba(69, 208, 255, 0.24), transparent 60%),
                radial-gradient(1000px 520px at 86% -16%, rgba(124, 246, 214, 0.2), transparent 56%),
                linear-gradient(180deg, #050911 0%, #060a12 100%);
        }

        .grid-overlay {
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 46px 46px;
            mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.62), transparent 86%);
        }

        .container {
            width: min(1120px, 92vw);
            margin: 0 auto;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 29px 0 10px;
            position: relative;
            z-index: 20;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 19px;
            letter-spacing: 0.3px;
            color: var(--text);
            text-decoration: none;
        }

        .brand-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: linear-gradient(140deg, var(--primary), var(--primary-2));
            box-shadow: 0 0 22px rgba(69, 208, 255, 0.65);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 26px;
            position: relative;
        }

        .link, .dropdown-toggle {
            color: #c8d8f5;
            text-decoration: none;
            font-size: 19px;
            letter-spacing: 0.2px;
            background: none;
            border: 0;
            cursor: pointer;
            padding: 10px 5px;
        }

        .link:hover, .dropdown-toggle:hover { color: #f0f6ff; }

        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 264px;
            border: 1px solid var(--line);
            background: rgba(8, 14, 28, 0.96);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 12px;
            display: none;
            box-shadow: var(--shadow);
        }

        .dropdown.open .dropdown-menu { display: block; }

        .dropdown-item {
            display: block;
            color: #dbe6ff;
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 18px;
        }

        .dropdown-item:hover { background: rgba(69, 208, 255, 0.13); }

        .actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .actions form {
            margin: 0;
        }

        .profile-menu {
            position: relative;
        }

        .profile-menu::after {
            content: "";
            position: absolute;
            right: -2px;
            bottom: 8px;
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 5px solid #d4e3ff;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.55));
            pointer-events: none;
            z-index: 2;
        }

        .profile-trigger {
            width: 66px;
            height: 66px;
            border-radius: 50%;
            border: 2px solid rgba(124, 246, 214, 0.35);
            background: #0f172b;
            color: #f3f7ff;
            font-weight: 700;
            font-size: 19px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 0 0 4px rgba(69, 208, 255, 0.12);
            overflow: hidden;
            line-height: 1;
        }

        .profile-trigger:hover {
            border-color: rgba(124, 246, 214, 0.5);
        }

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
            min-width: 250px;
            border: 1px solid var(--line);
            background: rgba(8, 14, 28, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            padding: 8px;
            display: none;
            box-shadow: var(--shadow);
        }

        .profile-menu.open .profile-dropdown {
            display: block;
        }

        .profile-head {
            padding: 10px;
            border-bottom: 1px solid var(--line);
            margin-bottom: 6px;
        }

        .profile-head strong {
            display: block;
            font-size: 16px;
            margin-bottom: 4px;
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
            font-size: 16px;
            display: block;
            cursor: pointer;
        }

        .profile-item:hover {
            background: rgba(69, 208, 255, 0.13);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border-radius: 14px;
            font-weight: 600;
            font-size: 19px;
            padding: 12px 19px;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }

        .btn-outline {
            color: #d9e8ff;
            border: 1px solid var(--line);
            background: rgba(17, 27, 48, 0.6);
        }

        .btn-outline:hover {
            transform: translateY(-1px);
            border-color: rgba(124, 246, 214, 0.5);
        }

        .btn-primary {
            color: #041220;
            background: linear-gradient(120deg, var(--primary), var(--primary-2));
            box-shadow: 0 12px 28px rgba(69, 208, 255, 0.26);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 15px 34px rgba(69, 208, 255, 0.35);
        }

        .hero {
            padding: 32px 0 24px;
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
        }

        .hero-card {
            border: 1px solid var(--line);
            background: var(--panel);
            backdrop-filter: blur(12px);
            border-radius: 26px;
            padding: clamp(22px, 3.2vw, 38px);
            box-shadow: var(--shadow);
            width: 100%;
        }

        .tag {
            display: inline-flex;
            padding: 8px 13px;
            border: 1px solid rgba(124, 246, 214, 0.45);
            border-radius: 999px;
            font-size: 12px;
            color: #b8f9e8;
            letter-spacing: 0.2px;
            background: rgba(13, 40, 43, 0.45);
        }

        h1 {
            margin: 18px 0 12px;
            font-size: clamp(28px, 3.8vw, 48px);
            line-height: 1.08;
            letter-spacing: -0.7px;
            max-width: 13ch;
        }

        .subtitle {
            margin: 0;
            max-width: 52ch;
            color: var(--muted);
            font-size: clamp(14px, 1.5vw, 17px);
            line-height: 1.65;
        }

        .hero-actions {
            margin-top: 18px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .stats {
            margin-top: 18px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .stat {
            border: 1px solid var(--line);
            background: rgba(14, 21, 38, 0.67);
            border-radius: 14px;
            padding: 14px;
        }

        .stat strong {
            display: block;
            font-size: 20px;
            margin-bottom: 6px;
            color: #f4f8ff;
        }

        .stat span {
            color: #9bb0d7;
            font-size: 13px;
        }

        @media (max-width: 900px) {
            body {
                overflow: auto;
            }

            .container {
                min-height: auto;
            }

            .nav {
                flex-wrap: wrap;
                gap: 14px;
            }

            .nav-links {
                width: 100%;
                order: 3;
                justify-content: flex-start;
                overflow-x: auto;
                padding-bottom: 4px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .hero { padding-top: 56px; }
        }

        .site-footer {
            border-top: 1px solid var(--line);
            margin-top: 8px;
            padding: 10px 0 14px;
            color: var(--muted);
            font-size: 13px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="grid-overlay" aria-hidden="true"></div>

    <div class="container">
        <nav class="nav">
            <a class="brand" href="{{ route('landing') }}">
                <span class="brand-dot"></span>
                <span>Skillify</span>
            </a>

            <div class="nav-links">
                <a class="link" href="#">Home</a>

                <div class="dropdown" id="courseDropdown">
                    <button class="dropdown-toggle" type="button" aria-expanded="false" aria-controls="coursesMenu">Courses v</button>
                    <div class="dropdown-menu" id="coursesMenu">
                        @forelse(($landingCourses ?? []) as $courseItem)
                            <a class="dropdown-item" href="{{ $courseItem['href'] }}">{{ $courseItem['title'] }}</a>
                        @empty
                            <span class="dropdown-item" style="opacity:.75; cursor:default;">No courses available yet</span>
                        @endforelse
                    </div>
                </div>

                @auth
                    @if(auth()->user()->role === 'student')
                        <a class="link" href="{{ route('teach.entry') }}">Teach On Skillify</a>
                    @else
                        <a class="link" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dosen.dashboard') }}">Teach On Skillify</a>
                    @endif
                @else
                    <a class="link" href="{{ route('teach.entry') }}">Teach On Skillify</a>
                @endauth
                <a class="link" href="#">About</a>
            </div>

            <div class="actions">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a class="btn btn-outline" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    @elseif(auth()->user()->role === 'dosen')
                        <a class="btn btn-outline" href="{{ route('dosen.dashboard') }}">Dashboard</a>
                    @else
                        <a class="btn btn-outline" href="{{ route('student.dashboard') }}">Dashboard</a>
                    @endif

                    @php
                        $nameParts = preg_split('/\s+/', trim(auth()->user()->name));
                        $initials = strtoupper(substr($nameParts[0] ?? '', 0, 1).substr($nameParts[1] ?? '', 0, 1));
                        $initials = $initials !== '' ? $initials : 'U';
                        $profileImageUrl = auth()->user()->profile_image ? asset('storage/'.auth()->user()->profile_image) : null;
                    @endphp
                    <div class="profile-menu" id="profileMenuLanding">
                        <a class="profile-trigger" href="{{ route('profile.show') }}" id="profileTriggerLanding" aria-expanded="false">
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
                @else
                    <a class="btn btn-outline" href="{{ route('login') }}">Login</a>
                    <a class="btn btn-primary" href="{{ route('register') }}">Sign Up</a>
                @endauth
            </div>
        </nav>

        <section class="hero">
            <div class="hero-card">
                <span class="tag">Future-ready Learning Experience</span>
                <h1>Build elite digital skills for tomorrow.</h1>
                <p class="subtitle">
                    Learn through curated programs, project-based paths, and AI-powered guidance. Designed for students and professionals who want a sharp, modern learning journey.
                </p>

                <div class="hero-actions">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a class="btn btn-primary" href="{{ route('admin.dashboard') }}">Open Dashboard</a>
                        @elseif(auth()->user()->role === 'dosen')
                            <a class="btn btn-primary" href="{{ route('dosen.dashboard') }}">Open Dashboard</a>
                        @else
                            <a class="btn btn-primary" href="{{ route('student.dashboard') }}">Continue Learning</a>
                        @endif
                        <a class="btn btn-outline" href="{{ route('login', ['switch' => 1]) }}">Switch Account</a>
                    @else
                        <a class="btn btn-primary" href="{{ route('register') }}">Create Account</a>
                        <a class="btn btn-outline" href="{{ route('login') }}">I Already Have an Account</a>
                    @endauth
                </div>

                <div class="stats">
                    <div class="stat">
                        <strong>{{ number_format((int) (($landingStats['total_courses'] ?? 0))) }}</strong>
                        <span>Available courses</span>
                    </div>
                    <div class="stat">
                        <strong>{{ number_format((int) (($landingStats['total_learners'] ?? 0))) }}</strong>
                        <span>Active learners</span>
                    </div>
                    <div class="stat">
                        <strong>{{ number_format((int) (($landingStats['total_mentors'] ?? 0))) }}</strong>
                        <span>Active mentors</span>
                    </div>
                </div>
            </div>
        </section>

        <footer class="site-footer">
            <p>&copy; {{ date('Y') }} Skillify. Build skills. Build future.</p>
        </footer>
    </div>

    <script>
        const courseDropdown = document.getElementById('courseDropdown');
        const courseToggle = courseDropdown.querySelector('.dropdown-toggle');

        courseToggle.addEventListener('click', function () {
            const isOpen = courseDropdown.classList.toggle('open');
            courseToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        const profileMenu = document.getElementById('profileMenuLanding');
        const profileTrigger = document.getElementById('profileTriggerLanding');

        if (profileMenu && profileTrigger) {
            profileTrigger.addEventListener('click', function (event) {
                if (!profileMenu.classList.contains('open')) {
                    event.preventDefault();
                    profileMenu.classList.add('open');
                    profileTrigger.setAttribute('aria-expanded', 'true');
                }
            });
        }

        document.addEventListener('click', function (event) {
            if (!courseDropdown.contains(event.target)) {
                courseDropdown.classList.remove('open');
                courseToggle.setAttribute('aria-expanded', 'false');
            }

            if (profileMenu && !profileMenu.contains(event.target)) {
                profileMenu.classList.remove('open');
                profileTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    </script>
</body>
</html>

