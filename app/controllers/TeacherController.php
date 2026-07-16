<?php

namespace App\Controllers;

use App\Models\AssignmentAttemptsModel;
use App\Models\CategoriesModel;
use App\Models\CoursesModel;
use App\Models\EnrollmentsModel;
use App\Models\MaterialsModel;
use App\Models\MaterialVideosModel;
use App\Models\QuizOptionsModel;
use App\Models\QuizQuestionsModel;
use App\Models\QuizzesModel;
use Core\Auth;
use Core\Controller;
use Core\Request;
use Core\Validator;
use Core\Str;
use Core\File;
use Core\Transaction;
use Core\Redirect;
use Core\Flash;

class TeacherController extends Controller {
    
    private $coursesModel;
    private $assignmentAttemptsModel;
    private $enrollmentsModel;
    private $materialsModel;
    private $categoriesModel;
    private $materialVideosModel;
    private $quizzesModel;
    private $quizQuestionsModel;
    private $quizOptionsModel;

    protected array $middleware = [];

    public function __construct() {
        $this->coursesModel = new CoursesModel();
        $this->assignmentAttemptsModel = new AssignmentAttemptsModel();
        $this->enrollmentsModel = new EnrollmentsModel();
        $this->materialsModel = new MaterialsModel();
        $this->categoriesModel = new CategoriesModel();
        $this->materialVideosModel = new MaterialVideosModel();
        $this->quizzesModel = new QuizzesModel();
        $this->quizQuestionsModel = new QuizQuestionsModel();
        $this->quizOptionsModel = new QuizOptionsModel();
    }

    public function dashboard() {
        $teacherId = Auth::info('id');

        $teacherStats = $this->coursesModel->getTeacherStat($teacherId);

        $recentCourse = $this->coursesModel->getLatestCoursesTeacher($teacherId);

        $rejectedCourse = $this->coursesModel->getLatestRejectedCourses($teacherId);
        $countRejectedCourse = $this->coursesModel->countRejectedCourses($teacherId);

        $submittedAssignment = $this->assignmentAttemptsModel->getSubmittedAssignments($teacherId);
        $totalSubmittedAssignments = $this->assignmentAttemptsModel->countPendingAssignments($teacherId);

        $recentActivities = $this->enrollmentsModel->getTeacherDashboardActivity($teacherId);

        $data = [
            'teacherStats' => $teacherStats,
            'recentCourse' => $recentCourse,
            'rejectedCourse' => $rejectedCourse,
            'countRejectedCourse' =>$countRejectedCourse,
            'submittedAssignment' => $submittedAssignment,
            'totalSubmittedAssignments' => $totalSubmittedAssignments,
            'recentActivities' => $recentActivities
        ];
        
        $this->view('teacher/dashboard', $data);
    }

    public function courses() {
        $teacherId = Auth::info('id');

        $status = Request::get('status', 'all');
        $sort = Request::get('sort', 'newest');
        $search = trim(Request::get('search', ''));
        $page = max(1, (int) Request::get('page', 1));

        $limit = 8;

        $courseModel = $this->coursesModel;
        
        $totalCourses = $courseModel->countTeacherCourses(
            $teacherId,
            $status,
            $search
        );

        $totalPages = max(1, (int) ceil($totalCourses / $limit));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;

        $courses = $courseModel->getTeacherCourses(
            $teacherId,
            $status,
            $sort,
            $search,
            $limit,
            $offset
        );

        $data = [
            'courses' => $courses,
            'totalCourses' => $totalCourses,
            'totalPages' => $totalPages,
            'page' => $page,
            'limit' => $limit,
            'status' => $status,
            'sort' => $sort,
            'search' => $search
        ];
        
        $this->view('teacher/courses_teacher', $data);
    }

    public function createCourse() {
        $categories = $this->categoriesModel->getAllCategories();

        $data = [
            'categories' => $categories
        ];

        $this->view('teacher/create_course', $data);
    }

