<?php

namespace App\Controllers;

use App\Models\AssignmentAttemptsModel;
use App\Models\CoursesModel;

use App\Models\EnrollmentsModel;
use Core\Auth;
use Core\Controller;

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
        $teacherId =  
    }

    
}