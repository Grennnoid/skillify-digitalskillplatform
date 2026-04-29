<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} | Course Info</title>
    <style>
        :root {
            --bg: #050914;
            --panel: rgba(11, 19, 35, 0.82);
            --line: rgba(154, 178, 225, 0.24);
            --text: #eaf2ff;
            --muted: #93a8cd;
            --gold: #f6d87a;
            --gold-2: #fff2c2;
            --shadow: 0 24px 60px rgba(2, 7, 20, 0.5);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(1000px 500px at 12% -10%, rgba(79, 156, 255, 0.2), transparent 58%),
                radial-gradient(980px 520px at 88% -14%, rgba(246, 216, 122, 0.16), transparent 58%),
                linear-gradient(180deg, #050911 0%, #060a12 100%);
        }

        .container {
            width: min(800px, 100vw);
            margin: 0 auto;
            padding: 24px 0 42px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .brand { font-weight: 700; letter-spacing: 0.3px; }

        .link {
            color: #c8d8f5;
            text-decoration: none;
            font-size: 16px;
        }

        .link:hover { color: #fff; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 700;
            font-size: 16px;
            text-decoration: none;
            color: #111318;
            background: linear-gradient(120deg, var(--gold), var(--gold-2));
            box-shadow: 0 10px 26px rgba(246, 216, 122, 0.28);
        }

        .hero {
            border: 1px solid var(--line);
            border-radius: 22px;
            overflow: hidden;
            background: var(--panel);
            box-shadow: var(--shadow);
            margin-bottom: 16px;
        }

        .hero-media {
            position: relative;
            height: 500px;
            border-bottom: 1px solid var(--line);
            background: rgba(9, 15, 28, 0.9);
        }

        .hero-media iframe,
        .hero-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(6, 10, 18, 0.9), rgba(6, 10, 18, 0.25));
            pointer-events: none;
        }

        .hero-content {
            position: absolute;
            left: 24px;
            right: 24px;
            bottom: 24px;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-end;
        }

        h1 {
            margin: 0;
            font-size: clamp(30px, 4.5vw, 54px);
            line-height: 1.06;
            letter-spacing: -0.6px;
        }

        .tagline {
            margin-top: 9px;
            color: #dbe7ff;
            font-size: 16px;
        }

        .instructor {
            width: 220px;
            height: 220px;
            display: grid;
            grid-template-rows: 58% 42%;
            border: 1px solid var(--line);
            background: rgba(12, 20, 37, 0.74);
            border-radius: 14px;
            overflow: hidden;
            padding: 0;
            backdrop-filter: blur(4px);
        }

        .instructor-media {
            border-bottom: 1px solid rgba(154, 178, 225, 0.25);
            background: rgba(8, 14, 27, 0.85);
            min-height: 0;
        }

        .instructor img {
            width: 100%;
            height: 100%;
            border-radius: 0;
            object-fit: cover;
            display: block;
        }

        .instructor-meta {
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            gap: 4px;
            background: linear-gradient(180deg, rgba(7, 13, 25, 0.96), rgba(8, 15, 30, 0.96));
            min-height: 0;
        }

        .instructor small {
            color: #9bb7de;
            display: block;
            font-size: 12px;
            letter-spacing: 0;
            line-height: 1.1;
        }

        .instructor strong {
            display: block;
            color: #f2f7ff;
            font-size: 18px;
            line-height: 1.2;
            letter-spacing: 0;
            font-weight: 800;
            text-shadow: 0 1px 14px rgba(0, 0, 0, 0.35);
            max-width: 100%;
            white-space: normal;
            overflow-wrap: anywhere;
        }

        .section {
            border: 1px solid var(--line);
            background: var(--panel);
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 14px;
        }

        .section h2 {
            margin: 0 0 8px;
            font-size: 22px;
        }

        .section p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .audience-chip {
            display: inline-flex;
            margin-top: 10px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid var(--line);
            color: #d9e8ff;
            font-size: 12px;
            background: rgba(14, 24, 44, 0.7);
        }

        .syllabus-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .duration {
            color: #fbe8aa;
            font-size: 13px;
            border: 1px solid rgba(246, 216, 122, 0.4);
            background: rgba(59, 48, 17, 0.4);
            border-radius: 999px;
            padding: 6px 10px;
        }

        .accordion-item {
            border: 1px solid var(--line);
            border-radius: 12px;
            margin-bottom: 8px;
            overflow: hidden;
            background: rgba(10, 17, 31, 0.82);
        }

        .accordion-btn {
            width: 100%;
            text-align: left;
            border: 0;
            background: transparent;
            color: var(--text);
            padding: 12px 14px;
            font-size: 14px;
            cursor: pointer;
            font-weight: 600;
        }

        .accordion-content {
            display: none;
            padding: 0 14px 12px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.65;
        }

        .accordion-item.open .accordion-content { display: block; }

        .outcomes {
            display: grid;
            gap: 10px;
        }

        .outcome {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            color: #dbe7ff;
            font-size: 14px;
            line-height: 1.6;
        }

        .check {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 12px;
            color: #061018;
            background: linear-gradient(120deg, #9ff6df, #7ee8c7);
            flex-shrink: 0;
            margin-top: 1px;
        }

        .bottom-cta {
            margin-top: 12px;
            text-align: center;
        }

        .flash {
            margin-bottom: 12px;
            border: 1px solid rgba(124, 246, 214, 0.45);
            border-radius: 12px;
            background: rgba(12, 42, 24, 0.45);
            color: #baf1cf;
            padding: 10px 12px;
            font-size: 13px;
        }

        .error-box {
            margin-bottom: 12px;
            border: 1px solid rgba(255, 107, 125, 0.45);
            border-radius: 12px;
            background: rgba(72, 21, 33, 0.45);
            color: #ffc4cc;
            padding: 10px 12px;
            font-size: 13px;
        }

        .form-grid {
            display: grid;
            gap: 8px;
            margin-top: 10px;
        }

        .form-grid input,
        .form-grid select,
        .form-grid textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px;
            font-size: 13px;
            color: var(--text);
            background: rgba(8, 14, 27, 0.82);
            outline: none;
            font-family: inherit;
        }

        .form-grid textarea {
            min-height: 96px;
            resize: vertical;
        }

        .form-grid button {
            border: 0;
            border-radius: 10px;
            padding: 10px 12px;
            cursor: pointer;
            font-weight: 700;
            color: #131822;
            background: linear-gradient(120deg, var(--gold), var(--gold-2));
        }

        .list {
            display: grid;
            gap: 10px;
            margin-top: 12px;
        }

        .item {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(10, 17, 31, 0.82);
            padding: 12px;
        }

        .item-head {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .rating {
            color: #f6d87a;
            letter-spacing: 0.4px;
            font-weight: 700;
        }

        .item p {
            margin: 0;
            color: #dbe7ff;
            line-height: 1.6;
            font-size: 14px;
        }

        @media (max-width: 900px) {
            .hero-content {
                position: static;
                display: grid;
                gap: 12px;
                padding: 16px;
                border-top: 1px solid var(--line);
            }

            .hero-media { height: 240px; }
            .hero-media iframe,
            .hero-media img { height: 240px; }
        }
    </style>
</head>
<body>
@php
    $syllabusItems = [];
    if (!empty($course->syllabus_json)) {
        $decoded = json_decode($course->syllabus_json, true);
        if (is_array($decoded)) {
            $syllabusItems = $decoded;
        }
    }

    if (empty($syllabusItems)) {
        $syllabusItems = [
            ['title' => 'Module 1: Fundamentals', 'description' => 'Pengenalan konsep inti, setup, dan pondasi belajar untuk course ini.'],
            ['title' => 'Module 2: Core Practice', 'description' => 'Pendalaman materi utama melalui latihan terarah dan studi kasus.'],
            ['title' => 'Module 3: Applied Project', 'description' => 'Implementasi materi dalam project yang relevan dengan kebutuhan industri.'],
            ['title' => 'Module 4: Final Review', 'description' => 'Evaluasi hasil belajar, perbaikan, dan strategi kelanjutan belajar.'],
        ];
    }

    $outcomes = !empty($course->learning_outcomes)
        ? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $course->learning_outcomes))))
        : [
            'Memahami konsep inti course ini secara terstruktur.',
            'Mampu menerapkan materi menjadi output praktis.',
            'Punya hasil belajar yang bisa jadi portofolio awal.',
        ];

    $defaultInstructorImage = 'https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?auto=format&fit=crop&w=1200&q=80';
    $mentorImage = !empty($course->instructor_photo_url)
        ? $course->instructor_photo_url
        : $defaultInstructorImage;
    $heroBackground = $course->hero_background_url ?: $course->trailer_poster_url ?: 'https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?auto=format&fit=crop&w=1400&q=80';

    $instructorName = $course->instructor_name ?: ($course->mentor_name ?: 'Digital Skill Team');

    $videoEmbedUrl = null;
    if (!empty($course->trailer_url)) {
        $url = $course->trailer_url;
        if (str_contains($url, 'youtube.com/watch?v=')) {
            $videoEmbedUrl = str_replace('watch?v=', 'embed/', $url);
        } elseif (str_contains($url, 'youtu.be/')) {
            $videoEmbedUrl = str_replace('youtu.be/', 'www.youtube.com/embed/', $url);
        } elseif (str_contains($url, 'youtube.com/embed/')) {
            $videoEmbedUrl = $url;
        }
    }
