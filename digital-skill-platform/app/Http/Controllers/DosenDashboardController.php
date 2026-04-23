<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DosenDashboardController extends Controller
{
    public function dashboard()
    {
        $dosenId = auth()->id();

        $stats = [
            'courses' => DB::table('quizzes')->where('created_by', $dosenId)->count(),
            'questions' => DB::table('question_bank')->where('created_by', $dosenId)->count(),
            'submissions' => DB::table('quiz_submissions as s')
                ->join('quizzes as q', 'q.id', '=', 's.quiz_id')
                ->where('q.created_by', $dosenId)
                ->count(),
        ];

        $avgScore = DB::table('quiz_submissions as s')
            ->join('quizzes as q', 'q.id', '=', 's.quiz_id')
            ->where('q.created_by', $dosenId)
            ->selectRaw('AVG(COALESCE(s.manual_score, s.score)) as avg_score')
            ->first();

        $courses = DB::table('quizzes')
            ->where('created_by', $dosenId)
            ->latest()
            ->limit(10)
            ->get();

        $courseInfosByQuizId = DB::table('quiz_course_infos')
            ->whereIn('quiz_id', $courses->pluck('id'))
            ->get()
            ->keyBy('quiz_id');

        $courseInfoData = [];
        foreach ($courses as $course) {
            $info = $courseInfosByQuizId->get($course->id);
            $syllabusLines = '';

            if (!empty($info?->syllabus_json)) {
                $rows = json_decode($info->syllabus_json, true);
                if (is_array($rows)) {
                    $syllabusLines = collect($rows)
                        ->map(fn ($row) => trim(($row['title'] ?? '').'|'.($row['description'] ?? '')))
                        ->filter()
                        ->implode("\n");
                }
            }

            $courseInfoData[$course->id] = [
                'mode' => 'quiz',
                'hero_title' => $info->hero_title ?? '',
                'hero_background_url' => $info->hero_background_url ?? '',
                'tagline' => $info->tagline ?? '',
                'instructor_name' => $info->instructor_name ?? '',
                'instructor_photo_url' => $info->instructor_photo_url ?? '',
                'about' => $info->about ?? '',
                'target_audience' => $info->target_audience ?? '',
                'duration_text' => $info->duration_text ?? '',
                'syllabus_lines' => $syllabusLines,
                'learning_outcomes' => $info->learning_outcomes ?? '',
                'trailer_url' => $info->trailer_url ?? '',
                'trailer_poster_url' => $info->trailer_poster_url ?? '',
            ];
        }

        $courseInfoRows = DB::table('quiz_course_infos as i')
            ->join('quizzes as q', 'q.id', '=', 'i.quiz_id')
            ->where('q.created_by', $dosenId)
            ->select('i.quiz_id', 'q.title', 'i.tagline', 'i.target_audience', 'i.updated_at')
            ->orderByDesc('i.updated_at')
            ->limit(10)
            ->get();

        $submissions = DB::table('quiz_submissions as s')
            ->join('quizzes as q', 'q.id', '=', 's.quiz_id')
            ->leftJoin('users as u', 'u.id', '=', 's.user_id')
            ->where('q.created_by', $dosenId)
            ->select(
                's.id',
                'q.title as course_title',
                'u.name as student_name',
                's.score',
                's.manual_score',
                's.status',
                's.submitted_at'
            )
            ->orderByDesc('s.submitted_at')
            ->limit(12)
            ->get();

        $analytics = DB::table('quiz_submissions as s')
            ->join('quizzes as q', 'q.id', '=', 's.quiz_id')
            ->where('q.created_by', $dosenId)
            ->select('q.category', DB::raw('COUNT(*) as total'))
            ->groupBy('q.category')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $qaInbox = DB::table('course_questions as cq')
            ->leftJoin('users as u', 'u.id', '=', 'cq.user_id')
            ->where('cq.dosen_id', $dosenId)
            ->select(
                'cq.id',
                'cq.course_title',
                'cq.course_slug',
                'cq.chapter_number',
                'cq.question_text',
                'cq.answer_text',
                'cq.created_at',
                'u.name as student_name'
            )
            ->orderByDesc('cq.created_at')
            ->limit(40)
            ->get();

        $frontendCraftContent = DB::table('course_page_contents')->where('course_slug', 'frontend-craft')->first();
        $frontendCraftSyllabusText = '';
        if (!empty($frontendCraftContent?->syllabus_json)) {
            $rows = json_decode($frontendCraftContent->syllabus_json, true);
            if (is_array($rows)) {
                $frontendCraftSyllabusText = collect($rows)
                    ->map(fn ($row) => trim(($row['title'] ?? '').'|'.($row['description'] ?? '')))
                    ->filter()
                    ->implode("\n");
            }
        }

        $frontendCraftFallbackSyllabus = [
            ['title' => 'Module 1: Frontend Fundamentals', 'description' => 'Setup lingkungan kerja, HTML semantic, CSS modern basics, dan mindset frontend developer.'],
            ['title' => 'Module 2: Layout & Responsive Design', 'description' => 'Flexbox, CSS Grid, responsive breakpoints, serta mobile-first strategy untuk UI yang rapi.'],
            ['title' => 'Module 3: JavaScript Interactivity', 'description' => 'DOM manipulation, event handling, state sederhana, dan best practice UI interaction.'],
            ['title' => 'Module 4: Project Build & Deployment', 'description' => 'Bangun project portfolio nyata dan deploy ke hosting agar bisa dipamerkan ke recruiter/client.'],
        ];
        $frontendCraftFallbackSyllabusText = collect($frontendCraftFallbackSyllabus)
            ->map(fn ($row) => $row['title'].'|'.$row['description'])
            ->implode("\n");
        $frontendCraftFallbackOutcomes = implode("\n", [
            'Kamu akan bisa membuat landing page dan dashboard frontend sendiri.',
            'Kamu memahami cara membangun UI yang responsive dan reusable.',
            'Kamu mampu mengubah design menjadi implementasi web yang rapi.',
            'Kamu punya 1 project portfolio frontend siap publish.',
        ]);

        $courseInfoData['frontend-craft'] = [
            'mode' => 'frontend-craft',
            'hero_title' => $frontendCraftContent->hero_title ?? 'Frontend Craft',
            'hero_background_url' => $frontendCraftContent->hero_background_url ?? '',
            'tagline' => $frontendCraftContent->tagline ?? 'Kuasai seni web development modern dalam 30 hari.',
            'instructor_name' => $frontendCraftContent->instructor_name ?? 'Raka Pradana',
            'instructor_photo_url' => $frontendCraftContent->instructor_photo_url ?? 'https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?auto=format&fit=crop&w=1400&q=80',
            'about' => $frontendCraftContent->about ?? 'Course ini dirancang untuk membantu kamu yang bingung memulai web development. Setelah menyelesaikan materi, kamu akan mampu membangun website interaktif, responsive, dan siap deploy dengan workflow profesional.',
            'target_audience' => $frontendCraftContent->target_audience ?? 'Pemula sampai Intermediate',
            'duration_text' => $frontendCraftContent->duration_text ?? 'Total Durasi: 18 Jam Video - 42 Materi',
            'syllabus_lines' => $frontendCraftSyllabusText !== '' ? $frontendCraftSyllabusText : $frontendCraftFallbackSyllabusText,
            'learning_outcomes' => $frontendCraftContent->outcomes_text ?? $frontendCraftFallbackOutcomes,
            'trailer_url' => $frontendCraftContent->trailer_url ?? 'https://cdn.coverr.co/videos/coverr-programming-workflow-1579/1080p.mp4',
            'trailer_poster_url' => $frontendCraftContent->trailer_poster_url ?? 'https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?auto=format&fit=crop&w=1400&q=80',
        ];

        $canManageFrontendCraft = (($frontendCraftContent->updated_by ?? null) === $dosenId);

        $manageableCourses = $courses->map(fn ($course) => (object) [
                'key' => (string) $course->id,
                'quiz_id' => $course->id,
                'title' => $course->title,
                'category' => $course->category,
                'difficulty' => $course->difficulty,
                'owner_name' => auth()->user()->name,
            ]);

        if ($canManageFrontendCraft) {
            $manageableCourses->prepend((object) [
                'key' => 'frontend-craft',
                'quiz_id' => null,
                'title' => 'Frontend Craft',
                'category' => 'Web Development',
                'difficulty' => 'core',
                'owner_name' => auth()->user()->name,
            ]);
        }

        return view('dosen.dashboard', compact('stats', 'avgScore', 'courses', 'submissions', 'analytics', 'qaInbox', 'courseInfoRows', 'frontendCraftContent', 'frontendCraftSyllabusText', 'courseInfoData', 'manageableCourses'));
    }

    public function storeCourse(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:120'],
            'difficulty' => ['required', 'in:beginner,intermediate,advanced'],
        ]);

        DB::table('quizzes')->insert([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'difficulty' => $validated['difficulty'],
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Course berhasil dibuat.');
    }

    public function storeQuestion(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'quiz_id' => [
                'required',
                Rule::exists('quizzes', 'id')->where(function ($query) {
                    $query->where('created_by', auth()->id());
                }),
            ],
            'question_text' => ['required', 'string'],
            'question_type' => ['required', 'in:mcq,essay,true_false'],
            'difficulty' => ['required', 'in:beginner,intermediate,advanced'],
            'correct_answer' => ['nullable', 'string'],
        ]);

        DB::table('question_bank')->insert([
            'quiz_id' => $validated['quiz_id'],
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'difficulty' => $validated['difficulty'],
            'correct_answer' => $validated['correct_answer'] ?? null,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function answerCourseQuestion(Request $request, int $question): RedirectResponse
    {
        $validated = $request->validate([
            'answer_text' => ['required', 'string', 'max:3000'],
        ]);

        $ownedQuestion = DB::table('course_questions')
            ->where('id', $question)
            ->where('dosen_id', auth()->id())
            ->first(['id']);

        abort_if(!$ownedQuestion, 404);

        DB::table('course_questions')
            ->where('id', $question)
            ->update([
                'answer_text' => $validated['answer_text'],
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Jawaban Q&A berhasil disimpan.');
    }

    public function exportScores(): StreamedResponse
    {
        $rows = DB::table('quiz_submissions as s')
            ->join('quizzes as q', 'q.id', '=', 's.quiz_id')
            ->leftJoin('users as u', 'u.id', '=', 's.user_id')
            ->where('q.created_by', auth()->id())
            ->select(
                'q.title as course_title',
                'u.name as student_name',
                's.score',
                's.manual_score',
                's.status',
                's.submitted_at'
            )
            ->orderByDesc('s.submitted_at')
            ->get();

        $fileName = 'dosen_scores_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['course_title', 'student_name', 'score', 'manual_score', 'status', 'submitted_at']);

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->course_title,
                    $row->student_name,
                    $row->score,
                    $row->manual_score,
                    $row->status,
                    $row->submitted_at,
                ]);
            }

            fclose($out);
        }, $fileName);
    }

    public function updateCourseInfo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'quiz_id' => [
                'required',
                Rule::exists('quizzes', 'id')->where(function ($query) {
                    $query->where('created_by', auth()->id());
                }),
            ],
            'hero_title' => ['nullable', 'string', 'max:150'],
            'hero_background_url' => ['nullable', 'url', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string', 'max:5000'],
            'target_audience' => ['nullable', 'string', 'max:120'],
            'duration_text' => ['nullable', 'string', 'max:120'],
            'syllabus_lines' => ['nullable', 'string', 'max:8000'],
            'learning_outcomes' => ['nullable', 'string', 'max:5000'],
            'trailer_url' => ['nullable', 'url', 'max:255'],
            'trailer_poster_url' => ['nullable', 'url', 'max:255'],
            'instructor_name' => ['nullable', 'string', 'max:120'],
            'instructor_photo_url' => ['nullable', 'url', 'max:255'],
            'trailer_file' => ['nullable', 'file', 'mimes:mp4,mov,m4v,webm,avi', 'max:512000'],
            'trailer_poster_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'instructor_photo_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hero_background_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $existing = DB::table('quiz_course_infos')
            ->where('quiz_id', $validated['quiz_id'])
            ->first(['hero_background_url', 'trailer_url', 'trailer_poster_url', 'instructor_photo_url']);

        $syllabus = [];
        if (!empty($validated['syllabus_lines'])) {
            $lines = preg_split('/\r\n|\r|\n/', trim($validated['syllabus_lines']));
            foreach ($lines as $line) {
                if (trim($line) === '') {
                    continue;
                }
                [$title, $desc] = array_pad(explode('|', $line, 2), 2, '');
                $syllabus[] = [
                    'title' => trim($title),
                    'description' => trim($desc),
                ];
            }
        }

        $heroBackgroundUrl = $validated['hero_background_url'] ?? null;
        if ($request->hasFile('hero_background_file')) {
            $this->deleteStoredMediaFromValue($existing?->hero_background_url);
            $heroBackgroundUrl = $this->storeMediaAsPublicUrl($request->file('hero_background_file'), 'course_media/hero_backgrounds');
        }

        $trailerUrl = $validated['trailer_url'] ?? null;
        if ($request->hasFile('trailer_file')) {
            $this->deleteStoredMediaFromValue($existing?->trailer_url);
            $trailerUrl = $this->storeMediaAsPublicUrl($request->file('trailer_file'), 'course_media/trailers');
        }

        $trailerPosterUrl = $validated['trailer_poster_url'] ?? null;
        if ($request->hasFile('trailer_poster_file')) {
            $this->deleteStoredMediaFromValue($existing?->trailer_poster_url);
            $trailerPosterUrl = $this->storeMediaAsPublicUrl($request->file('trailer_poster_file'), 'course_media/posters');
        }

        $instructorPhotoUrl = $validated['instructor_photo_url'] ?? null;
        if ($request->hasFile('instructor_photo_file')) {
            $this->deleteStoredMediaFromValue($existing?->instructor_photo_url);
            $instructorPhotoUrl = $this->storeMediaAsPublicUrl($request->file('instructor_photo_file'), 'course_media/instructors');
        }

        DB::table('quiz_course_infos')->updateOrInsert(
            ['quiz_id' => $validated['quiz_id']],
            [
                'hero_title' => $validated['hero_title'] ?? null,
                'hero_background_url' => $heroBackgroundUrl,
                'tagline' => $validated['tagline'] ?? null,
                'about' => $validated['about'] ?? null,
                'target_audience' => $validated['target_audience'] ?? null,
                'duration_text' => $validated['duration_text'] ?? null,
                'syllabus_json' => !empty($syllabus) ? json_encode($syllabus) : null,
                'learning_outcomes' => $validated['learning_outcomes'] ?? null,
                'trailer_url' => $trailerUrl,
                'trailer_poster_url' => $trailerPosterUrl,
                'instructor_name' => $validated['instructor_name'] ?? null,
                'instructor_photo_url' => $instructorPhotoUrl,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('success', 'Course info berhasil diperbarui.');
    }

    public function updateFrontendCraftPage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_title' => ['nullable', 'string', 'max:150'],
            'hero_background_url' => ['nullable', 'url', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string', 'max:5000'],
            'target_audience' => ['nullable', 'string', 'max:120'],
            'duration_text' => ['nullable', 'string', 'max:120'],
            'syllabus_lines' => ['nullable', 'string', 'max:8000'],
            'outcomes_text' => ['nullable', 'string', 'max:5000'],
            'trailer_url' => ['nullable', 'url', 'max:255'],
            'trailer_poster_url' => ['nullable', 'url', 'max:255'],
            'instructor_name' => ['nullable', 'string', 'max:120'],
            'instructor_photo_url' => ['nullable', 'url', 'max:255'],
            'trailer_file' => ['nullable', 'file', 'mimes:mp4,mov,m4v,webm,avi', 'max:512000'],
            'trailer_poster_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'instructor_photo_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hero_background_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $existing = DB::table('course_page_contents')
            ->where('course_slug', 'frontend-craft')
            ->first(['hero_background_url', 'trailer_url', 'trailer_poster_url', 'instructor_photo_url']);

        $syllabus = [];
        if (!empty($validated['syllabus_lines'])) {
            $lines = preg_split('/\r\n|\r|\n/', trim($validated['syllabus_lines']));
            foreach ($lines as $line) {
                if (trim($line) === '') {
                    continue;
                }
                [$title, $desc] = array_pad(explode('|', $line, 2), 2, '');
                $syllabus[] = [
                    'title' => trim($title),
                    'description' => trim($desc),
                ];
            }
        }

        $heroBackgroundUrl = $validated['hero_background_url'] ?? null;
        if ($request->hasFile('hero_background_file')) {
            $this->deleteStoredMediaFromValue($existing?->hero_background_url);
            $heroBackgroundUrl = $this->storeMediaAsPublicUrl($request->file('hero_background_file'), 'course_media/hero_backgrounds');
        }

        $trailerUrl = $validated['trailer_url'] ?? null;
        if ($request->hasFile('trailer_file')) {
            $this->deleteStoredMediaFromValue($existing?->trailer_url);
            $trailerUrl = $this->storeMediaAsPublicUrl($request->file('trailer_file'), 'course_media/trailers');
        }

        $trailerPosterUrl = $validated['trailer_poster_url'] ?? null;
        if ($request->hasFile('trailer_poster_file')) {
            $this->deleteStoredMediaFromValue($existing?->trailer_poster_url);
            $trailerPosterUrl = $this->storeMediaAsPublicUrl($request->file('trailer_poster_file'), 'course_media/posters');
        }

        $instructorPhotoUrl = $validated['instructor_photo_url'] ?? null;
        if ($request->hasFile('instructor_photo_file')) {
            $this->deleteStoredMediaFromValue($existing?->instructor_photo_url);
            $instructorPhotoUrl = $this->storeMediaAsPublicUrl($request->file('instructor_photo_file'), 'course_media/instructors');
        }

        DB::table('course_page_contents')->updateOrInsert(
            ['course_slug' => 'frontend-craft'],
            [
                'hero_title' => $validated['hero_title'] ?? null,
                'hero_background_url' => $heroBackgroundUrl,
                'tagline' => $validated['tagline'] ?? null,
                'about' => $validated['about'] ?? null,
                'target_audience' => $validated['target_audience'] ?? null,
                'duration_text' => $validated['duration_text'] ?? null,
                'syllabus_json' => !empty($syllabus) ? json_encode($syllabus) : null,
                'outcomes_text' => $validated['outcomes_text'] ?? null,
                'trailer_url' => $trailerUrl,
                'trailer_poster_url' => $trailerPosterUrl,
                'instructor_name' => $validated['instructor_name'] ?? null,
                'instructor_photo_url' => $instructorPhotoUrl,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('success', 'Frontend Craft page content berhasil diperbarui.');
    }

    private function storeMediaAsPublicUrl(\Illuminate\Http\UploadedFile $file, string $directory): string
    {
        $path = $file->store($directory, 'public');

        return asset('storage/'.$path);
    }

    private function deleteStoredMediaFromValue(?string $value): void
    {
        if (empty($value)) {
            return;
        }

        $marker = '/storage/';
        $pos = strpos($value, $marker);
        if ($pos === false) {
            return;
        }

        $relative = substr($value, $pos + strlen($marker));
        if ($relative !== false && $relative !== '') {
            Storage::disk('public')->delete($relative);
        }
    }
}
