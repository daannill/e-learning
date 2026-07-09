<?php

namespace App\Controllers;

use App\Models\CoursesModel;
use App\Models\EnrollmentsModel;
use App\Models\MaterialsModel;
use App\Models\MaterialTextsModel;
use App\Models\MaterialVideosModel;
use App\Models\QuizQuestionsModel;
use App\Models\QuizzesModel;
use App\Models\UserProgressModel;
use App\Models\UserQuizAnswersModel;
use App\Models\UserQuizAttemptsModel;

use Core\Abort;
use Core\Controller;
use Core\Request;
use Core\Auth;
use Core\Redirect;
use Core\Flash;
use Core\Response;
use Core\Transaction;
use Core\Str;

class LearningController extends Controller {

    private $coursesModel;
    private $materialsModel;
    private $enrollmentsModel;
    private $materialVideosModel;
    private $quizzesModel;
    private $quizQuestionsModel;
    private $userProgressModel;
    private $userQuizAttemptsModel;
    private $userQuizAnswersModel;
    private $materialTextsModel;

    protected $middleware = [

    ];

    public function __construct() {
        $this->coursesModel = new CoursesModel();
        $this->materialsModel = new MaterialsModel();
        $this->enrollmentsModel = new EnrollmentsModel();
        $this->materialVideosModel = new MaterialVideosModel();
        $this->quizzesModel = new QuizzesModel();
        $this->quizQuestionsModel = new QuizQuestionsModel();
        $this->userProgressModel = new UserProgressModel();
        $this->userQuizAttemptsModel = new UserQuizAttemptsModel();
        $this->userQuizAnswersModel = new UserQuizAnswersModel();
        $this->materialTextsModel = new MaterialTextsModel();
    }
    
    /*
    |--------------------------------------------------------------------------
    | Course
    |--------------------------------------------------------------------------
    */

    public function course(array $params) {
        $this->getCourseOrFail($params['course_id']);

        $course = $this->coursesModel->findPublishedCourseDetail($params['course_id']);

        $isEnroll = false;

        if (Auth::auth()) {
            $isEnroll = $this->enrollmentsModel->isEnrolled(
                Auth::info('id'),
                $params['course_id']
            );
        }

        $materials = $this->materialsModel->getAllMaterials($params['course_id']);

        $this->view('user/course', [
            'course' => $course,
            'materials' => $materials,
            'isEnroll' => $isEnroll,
        ]);
    }

    public function resume(array $params) {
        $courseId = $params['course_id'];

        $this->getCourseOrFail($courseId);

        $enrollment = $this->getEnrollmentOrRedirect($courseId);

        $materialId = $enrollment['last_material'];

        Redirect::to("/material/$materialId");
    }