    public function storeCourse() {
        $data = Request::post();
        $teacherId = Auth::info('id');

        Validator::validate(
            $data,
            [
                'title' => 'required|max:120',
                'category' => 'required',
                'difficulty' => 'required',
                'short_description' => 'required|max:200',
                'description' => 'required|max:2000'
            ],
            [
                'title' => 'Course title',
                'category' => 'Category',
                'difficulty' => 'Difficulty',
                'short_description' => 'Short description',
                'description' => 'Description'
            ]
        );

        Validator::validateFile(
            Request::file(),
            [
                'thumbnail' => 'image|mimes:jpg,jpeg,png|max:2'
            ]
        );

        $this->failIf(
            Validator::fails(),
            '/create/course',
            Validator::errors()
        );

        $courseId = Str::courseId();

        $thumbnail = '';

        if (Request::hasFile('thumbnail')) {
            $thumbnail = File::upload(
                Request::file('thumbnail'),
                UPLOAD_PATH . '/course-thumbnails',
                $courseId
            );

            $this->failIf(
                !$thumbnail,
                '/create/course',
                [
                    'thumbnail' => 'Upload thumbnail gagal.'
                ]
            );
        }

        $transaction = Transaction::run(function () use ($courseId, $teacherId, $data, $thumbnail) {
            $this->coursesModel->create([
                'course_id' => $courseId,
                'instructor_id' => $teacherId,
                'course_name' => $data['title'],
                'category_id' => $data['category'],
                'short_description' => $data['short_description'],
                'description' => $data['description'],
                'thumbnail' => $thumbnail,
                'difficulty' => $data['difficulty'],
                'status' => 'draft',
                'total_materials' => 0,
                'total_students' => 0,
                'total_duration' => 0,
                'average_rating' => 0,
                'total_reviews' => 0
            ]);
        });

        if (!$transaction) {
            File::delete(UPLOAD_PATH . '/course-thumbnails/' . $thumbnail);

            $this->redirectIf(
                true,
                '/teacher/create/course',
                'error',
                'Gagal membuat course.'
            );
        }

        Redirect::to('/teacher/course/' . $courseId);
    }

    public function course(array $params) {
        $courseId = $params['course_id'];
        $teacherId = Auth::info('id');

        $course = $this->getCourseAndAuthorized($courseId, $teacherId);

        $materials = $this->materialsModel->getAllMaterials($courseId);

        $data = [
            'course' => $course,
            'materials' => $materials
        ];
        
        $this->view('teacher/course_details', $data);
    }

    private function getCourseAndAuthorized(string $courseId, string $teacherId) {
        $course = $this->coursesModel->findCourse($courseId);

        $this->abortIf(!$course);

        $this->abortIf($course['instructor_id'] !== $teacherId, 403);

        return $course;
    }

    public function createVideo(array $params) {
        $courseId = $params['course_id'];

        $course = $this->coursesModel->findCourse($courseId);

        $data = [
            'isEdit' => false,
            'course' => [
                'course_id' => $courseId,
                'title' => $course['course_name']
            ],
            'formAction' => '/create/video/' . $course['course_id']
        ];
        
        $this->view('teacher/materials/material_video', $data);
    }

    public function editVideo(array $params) {
        $materialId = $params['material_id'];

        $material = $this->materialsModel->findMaterial($materialId);

        $course = $this->coursesModel->findCourse($material['course_id']);

        $materialVideo = $this->materialVideosModel->findMaterialVideo($materialId);

        $quiz = $this->quizzesModel->findQuiz($materialId);

        $this->quizQuestionsModel->getAllQuestionsWithOptions($quiz['quiz_id']);

        $materialFull = [
            'title'           => $material['title'],
            'video_source'    => $,
            'video_url'       => $materialVideo['video_source'] == 'youtube' ? $materialVideo['video_url'] : null,
            'video_path'      => $materialVideo['video_source'] == 'video' ? $materialVideo['video_url'] : null,
            'video_filename'  => $materialVideo['video_source'] == 'youtube' ? $materialVideo['video_url'] : null,
        ];

        $settings = [
            'minimum_correct'  => $quiz['minimum_correct'],
            'total_questions'  => 5,
            'max_attempts'     => 3,
            'reset_minutes'    => 30,
            'timer'            => 15,
        ];

        $data = [
            'isEdit' => true,
            'formAction' => '/edit/video/' . $materialId,
            'course' => [
                'course_id' => $course['course_id'],
                'title' => $course['course_name']
            ],
            'material' => $materialFull,
            'settings' => $settings,
            'oldQuestions' => $oldQustions
        ];
    }

