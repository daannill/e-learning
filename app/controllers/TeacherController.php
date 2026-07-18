<?php

namespace App\Controllers;

use App\Models\AssignmentAttemptsModel;
use App\Models\AssignmentsModel;
use App\Models\CategoriesModel;
use App\Models\CoursesModel;
use App\Models\EnrollmentsModel;
use App\Models\MaterialsModel;
use App\Models\MaterialTextsModel;
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
    private $materialTextsModel;
    private $assignmentsModel;

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
        $this->materialTextsModel = new MaterialTextsModel();
        $this->assignmentsModel = new AssignmentsModel();
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

    public function archived() {
        $teacherId = Auth::info('id');

        $sort = Request::get('sort', 'newest');
        $search = trim(Request::get('search', ''));
        $limit = 8;

        $courseModel = $this->coursesModel;

        $totalCourses = $courseModel->countTeacherArchivedCourses(
            $teacherId,
            $search
        );

        $pagination = $this->paginate(
            $totalCourses,
            $limit
        );

        $courses = $courseModel->getTeacherArchivedCourses(
            $teacherId,
            $sort,
            $search,
            $pagination['limit'],
            $pagination['offset']
        );

        $data = [
            'courses' => $courses,
            'totalCourses' => $totalCourses,
            'totalPages' => $pagination['total_pages'],
            'page' => $pagination['page'],
            'limit' => $pagination['limit'],
            'sort' => $sort,
            'search' => $search
        ];

        $this->view(
            'teacher/archived_course',
            $data
        );
    }

    public function createCourse() {
        $categories = $this->categoriesModel->getAllCategories();

        $data = [
            'isEdit' => false,
            'formAction' => '/create/course',
            'categories' => $categories,
            'cancelUrl' => '/teacher/courses'
        ];

        $this->view('teacher/create_course', $data);
    }

    public function editCourse(array $params) {
        $courseId = $params['course_id'];

        $course = $this->coursesModel->findCourseForEdit($courseId);
        $categories = $this->categoriesModel->getAllCategories();

        $data = [
            'isEdit' => true,
            'formAction' => '/edit/course/' . $courseId,
            'categories' => $categories,
            'cancelUrl' => '/course/' . $courseId,
            'course' => $course
        ];

        $this->view('teacher/create_course', $data);
    }

    public function storeCourse() {
        $data = Request::post();
        $teacherId = Auth::info('id');

        $this->validateCourse($data);

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

    public function editStoreCourse(array $params) {
        $courseId = $params['course_id'];
        $data = Request::post();
        $course = $this->coursesModel->findCourseForEdit($courseId);

        $this->validateCourse($data);

        $this->failIf(
            Validator::fails(),
            "/edit/course/$courseId",
            Validator::errors()
        );

        $thumbnail = $this->resolveThumbnail(
            $course,
            $courseId
        );

        $transaction = Transaction::run(function () use ($courseId, $data, $thumbnail) {

            $this->coursesModel->updateCourse(
                $courseId,
                [
                    'title' => $data['title'],
                    'category' => $data['category'],
                    'difficulty' => $data['difficulty'],
                    'short_description' => $data['short_description'],
                    'description' => $data['description'],
                    'thumbnail' => $thumbnail
                ]
            );
        });

        if (!$transaction) {

            $this->rollbackThumbnail($thumbnail);

            $this->failIf(
                true,
                "/edit/course/$courseId",
                'Something went wrong'
            );
        }

        $this->updateThumbnail(
            $course,
            $thumbnail
        );

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

    public function viewVideo(array $params) {
        $materialId = $params['material_id'];

        $material = $this->materialsModel->findMaterial($materialId);
        $course = $this->coursesModel->findCourse($material['course_id']);
        $materialVideo = $this->materialVideosModel->findMaterialVideo($materialId);

        $quiz = $this->getQuiz($materialId);

        $materialFull = [
            'title'           => $material['title'],
            'video_source'    => $materialVideo['source_type'],
            'video_url'       => $materialVideo['source_type'] == 'youtube' ? $materialVideo['video_url'] : null,
            'video_path'      => $materialVideo['source_type'] == 'video' ? $materialVideo['video_url'] : null,
            'video_filename'  => $materialVideo['source_type'] == 'video' ? $materialVideo['video_url'] : null,
            'material_id' => $materialId
        ];

        $data = [
            'course' => [
                'course_id' => $course['course_id'],
                'title' => $course['course_name'],
                'status' => $course['status']
            ],
            'material' => $materialFull,
            'settings' => $quiz['settings'],
            'questions' => $quiz['questions']
        ];

        $this->view('generic/video_view', $data);
    }

    public function editVideo(array $params) {
        $materialId = $params['material_id'];

        $material = $this->materialsModel->findMaterial($materialId);
        $course = $this->coursesModel->findCourse($material['course_id']);
        $materialVideo = $this->materialVideosModel->findMaterialVideo($materialId);

        $quiz = $this->getQuiz($materialId);

        $materialFull = [
            'title'           => $material['title'],
            'video_source'    => $materialVideo['source_type'],
            'video_url'       => $materialVideo['source_type'] == 'youtube' ? $materialVideo['video_url'] : null,
            'video_path'      => $materialVideo['source_type'] == 'video' ? $materialVideo['video_url'] : null,
            'video_filename'  => $materialVideo['source_type'] == 'video' ? $materialVideo['video_url'] : null
        ];

        $data = [
            'isEdit' => true,
            'formAction' => '/edit/video/' . $materialId,
            'course' => [
                'course_id' => $course['course_id'],
                'title' => $course['course_name']
            ],
            'material' => $materialFull,
            'settings' => $quiz['settings'],
            'oldQuestions' => $quiz['questions']
        ];

        $this->view('teacher/materials/material_video', $data);
    }

    public function storeVideo(array $params) {
        $courseId = $params['course_id'];
        $data = Request::post();
        $files = Request::file();

        $this->validateVideo($data, $files);

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

        $videoFile = $this->uploadVideo($materialId);

        $videoPath = $data['video_url'] ?: $videoFile;

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

            $this->saveQuiz($materialId, $data);
        });

        if (!$transaction) {
            if ($data['video_source'] === 'video') {
                $this->rollbackUploadedVideo($videoPath);
            }

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

    public function editStoreVideo(array $params) {
        $materialId = $params['material_id'];
        $data = Request::post();
        $files = Request::file();

        $material = $this->materialsModel->findMaterial($materialId);
        $materialVideo = $this->materialVideosModel->findMaterialVideo($materialId);

        $this->validateVideo($data, $files, true);

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
            "/edit/video/$materialId",
            Validator::errors()
        );

        $videoPath = $this->resolveVideoPath($materialVideo, $data, $materialId);

        $transaction = Transaction::run(function () use ($materialId, $data, $videoPath) {
            $this->materialsModel->updateMaterial(
                $materialId,
                [
                    'title' => $data['title']
                ]
            );

            $this->materialVideosModel->updateMaterialVideo(
                $materialId, 
                [
                    'source_type' => $data['video_source'],
                    'video_url' => $videoPath
                ]
            );

            $this->updateQuiz($materialId, $data);
        });

        if (!$transaction) {
            if ($data['video_source'] === 'video' && Request::hasFile('video_file')) {
                $this->rollbackUploadedVideo('edit' . $videoPath);
            }

            $this->failIf(
                true,
                "/edit/video/$materialId",
                [
                    'error' => 'Gagal membuat material.'
                ]
            );
        }

        $this->updateVideo(
            $materialVideo,
            $data,
            $videoPath
        );

        Flash::set(
            'success',
            'Material video berhasil ditambahkan.'
        );

        Redirect::to("/teacher/course/" . $material['course_id']);
    }

    public function createText(array $params) {
        $courseId = $params['course_id'];

        $course = $this->coursesModel->findCourse($courseId);

        $data = [
            'isEdit' => false,
            'course' => [
                'course_id' => $courseId,
                'title' => $course['course_name']
            ],
            'formAction' => '/create/text/' . $course['course_id']
        ];

        $this->view('teacher/materials/material_text', $data);
    }

    public function storeText(array $params) {
        $courseId = $params['course_id'];
        $data = Request::post();

        $this->validateText($data);

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
            "/create/text/$courseId",
            Validator::errors()
        );

        $materialId = Str::materialId();

        $orderIndex = $this->materialsModel->findLastOrderIndex($courseId) + 1;

        $transaction = Transaction::run(function () use ($courseId, $materialId, $data, $orderIndex) {

            $this->materialsModel->create([
                'material_id' => $materialId,
                'course_id' => $courseId,
                'title' => $data['title'],
                'type' => 'text',
                'order_index' => $orderIndex
            ]);

            $this->materialTextsModel->create([
                'material_id' => $materialId,
                'content' => $data['content']
            ]);

            $this->saveQuiz($materialId, $data);
        });

        $this->failIf(
            !$transaction,
            "/create/text/$courseId",
            [
                'error' => 'Gagal membuat material.'
            ]
        );

        Flash::set(
            'success',
            'Material text berhasil ditambahkan.'
        );

        Redirect::to("/teacher/course/$courseId");
    }

    public function editStoreText(array $params) {
        $materialId = $params['material_id'];
        $data = Request::post();
        $material = $this->materialsModel->findMaterial($materialId);

        $this->validateText($data);

        $this->validateQuiz(
            $data['quiz'] ?? [],
            [
                'minimum_correct' => $data['minimum_correct'],
                'max_attempts'    => $data['max_attempts'],
                'timer'           => $data['timer'],
                'reset_minutes'   => $data['reset_minutes']
            ]
        );

        $this->failIf(
            Validator::fails(),
            "/edit/text/$materialId",
            Validator::errors()
        );

        $transaction = Transaction::run(function () use ($materialId, $data) {

            $this->materialsModel->updateMaterial(
                $materialId,
                [
                    'title' => $data['title']
                ]
            );

            $this->materialTextsModel->updateMaterialText(
                $materialId,
                [
                    'content' => $data['content']
                ]
            );

            $this->updateQuiz($materialId, $data);
        });

        $this->failIf(
            !$transaction,
            "/edit/text/$materialId",
            [
                'error' => 'Gagal memperbarui material.'
            ]
        );

        Flash::set(
            'success',
            'Material text berhasil diperbarui.'
        );

        Redirect::to("/teacher/course/" . $material['course_id']);
    }

    public function editText(array $params) {
        $materialId = $params['material_id'];

        $material = $this->materialsModel->findMaterial($materialId);
        $course = $this->coursesModel->findCourse($material['course_id']);
        $materialText = $this->materialTextsModel->findMaterialText($materialId);

        $quiz = $this->getQuiz($materialId);

        $data = [
            'isEdit' => true,
            'course' => [
                'course_id' => $material['course_id'],
                'title' => $course['course_name']
            ],
            'material' => [
                'title' => $material['title'],
                'content' => $materialText['content']
            ],
            'oldQuestions' => $quiz['questions'],
            'settings' => $quiz['settings'],
            'formAction' => '/edit/text/' . $materialId
        ];

        $this->view('teacher/materials/material_text', $data);
    }

    public function viewText(array $params) {
        $materialId = $params['material_id'];

        $material = $this->materialsModel->findMaterial($materialId);
        $course = $this->coursesModel->findCourse($material['course_id']);
        $materialText = $this->materialTextsModel->findMaterialText($materialId);

        $quiz = $this->getQuiz($materialId);

        $data = [
            'course' => [
                'course_id' => $course['course_id'],
                'title' => $course['course_name'],
                'status' => $course['status']
            ],
            'material' => [
                'material_id' => $materialId,
                'title' => $material['title'],
                'content' => $materialText['content']
            ],
            'settings' => $quiz['settings'],
            'questions' => $quiz['questions']
        ];

        $this->view('generic/text_view', $data);
    }

    public function createQuiz(array $params) {
        $courseId = $params['course_id'];

        $course = $this->coursesModel->findCourse($courseId);

        $data = [
            'isEdit' => false,
            'course' => [
                'course_id' => $courseId,
                'title' => $course['course_name']
            ],
            'formAction' => '/create/quiz/' . $course['course_id']
        ];
        
        $this->view('teacher/materials/quiz', $data);
    }

    public function storeQuiz(array $params) {
        $courseId = $params['course_id'];
        $data = Request::post();

        Validator::validate(
            $data,
            [
                'title' => 'required|max:120'
            ],
            [
                'title' => 'Quiz title'
            ]
        );

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
            "/create/quiz/$courseId",
            Validator::errors()
        );

        $materialId = Str::materialId();

        $orderIndex = $this->materialsModel->findLastOrderIndex($courseId) + 1;

        $transaction = Transaction::run(function () use ($courseId, $materialId, $data, $orderIndex) {

            $this->materialsModel->create([
                'material_id' => $materialId,
                'course_id' => $courseId,
                'title' => $data['title'],
                'type' => 'quiz',
                'order_index' => $orderIndex
            ]);

            $this->saveQuiz($materialId, $data);
        });

        $this->failIf(
            !$transaction,
            "/create/quiz/$courseId",
            [
                'error' => 'Gagal membuat material.'
            ]
        );

        Flash::set(
            'success',
            'Material text berhasil ditambahkan.'
        );

        Redirect::to("/teacher/course/$courseId");
    }

    public function editQuiz(array $params) {
        $materialId = $params['material_id'];

        $material = $this->materialsModel->findMaterial($materialId);
        $course = $this->coursesModel->findCourse($material['course_id']);

        $quiz = $this->getQuiz($materialId);

        $data = [
            'isEdit' => true,
            'course' => [
                'course_id' => $material['course_id'],
                'title' => $course['course_name']
            ],
            'material' => [
                'title' => $material['title']
            ],
            'oldQuestions' => $quiz['questions'],
            'settings' => $quiz['settings'],
            'formAction' => '/edit/quiz/' . $materialId
        ];

        $this->view('teacher/materials/quiz', $data);
    }

    public function editStoreQuiz(array $params) {
        $materialId = $params['material_id'];

        $data = Request::post();

        $material = $this->materialsModel->findMaterial($materialId);

        $this->validateQuiz(
            $data['quiz'] ?? [],
            [
                'minimum_correct' => $data['minimum_correct'],
                'max_attempts'    => $data['max_attempts'],
                'timer'           => $data['timer'],
                'reset_minutes'   => $data['reset_minutes']
            ]
        );

        $this->failIf(
            Validator::fails(),
            "/edit/quiz/$materialId",
            Validator::errors()
        );

        $transaction = Transaction::run(function () use ($materialId, $data) {

            $this->materialsModel->updateMaterial(
                $materialId,
                [
                    'title' => $data['title']
                ]
            );

            $this->updateQuiz(
                $materialId,
                $data
            );
        });

        $this->failIf(
            !$transaction,
            "/edit/quiz/$materialId",
            [
                'error' => 'Gagal memperbarui quiz.'
            ]
        );

        Flash::set(
            'success',
            'Quiz berhasil diperbarui.'
        );

        Redirect::to(
            "/teacher/course/{$material['course_id']}"
        );
    }

    public function viewQuiz(array $params) {
        $materialId = $params['material_id'];

        $material = $this->materialsModel->findMaterial($materialId);
        $course = $this->coursesModel->findCourse($material['course_id']);

        $quiz = $this->getQuiz($materialId);

        $data = [
            'course' => [
                'course_id' => $course['course_id'],
                'title' => $course['course_name'],
                'status' => $course['status']
            ],
            'material' => [
                'material_id' => $materialId,
                'title' => $material['title']
            ],
            'settings' => $quiz['settings'],
            'questions' => $quiz['questions']
        ];

        $this->view('generic/quiz_view', $data);
    }

    public function createAssignment(array $params) {
        $courseId = $params['course_id'];

        $course = $this->coursesModel->findCourse($courseId);

        $data = [
            'isEdit' => false,
            'course' => [
                'course_id' => $courseId,
                'title' => $course['course_name']
            ],
            'formAction' => '/create/assignment/' . $course['course_id']
        ];

        $this->view('teacher/materials/assignment', $data);
    }

    public function storeAssignment(array $params) {
        $courseId = $params['course_id'];
        $data = Request::post();

        $this->validateAssignment($data);
        $this->validateText($data);

        $this->failIf(
            Validator::fails(),
            "/create/assignment/$courseId",
            Validator::errors()
        );

        $materialId = Str::materialId();

        $orderIndex = $this->materialsModel->findLastOrderIndex($courseId) + 1;

        $transaction = Transaction::run(function () use ($courseId, $materialId, $data, $orderIndex) {
            $this->materialsModel->create([
                'material_id' => $materialId,
                'course_id' => $courseId,
                'title' => $data['title'],
                'type' => 'assignment',
                'order_index' => $orderIndex
            ]);
            
            $this->assignmentsModel->create([
                'material_id'    => $materialId,
                'description'    => $data['content'],
                'passing_score'  => $data['passing_score'],
                'deadline_at'    => $data['deadline_at']
            ]);
        });

        $this->failIf(
            !$transaction,
            "/create/assignment/$courseId",
            [
                'error' => 'Gagal membuat material.'
            ]
        );

        Flash::set(
            'success',
            'Material text berhasil ditambahkan.'
        );

        Redirect::to("/teacher/course/$courseId");
    }

    public function editAssignment(array $params) {
        $materialId = $params['material_id'];

        $material = $this->materialsModel->findMaterial($materialId);
        $course = $this->coursesModel->findCourse($material['course_id']);
        $assignment = $this->assignmentsModel->findAssignment($materialId);

        $data = [
            'isEdit' => true,
            'course' => [
                'course_id' => $material['course_id'],
                'title' => $course['course_name']
            ],
            'material' => [
                'title' => $material['title'],
                'content' => $assignment['description'],
                'passing_score' => $assignment['passing_score'],
                'deadline_at' => $assignment['deadline_at']
            ],
            'formAction' => '/edit/assignment/' . $materialId
        ];

        $this->view('teacher/materials/assignment', $data);
    }

    public function editStoreAssignment(array $params) {
        $materialId = $params['material_id'];

        $data = Request::post();

        $material = $this->materialsModel->findMaterial($materialId);

        $this->validateText($data);
        $this->validateAssignment($data);

        $this->failIf(
            Validator::fails(),
            "/edit/assignment/$materialId",
            Validator::errors()
        );

        $transaction = Transaction::run(function () use ($materialId, $data) {

            $this->materialsModel->updateMaterial(
                $materialId,
                [
                    'title' => $data['title']
                ]
            );

            $this->assignmentsModel->updateAssignment(
                $materialId,
                [
                    'description'   => $data['content'],
                    'passing_score' => $data['passing_score'],
                    'deadline_at'   => $data['deadline_at']
                ]
            );
        });

        $this->failIf(
            !$transaction,
            "/edit/assignment/$materialId",
            [
                'error' => 'Gagal memperbarui assignment.'
            ]
        );

        Flash::set(
            'success',
            'Assignment berhasil diperbarui.'
        );

        Redirect::to(
            "/teacher/course/{$material['course_id']}"
        );
    }

    public function viewAssignment(array $params) {
        $materialId = $params['material_id'];

        $material = $this->materialsModel->findMaterial($materialId);
        $course = $this->coursesModel->findCourse($material['course_id']);
        $assignment = $this->assignmentsModel->findAssignment($materialId);

        $data = [
            'course' => [
                'course_id' => $course['course_id'],
                'title' => $course['course_name'],
                'status' => $course['status']
            ],
            'material' => [
                'material_id' => $materialId,
                'title' => $material['title'],
                'content' => $assignment['description'],
                'passing_score'  => $assignment['passing_score'],
                'deadline_at'    => $assignment['deadline_at']
            ]
        ];

        $this->view('generic/assignment_view', $data);
    }

    public function deleteMaterial(array $params) {
        $materialId = $params['material_id'];
        $material = $this->materialsModel->findMaterial($materialId);

        $transaction = Transaction::run(function () use ($materialId, $material) {
            switch ($material['type']) {

                case 'video':
                    $this->deleteVideo($materialId);
                    $this->deleteQuiz($materialId);
                    break;

                case 'text':
                    $this->materialTextsModel->deleteMaterialText($materialId);
                    $this->deleteQuiz($materialId);
                    break;

                case 'quiz':
                    $this->deleteQuiz($materialId);
                    break;

                case 'assignment':
                    $this->assignmentsModel->deleteAssignment($materialId);
                    break;
            }

            $this->materialsModel->deleteMaterial($materialId);

            $this->materialsModel->reorderMaterial(
                $material['course_id'],
                $material['order_index']
            );
        });

        $this->failIf(
            !$transaction,
            "/teacher/course/{$material['course_id']}",
            [
                'error' => 'Gagal Menghapus material.'
            ]
        );

        Flash::set(
            'success',
            'Material berhasil dihapus.'
        );

        Redirect::to(
            "/teacher/course/{$material['course_id']}"
        );
    }

    private function validateAssignment(array $data) {
        Validator::validate(
            $data,
            [
                'title' => 'required|max:120'
            ],
            [
                'title' => 'Assignment title'
            ]
        );

        Validator::check(
            !isset($data['passing_score']) || $data['passing_score'] === '',
            'passing_score',
            'Passing score is required.'
        );

        Validator::check(
            (int) $data['passing_score'] < 0 || (int) $data['passing_score'] > 100,
            'passing_score',
            'Passing score must be between 0 and 100.'
        );

        Validator::check(
            !isset($data['deadline_at']) || $data['deadline_at'] === '',
            'deadline_at',
            'Submission duration is required.'
        );

        Validator::check(
            (int) $data['deadline_at'] < 1,
            'deadline_at',
            'Submission duration must be at least 1 hour.'
        );
    }

    private function validateText(array $data): void {
        Validator::validate(
            $data,
            [
                'title' => 'required|max:120'
            ],
            [
                'title' => 'Material title'
            ]
        );

        $content = html_entity_decode($data['content'] ?? '');
        $content = strip_tags($content);
        $content = trim($content);

        Validator::check(
            $content === '',
            'content',
            'Content text wajib diisi.'
        );
    }

    private function rollbackUploadedVideo(?string $videoPath): void {
        if (!$videoPath) {
            return;
        }

        File::delete(
            UPLOAD_PATH . '/material-videos/' . $videoPath
        );
    }

    private function uploadVideo(string $materialId): ?string {
        if (!Request::hasFile('video_file')) {
            return null;
        }

        $videoFile = File::upload(
            Request::file('video_file'),
            UPLOAD_PATH . '/material-videos',
            $materialId
        );

        return $videoFile ?: null;
    }

    private function deleteVideo(string $materialId): void {
        $video = $this->materialVideosModel->findMaterialVideo($materialId);

        if (!$video) {
            return;
        }

        if ($video['source_type'] === 'video') {
            File::delete(
                UPLOAD_PATH . '/material-videos/' . $video['video_url']
            );
        }

        $this->materialVideosModel->deleteMaterialVideo($materialId);
    }

    private function validateVideo(
        array $data,
        array $files,
        bool $isEdit = false
    ): void {

        if ($data['video_source'] === 'youtube') {

            Validator::check(
                empty($data['video_url']),
                'video_url',
                'Video URL wajib diisi.'
            );

            return;
        }

        $rule = $isEdit
            ? 'mimes:mp4,webm|max:200'
            : 'required|mimes:mp4,webm|max:200';

        Validator::validateFile(
            $files,
            [
                'video_file' => $rule
            ],
            [
                'video_file' => 'Video'
            ]
        );
    }

    private function resolveVideoPath(
        array $oldVideo,
        array $data,
        string $materialId
    ): string {

        $oldSource = $oldVideo['source_type'];
        $newSource = $data['video_source'];

        if (
            $newSource === 'video'
            && $oldSource === 'video'
            && !Request::hasFile('video_file')
        ) {
            return $oldVideo['video_url'];
        }
        
        if ($newSource === 'youtube' && $oldSource === 'youtube') {
            return $data['video_url'];
        }

        if ($newSource === 'youtube' && $oldSource === 'video') {

            // File::delete(
            //     UPLOAD_PATH . '/material-videos/' . $oldVideo['video_url']
            // );

            return $data['video_url'];
        }

        if ($newSource === 'video' && $oldSource === 'youtube') {
            $tempVideo = $this->uploadVideo('edit' . $materialId);

            return str_replace('edit', '', $tempVideo);
        }

        if ($newSource === 'video' && $oldSource === 'video') {

            // if (!Request::hasFile('video_file')) {
            //     return $oldVideo['video_url'];
            // }

            // File::delete(
            //     UPLOAD_PATH . '/material-videos/' . $oldVideo['video_url']
            // );

            $tempVideo = $this->uploadVideo('edit' . $materialId);

            return str_replace('edit', '', $tempVideo);

        }

        return $oldVideo['video_url'];
    }

    private function updateVideo(
        array $oldVideo,
        array $data,
        string $videoPath
    ): void {

        if ($data['video_source'] !== 'video') {
            if ($oldVideo['source_type'] === 'video') {
                File::delete(
                    UPLOAD_PATH . '/material-videos/' . $oldVideo['video_url']
                );
            }

            return;
        }

        if (!Request::hasFile('video_file')) {
            return;
        }

        File::delete(
            UPLOAD_PATH . '/material-videos/' . $oldVideo['video_url']
        );

        rename(
            UPLOAD_PATH . '/material-videos/edit' . $videoPath,
            UPLOAD_PATH . '/material-videos/' . $videoPath
        );
    }

    private function getQuiz(string $materialId): array {
        $quiz = $this->quizzesModel->findQuizByMaterialId($materialId);

        return [
            'settings' => [
                'minimum_correct' => $quiz['minimum_correct'],
                'total_questions' => $quiz['total_questions'],
                'max_attempts' => $quiz['max_attempts'],
                'reset_minutes' => $quiz['reset_minutes'],
                'timer' => $quiz['timer'],
            ],
            'questions' => $this->getAllQuestion($quiz['quiz_id'])
        ];
    }

    private function deleteQuiz(string $materialId): void {
        $quiz = $this->quizzesModel->findQuizByMaterialId($materialId);

        if (!$quiz) {
            return;
        }

        $this->quizOptionsModel->deleteByQuizId($quiz['quiz_id']);
        $this->quizQuestionsModel->deleteByQuizId($quiz['quiz_id']);
        $this->quizzesModel->deleteQuiz($quiz['quiz_id']);
    }

    private function getAllQuestion(string $quizId) {
        $questions = $this->quizQuestionsModel->getAllQuestionsWithOptions($quizId);

        $oldQuestions = [];

        foreach ($questions as $index => $question) {
            $oldQuestions[$index + 1] = [
                'question' => $question['question'],
                'options' => [],
                'correct' => null
            ];

            foreach ($question['options'] as $option) {
                $oldQuestions[$index + 1]['options'][$option['option_order']] = $option['option_text'];

                if ($option['is_correct']) {
                    $oldQuestions[$index + 1]['correct'] = $option['option_order'];
                }
            }
        }

        return $oldQuestions;
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

    private function saveQuiz(string $materialId, array $data): void {
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

    private function updateQuiz(string $materialId, array $data): void {
        $quiz = $this->quizzesModel->findQuizByMaterialId($materialId);

        $this->quizzesModel->updateQuiz(
            $quiz['quiz_id'],
            [
                'minimum_correct' => $data['minimum_correct'],
                'total_questions' => count($data['quiz']),
                'max_attempts' => $data['max_attempts'],
                'reset_minutes' => $data['reset_minutes'],
                'timer' => $data['timer']
            ]
        );

        $this->quizOptionsModel->deleteByQuizId($quiz['quiz_id']);
        $this->quizQuestionsModel->deleteByQuizId($quiz['quiz_id']);

        $questionOrder = 1;

        foreach ($data['quiz'] as $question) {

            $questionId = $this->quizQuestionsModel->create([
                'quiz_id' => $quiz['quiz_id'],
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

    private function validateCourse(array $data): void {
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
    }

    private function rollbackThumbnail(
        string $thumbnail
    ): void {

        if (!Request::hasFile('thumbnail')) {
            return;
        }

        File::delete(
            UPLOAD_PATH . '/course-thumbnails/edit' . $thumbnail
        );
    }

    private function updateThumbnail(
        array $course,
        string $thumbnail
    ): void {

        if (!Request::hasFile('thumbnail')) {
            return;
        }

        File::delete(
            UPLOAD_PATH . '/course-thumbnails/' . $course['thumbnail']
        );

        File::rename(
            UPLOAD_PATH . '/course-thumbnails/edit' . $thumbnail,
            UPLOAD_PATH . '/course-thumbnails/' . $thumbnail
        );
    }

    private function resolveThumbnail(
        array $course,
        string $courseId
    ): string {

        if (!Request::hasFile('thumbnail')) {
            return $course['thumbnail'];
        }

        $thumbnail = File::upload(
            Request::file('thumbnail'),
            UPLOAD_PATH . '/course-thumbnails',
            'edit' . $courseId
        );

        $this->failIf(
            !$thumbnail,
            "/edit/course/$courseId",
            [
                'thumbnail' => 'Upload thumbnail gagal.'
            ]
        );

        return str_replace('edit', '', $thumbnail);
    }

    private function paginate(
        int $totalItems,
        int $limit
    ): array {

        $page = max(1, (int) Request::get('page', 1));

        $totalPages = max(
            1,
            (int) ceil($totalItems / $limit)
        );

        $page = min($page, $totalPages);

        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => ($page - 1) * $limit,
            'total_pages' => $totalPages
        ];
    }

    
}