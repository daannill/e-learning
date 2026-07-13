<?php

namespace App\Controllers;

use App\Models\AssignmentAttemptsModel;
use App\Models\CoursesModel;

use App\Models\EnrollmentsModel;
use Core\Auth;
use Core\Controller;
use Core\Request;

class TeacherController extends Controller {
    
    private $coursesModel;
    private $assignmentAttemptsModel;
    private $enrollmentsModel;

    protected array $middleware = [];

    public function __construct() {
        $this->coursesModel = new CoursesModel();
        $this->assignmentAttemptsModel = new AssignmentAttemptsModel();
        $this->enrollmentsModel = new EnrollmentsModel();
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

    
}