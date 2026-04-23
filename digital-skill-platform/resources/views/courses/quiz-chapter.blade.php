<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} Chapter {{ $chapter }} | Skillify</title>
    <style>
        :root {
            --bg: #050914;
            --panel: rgba(11, 19, 35, 0.84);
            --line: rgba(154, 178, 225, 0.24);
            --text: #e9f1ff;
            --muted: #9db1d6;
            --accent: #45d0ff;
            --accent2: #7cf6d6;
            --gold: #f6d87a;
            --gold-2: #fff2c2;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            background:
                radial-gradient(1200px 620px at 15% -18%, rgba(69, 208, 255, 0.2), transparent 60%),
                radial-gradient(900px 480px at 85% -20%, rgba(124, 246, 214, 0.16), transparent 56%),
                var(--bg);
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 40;
            border-bottom: 1px solid var(--line);
            background: rgba(8, 13, 24, 0.96);
            backdrop-filter: blur(10px);
        }

        .topbar-inner {
            width: min(1400px, 96vw);
            margin: 0 auto;
            padding: 10px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .brand-wrap {
            display: grid;
            gap: 3px;
            min-width: 0;
        }

        .brand-wrap strong {
            font-size: 20px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .brand-wrap span {
            font-size: 12px;
            color: var(--muted);
        }

        .top-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .chip {
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 6px 10px;
            color: #d7e8ff;
            text-decoration: none;
            font-size: 12px;
            background: rgba(12, 19, 35, 0.72);
        }

        .chip-primary {
            border: 0;
            background: linear-gradient(120deg, var(--gold), var(--gold-2));
            color: #2c210f;
            font-weight: 700;
            cursor: pointer;
        }

        .chip:hover { border-color: rgba(124, 246, 214, 0.52); }

        .wrap {
            width: min(1400px, 96vw);
            margin: 0 auto;
            padding: 14px 0 24px;
            display: grid;
            gap: 12px;
        }

        .tabs {
            border: 1px solid var(--line);
            background: var(--panel);
            border-radius: 14px;
            padding: 0 10px;
            display: flex;
            gap: 4px;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .tabs::-webkit-scrollbar { display: none; }

        .tab-btn {
            border: 0;
            background: transparent;
            color: #cfe0ff;
            font-size: 14px;
            padding: 12px 14px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            white-space: nowrap;
        }

        .tab-btn.active {
            color: #ffffff;
            border-color: var(--accent);
            font-weight: 700;
        }

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 12px;
            align-items: start;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--panel);
            padding: 14px;
        }

        h1 {
            margin: 0 0 4px;
            font-size: 34px;
            letter-spacing: -0.3px;
        }

        .muted {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .meta-grid {
            margin-top: 12px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .stat {
            border: 1px solid var(--line);
            border-radius: 10px;
            background: rgba(8, 14, 27, 0.72);
            padding: 10px;
        }

        .stat strong {
            display: block;
            font-size: 17px;
            margin-bottom: 2px;
        }

        .video-box {
            margin-top: 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            background: rgba(7, 13, 26, 0.9);
        }

        video, iframe {
            width: 100%;
            min-height: 500px;
            border: 0;
            display: block;
            background: #04070f;
        }

        .placeholder {
            min-height: 420px;
            display: grid;
            place-items: center;
            color: var(--muted);
            font-size: 14px;
            padding: 16px;
            text-align: center;
        }

        .lesson-actions {
            margin-top: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: space-between;
            align-items: center;
        }

        .left-actions,
        .right-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .desc {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid var(--line);
            color: #d8e6ff;
            line-height: 1.75;
            font-size: 14px;
        }

        .callout {
            margin-top: 12px;
            border: 1px solid rgba(124, 246, 214, 0.34);
            border-radius: 12px;
            background: rgba(10, 30, 33, 0.35);
            padding: 12px;
        }

        .callout h3 {
            margin: 0 0 6px;
            font-size: 16px;
        }

        .panel { display: none; }
        .panel.active { display: block; }

        .chapter-rail {
            position: sticky;
            top: 70px;
            max-height: calc(100vh - 86px);
            overflow: auto;
        }

        .chapter-list {
            display: grid;
            gap: 8px;
            margin-top: 10px;
        }

        .chapter-item {
            border: 1px solid var(--line);
            border-radius: 10px;
            background: rgba(9, 15, 29, 0.82);
            padding: 10px;
            text-decoration: none;
            color: #dcedff;
            display: grid;
            gap: 4px;
        }

        .chapter-item.active {
            border-color: rgba(69, 208, 255, 0.72);
            background: linear-gradient(120deg, rgba(69, 208, 255, 0.2), rgba(124, 246, 214, 0.18));
        }

        .chapter-item small { color: var(--muted); font-size: 11px; }

        .status-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            color: #b7c9ec;
            font-size: 12px;
        }

        .badge {
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
        }

        .notes-box textarea {
            width: 100%;
            min-height: 180px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: rgba(8, 14, 27, 0.82);
            color: var(--text);
            padding: 10px;
            font-size: 13px;
            resize: vertical;
        }

        .qa-form {
            display: grid;
            gap: 8px;
            margin-top: 10px;
        }

        .qa-form input,
        .qa-form select,
        .qa-form textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: rgba(8, 14, 27, 0.82);
            color: var(--text);
            padding: 10px;
            font-size: 13px;
            font-family: inherit;
        }

        .qa-form textarea {
            min-height: 90px;
            resize: vertical;
        }

        .qa-form button {
            border: 0;
            border-radius: 10px;
            padding: 10px 12px;
            cursor: pointer;
            font-weight: 700;
            color: #2c210f;
            background: linear-gradient(120deg, var(--gold), var(--gold-2));
            width: fit-content;
        }

        .qa-list {
            margin-top: 12px;
            display: grid;
            gap: 8px;
        }

        .qa-item {
            border: 1px solid var(--line);
            border-radius: 10px;
            background: rgba(8, 14, 27, 0.76);
            padding: 10px;
        }

        .qa-item-head {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 6px;
            font-size: 12px;
            color: var(--muted);
        }

        @media (max-width: 1080px) {
            .content-grid { grid-template-columns: 1fr; }
            .chapter-rail { position: static; max-height: none; }
            .meta-grid { grid-template-columns: 1fr 1fr; }
            video, iframe { min-height: 320px; }
        }

        @media (max-width: 700px) {
            .meta-grid { grid-template-columns: 1fr; }
            .brand-wrap strong { font-size: 18px; }
        }
    </style>
</head>
<body>
@php
    $embedVideoUrl = null;
    if (!empty($lesson?->video_url)) {
        $rawUrl = trim($lesson->video_url);
        $embedVideoUrl = $rawUrl;

        if (str_contains($rawUrl, 'youtube.com/watch?v=')) {
            $parts = parse_url($rawUrl);
            parse_str($parts['query'] ?? '', $query);
            if (!empty($query['v'])) {
                $embedVideoUrl = 'https://www.youtube.com/embed/'.$query['v'];
            }
        } elseif (str_contains($rawUrl, 'youtu.be/')) {
            $path = trim((string) parse_url($rawUrl, PHP_URL_PATH), '/');
            $videoId = explode('/', $path)[0] ?? '';
            if ($videoId !== '') {
                $embedVideoUrl = 'https://www.youtube.com/embed/'.$videoId;
            }
        } elseif (str_contains($rawUrl, 'youtube.com/shorts/')) {
            $path = trim((string) parse_url($rawUrl, PHP_URL_PATH), '/');
            $videoId = explode('/', str_replace('shorts/', '', $path))[0] ?? '';
            if ($videoId !== '') {
                $embedVideoUrl = 'https://www.youtube.com/embed/'.$videoId;
            }
        }
    }

    $estimatedHours = $course->duration_text ?: ($chaptersCount.' chapters');
@endphp
<header class="topbar">
    <div class="topbar-inner">
        <div class="brand-wrap">
            <strong>{{ $course->title }} - Chapter {{ $chapter }}</strong>
            <span>Learning mode • {{ $videoReadyCount }}/{{ $chaptersCount }} chapters with video</span>
        </div>
        <div class="top-actions">
            <a class="chip" href="{{ $roadmapUrl }}">Back To Roadmap</a>
            <a class="chip" href="{{ $dashboardUrl }}">Dashboard</a>
            <button class="chip chip-primary" type="button" id="markCompleteBtn">Mark Complete</button>
        </div>
    </div>
</header>

<div class="wrap">
    <nav class="tabs">
        <button class="tab-btn active" data-tab="course-content">Course Content</button>
        <button class="tab-btn" data-tab="overview">Overview</button>
        <button class="tab-btn" data-tab="qa">Q&A</button>
        <button class="tab-btn" data-tab="notes">Notes</button>
    </nav>

    <div class="content-grid">
        <main>
            <section class="card panel active" id="panel-course-content">
                <h1>{{ $lessonTitle ?? ("Chapter {$chapter}") }}</h1>
                <p class="muted">{{ $course->tagline ?: 'Materi chapter ini berasal dari Chapter Builder yang diatur oleh Admin/Dosen.' }}</p>

                <div class="meta-grid">
                    <div class="stat"><strong>{{ ucfirst($course->difficulty ?? 'beginner') }}</strong><span class="muted">Skill Level</span></div>
                    <div class="stat"><strong>{{ $chaptersCount }}</strong><span class="muted">Total Chapters</span></div>
                    <div class="stat"><strong>{{ $estimatedHours }}</strong><span class="muted">Estimated Duration</span></div>
                    <div class="stat"><strong>{{ $course->mentor_name ?? 'Digital Skill Team' }}</strong><span class="muted">Mentor</span></div>
                </div>

                <div class="video-box">
                    @if($lesson && $lesson->video_path)
                        <video controls>
                            <source src="{{ asset('storage/' . $lesson->video_path) }}" type="video/mp4">
                        </video>
                    @elseif($lesson && $lesson->video_url)
                        <iframe src="{{ $embedVideoUrl }}" allowfullscreen loading="lazy"></iframe>
                    @else
                        <div class="placeholder">Video chapter belum diunggah. Silakan lanjut ke chapter lain atau tunggu update mentor.</div>
                    @endif
                </div>

                <div class="lesson-actions">
                    <div class="left-actions">
                        @if($hasPrevious)
                            <a class="chip" href="{{ $chapterPrevUrl }}">&larr; Chapter {{ $chapter - 1 }}</a>
                        @endif
                        <a class="chip" href="{{ $roadmapUrl }}">All Chapters</a>
                        @if($hasNext)
                            <a class="chip" href="{{ $chapterNextUrl }}">Chapter {{ $chapter + 1 }} &rarr;</a>
                        @endif
                    </div>
                    <div class="right-actions">
                        <span class="badge" id="completionBadge">Not completed</span>
                    </div>
                </div>

                <div class="desc">
                    {!! nl2br(e($lessonDescription ?? 'Deskripsi chapter belum tersedia.')) !!}
                </div>

                <div class="callout">
                    <h3>Schedule Learning Time</h3>
                    <p class="muted">Belajar konsisten lebih efektif daripada maraton. Sisihkan 30-45 menit per hari untuk progress stabil.</p>
                </div>
            </section>

            <section class="card panel" id="panel-overview">
                <h1>{{ $course->title }} Overview</h1>
                <p class="muted">{{ $course->about ?: 'Course overview belum diisi oleh mentor untuk course ini.' }}</p>
                <div class="meta-grid">
                    <div class="stat"><strong>{{ ucfirst($course->difficulty ?? 'beginner') }}</strong><span class="muted">Level</span></div>
                    <div class="stat"><strong>{{ $course->category ?? 'General' }}</strong><span class="muted">Category</span></div>
                    <div class="stat"><strong>{{ $chaptersCount }}</strong><span class="muted">Chapters</span></div>
                    <div class="stat"><strong>{{ \Illuminate\Support\Carbon::parse($course->updated_at ?? $course->created_at)->format('d M Y') }}</strong><span class="muted">Updated</span></div>
                </div>
            </section>

            <section class="card panel" id="panel-qa">
                <h1>Q&A</h1>
                <p class="muted">Pertanyaan kamu dikirim ke dosen dan juga tampil di sini agar diskusi per chapter tetap rapi.</p>
                <form class="qa-form" action="{{ $qaPostUrl }}" method="POST">
                    @csrf
                    <input type="hidden" name="chapter_number" value="{{ $chapter }}">
                    <textarea name="question_text" placeholder="Tulis pertanyaan untuk dosen tentang chapter ini" required></textarea>
                    <button type="submit">Kirim Pertanyaan</button>
                </form>
                <div class="qa-list">
                    @forelse($qaItems as $qa)
                        <article class="qa-item">
                            <div class="qa-item-head">
                                <span>{{ $qaUsers[$qa->user_id] ?? 'Student' }}</span>
                                <span>{{ $qa->chapter_number ? 'Chapter '.$qa->chapter_number : 'General' }}</span>
                            </div>
                            <div>{{ $qa->question_text }}</div>
                            @if(!empty($qa->answer_text))
                                <div style="margin-top:6px;color:#cde6ff;"><strong>Jawaban dosen:</strong> {{ $qa->answer_text }}</div>
                            @endif
                        </article>
                    @empty
                        <article class="qa-item">Belum ada pertanyaan untuk chapter ini.</article>
                    @endforelse
                </div>
            </section>

            <section class="card panel notes-box" id="panel-notes">
                <h1>Notes</h1>
                <p class="muted">Catatan disimpan otomatis di browser perangkat ini.</p>
                <textarea id="notesField" placeholder="Tulis insight, ringkasan, atau to-do setelah nonton chapter ini..."></textarea>
            </section>
        </main>

        <aside class="card chapter-rail">
            <h3 style="margin:0;">Course Content</h3>
            <p class="muted" style="margin:6px 0 0;">Progress <span id="progressText">0/{{ $chaptersCount }}</span> chapters completed</p>
            <div class="chapter-list">
                @foreach($chapterItems as $item)
                    <a class="chapter-item {{ $item['number'] === $chapter ? 'active' : '' }}" href="{{ $item['href'] }}" data-chapter-number="{{ $item['number'] }}">
                        <div class="status-row">
                            <strong>Chapter {{ str_pad((string) $item['number'], 2, '0', STR_PAD_LEFT) }}</strong>
                            <span class="badge">{{ $item['has_video'] ? 'Video' : 'No Video' }}</span>
                        </div>
                        <span>{{ $item['title'] }}</span>
                        <small class="chapter-complete-text">Not completed</small>
                    </a>
                @endforeach
            </div>
        </aside>
    </div>
</div>

<script>
    const tabButtons = Array.from(document.querySelectorAll('.tab-btn'));
    const panels = {
        'course-content': document.getElementById('panel-course-content'),
        'overview': document.getElementById('panel-overview'),
        'qa': document.getElementById('panel-qa'),
        'notes': document.getElementById('panel-notes'),
    };

    tabButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const key = button.dataset.tab;
            tabButtons.forEach((b) => b.classList.remove('active'));
            Object.values(panels).forEach((panel) => panel.classList.remove('active'));
            button.classList.add('active');
            panels[key]?.classList.add('active');
        });
    });

    const currentChapter = {{ (int) $chapter }};
    const completedKey = @json($progressStorageKey);
    const notesKey = @json($notesStorageKey);

    const completionBadge = document.getElementById('completionBadge');
    const markCompleteBtn = document.getElementById('markCompleteBtn');
    const progressText = document.getElementById('progressText');
    const chapterLinks = Array.from(document.querySelectorAll('.chapter-item'));
    const notesField = document.getElementById('notesField');

    function getCompletedSet() {
        try {
            const raw = localStorage.getItem(completedKey);
            const parsed = raw ? JSON.parse(raw) : [];
            return new Set(Array.isArray(parsed) ? parsed.map(Number) : []);
        } catch (error) {
            return new Set();
        }
    }

    function saveCompletedSet(set) {
        localStorage.setItem(completedKey, JSON.stringify(Array.from(set)));
    }

    function renderCompletionUI() {
        const set = getCompletedSet();
        const isDone = set.has(currentChapter);

        completionBadge.textContent = isDone ? 'Completed' : 'Not completed';
        markCompleteBtn.textContent = isDone ? 'Mark Incomplete' : 'Mark Complete';

        chapterLinks.forEach((link) => {
            const chapterNo = Number(link.dataset.chapterNumber);
            const isChapterDone = set.has(chapterNo);
            const text = link.querySelector('.chapter-complete-text');
            if (text) {
                text.textContent = isChapterDone ? 'Completed' : 'Not completed';
            }
        });

        progressText.textContent = `${set.size}/{{ $chaptersCount }}`;
    }

    markCompleteBtn?.addEventListener('click', () => {
        const set = getCompletedSet();
        if (set.has(currentChapter)) {
            set.delete(currentChapter);
        } else {
            set.add(currentChapter);
        }
        saveCompletedSet(set);
        renderCompletionUI();
    });

    if (notesField) {
        notesField.value = localStorage.getItem(notesKey) || '';
        notesField.addEventListener('input', () => {
            localStorage.setItem(notesKey, notesField.value);
        });
    }

    renderCompletionUI();
</script>
</body>
</html>

