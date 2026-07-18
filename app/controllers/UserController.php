<?php

namespace App\Controllers;

use App\Models\CategoriesModel;
use App\Models\EnrollmentsModel;
use App\Models\SavedCoursesModel;
use App\Models\UserDetailsModel;
use App\Models\UserProgressModel;

use Core\Auth;
use Core\Controller;
use Core\Request;
use Core\Response;

class UserController extends Controller{

    private $enrollmentsModel;
    private $userDetailsModel;
    private $userProgressModel;
    private $savedCoursesModel;
    private $categoriesModel;

    protected array $middleware = [
        'auth' => [
            'only' => ['myLearning', 'accomplishment', 'myProfile', 'saveCourse']
        ],
    ];

    public function __construct() {
        $this->userDetailsModel = new UserDetailsModel();
        $this->enrollmentsModel = new EnrollmentsModel();
        $this->userProgressModel = new UserProgressModel();
        $this->savedCoursesModel = new SavedCoursesModel();
        $this->categoriesModel = new CategoriesModel();
    }

    /*
    |--------------------------------------------------------------------------
    | Wishlist
    |--------------------------------------------------------------------------
    */
    
    public function wishlist() {
        $userId = Auth::info('id');

        $category = Request::get('category', 'all');
        $sort = Request::get('sort', 'newest');
        $search = trim(Request::get('search', ''));

        $limit = 8;

        $categories = $this->categoriesModel->getAllCategories();

        $totalCourses = $this->savedCoursesModel->countSavedCourses(
            $userId,
            $category,
            $search
        );

        $pagination = $this->paginate(
            $totalCourses,
            $limit
        );

        $courses = $this->savedCoursesModel->getAllSavedCourses(
            $userId,
            $category,
            $sort,
            $search,
            $pagination['limit'],
            $pagination['offset']
        );

        $this->view(
            'user/wishlist',
            [
                'courses' => $courses,
                'totalCourses' => $totalCourses,
                'totalPages' => $pagination['total_pages'],
                'page' => $pagination['page'],
                'limit' => $pagination['limit'],
                'sort' => $sort,
                'search' => $search,
                'categories' => $categories,
                'category' => $category
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | My Learning
    |--------------------------------------------------------------------------
    */

    public function myLearning() {
        $userId = Auth::info('id');

        $stats = $this->enrollmentsModel->getUserStats($userId);
        $lastOpenedCourse = $this->enrollmentsModel->findLastOpenedCourse($userId);

        $userCourses = null;
        
        if ($lastOpenedCourse) {
            $userCourses = $this->enrollmentsModel->getAllEnrolledCoursesExceptCourse($userId, $lastOpenedCourse['course_id']);
        }

        $this->view('user/mylearning', [
            'stats' => $stats,
            'lastOpenedCourse' => $lastOpenedCourse,
            'userCourses' => $userCourses,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Accomplishments
    |--------------------------------------------------------------------------
    */

    public function accomplishments() {
        $this->view('user/accomplishments');
    }

    /*
    |--------------------------------------------------------------------------
    | My Profile
    |--------------------------------------------------------------------------
    */

    public function myProfile() {
        $userId = Auth::info('id');

        $user = $this->userDetailsModel->findUserDetailsById($userId);
        $stats = $this->enrollmentsModel->getUserStats($userId);
        $recentActivity = $this->userProgressModel->getAllRecentActivity($userId);

        $this->view('user/myprofile', [
            'user' => $user,
            'stats' => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | API Save Course
    |--------------------------------------------------------------------------
    */

    public function saveCourse() {
        if (!Auth::auth()) {
            Response::json([
                'success' => false,
                'message' => 'Unauthorized',
                'saved' => false,
            ]);
        }

        $courseId = Request::post('course_id');

        if (!$courseId) {
            Response::json([
                'success' => false,
                'message' => 'Course ID is required',
                'saved' => false,
            ]);
        }

        $userId = Auth::info('id');

        $savedCourse = $this->savedCoursesModel->findSavedCourse($userId, $courseId);

        if ($savedCourse) {
            $this->savedCoursesModel->deleteSavedCourse($userId, $courseId);

            Response::json([
                'success' => true,
                'saved' => false,
            ]);
        }

        $this->savedCoursesModel->create([
            'user_id' => $userId,
            'course_id' => $courseId,
        ]);

        Response::json([
            'success' => true,
            'saved' => true,
        ]);
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