    public function storeVideo(array $params) {
        $courseId = $params['course_id'];
        $data = Request::post();
        $file = Request::file();

        Validator::validate(
            $data,
            [
                'title' => 'required|max:120'
            ],
            [
                'title' => 'Material title'
            ]
        );

        if ($data['video_source'] === 'youtube') {
            Validator::check(
                empty($data['video_url']),
                'video_url',
                'Video URL wajib diisi.'
            );
        } else {
            Validator::validateFile(
                $file,
                [
                    'video_file' => 'required|mimes:mp4,webm|max:200'
                ],
                [
                    'video_file' => 'Video'
                ]
            );
        }

        $this->validateQuiz(
            $data['quiz'] ?? [],
            [
                'minimum_correct' => $data['minimum_correct'],
                'max_attempts' => $data['max_attempts'],
                'timer' => $data['timer'],
                'reset_minutes' => $data['reset_minutes']
            ]
        );

        $this->failIf(
            Validator::fails(),
            "/create/video/$courseId",
            Validator::errors()
        );

        $materialId = Str::materialId();

        $videoFile = '';
        
        if (Request::hasFile('video_file')) {
            $videoFile = File::upload(
                Request::file('video_file'),
                UPLOAD_PATH . '/material-videos',
                $materialId
            );

            $this->failIf(
                !$videoFile,
                "/create/video/$courseId",
                [
                    'error' => 'Upload video gagal'
                ]
            );
        }

        $videoPath = $data['video_url'] ?? $videoFile;

        $orderIndex = $this->materialsModel->findLastOrderIndex($courseId) + 1;

        $transaction = Transaction::run(function () use ($courseId, $materialId, $data, $videoPath, $orderIndex) {
            $this->materialsModel->create([
                'material_id' => $materialId,
                'course_id' => $courseId,
                'title' => $data['title'],
                'type' => 'video',
                'order_index' => $orderIndex
            ]);

            $this->materialVideosModel->create([
                'material_id' => $materialId,
                'source_type' => $data['video_source'],
                'video_url' => $videoPath
            ]);

            $this->createQuiz($materialId, $data);
        });

        if (!$transaction && $data['video_source'] === 'video') {
            File::delete(UPLOAD_PATH . '/material-videos/' . $videoPath);

            $this->failIf(
                true,
                "/create/video/$courseId",
                [
                    'error' => 'Gagal membuat material.'
                ]
            );
        }

        Flash::set(
            'success',
            'Material video berhasil ditambahkan.'
        );

        Redirect::to("/teacher/course/$courseId");
    }

    private function validateQuiz(array $questions, array $settings): void {
        Validator::validate(
            $settings,
            [
                'minimum_correct' => 'required',
                'max_attempts' => 'required',
                'timer' => 'required',
                'reset_minutes' => 'required'
            ],
            [
                'minimum_correct' => 'Minimum correct to pass',
                'max_attempts' => 'Maximum attempts',
                'timer' => 'Timer',
                'reset_minutes' => 'Reset after'
            ]
        );

        Validator::check(
            (int) $settings['minimum_correct'] > count($questions),
            'minimum_correct',
            'Tidak boleh melebihi total questions.'
        );

        Validator::check(
            count($questions) < 5,
            'quizerr',
            'Minimal 5 pertanyaan.'
        );

        foreach ($questions as $i => $question) {
            Validator::check(
                trim($question['question'] ?? '') === '',
                "quiz.$i.question",
                'Pertanyaan wajib diisi.'
            );

            $options = $question['options'] ?? [];

            for ($option = 1; $option <= 4; $option++) {
                Validator::check(
                    trim($options[$option] ?? '') === '',
                    "quiz.$i.options.$option",
                    'Opsi wajib diisi.'
                );
            }

            Validator::check(
                empty($question['correct']),
                "quiz.$i.correct",
                'Pilih salah satu jawaban yang benar.'
            );
        }
    }

    private function createQuiz(string $materialId, array $data): void {
        $quizId = $this->quizzesModel->create([
            'material_id' => $materialId,
            'minimum_correct' => $data['minimum_correct'],
            'total_questions' => count($data['quiz']),
            'max_attempts' => $data['max_attempts'],
            'reset_minutes' => $data['reset_minutes'],
            'timer' => $data['timer']
        ]);

        $questionOrder = 1;

        foreach ($data['quiz'] as $question) {
            $questionId = $this->quizQuestionsModel->create([
                'quiz_id' => $quizId,
                'question_order' => $questionOrder,
                'question' => trim($question['question'])
            ]);

            foreach ($question['options'] as $optionOrder => $optionText) {
                $this->quizOptionsModel->create([
                    'question_id' => $questionId,
                    'option_order' => $optionOrder,
                    'option_text' => trim($optionText),
                    'is_correct' => $optionOrder == $question['correct']
                ]);
            }

            $questionOrder++;
        }
    }

    
}