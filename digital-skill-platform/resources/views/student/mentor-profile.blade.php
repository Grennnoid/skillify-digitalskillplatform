<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mentor->name }} | Mentor Profile</title>
    <style>
        :root {
            --bg: #040812;
            --panel: rgba(10, 19, 36, 0.84);
            --panel-2: rgba(12, 22, 41, 0.82);
            --line: rgba(143, 175, 230, 0.28);
            --text: #eaf2ff;
            --muted: #99aed4;
            --primary: #47d2ff;
            --secondary: #7cf6d6;
            --shadow: 0 22px 50px rgba(1, 8, 24, 0.46);
        }
        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            min-height: 100vh;
            background:
                radial-gradient(1200px 680px at 14% -15%, rgba(71, 210, 255, 0.22), transparent 60%),
                radial-gradient(980px 560px at 88% -20%, rgba(124, 246, 214, 0.16), transparent 58%),
                linear-gradient(180deg, #03070f 0%, #060c18 100%);
        }

        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: min(1080px, 94vw);
            margin: 0 auto;
            padding: 24px 0 40px;
            flex: 1;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            border-bottom: 1px solid var(--line);
            padding-bottom: 10px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 28px;
        }

        .brand::before {
            content: "";
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            box-shadow: 0 0 14px rgba(71, 210, 255, 0.7);
        }

        .top a {
            color: #cbe0ff;
            text-decoration: none;
            font-size: 15px;
        }

        .top a:hover { color: #ffffff; }

        .profile {
            border: 1px solid var(--line);
            border-radius: 18px;
            background: var(--panel);
            box-shadow: var(--shadow);
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .avatar {
            width: 108px;
            height: 108px;
            border-radius: 50%;
            border: 3px solid rgba(71, 210, 255, 0.72);
            overflow: hidden;
            display: grid;
            place-items: center;
            font-size: 30px;
            font-weight: 700;
            background: rgba(8, 16, 31, 0.92);
            box-shadow: 0 0 0 6px rgba(71, 210, 255, 0.12);
        }

        .avatar img { width: 100%; height: 100%; object-fit: cover; }

        h1 {
            margin: 0 0 6px;
            font-size: clamp(30px, 4vw, 46px);
            letter-spacing: -0.4px;
        }

        .muted { color: var(--muted); font-size: 22px; line-height: 1.65; margin-top: 4px; }

        .courses {
            border: 1px solid var(--line);
            border-radius: 18px;
            background: var(--panel);
            box-shadow: var(--shadow);
            padding: 18px;
        }

        .courses h2 {
            margin: 0 0 12px;
            font-size: clamp(24px, 3.2vw, 38px);
            letter-spacing: -0.3px;
        }

        .list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 12px;
        }

        .item {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--panel-2);
            padding: 14px 14px 13px;
            transition: border-color 0.2s ease, transform 0.2s ease;
        }

        .item:hover {
            border-color: rgba(71, 210, 255, 0.55);
            transform: translateY(-1px);
        }

        .item strong {
            display: block;
            margin-bottom: 6px;
            font-size: 21px;
        }

        .item strong a {
            color: #eaf2ff;
            text-decoration: none;
        }

        .item strong a:hover { text-decoration: underline; }

        .item span { color: var(--muted); font-size: 18px; }

        @media (max-width: 760px) {
            .profile {
                flex-direction: column;
                align-items: flex-start;
            }
            .muted { font-size: 18px; }
            .item strong { font-size: 19px; }
            .item span { font-size: 16px; }
        }
    </style>
</head>
<body>
<div class="page">
<div class="container">
    <div class="top">
        <strong class="brand">Mentor Profile</strong>
        <a href="{{ route('student.dashboard') }}">Back To Dashboard</a>
    </div>

    <section class="profile">
        <div class="avatar">
            @if($mentor->profile_image)
                <img src="{{ asset('storage/' . $mentor->profile_image) }}" alt="{{ $mentor->name }}">
            @else
                {{ strtoupper(substr($mentor->name, 0, 1)) }}
            @endif
        </div>
        <div>
            <h1>{{ $mentor->name }}</h1>
            <p class="muted">
                Role: Dosen / Mentor<br>
                Total Courses: {{ $courses->count() }}<br>
                Bio: {{ $mentor->bio ?: 'Belum ada bio.' }}
            </p>
        </div>
    </section>

    <section class="courses">
        <h2>Courses by {{ $mentor->name }}</h2>
        <ul class="list">
            @forelse($courses as $course)
                <li class="item">
                    <strong><a href="{{ route('courses.quiz.show', ['quiz' => $course->id]) }}">{{ $course->title }}</a></strong>
                    <span>Category: {{ $course->category ?? 'General' }} • Difficulty: {{ ucfirst($course->difficulty ?? 'beginner') }}</span>
                </li>
            @empty
                <li class="item"><span>Mentor ini belum punya course.</span></li>
            @endforelse
        </ul>
    </section>
</div>
</div>
@include('partials.student-chatbot')
</body>
</html>
