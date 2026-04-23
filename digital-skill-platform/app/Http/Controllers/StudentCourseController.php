<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserCourseState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentCourseController extends Controller
{
    private function resolveCourseContextBySlug(string $slug): ?array
    {
        if ($slug === 'frontend-craft') {
            $content = DB::table('course_page_contents')
                ->where('course_slug', 'frontend-craft')
                ->first(['hero_title', 'updated_by']);

            return [
                'course_slug' => 'frontend-craft',
                'course_title' => !empty($content?->hero_title) ? $content->hero_title : 'Frontend Craft',
                'quiz_id' => null,
                'dosen_id' => $content->updated_by ?? null,
                'info_route' => route('courses.frontend-craft.info'),
            ];
        }

        $quizId = $this->quizIdFromSlug($slug);
        if (!$quizId) {
            return null;
        }

        $quiz = DB::table('quizzes')
            ->where('id', $quizId)
            ->first(['id', 'title', 'created_by']);

        if (!$quiz) {
            return null;
        }

        return [
            'course_slug' => $slug,
            'course_title' => $quiz->title,
            'quiz_id' => $quiz->id,
            'dosen_id' => $quiz->created_by,
            'info_route' => route('courses.quiz.info', ['quiz' => $quiz->id]),
        ];
    }

    private function catalog(): array
    {
        return [
            'frontend-craft' => [
                'title' => 'Frontend Craft',
                'route' => 'courses.frontend-craft',
                'roadmap_route' => 'courses.frontend-craft.roadmap',
            ],
        ];
    }

    private function quizCourseSlug(int $quizId): string
    {
        return 'quiz-'.$quizId;
    }

    private function quizIdFromSlug(string $slug): ?int
    {
        if (preg_match('/^quiz-(\d+)$/', $slug, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function chapterDefaults(): array
    {
        return [
            1 => ['title' => 'Kickoff Setup', 'description' => 'Atur tools dan workspace agar siap belajar tanpa hambatan.'],
            2 => ['title' => 'Semantic HTML', 'description' => 'Bangun struktur halaman yang bersih, rapi, dan accessible.'],
            3 => ['title' => 'CSS Foundation', 'description' => 'Kuasai color, spacing, typography, dan komposisi visual modern.'],
            4 => ['title' => 'Responsive Layout', 'description' => 'Pelajari Flexbox dan Grid supaya desain adaptif di semua layar.'],
            5 => ['title' => 'JavaScript Core', 'description' => 'Pahami dasar logika, function, dan alur interaksi UI.'],
            6 => ['title' => 'DOM Interaction', 'description' => 'Hubungkan script dan tampilan dengan event serta validasi form.'],
            7 => ['title' => 'Mini Project', 'description' => 'Bangun produk nyata dari nol agar skill langsung terpakai.'],
            8 => ['title' => 'Deploy Polish', 'description' => 'Optimasi final dan rilis project ke publik sebagai portfolio.'],
        ];
    }

    private function chapterCountForSlug(string $slug): int
    {
        $maxChapter = (int) (DB::table('course_lessons')
            ->where('course_slug', $slug)
            ->max('chapter_number') ?? 0);

        return max(8, $maxChapter);
    }

    private function findQuizCourse(int $quiz): ?object
    {
        return DB::table('quizzes as q')
            ->leftJoin('users as u', 'u.id', '=', 'q.created_by')
            ->leftJoin('quiz_course_infos as i', 'i.quiz_id', '=', 'q.id')
            ->select(
                'q.id',
                'q.title',
                'q.category',
                'q.difficulty',
                'q.created_at',
                'q.updated_at',
                'u.name as mentor_name',
                'u.profile_image as mentor_profile_image',
                'i.hero_title',
                'i.hero_background_url',
                'i.tagline',
                'i.about',
                'i.target_audience',
                'i.duration_text',
                'i.syllabus_json',
                'i.learning_outcomes',
                'i.trailer_url',
                'i.trailer_poster_url',
                'i.instructor_name',
                'i.instructor_photo_url'
            )
            ->where('q.id', $quiz)
            ->first();
    }

    private function resolveCourseBySlug(string $slug): ?array
    {
        $catalogCourse = $this->catalog()[$slug] ?? null;
        if ($catalogCourse) {
            return $catalogCourse;
        }

        $quizId = $this->quizIdFromSlug($slug);
        if (!$quizId) {
            return null;
        }

        $quiz = DB::table('quizzes')
            ->where('id', $quizId)
            ->first(['id', 'title']);

        if (!$quiz) {
            return null;
        }

        return [
            'title' => $quiz->title,
            'route' => 'courses.quiz.show',
            'roadmap_route' => 'courses.quiz.roadmap',
        ];
    }

    public function dashboard(): View|RedirectResponse
    {
        $states = auth()->user()
            ->courseStates()
            ->get()
            ->keyBy('course_slug');

        $enrolledCourses = [];
        $favoriteCourses = [];

        foreach ($states as $state) {
            if ($state->course_slug === 'frontend-craft') {
                if ($state->is_enrolled) {
                    $enrolledCourses[] = [
                        'slug' => $state->course_slug,
                        'title' => $state->course_title,
                        'route' => route('courses.frontend-craft'),
                        'roadmap_route' => route('courses.frontend-craft.roadmap'),
                    ];
                }

                if ($state->is_favorite) {
                    $favoriteCourses[] = [
                        'slug' => $state->course_slug,
                        'title' => $state->course_title,
                        'route' => route('courses.frontend-craft'),
                    ];
                }
                continue;
            }

            $quizId = $this->quizIdFromSlug($state->course_slug);
            if (!$quizId) {
                continue;
            }

            if ($state->is_enrolled) {
                $enrolledCourses[] = [
                    'slug' => $state->course_slug,
                    'title' => $state->course_title,
                    'route' => route('courses.quiz.show', ['quiz' => $quizId]),
                    'roadmap_route' => route('courses.quiz.roadmap', ['quiz' => $quizId]),
                ];
            }

            if ($state->is_favorite) {
                $favoriteCourses[] = [
                    'slug' => $state->course_slug,
                    'title' => $state->course_title,
                    'route' => route('courses.quiz.show', ['quiz' => $quizId]),
                ];
            }
        }

        $frontendCraftContent = DB::table('course_page_contents')
            ->where('course_slug', 'frontend-craft')
            ->first(['hero_background_url']);

        $quizCourses = DB::table('quizzes as q')
            ->leftJoin('quiz_course_infos as qi', 'qi.quiz_id', '=', 'q.id')
            ->leftJoin('users as u', 'u.id', '=', 'q.created_by')
            ->select(
                'q.id',
                'q.title',
                'q.category',
                'q.difficulty',
                'u.name as mentor_name',
                'qi.hero_background_url'
            )
            ->orderByDesc('q.created_at')
            ->limit(18)
            ->get();

        $carouselCourses = collect([
            [
                'title' => 'Frontend Craft',
                'category' => 'Web Development',
                'description' => 'Build interactive and responsive modern websites with practical projects.',
                'href' => route('courses.frontend-craft'),
                'image' => $frontendCraftContent?->hero_background_url,
            ],
        ]);

        foreach ($quizCourses as $quizCourse) {
            if (Str::slug((string) $quizCourse->title) === 'frontend-craft') {
                continue;
            }

            $carouselCourses->push([
                'title' => $quizCourse->title,
                'category' => $quizCourse->category ?: 'General Course',
                'description' => 'Mentor: '.($quizCourse->mentor_name ?? 'Digital Skill Team').' • Difficulty: '.ucfirst((string) $quizCourse->difficulty),
                'href' => route('courses.quiz.show', ['quiz' => $quizCourse->id]),
                'image' => $quizCourse->hero_background_url,
            ]);
        }

        $mentors = DB::table('users as u')
            ->join('quizzes as q', 'q.created_by', '=', 'u.id')
            ->where('u.role', 'dosen')
            ->select(
                'u.id',
                'u.name',
                'u.profile_image',
                DB::raw('COUNT(q.id) as courses_count')
            )
            ->groupBy('u.id', 'u.name', 'u.profile_image')
            ->orderByDesc('courses_count')
            ->get();

        return view('student.dashboard', compact('enrolledCourses', 'favoriteCourses', 'carouselCourses', 'mentors'));
    }

    public function showQuizCourse(int $quiz): View|RedirectResponse
    {
        $courseSlug = $this->quizCourseSlug($quiz);
        $state = auth()->user()
            ->courseStates()
            ->where('course_slug', $courseSlug)
            ->first();

        if ($state && $state->is_enrolled) {
            return redirect()->route('courses.quiz.roadmap', ['quiz' => $quiz]);
        }

        return $this->renderQuizCoursePage($quiz, (bool) optional($state)->is_enrolled);
    }

    public function quizInfo(int $quiz): View
    {
        $courseSlug = $this->quizCourseSlug($quiz);
        $state = auth()->user()
            ->courseStates()
            ->where('course_slug', $courseSlug)
            ->first();

        return $this->renderQuizCoursePage($quiz, (bool) optional($state)->is_enrolled);
    }

    private function renderQuizCoursePage(int $quiz, bool $isEnrolled): View
    {
        $course = $this->findQuizCourse($quiz);

        abort_if(!$course, 404);

        $courseSlug = $this->quizCourseSlug($quiz);
        $reviews = DB::table('course_reviews')
            ->where('course_slug', $courseSlug)
            ->latest()
            ->limit(12)
            ->get(['user_id', 'rating', 'review_text', 'created_at']);
        $reviewUsers = User::query()
            ->whereIn('id', $reviews->pluck('user_id')->unique())
            ->pluck('name', 'id');
        $reviewSummary = DB::table('course_reviews')
            ->where('course_slug', $courseSlug)
            ->selectRaw('COUNT(*) as total_reviews, ROUND(AVG(rating), 1) as avg_rating')
            ->first();
        $userReview = DB::table('course_reviews')
            ->where('course_slug', $courseSlug)
            ->where('user_id', auth()->id())
            ->first(['id', 'rating', 'review_text']);

        $questions = DB::table('course_questions')
            ->where('course_slug', $courseSlug)
            ->latest()
            ->limit(12)
            ->get(['user_id', 'chapter_number', 'question_text', 'answer_text', 'created_at']);
        $questionUsers = User::query()
            ->whereIn('id', $questions->pluck('user_id')->unique())
            ->pluck('name', 'id');
        $chapterCount = $this->chapterCountForSlug($courseSlug);

        return view('courses.quiz-course', [
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'courseSlug' => $courseSlug,
            'reviews' => $reviews,
            'reviewUsers' => $reviewUsers,
            'reviewSummary' => $reviewSummary,
            'userReview' => $userReview,
            'questions' => $questions,
            'questionUsers' => $questionUsers,
            'chapterCount' => $chapterCount,
        ]);
    }

    public function mentorProfile(User $mentor): View
    {
        abort_unless($mentor->role === 'dosen', 404);

        $courses = DB::table('quizzes')
            ->where('created_by', $mentor->id)
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'category', 'difficulty', 'created_at']);

        return view('student.mentor-profile', compact('mentor', 'courses'));
    }

    public function frontendCraft(): View|RedirectResponse
    {
        $state = auth()->user()
            ->courseStates()
            ->where('course_slug', 'frontend-craft')
            ->first();

        if ($state && $state->is_enrolled) {
            return redirect()->route('courses.frontend-craft.roadmap');
        }

        return $this->renderFrontendCraftPage((bool) optional($state)->is_enrolled);
    }

    public function frontendCraftInfo(): View
    {
        $state = auth()->user()
            ->courseStates()
            ->where('course_slug', 'frontend-craft')
            ->first();

        return $this->renderFrontendCraftPage((bool) optional($state)->is_enrolled);
    }

    private function renderFrontendCraftPage(bool $isEnrolled): View
    {
        $content = DB::table('course_page_contents')
            ->where('course_slug', 'frontend-craft')
            ->first();

        $syllabus = [];
        if (!empty($content?->syllabus_json)) {
            $decoded = json_decode($content->syllabus_json, true);
            if (is_array($decoded)) {
                $syllabus = $decoded;
            }
        }

        $reviews = DB::table('course_reviews')
            ->where('course_slug', 'frontend-craft')
            ->latest()
            ->limit(12)
            ->get(['user_id', 'rating', 'review_text', 'created_at']);
        $reviewUsers = User::query()
            ->whereIn('id', $reviews->pluck('user_id')->unique())
            ->pluck('name', 'id');
        $reviewSummary = DB::table('course_reviews')
            ->where('course_slug', 'frontend-craft')
            ->selectRaw('COUNT(*) as total_reviews, ROUND(AVG(rating), 1) as avg_rating')
            ->first();
        $userReview = DB::table('course_reviews')
            ->where('course_slug', 'frontend-craft')
            ->where('user_id', auth()->id())
            ->first(['id', 'rating', 'review_text']);

        $questions = DB::table('course_questions')
            ->where('course_slug', 'frontend-craft')
            ->latest()
            ->limit(12)
            ->get(['user_id', 'chapter_number', 'question_text', 'answer_text', 'created_at']);
        $questionUsers = User::query()
            ->whereIn('id', $questions->pluck('user_id')->unique())
            ->pluck('name', 'id');
        $chapterCount = $this->chapterCountForSlug('frontend-craft');

        return view('courses.frontend-craft', [
            'isEnrolled' => $isEnrolled,
            'courseContent' => $content,
            'courseSyllabus' => $syllabus,
            'reviews' => $reviews,
            'reviewUsers' => $reviewUsers,
            'reviewSummary' => $reviewSummary,
            'userReview' => $userReview,
            'questions' => $questions,
            'questionUsers' => $questionUsers,
            'chapterCount' => $chapterCount,
        ]);
    }

    public function frontendCraftRoadmap(): View
    {
        $state = auth()->user()
            ->courseStates()
            ->where('course_slug', 'frontend-craft')
            ->first();
        $content = DB::table('course_page_contents')
            ->where('course_slug', 'frontend-craft')
            ->first(['hero_title']);

        $lessons = DB::table('course_lessons')
            ->where('course_slug', 'frontend-craft')
            ->get()
            ->keyBy('chapter_number');

        $chapterDefaults = $this->chapterDefaults();

        $chaptersCount = $this->chapterCountForSlug('frontend-craft');
        $chapters = [];
        for ($i = 1; $i <= $chaptersCount; $i++) {
            $lesson = $lessons->get($i);
            $default = $chapterDefaults[$i] ?? [
                'title' => 'Chapter '.$i,
                'description' => 'Materi lanjutan chapter '.$i.'.',
            ];
            $chapters[] = [
                'number' => $i,
                'title' => $lesson?->title ?? $default['title'],
                'description' => $lesson?->description ? Str::limit($lesson->description, 90) : $default['description'],
                'video_ready' => (bool) ($lesson && ($lesson->video_url || $lesson->video_path)),
                'position' => $i % 2 === 0 ? 'up' : 'down',
                'href' => route('courses.frontend-craft.chapter', ['chapter' => $i]),
            ];
        }

        $roadmapQuestions = DB::table('course_questions')
            ->where('course_slug', 'frontend-craft')
            ->latest()
            ->limit(40)
            ->get(['user_id', 'chapter_number', 'question_text', 'answer_text', 'created_at']);
        $roadmapQuestionUsers = User::query()
            ->whereIn('id', $roadmapQuestions->pluck('user_id')->unique())
            ->pluck('name', 'id');

        return view('courses.frontend-craft-roadmap', [
            'isFavorite' => (bool) optional($state)->is_favorite,
            'roadmapTitle' => !empty($content?->hero_title) ? $content->hero_title : 'Frontend Craft',
            'chapters' => $chapters,
            'roadmapQuestions' => $roadmapQuestions,
            'roadmapQuestionUsers' => $roadmapQuestionUsers,
        ]);
    }

    public function enrollQuiz(int $quiz): RedirectResponse
    {
        $course = DB::table('quizzes')
            ->where('id', $quiz)
            ->first(['id', 'title']);

        abort_if(!$course, 404);

        $slug = $this->quizCourseSlug($quiz);

        UserCourseState::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'course_slug' => $slug,
            ],
            [
                'course_title' => $course->title,
                'is_enrolled' => true,
            ]
        );

        return redirect()->route('courses.quiz.roadmap', ['quiz' => $quiz]);
    }

    public function quizRoadmap(int $quiz): View|RedirectResponse
    {
        $course = DB::table('quizzes as q')
            ->leftJoin('quiz_course_infos as i', 'i.quiz_id', '=', 'q.id')
            ->where('q.id', $quiz)
            ->first([
                'q.id',
                'q.title',
                'i.hero_title',
            ]);
        abort_if(!$course, 404);

        $slug = $this->quizCourseSlug($quiz);
        $state = auth()->user()
            ->courseStates()
            ->where('course_slug', $slug)
            ->first();

        if (!$state || !$state->is_enrolled) {
            return redirect()->route('courses.quiz.show', ['quiz' => $quiz]);
        }

        $lessons = DB::table('course_lessons')
            ->where('course_slug', $slug)
            ->get()
            ->keyBy('chapter_number');

        $chapterDefaults = $this->chapterDefaults();
        $chaptersCount = $this->chapterCountForSlug($slug);
        $chapters = [];
        for ($i = 1; $i <= $chaptersCount; $i++) {
            $lesson = $lessons->get($i);
            $default = $chapterDefaults[$i] ?? [
                'title' => 'Chapter '.$i,
                'description' => 'Materi lanjutan chapter '.$i.'.',
            ];
            $chapters[] = [
                'number' => $i,
                'title' => $lesson?->title ?? $default['title'],
                'description' => $lesson?->description ? Str::limit($lesson->description, 90) : $default['description'],
                'video_ready' => (bool) ($lesson && ($lesson->video_url || $lesson->video_path)),
                'position' => $i % 2 === 0 ? 'up' : 'down',
                'href' => route('courses.quiz.chapter', ['quiz' => $quiz, 'chapter' => $i]),
            ];
        }

        $roadmapQuestions = DB::table('course_questions')
            ->where('course_slug', $slug)
            ->latest()
            ->limit(40)
            ->get(['user_id', 'chapter_number', 'question_text', 'answer_text', 'created_at']);
        $roadmapQuestionUsers = User::query()
            ->whereIn('id', $roadmapQuestions->pluck('user_id')->unique())
            ->pluck('name', 'id');

        return view('courses.quiz-roadmap', [
            'course' => $course,
            'roadmapTitle' => !empty($course->hero_title) ? $course->hero_title : $course->title,
            'chapters' => $chapters,
            'isFavorite' => (bool) optional($state)->is_favorite,
            'roadmapQuestions' => $roadmapQuestions,
            'roadmapQuestionUsers' => $roadmapQuestionUsers,
        ]);
    }

    public function quizChapter(int $quiz, int $chapter): View|RedirectResponse
    {
        $course = $this->findQuizCourse($quiz);
        abort_if(!$course, 404);

        $slug = $this->quizCourseSlug($quiz);
        $chaptersCount = $this->chapterCountForSlug($slug);
        if ($chapter < 1 || $chapter > $chaptersCount) {
            abort(404);
        }
        $state = auth()->user()
            ->courseStates()
            ->where('course_slug', $slug)
            ->first();

        if (!$state || !$state->is_enrolled) {
            return redirect()->route('courses.quiz.show', ['quiz' => $quiz]);
        }

        $lesson = DB::table('course_lessons')
            ->where('course_slug', $slug)
            ->where('chapter_number', $chapter)
            ->first();

        $lessonRows = DB::table('course_lessons')
            ->where('course_slug', $slug)
            ->get()
            ->keyBy('chapter_number');

        $chapterDefaults = $this->chapterDefaults();
        $chapterItems = [];
        for ($i = 1; $i <= $chaptersCount; $i++) {
            $row = $lessonRows->get($i);
            $default = $chapterDefaults[$i] ?? [
                'title' => 'Chapter '.$i,
                'description' => 'Materi lanjutan chapter '.$i.'.',
            ];

            $chapterItems[] = [
                'number' => $i,
                'title' => $row?->title ?? $default['title'],
                'has_video' => (bool) ($row && ($row->video_url || $row->video_path)),
                'href' => route('courses.quiz.chapter', ['quiz' => $course->id, 'chapter' => $i]),
            ];
        }

        $videoReadyCount = collect($chapterItems)->where('has_video', true)->count();
        $currentDefault = $chapterDefaults[$chapter] ?? [
            'title' => 'Chapter '.$chapter,
            'description' => 'Materi lanjutan chapter '.$chapter.'.',
        ];
        $qaItems = DB::table('course_questions')
            ->where('course_slug', $slug)
            ->where(function ($query) use ($chapter) {
                $query->whereNull('chapter_number')
                    ->orWhere('chapter_number', $chapter);
            })
            ->latest()
            ->limit(20)
            ->get(['user_id', 'chapter_number', 'question_text', 'answer_text', 'created_at']);
        $qaUsers = User::query()
            ->whereIn('id', $qaItems->pluck('user_id')->unique())
            ->pluck('name', 'id');

        return view('courses.quiz-chapter', [
            'course' => $course,
            'chapter' => $chapter,
            'lesson' => $lesson,
            'lessonTitle' => $lesson?->title ?? $currentDefault['title'],
            'lessonDescription' => $lesson?->description ?? $currentDefault['description'],
            'chapterItems' => $chapterItems,
            'chaptersCount' => $chaptersCount,
            'videoReadyCount' => $videoReadyCount,
            'hasPrevious' => $chapter > 1,
            'hasNext' => $chapter < $chaptersCount,
            'roadmapUrl' => route('courses.quiz.roadmap', ['quiz' => $course->id]),
            'dashboardUrl' => route('student.dashboard'),
            'chapterPrevUrl' => $chapter > 1 ? route('courses.quiz.chapter', ['quiz' => $course->id, 'chapter' => $chapter - 1]) : null,
            'chapterNextUrl' => $chapter < $chaptersCount ? route('courses.quiz.chapter', ['quiz' => $course->id, 'chapter' => $chapter + 1]) : null,
            'progressStorageKey' => 'quiz_course_completed_'.$course->id,
            'notesStorageKey' => 'quiz_course_notes_'.$course->id.'_'.$chapter,
            'qaItems' => $qaItems,
            'qaUsers' => $qaUsers,
            'qaPostUrl' => route('courses.questions.store', ['slug' => $slug]),
        ]);
    }

    public function frontendCraftChapter(int $chapter): View|RedirectResponse
    {
        $chaptersCount = $this->chapterCountForSlug('frontend-craft');
        if ($chapter < 1 || $chapter > $chaptersCount) {
            abort(404);
        }

        $state = auth()->user()
            ->courseStates()
            ->where('course_slug', 'frontend-craft')
            ->first();

        if (!$state || !$state->is_enrolled) {
            return redirect()->route('courses.frontend-craft.info');
        }

        $lesson = DB::table('course_lessons')
            ->where('course_slug', 'frontend-craft')
            ->where('chapter_number', $chapter)
            ->first();
        $lessonRows = DB::table('course_lessons')
            ->where('course_slug', 'frontend-craft')
            ->get()
            ->keyBy('chapter_number');
        $content = DB::table('course_page_contents')
            ->where('course_slug', 'frontend-craft')
            ->first();

        $chapterDefaults = $this->chapterDefaults();
        $chapterItems = [];
        for ($i = 1; $i <= $chaptersCount; $i++) {
            $row = $lessonRows->get($i);
            $default = $chapterDefaults[$i] ?? [
                'title' => 'Chapter '.$i,
                'description' => 'Materi lanjutan chapter '.$i.'.',
            ];

            $chapterItems[] = [
                'number' => $i,
                'title' => $row?->title ?? $default['title'],
                'has_video' => (bool) ($row && ($row->video_url || $row->video_path)),
                'href' => route('courses.frontend-craft.chapter', ['chapter' => $i]),
            ];
        }

        $videoReadyCount = collect($chapterItems)->where('has_video', true)->count();
        $currentDefault = $chapterDefaults[$chapter] ?? [
            'title' => 'Chapter '.$chapter,
            'description' => 'Materi lanjutan chapter '.$chapter.'.',
        ];
        $qaItems = DB::table('course_questions')
            ->where('course_slug', 'frontend-craft')
            ->where(function ($query) use ($chapter) {
                $query->whereNull('chapter_number')
                    ->orWhere('chapter_number', $chapter);
            })
            ->latest()
            ->limit(20)
            ->get(['user_id', 'chapter_number', 'question_text', 'answer_text', 'created_at']);
        $qaUsers = User::query()
            ->whereIn('id', $qaItems->pluck('user_id')->unique())
            ->pluck('name', 'id');
        $course = (object) [
            'id' => null,
            'title' => !empty($content?->hero_title) ? $content->hero_title : 'Frontend Craft',
            'tagline' => $content->tagline ?? null,
            'difficulty' => 'beginner',
            'duration_text' => $content->duration_text ?? null,
            'mentor_name' => $content->instructor_name ?? 'Digital Skill Team',
            'about' => $content->about ?? null,
            'category' => 'Web Development',
            'updated_at' => $content->updated_at ?? now(),
            'created_at' => $content->created_at ?? now(),
        ];

        return view('courses.quiz-chapter', [
            'course' => $course,
            'chapter' => $chapter,
            'lesson' => $lesson,
            'lessonTitle' => $lesson?->title ?? $currentDefault['title'],
            'lessonDescription' => $lesson?->description ?? $currentDefault['description'],
            'chapterItems' => $chapterItems,
            'chaptersCount' => $chaptersCount,
            'videoReadyCount' => $videoReadyCount,
            'hasPrevious' => $chapter > 1,
            'hasNext' => $chapter < $chaptersCount,
            'roadmapUrl' => route('courses.frontend-craft.roadmap'),
            'dashboardUrl' => route('student.dashboard'),
            'chapterPrevUrl' => $chapter > 1 ? route('courses.frontend-craft.chapter', ['chapter' => $chapter - 1]) : null,
            'chapterNextUrl' => $chapter < $chaptersCount ? route('courses.frontend-craft.chapter', ['chapter' => $chapter + 1]) : null,
            'progressStorageKey' => 'frontend_craft_completed',
            'notesStorageKey' => 'frontend_craft_notes_'.$chapter,
            'qaItems' => $qaItems,
            'qaUsers' => $qaUsers,
            'qaPostUrl' => route('courses.questions.store', ['slug' => 'frontend-craft']),
        ]);
    }

    public function enroll(string $slug): RedirectResponse
    {
        $course = $this->catalog()[$slug] ?? null;
        abort_if(!$course, 404);

        UserCourseState::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'course_slug' => $slug,
            ],
            [
                'course_title' => $course['title'],
                'is_enrolled' => true,
            ]
        );

        return redirect()->route($course['roadmap_route']);
    }

    public function toggleFavorite(string $slug): JsonResponse
    {
        $course = $this->resolveCourseBySlug($slug);
        abort_if(!$course, 404);

        $state = UserCourseState::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'course_slug' => $slug,
            ],
            [
                'course_title' => $course['title'],
                'is_enrolled' => false,
                'is_favorite' => false,
            ]
        );

        $state->is_favorite = !$state->is_favorite;
        $state->save();

        return response()->json([
            'is_favorite' => $state->is_favorite,
        ]);
    }

    public function submitReview(Request $request, string $slug): RedirectResponse
    {
        $context = $this->resolveCourseContextBySlug($slug);
        abort_if(!$context, 404);

        $enrolled = auth()->user()
            ->courseStates()
            ->where('course_slug', $context['course_slug'])
            ->where('is_enrolled', true)
            ->exists();

        if (!$enrolled) {
            return redirect()->to($context['info_route'])->withErrors(['review' => 'Enroll course dulu sebelum kirim review.']);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review_text' => ['nullable', 'string', 'max:2000'],
        ]);

        $existingReview = DB::table('course_reviews')
            ->where('course_slug', $context['course_slug'])
            ->where('user_id', auth()->id())
            ->first(['id']);

        if ($existingReview) {
            DB::table('course_reviews')
                ->where('id', $existingReview->id)
                ->update([
                    'dosen_id' => $context['dosen_id'],
                    'quiz_id' => $context['quiz_id'],
                    'course_title' => $context['course_title'],
                    'rating' => $validated['rating'],
                    'review_text' => $validated['review_text'] ?? null,
                    'updated_at' => now(),
                ]);

            return redirect()->to($context['info_route'].'#review')->with('success', 'Review berhasil diupdate.');
        }

        DB::table('course_reviews')->insert([
            'user_id' => auth()->id(),
            'dosen_id' => $context['dosen_id'],
            'quiz_id' => $context['quiz_id'],
            'course_slug' => $context['course_slug'],
            'course_title' => $context['course_title'],
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->to($context['info_route'].'#review')->with('success', 'Review berhasil dikirim.');
    }

    public function submitQuestion(Request $request, string $slug): RedirectResponse
    {
        $context = $this->resolveCourseContextBySlug($slug);
        abort_if(!$context, 404);

        $enrolled = auth()->user()
            ->courseStates()
            ->where('course_slug', $context['course_slug'])
            ->where('is_enrolled', true)
            ->exists();

        if (!$enrolled) {
            return redirect()->to($context['info_route'])->withErrors(['qa' => 'Enroll course dulu sebelum kirim pertanyaan.']);
        }

        $maxChapter = $this->chapterCountForSlug($context['course_slug']);
        $validated = $request->validate([
            'chapter_number' => ['nullable', 'integer', 'min:1', 'max:'.$maxChapter],
            'question_text' => ['required', 'string', 'max:3000'],
        ]);

        DB::table('course_questions')->insert([
            'user_id' => auth()->id(),
            'dosen_id' => $context['dosen_id'],
            'quiz_id' => $context['quiz_id'],
            'course_slug' => $context['course_slug'],
            'course_title' => $context['course_title'],
            'chapter_number' => $validated['chapter_number'] ?? null,
            'question_text' => $validated['question_text'],
            'answer_text' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->to($context['info_route'].'#qa')->with('success', 'Pertanyaan berhasil dikirim ke dosen.');
    }
}
