<?php

namespace App\Controllers;

use App\Models\CoursesModel;

use Core\Controller;
use Core\Auth;

class HomeController extends Controller {

    private $coursesModel;

    public function __construct() {
        $this->coursesModel = new CoursesModel();
    }

    public function index() {
        $this->view('user/home');
    }

    public function courses() {
        $userId = Auth::auth() ? Auth::info('id') : null;

        $courses = $this->coursesModel->getAllPublishedCourses($userId);

        $this->view('user/courses', [
            'courses' => $courses
        ]);
    }

    public function teachers() {
        $this->view('user/teachers');
    }
}