    public function enroll() {
        $courseId = Request::post('course_id');

        $this->getCourseOrFail($courseId);

        $userId = Auth::info('id');

        $isEnroll = $this->enrollmentsModel->isEnrolled($userId, $courseId);

        if ($isEnroll) {
            Flash::set('error', 'Already Enrolled');

            $this->resume(['course_id' => $courseId]);
        }

        $firstMaterial = $this->materialsModel->findMaterialIdByCourseIdAndOrderIndex($courseId, 1);

        $transaction = Transaction::run(function () use ($userId, $courseId, $firstMaterial) {
            $enrollmentId = Str::enrollmentId();

            $this->enrollmentsModel->create([
                'enrollment_id' => $enrollmentId,
                'user_id' => $userId,
                'course_id' => $courseId,
                'last_material' => $firstMaterial,
            ]);

            $this->coursesModel->incrementTotalStudents($courseId);

            $this->userProgressModel->create([
                'enrollment_id' => $enrollmentId,
                'material_id' => $firstMaterial,
            ]);
        });

        $this->redirectIf(
            !$transaction,
            "/course/$courseId",
            "Something Went Wrong
        ");

        Flash::set('success', 'Successfully enrolled in course.');

        $this->resume(['course_id' => $courseId]);
    }

    /*
    |--------------------------------------------------------------------------
    | Material
    |--------------------------------------------------------------------------
    */

    public function material(array $params) {
        $material = $this->getMaterialOrFail($params['material_id']);

        $enrollment = $this->getEnrollmentOrRedirect($material['course_id']);

        $this->validateMaterialAccess($material, $enrollment);

        $this->userProgressModel->updateLastOpenedMaterial($enrollment['enrollment_id'], $params['material_id']);

        $learningData = $this->getCourseLearningData($material, $enrollment);

        switch ($material['type']) {
            case 'video':
                $view = 'user/materials/video';

                $data = array_merge(
                    $learningData,
                    [
                        'materialContent' => $this->materialVideosModel->findMaterialVideo($material['material_id']),
                    ]
                );

                break;

            case 'text':
                $view = 'user/materials/text';

                $data = array_merge(
                    $learningData,
                    [
                        'materialContent' => $this->materialTextsModel->findMaterialText($params['material_id']),
                    ]
                );

                break;

            case 'quiz':
                $view = 'user/materials/quiz';

                $quizData = $this->getQuizData($params['material_id'], $enrollment['enrollment_id']);

                $data = array_merge(
                    $learningData,
                    $quizData,
                    [
                        'isQuizMaterial' => true,
                    ]
                );

                break;

            case 'assignment':
                $view = 'user/materials/assignment';

                // $content = $this->materialAssignmentsModel
                //     ->getByMaterialId($materialId);

                break;

            default:
                Abort::error(404);
        }

        $this->view($view, $data);
    }

    /*
    |--------------------------------------------------------------------------
    | Quiz
    |--------------------------------------------------------------------------
    */

    public function quiz(array $params) {
        $material = $this->getMaterialOrFail($params['material_id']);

        $enrollment = $this->getEnrollmentOrRedirect($material['course_id']);

        $this->validateMaterialAccess($material, $enrollment);

        $learningData = $this->getCourseLearningData($material, $enrollment);

        $quizData = $this->getQuizData(
            $params['material_id'],
            $enrollment['enrollment_id']
        );

        $data = array_merge($learningData, $quizData);

        $this->view('user/materials/quiz', $data);
    }

    public function submitQuiz() {
        $data = Request::post();
        
        $quiz = $this->quizzesModel->findQuiz($data['quiz_id']);

        $questions = $this->quizQuestionsModel->getAllQuestionsWithOptions($data['quiz_id']);

        $totalQuestions = $quiz['total_questions'];
        $correctAnswers = 0;

        $selectedOptionId = [];

        foreach ($questions as $question) {
            $answer = $data['answers'][$question['question_id']] ?? null;

            $selectedOptionId[$question['question_id']] = 0;

            foreach ($question['options'] as $option) {
                if (($answer == $option['option_id']) && $option['is_correct']) {
                    $selectedOptionId[$question['question_id']] = 1;

                    $correctAnswers++;
                    break;
                }
            }
        }

        $score = round(($correctAnswers / $totalQuestions) * 100);

        $isPassed = $correctAnswers >= $quiz['minimum_correct'] ? 1 : 0;

        $transaction = Transaction::run(function () use (
            $questions,
            $data,
            $isPassed,
            $selectedOptionId,
            $correctAnswers,
            $score
        ) {
            $attemptId = Str::attemptId();

            $this->userQuizAttemptsModel->create([
                'attempt_id' => $attemptId,
                'user_progress_id' => $data['user_progress_id'],
                'quiz_id' => $data['quiz_id'],
                'correct_answers' => $correctAnswers,
                'score' => $score,
                'is_passed' => $isPassed,
            ]);

            foreach ($questions as $question) {
                $optionId = $data['answers'][$question['question_id']] ?? null;

                $correct = $selectedOptionId[$question['question_id']];

                $this->userQuizAnswersModel->create([
                    'attempt_id' => $attemptId,
                    'question_id' => $question['question_id'],
                    'selected_option_id' => $optionId,
                    'correct' => $correct,
                ]);
            }

            if ($isPassed) {
                $this->userProgressModel->markCompleted($data['enrollment_id']);

                $materialPrevNext = $this->materialsModel
                    ->findPrevAndNextMaterialId(
                        $data['course_id'],
                        $data['material_id']
                    );

                $this->enrollmentsModel->updateLastMaterialAndTotalCompleted(
                    $data['enrollment_id'],
                    $materialPrevNext['next_material_id']
                );

                $this->userProgressModel->create([
                    'enrollment_id' => $data['enrollment_id'],
                    'material_id' => $materialPrevNext['next_material_id'],
                ]);
            }
        });

        $this->redirectIf(
            !$transaction, 
            '/material/' . $data['material_id'] . '/quiz', 
            'Something Went Wrong'
        );

        Redirect::to('/material/' . $data['material_id'] . '/quiz');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function getCourseOrFail(string $courseId) {
        $course = $this->coursesModel->isCoursePublish($courseId);

        $this->abortIf(!$course);
    }

    private function getMaterialOrFail(string $materialId) {
        $material = $this->materialsModel->findMaterial($materialId);

        $this->abortIf(!$material);

        return $material;
    }

    private function getEnrollmentOrRedirect(string $courseId) {
        $enrollment = $this->enrollmentsModel->findEnrollment(Auth::info('id'), $courseId);

        $this->redirectIf(!$enrollment, "/course/$courseId");

        return $enrollment;
    }

    private function validateMaterialAccess(array $material, array $enrollment) {
        $allowedMaterial = $this->materialsModel->findMaterial($enrollment['last_material']);

        $materialId = $enrollment['last_material'];

        $this->redirectIf($material['order_index'] > $allowedMaterial['order_index'], "/material/$materialId");
    }

    private function getMaxMaterialOrder(array $enrollment) {
        $lastMaterial = $this->materialsModel->findMaterial($enrollment['last_material']);

        return $lastMaterial['order_index'] ?? 0;
    }

    private function getCourseLearningData(array $material, array $enrollment) {
        $course = $this->coursesModel->findCourseCategoryAndTotalMaterials(
            $material['course_id']
        );

        $userProgress = $this->userProgressModel->findUserProgress(
            $enrollment['enrollment_id'],
            $material['material_id']
        );

        $materials = $this->materialsModel->getAllMaterials(
            $material['course_id'],
            $enrollment['enrollment_id']
        );

        $progress = $course['total_materials']
            ? round(($enrollment['total_completed'] / $course['total_materials']) * 100)
            : 0;

        $maxMaterialAccess = $this->getMaxMaterialOrder($enrollment);

        $navigation = $this->materialsModel->findPrevAndNextMaterialId(
            $enrollment['course_id'],
            $material['material_id']
        );

        return [
            // Enrollment
            'enrollmentId' => $enrollment['enrollment_id'],
            'userProgress' => $userProgress,

            // Course
            'course' => $course,
            'courseId' => $material['course_id'],
            'materials' => $materials,
            'progress' => $progress,

            // Navigation
            'currentMaterial' => $material['material_id'],
            'maxMaterialAccess' => $maxMaterialAccess,
            'prevMaterial' => $navigation['previous_material_id'],
            'nextMaterial' => $navigation['next_material_id'],

            // Current Material
            'materialInfo' => [
                'material_id' => $material['material_id'],
                'title' => $material['title'],
                'type' => $material['type'],
                'order_index' => $material['order_index'],
            ],
        ];
    }

    public function getQuestions(array $params) {
        $questions = $this->quizQuestionsModel->getAllQuestionsWithOptions(
            $params['quiz_id']
        );

        Response::json($questions);
    }

    private function getQuizData(string $materialId, string $enrollmentId) {
        $quizInfo = $this->quizzesModel->getQuizUser(
            $materialId,
            $enrollmentId
        );

        $userQuizProgress = $this->userQuizAttemptsModel->getQuizAttemptsProgress(
            $enrollmentId,
            $materialId
        );

        $userQuizProgress = array_merge(
            [
                'score' => null,
                'best_score' => null,
                'is_passed' => false,
            ],
            is_array($userQuizProgress) ? $userQuizProgress : []
        );

        return [
            'quizInfo' => $quizInfo,
            'userQuizProgress' => $userQuizProgress,
        ];
    }
}