<?php

namespace App\Http\Controllers;

use App\Services\AiQuestionGenerator;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardController extends Controller
{
    private const DASHBOARD_CACHE_TTL_SECONDS = 120;
    private const AI_PREVIEW_SESSION_KEY = 'admin_ai_question_preview';

    private function dashboardCacheKey(): string
    {
        return 'dashboard:admin:data';
    }

    private function buildSyllabusLines(?string $syllabusJson): string
    {
        if (empty($syllabusJson)) {
            return '';
        }

        $rows = json_decode($syllabusJson, true);
        if (!is_array($rows)) {
            return '';
        }

        return collect($rows)
            ->map(fn ($row) => trim(($row['title'] ?? '').'|'.($row['description'] ?? '')))
            ->filter()
            ->implode("\n");
    }

    private function forgetDashboardCaches(): void
    {
        Cache::forget($this->dashboardCacheKey());
        Cache::forget('settings:chatbot');
    }

    private function forgetCourseCaches(?int $quizId = null, ?string $slug = null): void
    {
        $this->forgetDashboardCaches();
        Cache::forget('landing:courses');
        Cache::forget('landing:stats');
        Cache::forget('student_course:carousel_courses:all');
        Cache::forget('student_course:mentors:all');

        if ($quizId !== null) {
            Cache::forget('student_course:quiz_record:'.$quizId);
            Cache::forget('student_course:quiz_course_info:'.$quizId);
        }

        if ($slug !== null) {
            Cache::forget('student_course:course_lessons:'.$slug);
            Cache::forget('student_course:course_page_content:'.$slug);
        }
    }

    private function courseContextFromKey(string $courseKey): array
    {
        if ($courseKey === 'frontend-craft') {
            $content = DB::table('course_page_contents')
                ->where('course_slug', 'frontend-craft')
                ->first(['hero_title']);

            return [
                'course_key' => 'frontend-craft',
                'course_slug' => 'frontend-craft',
                'quiz_id' => null,
                'course_title' => $content->hero_title ?? 'Frontend Craft',
                'course_category' => 'Web Development',
                'chapter_count' => max(
                    8,
                    (int) (DB::table('course_lessons')->where('course_slug', 'frontend-craft')->max('chapter_number') ?? 0)
                ),
            ];
        }

        $quizId = (int) $courseKey;
        $quiz = DB::table('quizzes')
            ->where('id', $quizId)
            ->first(['id', 'title', 'category']);

        abort_if(!$quiz, 404);

        return [
            'course_key' => (string) $quiz->id,
            'course_slug' => 'quiz-'.$quiz->id,
            'quiz_id' => (int) $quiz->id,
            'course_title' => $quiz->title,
            'course_category' => $quiz->category,
            'chapter_count' => max(
                8,
                (int) (DB::table('course_lessons')->where('course_slug', 'quiz-'.$quiz->id)->max('chapter_number') ?? 0)
            ),
        ];
    }

    private function validateAiQuestionRequest(Request $request): array
    {
        $validated = $request->validate([
            'course_key' => ['required', 'string', 'max:120'],
            'generation_notes' => ['required', 'string', 'max:4000'],
            'difficulty' => ['required', 'in:beginner,intermediate,advanced'],
            'question_count' => ['required', 'integer', 'min:1', 'max:10'],
            'question_type_mode' => ['required', 'in:mcq,essay,true_false,mixed_mcq_essay,mixed_all'],
            'placement_after_chapter' => ['nullable', 'integer', 'min:1', 'max:40'],
            'is_pop_quiz' => ['nullable', 'boolean'],
        ]);

        $context = $this->courseContextFromKey($validated['course_key']);
        if (!empty($validated['placement_after_chapter']) && $validated['placement_after_chapter'] > $context['chapter_count']) {
            throw ValidationException::withMessages([
                'placement_after_chapter' => 'Posisi chapter melebihi jumlah chapter course.',
            ]);
        }

        return array_merge($validated, $context, [
            'is_pop_quiz' => (bool) ($validated['is_pop_quiz'] ?? false),
        ]);
    }

    private function insertGeneratedQuestions(array $preview): int
    {
        $rows = [];
        foreach ($preview['questions'] as $question) {
            $rows[] = [
                'quiz_id' => $preview['quiz_id'],
                'course_slug' => $preview['course_slug'],
                'question_text' => $question['question_text'],
                'question_type' => $question['question_type'],
                'category' => $preview['course_category'],
                'difficulty' => $question['difficulty'],
                'correct_answer' => $question['correct_answer'] !== '' ? $question['correct_answer'] : null,
                'options_json' => $question['options_json'],
                'placement_after_chapter' => $preview['placement_after_chapter'] ?: null,
                'is_pop_quiz' => (bool) $preview['is_pop_quiz'],
                'requires_perfect_score' => (bool) $preview['is_pop_quiz'],
                'question_origin' => 'ai',
                'generation_notes' => $preview['generation_notes'],
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('question_bank')->insert($rows);

        if (!empty($preview['usage'])) {
            DB::table('ai_usage_logs')->insert([
                'provider' => $preview['usage']['provider'],
                'model' => $preview['usage']['model'],
                'token_count' => (int) ($preview['usage']['token_count'] ?? 0),
                'cost_usd' => 0,
                'logged_at' => now(),
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return count($rows);
    }

    public function dashboard()
    {
        $dashboardData = Cache::remember($this->dashboardCacheKey(), self::DASHBOARD_CACHE_TTL_SECONDS, function () {
            $users = User::query()->orderBy('created_at', 'desc')->get();
            $dosenRequests = User::query()
                ->where('requested_role', 'dosen')
                ->where('dosen_request_status', 'pending')
                ->orderByDesc('created_at')
                ->get();

            $userCounts = User::query()
                ->selectRaw("
                    COUNT(*) as total_users,
                    SUM(CASE WHEN role = 'student' THEN 1 ELSE 0 END) as total_students,
                    SUM(CASE WHEN role = 'dosen' THEN 1 ELSE 0 END) as total_dosen,
                    SUM(CASE WHEN account_status = 'active' THEN 1 ELSE 0 END) as active_accounts,
                    SUM(CASE WHEN account_status = 'suspended' THEN 1 ELSE 0 END) as suspended_accounts
                ")
                ->first();

            $stats = [
                'total_users' => (int) ($userCounts->total_users ?? 0),
                'total_students' => (int) ($userCounts->total_students ?? 0),
                'total_dosen' => (int) ($userCounts->total_dosen ?? 0),
                'active_accounts' => (int) ($userCounts->active_accounts ?? 0),
                'suspended_accounts' => (int) ($userCounts->suspended_accounts ?? 0),
                'total_quizzes' => (int) DB::table('quizzes')->count(),
                'question_bank' => (int) DB::table('question_bank')->count(),
                'modules' => (int) DB::table('learning_modules')->count(),
                'submissions' => (int) DB::table('quiz_submissions')->count(),
            ];

            $quizzes = DB::table('quizzes')
                ->select('id', 'title', 'category', 'difficulty', 'created_at')
                ->latest()
                ->limit(8)
                ->get();

            $allQuizzes = DB::table('quizzes as q')
                ->leftJoin('users as u', 'u.id', '=', 'q.created_by')
                ->select('q.id', 'q.title', 'q.category', 'q.difficulty', 'u.name as owner_name')
                ->orderBy('title')
                ->get();

            $courseInfosByQuizId = DB::table('quiz_course_infos')
                ->whereIn('quiz_id', $allQuizzes->pluck('id'))
                ->get()
                ->keyBy('quiz_id');

            $courseInfoData = [];
            foreach ($allQuizzes as $course) {
                $info = $courseInfosByQuizId->get($course->id);

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
                    'syllabus_lines' => $this->buildSyllabusLines($info->syllabus_json ?? null),
                    'learning_outcomes' => $info->learning_outcomes ?? '',
                    'trailer_url' => $info->trailer_url ?? '',
                    'trailer_poster_url' => $info->trailer_poster_url ?? '',
                ];
            }

            $courseInfoRows = DB::table('quiz_course_infos as i')
                ->join('quizzes as q', 'q.id', '=', 'i.quiz_id')
                ->select('i.quiz_id', 'q.title', 'i.tagline', 'i.target_audience', 'i.updated_at')
                ->orderByDesc('i.updated_at')
                ->limit(10)
                ->get();

            $categories = DB::table('skill_categories')->orderBy('name')->get();

            $submissions = DB::table('quiz_submissions as s')
                ->leftJoin('users as u', 'u.id', '=', 's.user_id')
                ->leftJoin('quizzes as q', 'q.id', '=', 's.quiz_id')
                ->select(
                    's.id',
                    'u.name as student_name',
                    'q.title as quiz_title',
                    's.score',
                    's.manual_score',
                    's.status',
                    's.submitted_at'
                )
                ->orderByDesc('s.submitted_at')
                ->limit(10)
                ->get();

            $questionBankRows = DB::table('question_bank as qb')
                ->leftJoin('quizzes as q', 'q.id', '=', 'qb.quiz_id')
                ->leftJoin('users as u', 'u.id', '=', 'qb.created_by')
                ->select(
                    'qb.id',
                    'qb.question_text',
                    'qb.question_type',
                    'qb.difficulty',
                    'qb.question_origin',
                    'qb.placement_after_chapter',
                    'qb.is_pop_quiz',
                    'qb.course_slug',
                    'q.title as quiz_title',
                    'u.name as creator_name',
                    'qb.created_at'
                )
                ->orderByDesc('qb.created_at')
                ->limit(30)
                ->get();

            $learningAnalytics = DB::table('quiz_submissions as s')
                ->join('quizzes as q', 'q.id', '=', 's.quiz_id')
                ->select('q.category', DB::raw('COUNT(*) as total'))
                ->whereNotNull('q.category')
                ->groupBy('q.category')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            $tokenLogs = DB::table('ai_usage_logs')
                ->select('provider', 'model', 'token_count', 'cost_usd', 'logged_at')
                ->orderByDesc('logged_at')
                ->limit(8)
                ->get();

            $tokenSummary = DB::table('ai_usage_logs')
                ->selectRaw('COALESCE(SUM(token_count),0) as total_tokens, COALESCE(SUM(cost_usd),0) as total_cost')
                ->first();

            $feedbackLogs = DB::table('ai_feedback_logs')
                ->select('prompt_summary', 'detected_topic', 'sentiment', 'created_at')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();

            $attendanceStatsRow = DB::table('course_attendance_logs')
                ->selectRaw('COUNT(*) as total_sessions, SUM(CASE WHEN is_attended THEN 1 ELSE 0 END) as attended_sessions')
                ->first();

            $attendanceStats = [
                'total_sessions' => (int) ($attendanceStatsRow->total_sessions ?? 0),
                'attended_sessions' => (int) ($attendanceStatsRow->attended_sessions ?? 0),
                'active_consistent_students' => (int) DB::table('user_course_states')
                    ->where('consistent_mode_enabled', true)
                    ->distinct('user_id')
                    ->count('user_id'),
            ];

            $attendanceOverview = DB::table('course_attendance_logs as a')
                ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
                ->select(
                    'a.course_title',
                    'a.attendance_date',
                    'a.target_chapters',
                    'a.chapters_completed',
                    'a.is_attended',
                    'u.name as student_name'
                )
                ->orderByDesc('a.attendance_date')
                ->orderByDesc('a.updated_at')
                ->limit(18)
                ->get();

            $settings = DB::table('system_settings')->pluck('value', 'key');
            $frontendCraftContent = DB::table('course_page_contents')->where('course_slug', 'frontend-craft')->first();
            $frontendCraftSyllabusText = $this->buildSyllabusLines($frontendCraftContent?->syllabus_json);

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

            return compact(
                'users',
                'dosenRequests',
                'stats',
                'quizzes',
                'allQuizzes',
                'courseInfoRows',
                'categories',
                'submissions',
                'questionBankRows',
                'learningAnalytics',
                'tokenLogs',
                'tokenSummary',
                'feedbackLogs',
                'attendanceStats',
                'attendanceOverview',
                'settings',
                'frontendCraftContent',
                'frontendCraftSyllabusText',
                'courseInfoData'
            );
        });

        extract($dashboardData);

        $manageableCourses = collect([
            (object) [
                'key' => 'frontend-craft',
                'quiz_id' => null,
                'title' => 'Frontend Craft',
                'category' => 'Web Development',
                'difficulty' => 'core',
                'owner_name' => auth()->user()->name,
            ],
        ])->merge(
            $allQuizzes->map(fn ($course) => (object) [
                'key' => (string) $course->id,
                'quiz_id' => $course->id,
                'title' => $course->title,
                'category' => $course->category,
                'difficulty' => $course->difficulty,
                'owner_name' => $course->owner_name ?? 'Unknown',
            ])
        );

        return view('admin.dashboard', compact(
            'users',
            'dosenRequests',
            'stats',
            'quizzes',
            'allQuizzes',
            'courseInfoRows',
            'categories',
            'submissions',
            'questionBankRows',
            'learningAnalytics',
            'tokenLogs',
            'tokenSummary',
            'feedbackLogs',
            'attendanceStats',
            'attendanceOverview',
            'settings',
            'frontendCraftContent',
            'frontendCraftSyllabusText',
            'courseInfoData',
            'manageableCourses'
        ));
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:admin,dosen,student'],
        ]);

        $payload = ['role' => $validated['role']];
        if ($validated['role'] === 'dosen') {
            $payload['requested_role'] = 'dosen';
            $payload['dosen_request_status'] = 'approved';
        }

        $user->update($payload);
        $this->forgetDashboardCaches();

        return back()->with('success', 'User role updated successfully.');
    }

    public function updateUserStatus(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'account_status' => ['required', 'in:active,suspended'],
        ]);

        $user->update(['account_status' => $validated['account_status']]);
        $this->forgetDashboardCaches();

        return back()->with('success', 'Account status updated successfully.');
    }

    public function handleDosenRequest(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
        ]);

        if ($validated['action'] === 'approve') {
            $user->update([
                'role' => 'dosen',
                'requested_role' => 'dosen',
                'dosen_request_status' => 'approved',
                'account_status' => 'active',
            ]);
            $this->forgetDashboardCaches();

            return back()->with('success', 'Pengajuan dosen disetujui.');
        }

        $user->update([
            'role' => 'student',
            'requested_role' => 'dosen',
            'dosen_request_status' => 'rejected',
        ]);
        $this->forgetDashboardCaches();

        return back()->with('success', 'Pengajuan dosen ditolak.');
    }

    public function deleteUser(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['delete_user' => 'Kamu tidak bisa menghapus akunmu sendiri dari menu admin.']);
        }

        if (!empty($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->delete();
        $this->forgetDashboardCaches();

        return back()->with('success', 'Akun user berhasil dihapus.');
    }

    public function storeQuiz(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
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

        $this->forgetCourseCaches();

        return back()->with('success', 'Quiz created successfully.');
    }

    public function storeQuestion(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_key' => ['required', 'string', 'max:120'],
            'question_text' => ['required', 'string'],
            'question_type' => ['required', 'in:mcq,essay,true_false'],
            'difficulty' => ['required', 'in:beginner,intermediate,advanced'],
            'category' => ['nullable', 'string', 'max:100'],
            'correct_answer' => ['nullable', 'string'],
            'options_json' => ['nullable', 'string'],
            'placement_after_chapter' => ['nullable', 'integer', 'min:1', 'max:40'],
            'is_pop_quiz' => ['nullable', 'boolean'],
        ]);

        $context = $this->courseContextFromKey($validated['course_key']);

        DB::table('question_bank')->insert([
            'quiz_id' => $context['quiz_id'],
            'course_slug' => $context['course_slug'],
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'difficulty' => $validated['difficulty'],
            'category' => $validated['category'] ?? $context['course_category'],
            'correct_answer' => $validated['correct_answer'] ?? null,
            'options_json' => $validated['options_json'] ?? null,
            'placement_after_chapter' => $validated['placement_after_chapter'] ?? null,
            'is_pop_quiz' => (bool) ($validated['is_pop_quiz'] ?? false),
            'requires_perfect_score' => (bool) ($validated['is_pop_quiz'] ?? false),
            'question_origin' => 'manual',
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->forgetDashboardCaches();

        return back()->with('success', 'Question added to bank.');
    }

    public function deleteQuestion(int $question): RedirectResponse
    {
        $row = DB::table('question_bank')
            ->where('id', $question)
            ->first(['id', 'quiz_id', 'course_slug']);

        abort_if(!$row, 404);

        DB::table('question_bank')->where('id', $question)->delete();

        $this->forgetCourseCaches($row->quiz_id ? (int) $row->quiz_id : null, $row->course_slug);

        return back()->with('success', 'Question berhasil dihapus dari question bank.');
    }

    public function previewAiQuestions(Request $request, AiQuestionGenerator $generator): RedirectResponse
    {
        $payload = $this->validateAiQuestionRequest($request);

        try {
            $generated = $generator->generate($payload);
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['ai_preview' => $exception->getMessage()])->withInput();
        }

        session([
            self::AI_PREVIEW_SESSION_KEY => array_merge($payload, [
                'preview_batch' => (string) Str::uuid(),
                'questions' => $generated['questions'],
                'usage' => $generated['usage'],
            ]),
        ]);

        return back()->with('success', 'Preview soal AI berhasil dibuat.');
    }

    public function saveAiQuestions(Request $request): RedirectResponse
    {
        $preview = session(self::AI_PREVIEW_SESSION_KEY);
        if (!is_array($preview) || empty($preview['questions'])) {
            return back()->withErrors(['ai_preview' => 'Preview soal AI belum tersedia.']);
        }

        $savedCount = $this->insertGeneratedQuestions($preview);
        session()->forget(self::AI_PREVIEW_SESSION_KEY);
        $this->forgetDashboardCaches();

        return back()->with('success', $savedCount.' soal AI berhasil disimpan ke question bank.');
    }

    public function importQuestions(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'questions_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $path = $validated['questions_file']->getRealPath();
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return back()->withErrors(['questions_file' => 'Cannot read uploaded file.']);
        }

        $rows = [];
        $index = 0;

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if ($index === 0 && isset($data[0]) && strtolower(trim($data[0])) === 'question_text') {
                $index++;
                continue;
            }

            if (!isset($data[0]) || trim($data[0]) === '') {
                $index++;
                continue;
            }

            $rows[] = [
                'question_text' => trim($data[0]),
                'question_type' => isset($data[1]) && trim($data[1]) !== '' ? trim($data[1]) : 'mcq',
                'difficulty' => isset($data[2]) && trim($data[2]) !== '' ? trim($data[2]) : 'beginner',
                'category' => $data[3] ?? null,
                'correct_answer' => $data[4] ?? null,
                'options_json' => $data[5] ?? null,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $index++;
        }

        fclose($handle);

        if (empty($rows)) {
            return back()->withErrors(['questions_file' => 'CSV file is empty or invalid.']);
        }

        DB::table('question_bank')->insert($rows);
        $this->forgetDashboardCaches();

        return back()->with('success', count($rows).' questions imported.');
    }

    public function exportQuestions(): StreamedResponse
    {
        $fileName = 'question_bank_'.now()->format('Ymd_His').'.csv';
        $rows = DB::table('question_bank')->orderByDesc('id')->get();

        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['question_text', 'question_type', 'difficulty', 'category', 'correct_answer', 'options_json']);

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->question_text,
                    $row->question_type,
                    $row->difficulty,
                    $row->category,
                    $row->correct_answer,
                    $row->options_json,
                ]);
            }

            fclose($out);
        }, $fileName);
    }

    public function gradeSubmission(Request $request, int $submission): RedirectResponse
    {
        $validated = $request->validate([
            'manual_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::table('quiz_submissions')
            ->where('id', $submission)
            ->update([
                'manual_score' => $validated['manual_score'],
                'status' => 'graded',
                'graded_at' => now(),
                'graded_by' => auth()->id(),
                'remarks' => $validated['remarks'] ?? null,
                'updated_at' => now(),
            ]);
        $this->forgetDashboardCaches();

        return back()->with('success', 'Submission graded successfully.');
    }

    public function exportGrades(): StreamedResponse
    {
        $fileName = 'grades_'.now()->format('Ymd_His').'.csv';

        $rows = DB::table('quiz_submissions as s')
            ->leftJoin('users as u', 'u.id', '=', 's.user_id')
            ->leftJoin('quizzes as q', 'q.id', '=', 's.quiz_id')
            ->select(
                'u.name as student_name',
                'u.email',
                'q.title as quiz_title',
                's.score',
                's.manual_score',
                's.status',
                's.submitted_at'
            )
            ->orderByDesc('s.submitted_at')
            ->get();

        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['student_name', 'email', 'quiz_title', 'score', 'manual_score', 'status', 'submitted_at']);

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->student_name,
                    $row->email,
                    $row->quiz_title,
                    $row->score,
                    $row->manual_score,
                    $row->status,
                    $row->submitted_at,
                ]);
            }

            fclose($out);
        }, $fileName);
    }

    public function storeModule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:skill_categories,id'],
            'type' => ['required', 'in:pdf,youtube,article'],
            'content_url' => ['nullable', 'string', 'max:255'],
            'article_body' => ['nullable', 'string'],
        ]);

        DB::table('learning_modules')->insert([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'] ?? null,
            'type' => $validated['type'],
            'content_url' => $validated['content_url'] ?? null,
            'article_body' => $validated['article_body'] ?? null,
            'uploaded_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->forgetDashboardCaches();

        return back()->with('success', 'Learning module uploaded.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:skill_categories,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        DB::table('skill_categories')->insert([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->forgetDashboardCaches();

        return back()->with('success', 'Category created successfully.');
    }

    public function setMaintenance(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:on,off'],
        ]);

        if ($validated['mode'] === 'on') {
            Artisan::call('down');
            return back()->with('success', 'Maintenance mode enabled.');
        }

        Artisan::call('up');

        return back()->with('success', 'Maintenance mode disabled.');
    }

    public function updateCourseInfo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'quiz_id' => ['required', 'exists:quizzes,id'],
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
            'hero_background_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $existing = DB::table('quiz_course_infos')
            ->where('quiz_id', $validated['quiz_id'])
            ->first(['hero_background_url']);

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
                'trailer_url' => $validated['trailer_url'] ?? null,
                'trailer_poster_url' => $validated['trailer_poster_url'] ?? null,
                'instructor_name' => $validated['instructor_name'] ?? null,
                'instructor_photo_url' => $validated['instructor_photo_url'] ?? null,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $this->forgetCourseCaches((int) $validated['quiz_id'], 'quiz-'.$validated['quiz_id']);

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
            'hero_background_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $existing = DB::table('course_page_contents')
            ->where('course_slug', 'frontend-craft')
            ->first(['hero_background_url']);

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
                'trailer_url' => $validated['trailer_url'] ?? null,
                'trailer_poster_url' => $validated['trailer_poster_url'] ?? null,
                'instructor_name' => $validated['instructor_name'] ?? null,
                'instructor_photo_url' => $validated['instructor_photo_url'] ?? null,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $this->forgetCourseCaches(null, 'frontend-craft');

        return back()->with('success', 'Frontend Craft page content berhasil diperbarui.');
    }

    public function updateSiteIdentity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:120'],
            'logo_url' => ['nullable', 'string', 'max:255'],
            'theme_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $this->setSetting('site_name', $validated['site_name']);
        $this->setSetting('logo_url', $validated['logo_url'] ?? '');
        $this->setSetting('theme_color', $validated['theme_color']);
        $this->forgetDashboardCaches();

        return back()->with('success', 'Site identity updated.');
    }

    public function updateChatbotSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'chatbot_name' => ['required', 'string', 'max:80'],
            'chatbot_welcome' => ['required', 'string', 'max:255'],
            'chatbot_placeholder' => ['required', 'string', 'max:120'],
            'chatbot_personality' => ['required', 'string', 'max:4000'],
        ]);

        $this->setSetting('chatbot_name', $validated['chatbot_name']);
        $this->setSetting('chatbot_welcome', $validated['chatbot_welcome']);
        $this->setSetting('chatbot_placeholder', $validated['chatbot_placeholder']);
        $this->setSetting('chatbot_personality', $validated['chatbot_personality']);
        $this->forgetDashboardCaches();

        return back()->with('success', 'AI chatbot settings updated.');
    }

    private function setSetting(string $key, ?string $value): void
    {
        DB::table('system_settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'updated_at' => now(), 'created_at' => now()]
        );
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
