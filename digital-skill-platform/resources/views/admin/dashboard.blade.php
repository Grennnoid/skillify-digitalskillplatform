<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Control Center | Skillify</title>
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
            --danger: #ff6b7d;
            --ok: #49d68b;
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
            letter-spacing: 0.2px;
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

        .nav-btn:hover {
            border-color: rgba(69, 208, 255, 0.5);
        }

        .nav-btn.active {
            border-color: rgba(69, 208, 255, 0.75);
            background: linear-gradient(120deg, rgba(69, 208, 255, 0.25), rgba(124, 246, 214, 0.2));
            color: #f3f9ff;
        }

        .side-actions {
            display: grid;
            gap: 8px;
        }

        .content {
            padding: 22px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .topbar h1 {
            margin: 0;
            font-size: 24px;
        }

        .muted {
            color: var(--muted);
            font-size: 13px;
        }

        .panel {
            border: 1px solid var(--line);
            background: var(--panel);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 16px;
        }

        .panel h2 {
            margin: 0 0 6px;
            font-size: 19px;
        }

        .panel p { margin: 0 0 12px; }

        .view {
            display: none;
        }

        .view.active {
            display: block;
        }

        .cards-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .cards-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
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

        .table-wrap {
            overflow-x: auto;
            border: 1px solid var(--line);
            border-radius: 12px;
        }

        table {
            width: 100%;
            min-width: 780px;
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

        .status {
            display: inline-flex;
            padding: 3px 8px;
            border-radius: 999px;
            border: 1px solid var(--line);
            font-size: 12px;
        }

        .status.active { border-color: rgba(73, 214, 139, 0.45); color: #9aefbf; }
        .status.suspended { border-color: rgba(255, 107, 125, 0.45); color: #ffb5be; }

        .fields {
            display: grid;
            gap: 8px;
        }

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

        input:focus, select:focus, textarea:focus {
            border-color: rgba(69, 208, 255, 0.7);
            box-shadow: 0 0 0 3px rgba(69, 208, 255, 0.14);
        }

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

        .inline-form {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .inline-form select { min-width: 110px; }

        .flash {
            margin-bottom: 10px;
            border: 1px solid rgba(73, 214, 139, 0.4);
            border-radius: 10px;
            background: rgba(12, 42, 24, 0.5);
            color: #b4f2cc;
            padding: 10px 12px;
            font-size: 13px;
        }

        .error-box {
            margin-bottom: 10px;
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
            margin-bottom: 12px;
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
            font-size: 12px;
        }

        @media (max-width: 1180px) {
            .cards-3 { grid-template-columns: 1fr; }
            .cards-2 { grid-template-columns: 1fr; }
            .kpi { grid-template-columns: repeat(2, minmax(0, 1fr)); }
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
            .nav {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .nav { grid-template-columns: 1fr; }
            .kpi { grid-template-columns: 1fr; }
            .inline-form { flex-direction: column; align-items: stretch; }
            .question-bank-summary { grid-template-columns: 1fr; }
        }

        .site-footer {
            margin-top: 18px;
            border-top: 1px solid var(--line);
            padding-top: 14px;
            color: var(--muted);
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <span class="dot"></span>
            <span>Admin Control Center</span>
        </div>

        <div class="side-meta">
            Login as <strong>{{ auth()->user()->name }}</strong><br>
            Role: <strong>{{ strtoupper(auth()->user()->role) }}</strong>
        </div>

        <nav class="nav" id="sideNav">
            <button class="nav-btn active" data-target="user-management">Manajemen Pengguna</button>
            <button class="nav-btn" data-target="manage-courses">Kelola Course</button>
            <button class="nav-btn" data-target="manage-quiz">Kelola Quiz</button>
            <button class="nav-btn" data-target="gradebook">Monitoring Hasil & Nilai</button>
            <button class="nav-btn" data-target="ai-analytics">AI Dashboard & Analytics</button>
            <button class="nav-btn" data-target="system-settings">Pengaturan Global</button>
        </nav>

        <div class="side-actions">
            <a class="btn btn-ghost" href="{{ route('landing') }}">Back To Landing</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-danger" type="submit" style="width: 100%;">Logout</button>
            </form>
        </div>
    </aside>

    <main class="content">
        <div class="topbar">
            <div>
                <h1>Dashboard Admin</h1>
                <p class="muted">One module at a time for focused workflow.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        <section class="kpi">
            <article class="box"><strong>{{ $stats['total_users'] }}</strong><span class="muted">Total Users</span></article>
            <article class="box"><strong>{{ $stats['active_accounts'] }}</strong><span class="muted">Active Accounts</span></article>
            <article class="box"><strong>{{ $stats['total_quizzes'] }}</strong><span class="muted">Total Quizzes</span></article>
            <article class="box"><strong>{{ $stats['submissions'] }}</strong><span class="muted">Submissions</span></article>
        </section>

        <section class="panel view active" id="user-management">
            <h2>Manajemen Pengguna</h2>
            <p class="muted">Master user table, role switcher, suspend/active account.</p>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Role Switcher</th>
                        <th>Account Status</th>
                        <th>Delete Account</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td><span class="status {{ $user->account_status }}">{{ ucfirst($user->account_status) }}</span></td>
                            <td>
                                <form class="inline-form" action="{{ route('admin.users.role', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role">
                                        <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                        <option value="dosen" @selected($user->role === 'dosen')>Dosen</option>
                                        <option value="student" @selected($user->role === 'student')>Student</option>
                                    </select>
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </form>
                            </td>
                            <td>
                                <form class="inline-form" action="{{ route('admin.users.status', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="account_status">
                                        <option value="active" @selected($user->account_status === 'active')>Active</option>
                                        <option value="suspended" @selected($user->account_status === 'suspended')>Suspended</option>
                                    </select>
                                    <button class="btn btn-ghost" type="submit">Update</button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Delete account for {{ $user->name }}? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">Belum ada data user.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card" style="margin-top:12px;">
                <h3>Pengajuan Dosen (Need Approval)</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Requested Role</th>
                            <th>Requested At</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($dosenRequests as $requestUser)
                            <tr>
                                <td>{{ $requestUser->name }}</td>
                                <td>{{ $requestUser->email }}</td>
                                <td>{{ ucfirst($requestUser->requested_role ?? 'dosen') }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($requestUser->created_at)->format('d M Y H:i') }}</td>
                                <td>
                                    <form class="inline-form" action="{{ route('admin.users.dosen-request', $requestUser->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="approve">
                                        <button class="btn btn-primary" type="submit">Approve</button>
                                    </form>
                                    <form class="inline-form" action="{{ route('admin.users.dosen-request', $requestUser->id) }}" method="POST" style="margin-top:6px;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="reject">
                                        <button class="btn btn-danger" type="submit">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">Belum ada pengajuan dosen.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="panel view" id="manage-courses">
            <h2>Kelola Course</h2>
            <p class="muted">Pilih course dari dropdown untuk lihat/edit setting saat ini, atau tambah course baru.</p>
            <div style="margin-bottom: 10px;">
                <button class="btn btn-primary" type="button" id="jumpAddCourseBtnAdmin">Add New Course</button>
            </div>

            <div class="cards-2">
                <article class="card" id="add-course-form-admin">
                    <h3>Buat Course Baru</h3>
                    <form class="fields" action="{{ route('admin.courses.store') }}" method="POST">
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
                    <h3>Semua Course</h3>
                    <div class="table-wrap">
                        <table style="min-width:100%;">
                            <thead>
                            <tr><th>Title</th><th>Category</th><th>Difficulty</th><th>Owner</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                            @forelse($manageableCourses as $course)
                                <tr>
                                    <td>{{ $course->title }}</td>
                                    <td>{{ $course->category }}</td>
                                    <td>{{ ucfirst($course->difficulty) }}</td>
                                    <td>{{ $course->owner_name }}</td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-ghost js-edit-course-admin"
                                            data-course-key="{{ $course->key }}"
                                            style="padding:6px 10px;font-size:11px;"
                                        >
                                            Edit Info
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5">Belum ada course.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <div class="card" style="margin-top: 12px;">
                <h3>Course Info Editor</h3>
                <p class="muted">Semua setting Kelola Course ada di sini. Pilih course untuk menampilkan setting saat ini.</p>
                <form class="fields" id="courseInfoFormAdmin" action="{{ route('admin.courses.info.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <select id="courseInfoSelectorAdmin" required>
                        <option value="">Pilih course</option>
                        @foreach($manageableCourses as $courseOption)
                            <option value="{{ $courseOption->key }}">{{ $courseOption->title }}</option>
                        @endforeach
                    </select>
                    <a
                        id="chapterBuilderLinkAdmin"
                        class="btn btn-ghost"
                        href="{{ route('instructor.courses.roadmap', ['course' => 'frontend-craft']) }}"
                        style="display:none; width: fit-content;"
                    >
                        Open Chapter Builder
                    </a>
                    <input type="hidden" name="quiz_id" id="courseInfoQuizIdAdmin">
                    <input type="text" name="hero_title" id="courseInfoHeroTitleAdmin" placeholder="Hero title (judul besar)">
                    <input type="url" name="hero_background_url" id="courseInfoHeroBackgroundUrlAdmin" placeholder="Carousel/Hero background URL (opsional)">
                    <input type="file" name="hero_background_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    <p class="muted" style="margin:0;">Background image ini juga dipakai di card carousel student dashboard. Ukuran card tetap fixed.</p>
                    <input type="text" name="tagline" id="courseInfoTaglineAdmin" placeholder='Tagline, contoh: "Belajar praktis langsung project"'>
                    <input type="text" name="instructor_name" id="courseInfoInstructorNameAdmin" placeholder="Instructor name (opsional)">
                    <input type="url" name="instructor_photo_url" id="courseInfoInstructorPhotoAdmin" placeholder="Instructor photo URL (opsional)">
                    <textarea name="about" id="courseInfoAboutAdmin" placeholder="About this course"></textarea>
                    <input type="text" name="target_audience" id="courseInfoTargetAudienceAdmin" placeholder="Target audience">
                    <input type="text" name="duration_text" id="courseInfoDurationTextAdmin" placeholder="Duration text, contoh: Total Durasi: 8 Jam Video">
                    <textarea name="syllabus_lines" id="courseInfoSyllabusLinesAdmin" placeholder="Syllabus per baris: Judul Module|Deskripsi module"></textarea>
                    <textarea name="learning_outcomes" id="courseInfoLearningOutcomesAdmin" placeholder="Learning outcomes (satu baris per poin)"></textarea>
                    <input type="url" name="trailer_url" id="courseInfoTrailerUrlAdmin" placeholder="Trailer video URL (opsional)">
                    <input type="url" name="trailer_poster_url" id="courseInfoTrailerPosterUrlAdmin" placeholder="Trailer poster URL (opsional)">
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
            <p class="muted">Tambah soal manual atau minta AI membuat preview soal berdasarkan course, lore, difficulty, dan posisi pop quiz.</p>
            @php($adminAiPreview = session('admin_ai_question_preview'))

            <div class="cards-2">
                <article class="card">
                    <h3>AI Quiz Generator</h3>
                    <form class="fields" action="{{ route('admin.questions.ai.preview') }}" method="POST">
                        @csrf
                        <select name="course_key" required>
                            <option value="">Pilih course target</option>
                            @foreach($manageableCourses as $course)
                                <option value="{{ $course->key }}" @selected(old('course_key', $adminAiPreview['course_key'] ?? '') == $course->key)>
                                    {{ $course->title }} ({{ ucfirst($course->difficulty) }})
                                </option>
                            @endforeach
                        </select>
                        <textarea name="generation_notes" placeholder="Comment / lore untuk AI. Jelaskan topik soal, gaya, fokus chapter, konsep yang harus dites, atau konteks pop quiz." required>{{ old('generation_notes', $adminAiPreview['generation_notes'] ?? '') }}</textarea>
                        <select name="difficulty" required>
                            <option value="beginner" @selected(old('difficulty', $adminAiPreview['difficulty'] ?? '') === 'beginner')>Beginner</option>
                            <option value="intermediate" @selected(old('difficulty', $adminAiPreview['difficulty'] ?? '') === 'intermediate')>Intermediate</option>
                            <option value="advanced" @selected(old('difficulty', $adminAiPreview['difficulty'] ?? '') === 'advanced')>Advanced</option>
                        </select>
                        <input type="number" name="question_count" min="1" max="10" value="{{ old('question_count', $adminAiPreview['question_count'] ?? 5) }}" placeholder="How many questions?">
                        <select name="question_type_mode" required>
                            <option value="mcq" @selected(old('question_type_mode', $adminAiPreview['question_type_mode'] ?? '') === 'mcq')>MCQ Only</option>
                            <option value="essay" @selected(old('question_type_mode', $adminAiPreview['question_type_mode'] ?? '') === 'essay')>Essay Only</option>
                            <option value="true_false" @selected(old('question_type_mode', $adminAiPreview['question_type_mode'] ?? '') === 'true_false')>True / False Only</option>
                            <option value="mixed_mcq_essay" @selected(old('question_type_mode', $adminAiPreview['question_type_mode'] ?? '') === 'mixed_mcq_essay')>Mixed MCQ + Essay</option>
                            <option value="mixed_all" @selected(old('question_type_mode', $adminAiPreview['question_type_mode'] ?? '') === 'mixed_all')>Mixed All Types</option>
                        </select>
                        <input type="number" name="placement_after_chapter" min="1" max="40" value="{{ old('placement_after_chapter', $adminAiPreview['placement_after_chapter'] ?? '') }}" placeholder="Insert after chapter (optional)">
                        <p class="muted" style="margin:0;">Jika kamu isi posisi chapter, sistem otomatis menjadikannya pop quiz merah yang wajib diselesaikan sebelum lanjut.</p>
                        <button class="btn btn-primary" type="submit">Preview Questions</button>
                    </form>
                </article>

                <article class="card">
                    <h3>Tambah Soal Manual</h3>
                    <form class="fields" action="{{ route('admin.questions.store') }}" method="POST">
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
                        <input type="text" name="category" placeholder="Kategori soal">
                        <input type="number" name="placement_after_chapter" min="1" max="40" placeholder="Insert after chapter (optional)">
                        <p class="muted" style="margin:0;">Isi posisi chapter jika soal ini harus otomatis muncul sebagai pop quiz merah / wajib perfect score.</p>
                        <input type="text" name="correct_answer" placeholder="Jawaban benar (opsional)">
                        <textarea name="options_json" placeholder='Pilihan JSON, ex: ["A","B","C","D"]'></textarea>
                        <button class="btn btn-primary" type="submit">Simpan Soal</button>
                    </form>
                </article>
            </div>

            @if(is_array($adminAiPreview) && !empty($adminAiPreview['questions']))
                <div class="card" style="margin-top:12px;">
                    <h3>AI Preview: {{ $adminAiPreview['course_title'] }}</h3>
                    <p class="muted">Placement:
                        @if(!empty($adminAiPreview['placement_after_chapter']))
                            after chapter {{ $adminAiPreview['placement_after_chapter'] }}
                        @else
                            no pop quiz placement
                        @endif
                        &bull; Type mode: {{ str_replace('_', ' ', $adminAiPreview['question_type_mode']) }}
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
                            @foreach($adminAiPreview['questions'] as $index => $question)
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
                    <form action="{{ route('admin.questions.ai.save') }}" method="POST" style="margin-top:10px;">
                        @csrf
                        <button class="btn btn-primary" type="submit">Save Preview To Question Bank</button>
                    </form>
                </div>
            @endif

            <div class="card" style="margin-top: 12px;">
                <h3>Question Bank Tersimpan</h3>
                <p class="muted">Bank soal sekarang dirapikan per item supaya lebih mudah discan, cek placement pop quiz, dan hapus soal lama tanpa tenggelam di tabel panjang.</p>
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
                                                            <form action="{{ route('admin.questions.delete', $row->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-danger" type="submit">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <p class="question-bank-text">{{ \Illuminate\Support\Str::limit($row->question_text, 180) }}</p>
                                                    <div class="question-bank-meta">
                                                        <span class="muted">Created by {{ $row->creator_name ?? '-' }}</span>
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

        <section class="panel view" id="gradebook">
            <h2>Monitoring Hasil & Nilai</h2>
            <p class="muted">Submissions overview, manual grading, export nilai.</p>
            <div style="margin-bottom: 10px;">
                <a class="btn btn-ghost" href="{{ route('admin.grades.export') }}">Export Nilai (CSV)</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Student</th>
                        <th>Quiz</th>
                        <th>Auto Score</th>
                        <th>Manual Score</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Manual Grading</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($submissions as $submission)
                        <tr>
                            <td>{{ $submission->student_name ?? '-' }}</td>
                            <td>{{ $submission->quiz_title ?? '-' }}</td>
                            <td>{{ $submission->score ?? '-' }}</td>
                            <td>{{ $submission->manual_score ?? '-' }}</td>
                            <td>{{ ucfirst($submission->status) }}</td>
                            <td>{{ $submission->submitted_at ? \Illuminate\Support\Carbon::parse($submission->submitted_at)->format('d M Y H:i') : '-' }}</td>
                            <td>
                                <form class="fields" action="{{ route('admin.submissions.grade', $submission->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" min="0" max="100" step="0.01" name="manual_score" placeholder="0-100" required>
                                    <input type="text" name="remarks" placeholder="Catatan grading (opsional)">
                                    <button class="btn btn-primary" type="submit">Save Grade</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">Belum ada submission.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card" style="margin-top: 12px;">
                <h3>Attendance Overview</h3>
                <div class="cards-3">
                    <article class="card">
                        <strong style="display:block;font-size:22px;">{{ $attendanceStats['total_sessions'] ?? 0 }}</strong>
                        <span class="muted">Total attendance sessions</span>
                    </article>
                    <article class="card">
                        <strong style="display:block;font-size:22px;">{{ $attendanceStats['attended_sessions'] ?? 0 }}</strong>
                        <span class="muted">Attendance counted</span>
                    </article>
                    <article class="card">
                        <strong style="display:block;font-size:22px;">{{ $attendanceStats['active_consistent_students'] ?? 0 }}</strong>
                        <span class="muted">Students in consistent mode</span>
                    </article>
                </div>

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
                        @forelse($attendanceOverview as $attendanceItem)
                            <tr>
                                <td>{{ $attendanceItem->student_name ?? 'Student' }}</td>
                                <td>{{ $attendanceItem->course_title }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($attendanceItem->attendance_date)->format('d M Y') }}</td>
                                <td>{{ $attendanceItem->chapters_completed }}/{{ $attendanceItem->target_chapters }} chapters</td>
                                <td>
                                    <span class="status {{ $attendanceItem->is_attended ? 'active' : 'suspended' }}">
                                        {{ $attendanceItem->is_attended ? 'Counted' : 'In Progress' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">Belum ada data attendance.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="panel view" id="ai-analytics">
            <h2>AI Dashboard & Analytics</h2>
            <p class="muted">Pantau kesehatan belajar platform, konsistensi pengguna, dan performa dosen dalam satu pusat analitik.</p>

            <div class="cards-3">
                <article class="card">
                    <strong style="display:block;font-size:22px;">{{ $platformOverview['active_learners'] ?? 0 }}</strong>
                    <span class="muted">Active learners</span>
                </article>
                <article class="card">
                    <strong style="display:block;font-size:22px;">{{ $platformOverview['consistent_rate'] ?? 0 }}%</strong>
                    <span class="muted">Consistent mode adoption</span>
                </article>
                <article class="card">
                    <strong style="display:block;font-size:22px;">{{ $platformOverview['avg_progress'] ?? 0 }}%</strong>
                    <span class="muted">Average chapter progress</span>
                </article>
                <article class="card">
                    <strong style="display:block;font-size:22px;">{{ $platformOverview['attendance_rate'] ?? 0 }}%</strong>
                    <span class="muted">Attendance success rate</span>
                </article>
                <article class="card">
                    <strong style="display:block;font-size:22px;">{{ $platformOverview['pop_quiz_mastery'] ?? 0 }}%</strong>
                    <span class="muted">Pop quiz mastery</span>
                </article>
                <article class="card">
                    <strong style="display:block;font-size:22px;">{{ $platformOverview['qa_answer_rate'] ?? 0 }}%</strong>
                    <span class="muted">Q&amp;A response coverage</span>
                </article>
            </div>

            <div class="cards-2" style="margin-top:12px;">
                <article class="card">
                    <h3>7-Day Learning Momentum</h3>
                    <p class="muted">Gabungan penyelesaian chapter dan attendance counted di seluruh platform selama 7 hari terakhir.</p>
                    <div class="analytics-bars">
                        @if(!empty($platformWeeklyRows))
                            @foreach($platformWeeklyRows as $row)
                                <div class="bar">
                                    <div style="width: {{ max(14, $row['width']) }}%;">
                                        {{ $row['label'] }} - {{ $row['completions'] }} chapter selesai / {{ $row['attendance'] }} attendance counted
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="muted">Belum ada momentum belajar yang bisa dianalisis.</p>
                        @endif
                    </div>
                </article>

                <article class="card">
                    <h3>Learning Category Snapshot</h3>
                    <p class="muted">Kategori dengan aktivitas submission tertinggi untuk membaca demand materi yang paling aktif.</p>
                    <div class="analytics-bars">
                        @forelse($learningAnalytics as $row)
                            <div class="bar">
                                <div style="width: {{ min(100, $row->total * 10) }}%;">{{ $row->category }} - {{ $row->total }} activities</div>
                            </div>
                        @empty
                            <p class="muted">Belum ada category activity.</p>
                        @endforelse
                    </div>
                </article>
            </div>

            <div class="card" style="margin-top:12px;">
                <h3>Course Health Radar</h3>
                <p class="muted">Lihat course mana yang paling sehat, mana yang progresnya seret, dan di mana Q&amp;A mulai menumpuk.</p>
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Course</th>
                            <th>Mentor</th>
                            <th>Students</th>
                            <th>Avg Progress</th>
                            <th>Attendance</th>
                            <th>Pop Quiz Mastery</th>
                            <th>Open Q&amp;A</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($platformCourseHealthRows))
                            @foreach($platformCourseHealthRows as $row)
                                <tr>
                                    <td>{{ $row['course_title'] }}</td>
                                    <td>{{ $row['mentor_name'] }}</td>
                                    <td>{{ $row['enrolled_students'] }}</td>
                                    <td>{{ $row['avg_progress'] }}%</td>
                                    <td>{{ $row['attendance_rate'] }}%</td>
                                    <td>{{ $row['pop_quiz_mastery'] }}%</td>
                                    <td>{{ $row['open_questions'] }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="7">Belum ada course health data.</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card" style="margin-top:12px;">
                <h3>Dosen Analytics</h3>
                <p class="muted">Admin bisa langsung melihat dosen mana yang paling aktif, siapa yang butuh support, dan bagaimana kesehatan siswa di bawah mereka.</p>
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Mentor</th>
                            <th>Courses</th>
                            <th>Active Learners</th>
                            <th>Avg Progress</th>
                            <th>Attendance</th>
                            <th>Q&amp;A Response</th>
                            <th>Pop Quiz Mastery</th>
                            <th>Needs Attention</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($platformDosenRows))
                            @foreach($platformDosenRows as $row)
                                <tr>
                                    <td>{{ $row['mentor_name'] }}</td>
                                    <td>{{ $row['course_count'] }}</td>
                                    <td>{{ $row['active_learners'] }}</td>
                                    <td>{{ $row['avg_progress'] }}%</td>
                                    <td>{{ $row['attendance_rate'] }}%</td>
                                    <td>{{ $row['qa_answer_rate'] }}%</td>
                                    <td>{{ $row['pop_quiz_mastery'] }}%</td>
                                    <td>{{ $row['needs_attention'] }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="8">Belum ada data dosen analytics.</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="cards-2" style="margin-top:12px;">
                <article class="card">
                    <h3>AI Token Monitor</h3>
                    <p class="muted">Total token: {{ number_format($tokenSummary->total_tokens ?? 0) }}</p>
                    <p class="muted">Total biaya: ${{ number_format((float)($tokenSummary->total_cost ?? 0), 4) }}</p>
                    <div class="table-wrap">
                        <table style="min-width: 100%;">
                            <thead><tr><th>Provider</th><th>Model</th><th>Token</th></tr></thead>
                            <tbody>
                            @forelse($tokenLogs as $log)
                                <tr>
                                    <td>{{ $log->provider }}</td>
                                    <td>{{ $log->model }}</td>
                                    <td>{{ number_format($log->token_count) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3">Belum ada token log.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="card">
                    <h3>AI Feedback Logs</h3>
                    <p class="muted">Gunakan ini untuk membaca tone dan kebutuhan user yang sering muncul di interaksi AI.</p>
                    <div class="table-wrap">
                        <table style="min-width: 100%;">
                            <thead><tr><th>Summary</th><th>Topic</th><th>Sentiment</th></tr></thead>
                            <tbody>
                            @forelse($feedbackLogs as $item)
                                <tr>
                                    <td>{{ \Illuminate\Support\Str::limit($item->prompt_summary, 70) }}</td>
                                    <td>{{ $item->detected_topic ?? '-' }}</td>
                                    <td>{{ $item->sentiment ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3">Belum ada feedback log.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>
        </section>

        <section class="panel view" id="system-settings">
            <h2>Pengaturan Global</h2>
            <p class="muted">Maintenance mode, site identity, admin password.</p>
            <div class="cards-3">
                <article class="card">
                    <h3>Maintenance Mode</h3>
                    <form class="fields" action="{{ route('admin.settings.maintenance') }}" method="POST">
                        @csrf
                        <select name="mode" required>
                            <option value="on">Enable Maintenance</option>
                            <option value="off">Disable Maintenance</option>
                        </select>
                        <button class="btn btn-primary" type="submit">Apply</button>
                    </form>
                </article>

                <article class="card">
                    <h3>Site Identity</h3>
                    <form class="fields" action="{{ route('admin.settings.identity') }}" method="POST">
                        @csrf
                        <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'Skillify' }}" required>
                        <input type="text" name="logo_url" value="{{ $settings['logo_url'] ?? '' }}" placeholder="Logo URL">
                        <input type="text" name="theme_color" value="{{ $settings['theme_color'] ?? '#45d0ff' }}" required>
                        <button class="btn btn-primary" type="submit">Save Identity</button>
                    </form>
                </article>

                <article class="card">
                    <h3>Update Password</h3>
                    <form class="fields" action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <input type="password" name="current_password" placeholder="Current password" required>
                        <input type="password" name="new_password" placeholder="New password" required>
                        <input type="password" name="new_password_confirmation" placeholder="Confirm new password" required>
                        <button class="btn btn-ghost" type="submit">Update Password</button>
                    </form>
                </article>
            </div>

            <div class="cards-2" style="margin-top: 12px;">
                <article class="card">
                    <h3>AI Chatbot Personality</h3>
                    <form class="fields" action="{{ route('admin.settings.chatbot') }}" method="POST">
                        @csrf
                        <input type="text" name="chatbot_name" value="{{ $settings['chatbot_name'] ?? 'Skillify AI' }}" placeholder="Chatbot name" required>
                        <input type="text" name="chatbot_welcome" value="{{ $settings['chatbot_welcome'] ?? 'Hi, I am here to help with your courses, roadmap, and study questions.' }}" placeholder="Welcome message" required>
                        <input type="text" name="chatbot_placeholder" value="{{ $settings['chatbot_placeholder'] ?? 'Ask about this course, chapter, or your study plan...' }}" placeholder="Input placeholder" required>
                        <textarea name="chatbot_personality" placeholder="Describe the chatbot personality and behavior." required>{{ $settings['chatbot_personality'] ?? "You are Skillify AI, a warm and capable learning assistant inside a digital skills platform. Help students understand lessons, stay motivated, break down concepts clearly, and suggest practical next steps. Keep answers supportive, concise, and easy to follow. Do not claim to have accessed grades or hidden platform data unless the user explicitly provides it in the chat." }}</textarea>
                        <button class="btn btn-primary" type="submit">Save Chatbot</button>
                    </form>
                </article>

                <article class="card">
                    <h3>DeepSeek Setup</h3>
                    <p class="muted">Put your API key in <code>digital-skill-platform/.env</code> with <code>DEEPSEEK_API_KEY=your_key_here</code>.</p>
                    <p class="muted">Optional: set <code>DEEPSEEK_MODEL=deepseek-chat</code> if you want to override the default model used by the chatbot.</p>
                    <p class="muted">This chatbot appears on student pages only and uses the personality you save here.</p>
                </article>
            </div>
        </section>

        <footer class="site-footer">
            <p>&copy; {{ date('Y') }} Skillify Admin Control Center.</p>
        </footer>
    </main>
</div>

<script>
    const nav = document.getElementById('sideNav');
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

    const courseInfoForm = document.getElementById('courseInfoFormAdmin');
    const courseSelector = document.getElementById('courseInfoSelectorAdmin');
    const quizIdHidden = document.getElementById('courseInfoQuizIdAdmin');
    const editButtons = Array.from(document.querySelectorAll('.js-edit-course-admin'));
    const jumpAddCourseBtn = document.getElementById('jumpAddCourseBtnAdmin');
    const addCourseForm = document.getElementById('add-course-form-admin');
    const chapterBuilderLink = document.getElementById('chapterBuilderLinkAdmin');
    const chapterBuilderBaseUrl = @json(url('/instructor/courses'));
    const quizInfoUpdateUrl = "{{ route('admin.courses.info.update') }}";
    const frontendCraftUpdateUrl = "{{ route('admin.courses.frontend-craft.page.update') }}";
    const learningOutcomesField = document.getElementById('courseInfoLearningOutcomesAdmin');

    function fillCourseInfoForm(courseKey) {
        const data = courseInfoData[String(courseKey)] || {};
        document.getElementById('courseInfoHeroTitleAdmin').value = data.hero_title || '';
        document.getElementById('courseInfoHeroBackgroundUrlAdmin').value = data.hero_background_url || '';
        document.getElementById('courseInfoTaglineAdmin').value = data.tagline || '';
        document.getElementById('courseInfoInstructorNameAdmin').value = data.instructor_name || '';
        document.getElementById('courseInfoInstructorPhotoAdmin').value = data.instructor_photo_url || '';
        document.getElementById('courseInfoAboutAdmin').value = data.about || '';
        document.getElementById('courseInfoTargetAudienceAdmin').value = data.target_audience || '';
        document.getElementById('courseInfoDurationTextAdmin').value = data.duration_text || '';
        document.getElementById('courseInfoSyllabusLinesAdmin').value = data.syllabus_lines || '';
        document.getElementById('courseInfoLearningOutcomesAdmin').value = data.learning_outcomes || '';
        document.getElementById('courseInfoTrailerUrlAdmin').value = data.trailer_url || '';
        document.getElementById('courseInfoTrailerPosterUrlAdmin').value = data.trailer_poster_url || '';

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

