<?php

namespace App\Controllers;

use App\Models\CategoriesModel;
use App\Models\CoursesModel;

use Core\Controller;
use Core\Auth;
use Core\Request;

class HomeController extends Controller {

    private $coursesModel;
    private $categoriesModel;

    public function __construct() {
        $this->coursesModel = new CoursesModel();
        $this->categoriesModel = new CategoriesModel();
    }

    public function index() {
        $this->view('user/home');
    }

    public function courses() {
        $userId = Auth::auth()
            ? Auth::info('id')
            : null;

        $category = Request::get('category', 'all');
        $sort = Request::get('sort', 'newest');
        $search = trim(Request::get('search', ''));

        $categories = $this->categoriesModel->getAllCategories();

        $limit = 8;

        $totalCourses = $this->coursesModel->countPublishedCourses(
            $category,
            $search
        );

        $pagination = $this->paginate(
            $totalCourses,
            $limit
        );

        $courses = $this->coursesModel->getAllPublishedCourses(
            $userId,
            $category,
            $sort,
            $search,
            $pagination['limit'],
            $pagination['offset']
        );

        $this->view(
            'user/courses',
            [
                'courses' => $courses,
                'totalCourses' => $totalCourses,
                'totalPages' => $pagination['total_pages'],
                'page' => $pagination['page'],
                'limit' => $pagination['limit'],
                'category' => $category,
                'sort' => $sort,
                'search' => $search,
                'categories' => $categories
            ]
        );
    }

    public function teachers() {
        $this->view('user/teachers');
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