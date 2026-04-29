<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dosen Dashboard | Skillify</title>
    <style>
        :root {
            --bg: #070b14;
            --sidebar: #0a1223;
            --panel: rgba(13, 21, 39, 0.82);
            --line: rgba(152, 179, 230, 0.28);
            --text: #e8f1ff;
            --muted: #98acd1;
            --primary: #45d0ff;
            --secondary: #7cf6d6;
            --shadow: 0 20px 46px rgba(1, 7, 21, 0.45);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--text);
            font-family: "Segoe UI", "Inter", Arial, sans-serif;
            background:
                radial-gradient(1200px 620px at 15% -18%, rgba(69, 208, 255, 0.2), transparent 60%),
                radial-gradient(900px 480px at 85% -20%, rgba(124, 246, 214, 0.18), transparent 56%),
                linear-gradient(180deg, #050911 0%, #060a12 100%);
        }

        .layout {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 280px 1fr;
        }

        .sidebar {
            border-right: 1px solid var(--line);
            background: linear-gradient(180deg, #081022 0%, #0b1428 100%);
            padding: 18px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            font-weight: 700;
        }

        .dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            box-shadow: 0 0 16px rgba(69, 208, 255, 0.7);
        }

        .side-meta {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            background: rgba(14, 23, 42, 0.72);
            margin-bottom: 14px;
            font-size: 12px;
            color: var(--muted);
            line-height: 1.5;
        }

        .side-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .side-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid var(--line);
            display: grid;
            place-items: center;
            font-size: 13px;
            font-weight: 700;
            color: #f0f6ff;
            background: rgba(10, 17, 31, 0.9);
            overflow: hidden;
            flex-shrink: 0;
        }

        .side-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .nav {
            display: grid;
            gap: 8px;
            margin-bottom: 14px;
        }

        .nav-btn {
            width: 100%;
            text-align: left;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 11px;
            font-size: 13px;
            color: #d8e7ff;
            background: rgba(14, 23, 42, 0.72);
            cursor: pointer;
        }

        .nav-btn.active {
            border-color: rgba(69, 208, 255, 0.75);
            background: linear-gradient(120deg, rgba(69, 208, 255, 0.25), rgba(124, 246, 214, 0.2));
            color: #fff;
        }

        .side-actions { display: grid; gap: 8px; }

        .content { padding: 22px; }

        .topbar h1 {
            margin: 0;
            font-size: 25px;
        }

        .muted {
            color: var(--muted);
            font-size: 13px;
        }

        .flash {
            margin: 10px 0;
            border: 1px solid rgba(73, 214, 139, 0.4);
            border-radius: 10px;
            background: rgba(12, 42, 24, 0.5);
            color: #b4f2cc;
            padding: 10px 12px;
            font-size: 13px;
        }

        .error-box {
            margin: 10px 0;
            border: 1px solid rgba(255, 107, 125, 0.45);
            border-radius: 10px;
            background: rgba(72, 21, 33, 0.45);
            color: #ffc4cc;
            padding: 10px 12px;
            font-size: 13px;
        }

        .kpi {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin: 12px 0;
        }

        .kpi .box {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(12, 19, 35, 0.72);
            padding: 12px;
        }

        .kpi strong {
            display: block;
            font-size: 20px;
            margin-bottom: 3px;
        }

        .panel {
            border: 1px solid var(--line);
            background: var(--panel);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 16px;
        }

        .panel h2 {
            margin: 0 0 6px;
            font-size: 19px;
        }

        .view { display: none; }
        .view.active { display: block; }

        .cards-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(12, 19, 35, 0.72);
            padding: 12px;
        }

        .card h3 {
            margin: 0 0 10px;
            font-size: 15px;
        }

        .fields { display: grid; gap: 8px; }

        input, select, textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px;
            font-size: 13px;
            color: var(--text);
            background: rgba(8, 14, 27, 0.82);
            outline: none;
        }

        textarea { min-height: 95px; resize: vertical; }

        .btn {
            border: 0;
            border-radius: 10px;
            padding: 9px 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            color: #041220;
            background: linear-gradient(120deg, var(--primary), var(--secondary));
        }

        .btn-ghost {
            color: #d9e8ff;
            border: 1px solid var(--line);
            background: rgba(14, 24, 44, 0.7);
        }

        .btn-danger {
            color: #fff;
            background: linear-gradient(120deg, #ff6b7d, #ff9068);
        }

        .question-bank-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .question-bank-metric {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(12, 19, 35, 0.72);
            padding: 12px;
        }

        .question-bank-metric strong {
            display: block;
            font-size: 20px;
            margin-bottom: 4px;
        }

        .question-bank-list {
            display: grid;
            gap: 10px;
        }

        .question-bank-course {
            border: 1px solid var(--line);
            border-radius: 16px;
            background: rgba(9, 15, 28, 0.74);
            padding: 14px;
        }

        .question-bank-course-head {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .question-bank-course-head h4 {
            margin: 0;
            font-size: 18px;
            color: #f3f7ff;
        }

        .question-bank-course-meta {
            color: var(--muted);
            font-size: 12px;
        }

        .question-bank-chapter-group {
            border-top: 1px solid rgba(154, 178, 225, 0.14);
            padding-top: 12px;
            margin-top: 12px;
        }

        .question-bank-chapter-group:first-of-type {
            border-top: 0;
            padding-top: 0;
            margin-top: 0;
        }

        .question-bank-chapter-head {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .question-bank-chapter-head h5 {
            margin: 0;
            font-size: 14px;
            color: #dfeeff;
            letter-spacing: 0.2px;
        }

        .question-bank-chapter-toggle {
            border-top: 1px solid rgba(154, 178, 225, 0.14);
            padding-top: 12px;
            margin-top: 12px;
        }

        .question-bank-chapter-toggle:first-of-type {
            border-top: 0;
            padding-top: 0;
            margin-top: 0;
        }

        .question-bank-chapter-toggle > summary {
            list-style: none;
            cursor: pointer;
        }

        .question-bank-chapter-toggle > summary::-webkit-details-marker {
            display: none;
        }

        .question-bank-chapter-toggle > summary .question-bank-chapter-head::after {
            content: '+';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 999px;
            border: 1px solid var(--line);
            color: #dff1ff;
            background: rgba(12, 19, 35, 0.8);
            font-size: 15px;
            font-weight: 700;
            margin-left: auto;
            flex-shrink: 0;
        }

        .question-bank-chapter-toggle[open] > summary .question-bank-chapter-head::after {
            content: '-';
        }

        .question-bank-count {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 999px;
            border: 1px solid var(--line);
            color: #cfe0ff;
            background: rgba(14, 24, 44, 0.65);
            font-size: 11px;
            font-weight: 700;
        }

        .question-bank-item {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: rgba(10, 17, 31, 0.84);
            padding: 14px;
        }

        .question-bank-header {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .question-bank-title {
            margin: 0 0 6px;
            font-size: 16px;
            color: #f1f6ff;
        }

        .question-bank-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .question-tag {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(15, 24, 43, 0.9);
            color: #cfe0ff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .question-tag.pop {
            border-color: rgba(255, 107, 125, 0.38);
            background: rgba(70, 18, 31, 0.6);
            color: #ffc9d2;
        }

        .question-tag.ai {
            border-color: rgba(69, 208, 255, 0.38);
            background: rgba(10, 31, 48, 0.65);
            color: #bfeeff;
        }

        .question-bank-text {
            margin: 0 0 12px;
            color: #deebff;
            line-height: 1.6;
            font-size: 14px;
        }

        .question-bank-meta {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .question-bank-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .question-bank-empty {
            border: 1px dashed var(--line);
            border-radius: 14px;
            padding: 16px;
            color: var(--muted);
            background: rgba(10, 17, 31, 0.5);
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid var(--line);
            border-radius: 12px;
            margin-top: 10px;
        }

        table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
        }

        th, td {
            padding: 9px;
            border-bottom: 1px solid rgba(152, 179, 230, 0.16);
            font-size: 13px;
            text-align: left;
            vertical-align: top;
        }

        th { color: #bdd0f4; }

        .analytics-bars { display: grid; gap: 8px; }

        .bar {
            border: 1px solid var(--line);
            border-radius: 10px;
            overflow: hidden;
            background: rgba(10, 17, 31, 0.8);
        }

        .bar > div {
            padding: 8px 10px;
            background: linear-gradient(120deg, rgba(69, 208, 255, 0.48), rgba(124, 246, 214, 0.42));
            color: #eaf8ff;
            font-size: 12px;
        }

        .site-footer {
            margin-top: 16px;
            border-top: 1px solid var(--line);
            padding-top: 14px;
            color: var(--muted);
            font-size: 12px;
            text-align: center;
        }

        @media (max-width: 1180px) {
            .kpi { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .cards-2 { grid-template-columns: 1fr; }
            .question-bank-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 920px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar {
                position: static;
                height: auto;
                border-right: 0;
                border-bottom: 1px solid var(--line);
            }
            .nav { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 640px) {
            .nav { grid-template-columns: 1fr; }
            .kpi { grid-template-columns: 1fr; }
            .question-bank-summary { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        @php
            $profileImageUrl = auth()->user()->profile_image ? asset('storage/'.auth()->user()->profile_image) : null;
            $nameParts = preg_split('/\s+/', trim(auth()->user()->name));
            $initials = strtoupper(substr($nameParts[0] ?? '', 0, 1).substr($nameParts[1] ?? '', 0, 1));
            $initials = $initials !== '' ? $initials : 'U';
        @endphp

        <div class="brand">
            <span class="dot"></span>
            <span>Dosen Workspace</span>
        </div>

        <div class="side-meta">
            <div class="side-profile">
                <div class="side-avatar">
                    @if($profileImageUrl)
                        <img src="{{ $profileImageUrl }}" alt="Profile picture">
                    @else
                        {{ $initials }}
                    @endif
                </div>
                <div>
                    <strong style="color:#e8f1ff;">{{ auth()->user()->name }}</strong>
                </div>
            </div>
            Login as <strong>{{ auth()->user()->name }}</strong><br>
            Role: <strong>{{ strtoupper(auth()->user()->role) }}</strong>
        </div>

        <nav class="nav" id="sideNavDosen">
            <button class="nav-btn active" data-target="manage-courses">Kelola Course</button>
            <button class="nav-btn" data-target="manage-quiz">Kelola Quiz</button>
            <button class="nav-btn" data-target="scores">Lihat Nilai</button>
            <button class="nav-btn" data-target="qa-inbox">Q&A Siswa</button>
            <button class="nav-btn" data-target="analytics">Analytics</button>
        </nav>

        <div class="side-actions">
            <a class="btn btn-ghost" href="{{ route('profile.show') }}">Profile</a>
            <a class="btn btn-ghost" href="{{ route('landing') }}">Back To Landing</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-danger" type="submit" style="width:100%;">Logout</button>
            </form>
        </div>
    </aside>

    <main class="content">
        <div class="topbar">
            <h1>Dosen Dashboard</h1>
            <p class="muted">Fokus mengajar: buat course, bangun quiz, dan monitor progress siswa.</p>
        </div>

        @if(session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        <section class="kpi">
            <article class="box"><strong>{{ $stats['courses'] }}</strong><span class="muted">Courses</span></article>
            <article class="box"><strong>{{ $stats['questions'] }}</strong><span class="muted">Questions</span></article>
            <article class="box"><strong>{{ $stats['submissions'] }}</strong><span class="muted">Submissions</span></article>
            <article class="box"><strong>{{ number_format((float)($avgScore->avg_score ?? 0), 1) }}</strong><span class="muted">Average Score</span></article>
        </section>

        <section class="panel view active" id="manage-courses">
            <h2>Kelola Course</h2>
            <p class="muted">Pilih course dari dropdown untuk lihat/edit setting saat ini, atau tambah course baru.</p>
            <div style="margin-bottom: 10px;">
                <button class="btn btn-primary" type="button" id="jumpAddCourseBtn">Add New Course</button>
            </div>

            <div class="cards-2">
                <article class="card" id="add-course-form">
                    <h3>Buat Course Baru</h3>
                    <form class="fields" action="{{ route('dosen.courses.store') }}" method="POST">
                        @csrf
                        <input type="text" name="title" placeholder="Judul course" required>
                        <input type="text" name="category" placeholder="Kategori: Web Dev, UI/UX, dll" required>
                        <select name="difficulty" required>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                        <button class="btn btn-primary" type="submit">Create Course</button>
                    </form>
                </article>

                <article class="card">
                    <h3>Course Saya - {{ auth()->user()->name }}</h3>
                    <div class="table-wrap">
                        <table style="min-width:100%;">
                            <thead>
                            <tr><th>Title</th><th>Category</th><th>Difficulty</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                            @forelse($manageableCourses as $course)
                                <tr>
                                    <td>{{ $course->title }}</td>
                                    <td>{{ $course->category }}</td>
                                    <td>{{ ucfirst($course->difficulty) }}</td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-ghost js-edit-course"
                                            data-course-key="{{ $course->key }}"
                                            style="padding:6px 10px;font-size:11px;"
                                        >
                                            Edit Info
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4">Belum ada course.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <div class="card" style="margin-top: 12px;">
                <h3>Course Info Editor</h3>
                <p class="muted">Semua setting Kelola Course ada di sini. Pilih course untuk menampilkan setting saat ini.</p>
                <form class="fields" id="courseInfoForm" action="{{ route('dosen.courses.info.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <select id="courseInfoSelector" required>
                        <option value="">Pilih course</option>
                        @foreach($manageableCourses as $courseOption)
                            <option value="{{ $courseOption->key }}">{{ $courseOption->title }}</option>
                        @endforeach
                    </select>
                    <a
                        id="chapterBuilderLink"
                        class="btn btn-ghost"
                        href="{{ route('instructor.courses.roadmap', ['course' => 'frontend-craft']) }}"
                        style="display:none; width: fit-content;"
                    >
                        Open Chapter Builder
                    </a>
                    <input type="hidden" name="quiz_id" id="courseInfoQuizId">
                    <input type="text" name="hero_title" id="courseInfoHeroTitle" placeholder="Hero title (judul besar)">
                    <input type="url" name="hero_background_url" id="courseInfoHeroBackgroundUrl" placeholder="Hero background URL (opsional)">
                    <input type="file" name="hero_background_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    <input type="text" name="tagline" id="courseInfoTagline" placeholder='Tagline, contoh: "Belajar praktis langsung project"'>
                    <input type="text" name="instructor_name" id="courseInfoInstructorName" placeholder="Instructor name (opsional)">
                    <input type="url" name="instructor_photo_url" id="courseInfoInstructorPhoto" placeholder="Instructor photo URL (opsional)">
                    <input type="file" name="instructor_photo_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    <textarea name="about" id="courseInfoAbout" placeholder="About this course"></textarea>
                    <input type="text" name="target_audience" id="courseInfoTargetAudience" placeholder="Target audience">
                    <input type="text" name="duration_text" id="courseInfoDurationText" placeholder="Duration text, contoh: Total Durasi: 8 Jam Video">
                    <textarea name="syllabus_lines" id="courseInfoSyllabusLines" placeholder="Syllabus per baris: Judul Module|Deskripsi module"></textarea>
                    <textarea name="learning_outcomes" id="courseInfoLearningOutcomes" placeholder="Learning outcomes (satu baris per poin)"></textarea>
                    <input type="url" name="trailer_url" id="courseInfoTrailerUrl" placeholder="Trailer video URL (opsional)">
                    <input type="file" name="trailer_file" accept=".mp4,.mov,.m4v,.webm,.avi,video/mp4,video/quicktime,video/webm,video/x-msvideo">
                    <input type="url" name="trailer_poster_url" id="courseInfoTrailerPosterUrl" placeholder="Trailer poster URL (opsional)">
                    <input type="file" name="trailer_poster_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    <button class="btn btn-primary" type="submit">Save Course Info</button>
                </form>

                <div class="table-wrap">
                    <table style="min-width:100%;">
                        <thead>
                        <tr>
                            <th>Course</th>
                            <th>Tagline</th>
                            <th>Audience</th>
                            <th>Updated</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($courseInfoRows as $row)
                            <tr>
                                <td>{{ $row->title }}</td>
                                <td>{{ $row->tagline ?? '-' }}</td>
                                <td>{{ $row->target_audience ?? '-' }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($row->updated_at)->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">Belum ada course info custom.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </section>

        <section class="panel view" id="manage-quiz">
            <h2>Kelola Quiz</h2>
            <p class="muted">Tambah soal manual atau generate preview soal AI untuk course yang kamu kelola, termasuk pop quiz setelah chapter tertentu.</p>
            @php($dosenAiPreview = session('dosen_ai_question_preview'))

            <div class="cards-2">
                <article class="card">
                    <h3>AI Quiz Generator</h3>
                    <form class="fields" action="{{ route('dosen.questions.ai.preview') }}" method="POST">
                        @csrf
                        <select name="course_key" required>
                            <option value="">Pilih course target</option>
                            @foreach($manageableCourses as $course)
                                <option value="{{ $course->key }}" @selected(old('course_key', $dosenAiPreview['course_key'] ?? '') == $course->key)>
                                    {{ $course->title }} ({{ ucfirst($course->difficulty) }})
                                </option>
                            @endforeach
                        </select>
                        <textarea name="generation_notes" placeholder="Comment / lore untuk AI. Jelaskan materi, konsep, gaya pertanyaan, atau vibe pop quiz yang kamu inginkan." required>{{ old('generation_notes', $dosenAiPreview['generation_notes'] ?? '') }}</textarea>
                        <select name="difficulty" required>
                            <option value="beginner" @selected(old('difficulty', $dosenAiPreview['difficulty'] ?? '') === 'beginner')>Beginner</option>
                            <option value="intermediate" @selected(old('difficulty', $dosenAiPreview['difficulty'] ?? '') === 'intermediate')>Intermediate</option>
                            <option value="advanced" @selected(old('difficulty', $dosenAiPreview['difficulty'] ?? '') === 'advanced')>Advanced</option>
                        </select>
                        <input type="number" name="question_count" min="1" max="10" value="{{ old('question_count', $dosenAiPreview['question_count'] ?? 5) }}" placeholder="How many questions?">
                        <select name="question_type_mode" required>
                            <option value="mcq" @selected(old('question_type_mode', $dosenAiPreview['question_type_mode'] ?? '') === 'mcq')>MCQ Only</option>
                            <option value="essay" @selected(old('question_type_mode', $dosenAiPreview['question_type_mode'] ?? '') === 'essay')>Essay Only</option>
                            <option value="true_false" @selected(old('question_type_mode', $dosenAiPreview['question_type_mode'] ?? '') === 'true_false')>True / False Only</option>
                            <option value="mixed_mcq_essay" @selected(old('question_type_mode', $dosenAiPreview['question_type_mode'] ?? '') === 'mixed_mcq_essay')>Mixed MCQ + Essay</option>
                            <option value="mixed_all" @selected(old('question_type_mode', $dosenAiPreview['question_type_mode'] ?? '') === 'mixed_all')>Mixed All Types</option>
                        </select>
                        <input type="number" name="placement_after_chapter" min="1" max="40" value="{{ old('placement_after_chapter', $dosenAiPreview['placement_after_chapter'] ?? '') }}" placeholder="Insert after chapter (optional)">
                        <label class="muted" style="display:flex;gap:8px;align-items:center;">
                            <input type="checkbox" name="is_pop_quiz" value="1" @checked(old('is_pop_quiz', $dosenAiPreview['is_pop_quiz'] ?? false))>
                            Jadikan pop quiz merah yang disisipkan setelah chapter tersebut
                        </label>
                        <button class="btn btn-primary" type="submit">Preview Questions</button>
                    </form>
                </article>

                <article class="card">
                    <h3>Tambah Soal Manual</h3>
                    <form class="fields" action="{{ route('dosen.questions.store') }}" method="POST">
                        @csrf
                        <select name="course_key" required>
                            <option value="">Pilih course/quiz</option>
                            @foreach($manageableCourses as $course)
                                <option value="{{ $course->key }}">{{ $course->title }} ({{ ucfirst($course->difficulty) }})</option>
                            @endforeach
                        </select>
                        <textarea name="question_text" placeholder="Tulis soal..." required></textarea>
                        <select name="question_type" required>
                            <option value="mcq">MCQ</option>
                            <option value="essay">Essay</option>
                            <option value="true_false">True / False</option>
                        </select>
                        <select name="difficulty" required>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                        <input type="number" name="placement_after_chapter" min="1" max="40" placeholder="Insert after chapter (optional)">
                        <label class="muted" style="display:flex;gap:8px;align-items:center;">
                            <input type="checkbox" name="is_pop_quiz" value="1">
                            Jadikan pop quiz merah / wajib perfect score
                        </label>
                        <input type="text" name="correct_answer" placeholder="Jawaban benar (opsional)">
                        <textarea name="options_json" placeholder='Pilihan JSON, ex: ["A","B","C","D"]'></textarea>
                        <button class="btn btn-primary" type="submit">Simpan Soal</button>
                    </form>
                </article>
            </div>

            @if(is_array($dosenAiPreview) && !empty($dosenAiPreview['questions']))
                <div class="card" style="margin-top:12px;">
                    <h3>AI Preview: {{ $dosenAiPreview['course_title'] }}</h3>
                    <p class="muted">Placement:
                        @if(!empty($dosenAiPreview['placement_after_chapter']))
                            after chapter {{ $dosenAiPreview['placement_after_chapter'] }}
                        @else
                            no pop quiz placement
                        @endif
                        &bull; Type mode: {{ str_replace('_', ' ', $dosenAiPreview['question_type_mode']) }}
                    </p>
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Difficulty</th>
                                <th>Answer / Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($dosenAiPreview['questions'] as $index => $question)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $question['question_text'] }}</td>
                                    <td>{{ strtoupper($question['question_type']) }}</td>
                                    <td>{{ ucfirst($question['difficulty']) }}</td>
                                    <td>
                                        <div>{{ $question['correct_answer'] ?: '-' }}</div>
                                        @if(!empty($question['options_json']))
                                            <div class="muted">{{ $question['options_json'] }}</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <form action="{{ route('dosen.questions.ai.save') }}" method="POST" style="margin-top:10px;">
                        @csrf
                        <button class="btn btn-primary" type="submit">Save Preview To Question Bank</button>
                    </form>
                </div>
            @endif

            <div class="card" style="margin-top: 12px;">
                <h3>Question Bank Tersimpan</h3>
                <p class="muted">Semua soal yang kamu buat atau generate dari AI terkumpul di sini, jadi lebih gampang cek mana pop quiz, mana soal reguler, lalu hapus yang sudah tidak dipakai.</p>
                <div class="question-bank-summary">
                    <article class="question-bank-metric">
                        <strong>{{ $questionBankPresentation['summary']['total'] ?? 0 }}</strong>
                        <span class="muted">Total stored questions</span>
                    </article>
                    <article class="question-bank-metric">
                        <strong>{{ $questionBankPresentation['summary']['pop_quiz'] ?? 0 }}</strong>
                        <span class="muted">Pop quiz questions</span>
                    </article>
                    <article class="question-bank-metric">
                        <strong>{{ $questionBankPresentation['summary']['ai'] ?? 0 }}</strong>
                        <span class="muted">AI-generated</span>
                    </article>
                    <article class="question-bank-metric">
                        <strong>{{ $questionBankPresentation['summary']['manual'] ?? 0 }}</strong>
                        <span class="muted">Manual entries</span>
                    </article>
                </div>
                <div class="question-bank-list">
                    @forelse(($questionBankPresentation['courses'] ?? []) as $courseGroup)
                        <section class="question-bank-course">
                            <div class="question-bank-course-head">
                                <div>
                                    <h4>{{ $courseGroup['course_label'] }}</h4>
                                    <div class="question-bank-course-meta">{{ $courseGroup['course_slug'] }} - {{ $courseGroup['count'] }} questions stored</div>
                                </div>
                                <span class="question-bank-count">{{ $courseGroup['pop_quiz_count'] }} pop quiz</span>
                            </div>

                            @foreach($courseGroup['chapters'] as $chapterIndex => $chapterGroup)
                                <details class="question-bank-chapter-toggle" {{ $chapterIndex === 0 ? 'open' : '' }}>
                                    <summary>
                                        <div class="question-bank-chapter-head">
                                            <h5>{{ $chapterGroup['label'] }}</h5>
                                            <span class="question-bank-count">{{ $chapterGroup['count'] }} questions</span>
                                        </div>
                                    </summary>
                                    <div class="question-bank-chapter-group">
                                        <div class="question-bank-list">
                                            @foreach($chapterGroup['rows'] as $row)
                                                <article class="question-bank-item">
                                                    <div class="question-bank-header">
                                                        <div>
                                                            <h4 class="question-bank-title">{{ \Illuminate\Support\Str::limit($row->question_text, 68) }}</h4>
                                                            <div class="question-bank-tags">
                                                                <span class="question-tag">{{ strtoupper($row->question_type) }}</span>
                                                                <span class="question-tag">{{ ucfirst($row->difficulty) }}</span>
                                                                <span class="question-tag {{ $row->question_origin === 'ai' ? 'ai' : '' }}">{{ strtoupper($row->question_origin) }}</span>
                                                                @if($row->is_pop_quiz && $row->placement_after_chapter)
                                                                    <span class="question-tag pop">Pop quiz gate</span>
                                                                @elseif($row->placement_after_chapter)
                                                                    <span class="question-tag">Lesson checkpoint</span>
                                                                @else
                                                                    <span class="question-tag">Reusable</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="question-bank-actions">
                                                            <form action="{{ route('dosen.questions.delete', $row->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-danger" type="submit">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <p class="question-bank-text">{{ \Illuminate\Support\Str::limit($row->question_text, 180) }}</p>
                                                    <div class="question-bank-meta">
                                                        <span class="muted">Created by {{ auth()->user()->name }}</span>
                                                        <span class="muted">{{ \Illuminate\Support\Carbon::parse($row->created_at)->format('d M Y H:i') }}</span>
                                                    </div>
                                                </article>
                                            @endforeach
                                        </div>
                                    </div>
                                </details>
                            @endforeach
                        </section>
                    @empty
                        <div class="question-bank-empty">Question bank masih kosong. Tambah soal manual atau generate dari AI dulu, nanti semuanya akan terkumpul rapi di sini.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="panel view" id="scores">
            <h2>Lihat Nilai</h2>
            <p class="muted">Pantau performa siswa untuk semua course yang kamu ajar.</p>
            <a class="btn btn-ghost" href="{{ route('dosen.scores.export') }}">Export Scores (CSV)</a>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Auto Score</th>
                        <th>Manual Score</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($submissions as $submission)
                        <tr>
                            <td>{{ $submission->student_name ?? '-' }}</td>
                            <td>{{ $submission->course_title ?? '-' }}</td>
                            <td>{{ $submission->score ?? '-' }}</td>
                            <td>{{ $submission->manual_score ?? '-' }}</td>
                            <td>{{ ucfirst($submission->status) }}</td>
                            <td>{{ $submission->submitted_at ? \Illuminate\Support\Carbon::parse($submission->submitted_at)->format('d M Y H:i') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Belum ada data nilai.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card" style="margin-top: 12px;">
                <h3>Attendance Course</h3>
                <div class="cards-2">
                    <article class="card">
                        <strong style="display:block;font-size:22px;">{{ $attendanceStats['total_sessions'] ?? 0 }}</strong>
                        <span class="muted">Total attendance sessions</span>
                    </article>
                    <article class="card">
                        <strong style="display:block;font-size:22px;">{{ $attendanceStats['attended_sessions'] ?? 0 }}</strong>
                        <span class="muted">Attendance counted</span>
                    </article>
                </div>
                <p class="muted" style="margin-top:10px;">Students tracked in consistent mode: {{ $attendanceStats['students_in_mode'] ?? 0 }}</p>
                <div class="table-wrap" style="margin-top:10px;">
                    <table>
                        <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Date</th>
                            <th>Progress</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($attendanceRecords as $attendanceItem)
                            <tr>
                                <td>{{ $attendanceItem->student_name ?? 'Student' }}</td>
                                <td>{{ $attendanceItem->course_title }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($attendanceItem->attendance_date)->format('d M Y') }}</td>
                                <td>{{ $attendanceItem->chapters_completed }}/{{ $attendanceItem->target_chapters }} chapters</td>
                                <td>{{ $attendanceItem->is_attended ? 'Counted' : 'In Progress' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">Belum ada data attendance untuk course kamu.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="panel view" id="qa-inbox">
            <h2>Q&A Siswa</h2>
            <p class="muted">Pertanyaan dari siswa masuk ke sini, lengkap dengan asal course dan chapter.</p>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Chapter</th>
                        <th>Pertanyaan</th>
                        <th>Jawaban Dosen</th>
                        <th>Waktu</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($qaInbox as $qa)
                        <tr>
                            <td>{{ $qa->student_name ?? 'Student' }}</td>
                            <td>{{ $qa->course_title }}</td>
                            <td>{{ $qa->chapter_number ? 'Chapter '.$qa->chapter_number : 'General' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($qa->question_text, 140) }}</td>
                            <td>
                                <form action="{{ route('dosen.questions.answer', ['question' => $qa->id]) }}" method="POST" class="fields" style="gap:6px;">
                                    @csrf
                                    @method('PATCH')
                                    <textarea name="answer_text" style="min-height:64px;" placeholder="Tulis jawaban untuk siswa..." required>{{ $qa->answer_text }}</textarea>
                                    <button class="btn btn-ghost" type="submit" style="width:fit-content;">Save Answer</button>
                                </form>
                            </td>
                            <td>{{ \Illuminate\Support\Carbon::parse($qa->created_at)->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Belum ada pertanyaan dari siswa.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel view" id="analytics">
            <h2>Analytics</h2>
            <p class="muted">Kategori course dengan aktivitas submission tertinggi.</p>
            <div class="analytics-bars">
                @forelse($analytics as $item)
                    <div class="bar">
                        <div style="width: {{ min(100, $item->total * 12) }}%;">{{ $item->category }} - {{ $item->total }} submissions</div>
                    </div>
                @empty
                    <p class="muted">Belum ada data analytics.</p>
                @endforelse
            </div>
        </section>

        <footer class="site-footer">
            <p>&copy; {{ date('Y') }} Skillify - Dosen Dashboard</p>
        </footer>
    </main>
</div>

<script>
    const nav = document.getElementById('sideNavDosen');
    const buttons = Array.from(nav.querySelectorAll('.nav-btn'));
    const views = Array.from(document.querySelectorAll('.view'));
    const courseInfoData = @json($courseInfoData ?? []);

    function setActive(targetId, pushHash = true) {
        buttons.forEach((btn) => btn.classList.toggle('active', btn.dataset.target === targetId));
        views.forEach((view) => view.classList.toggle('active', view.id === targetId));
        if (pushHash) {
            window.location.hash = targetId;
        }
    }

    buttons.forEach((btn) => {
        btn.addEventListener('click', () => setActive(btn.dataset.target));
    });

    const hash = window.location.hash.replace('#', '');
    if (hash && views.some((v) => v.id === hash)) {
        setActive(hash, false);
    }

    const courseInfoForm = document.getElementById('courseInfoForm');
    const courseSelector = document.getElementById('courseInfoSelector');
    const quizIdHidden = document.getElementById('courseInfoQuizId');
    const editButtons = Array.from(document.querySelectorAll('.js-edit-course'));
    const jumpAddCourseBtn = document.getElementById('jumpAddCourseBtn');
    const addCourseForm = document.getElementById('add-course-form');
    const chapterBuilderLink = document.getElementById('chapterBuilderLink');
    const chapterBuilderBaseUrl = @json(url('/instructor/courses'));
    const quizInfoUpdateUrl = "{{ route('dosen.courses.info.update') }}";
    const frontendCraftUpdateUrl = "{{ route('dosen.courses.frontend-craft.page.update') }}";
    const learningOutcomesField = document.getElementById('courseInfoLearningOutcomes');

    function fillCourseInfoForm(courseKey) {
        const data = courseInfoData[String(courseKey)] || {};
        document.getElementById('courseInfoHeroTitle').value = data.hero_title || '';
        document.getElementById('courseInfoHeroBackgroundUrl').value = data.hero_background_url || '';
        document.getElementById('courseInfoTagline').value = data.tagline || '';
        document.getElementById('courseInfoInstructorName').value = data.instructor_name || '';
        document.getElementById('courseInfoInstructorPhoto').value = data.instructor_photo_url || '';
        document.getElementById('courseInfoAbout').value = data.about || '';
        document.getElementById('courseInfoTargetAudience').value = data.target_audience || '';
        document.getElementById('courseInfoDurationText').value = data.duration_text || '';
        document.getElementById('courseInfoSyllabusLines').value = data.syllabus_lines || '';
        document.getElementById('courseInfoLearningOutcomes').value = data.learning_outcomes || '';
        document.getElementById('courseInfoTrailerUrl').value = data.trailer_url || '';
        document.getElementById('courseInfoTrailerPosterUrl').value = data.trailer_poster_url || '';

        if (!courseInfoForm || !quizIdHidden) {
            return;
        }

        if (!courseKey) {
            if (chapterBuilderLink) {
                chapterBuilderLink.style.display = 'none';
            }
            return;
        }

        if (chapterBuilderLink) {
            const builderSlug = String(courseKey) === 'frontend-craft' ? 'frontend-craft' : `quiz-${courseKey}`;
            chapterBuilderLink.href = `${chapterBuilderBaseUrl}/${builderSlug}/roadmap`;
            chapterBuilderLink.style.display = 'inline-flex';
        }

        if (String(courseKey) === 'frontend-craft') {
            courseInfoForm.action = frontendCraftUpdateUrl;
            quizIdHidden.value = '';
            quizIdHidden.disabled = true;
            if (learningOutcomesField) {
                learningOutcomesField.name = 'outcomes_text';
            }
        } else {
            courseInfoForm.action = quizInfoUpdateUrl;
            quizIdHidden.value = String(courseKey);
            quizIdHidden.disabled = false;
            if (learningOutcomesField) {
                learningOutcomesField.name = 'learning_outcomes';
            }
        }
    }

    if (courseSelector) {
        courseSelector.addEventListener('change', (event) => {
            const selectedId = event.target.value;
            fillCourseInfoForm(selectedId);
        });
    }

    editButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const courseKey = btn.dataset.courseKey;
            if (!courseKey) {
                return;
            }
            setActive('manage-courses');
            if (courseSelector) {
                courseSelector.value = courseKey;
            }
            fillCourseInfoForm(courseKey);
            courseSelector?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });

    jumpAddCourseBtn?.addEventListener('click', () => {
        setActive('manage-courses');
        addCourseForm?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
</script>
</body>
</html>