@endphp
<div class="container">
    @if(session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="error-box">{{ $errors->first() }}</div>
    @endif

    <div class="topbar">
        <div class="brand">Skillify</div>
        <div style="display:flex; gap:10px; align-items:center;">
            <a class="link" href="{{ route('student.dashboard') }}">Back To Dashboard</a>
            @if(!empty($isEnrolled))
                <a class="btn" href="{{ route('courses.quiz.roadmap', ['quiz' => $course->id]) }}">Continue Learning</a>
            @else
                <form action="{{ route('courses.quiz.enroll', ['quiz' => $course->id]) }}" method="POST" style="margin:0;">
                    @csrf
                    <button class="btn" type="submit" style="border:0; cursor:pointer;">Start Learning</button>
                </form>
            @endif
        </div>
    </div>

    <section class="hero">
        <div class="hero-media">
            @if($videoEmbedUrl)
                <iframe src="{{ $videoEmbedUrl }}" title="{{ $course->title }} trailer" loading="lazy" allowfullscreen></iframe>
            @else
                <img src="{{ $heroBackground }}" alt="{{ $course->title }} cover">
            @endif
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div>
                    <h1>{{ $course->hero_title ?: $course->title }}</h1>
                    <p class="tagline">{{ $course->tagline ?: 'Pelajari skill digital ini secara terstruktur sampai siap praktik.' }}</p>
                </div>
                <div class="instructor">
                    <div class="instructor-media">
                        <img src="{{ $mentorImage }}" alt="Instructor {{ $instructorName }}">
                    </div>
                    <div class="instructor-meta">
                        <small>Instruktur</small>
                        <strong>{{ $instructorName }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="overview">
        <h2>About This Course</h2>
        <p>{{ $course->about ?: 'Course ini dirancang untuk membantu kamu belajar bertahap dari konsep dasar sampai implementasi nyata.' }}</p>
        <div class="audience-chip">Cocok untuk: {{ $course->target_audience ?: ucfirst($course->difficulty ?? 'beginner') }}</div>
    </section>

    <section class="section">
        <div class="syllabus-top">
            <h2>Silabus / Kurikulum</h2>
            <span class="duration">{{ $course->duration_text ?: 'Total Durasi: 10 Jam Video + Materi Praktik' }}</span>
        </div>

        @foreach($syllabusItems as $index => $module)
            <div class="accordion-item {{ $index === 0 ? 'open' : '' }}">
                <button class="accordion-btn" type="button">{{ $module['title'] ?? ('Module '.($index + 1)) }}</button>
                <div class="accordion-content">{{ $module['description'] ?? 'Belum ada deskripsi module.' }}</div>
            </div>
        @endforeach
    </section>

    <section class="section">
        <h2>Learning Outcomes</h2>
        <div class="outcomes">
            @foreach($outcomes as $outcome)
                <div class="outcome"><span class="check">&#10003;</span><span>{{ $outcome }}</span></div>
            @endforeach
        </div>
    </section>

    <section class="section" id="review">
        <h2>Review</h2>
        <p>
            Rating rata-rata:
            <strong>{{ number_format((float) ($reviewSummary->avg_rating ?? 0), 1) }}/5</strong>
            dari {{ (int) ($reviewSummary->total_reviews ?? 0) }} review.
        </p>

        @if(!empty($isEnrolled))
            <form class="form-grid" action="{{ route('courses.reviews.store', ['slug' => 'quiz-'.$course->id]) }}" method="POST">
                @csrf
                <select name="rating" required>
                    <option value="">Pilih rating</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" @selected((int) ($userReview->rating ?? 0) === $i)>{{ $i }} Star</option>
                    @endfor
                </select>
                <textarea name="review_text" placeholder="Tulis review kamu (opsional)">{{ $userReview->review_text ?? '' }}</textarea>
                <button type="submit">{{ !empty($userReview) ? 'Update Review' : 'Kirim Review' }}</button>
            </form>
        @endif

        <div class="list">
            @forelse($reviews as $review)
                <article class="item">
                    <div class="item-head">
                        <span>{{ $reviewUsers[$review->user_id] ?? 'Student' }}</span>
                        <span class="rating">{{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', max(0, 5 - (int) $review->rating)) }}</span>
                    </div>
                    <p>{{ $review->review_text ?: 'Memberi rating tanpa komentar.' }}</p>
                </article>
            @empty
                <article class="item"><p>Belum ada review untuk course ini.</p></article>
            @endforelse
        </div>
    </section>

    <div class="bottom-cta">
        @if(!empty($isEnrolled))
            <a class="btn" href="{{ route('courses.quiz.roadmap', ['quiz' => $course->id]) }}">Continue Learning</a>
        @else
            <form action="{{ route('courses.quiz.enroll', ['quiz' => $course->id]) }}" method="POST" style="margin:0;">
                @csrf
                <button class="btn" type="submit" style="border:0; cursor:pointer;">Enroll Me</button>
            </form>
        @endif
    </div>
</div>

<script>
    const accordionButtons = document.querySelectorAll('.accordion-btn');

    accordionButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const item = button.closest('.accordion-item');
            item.classList.toggle('open');
        });
    });
</script>
@include('partials.student-chatbot')
</body>
</html>
