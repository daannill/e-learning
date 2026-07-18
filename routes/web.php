<?php

use Core\Routes;

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\LearningController;
use App\Controllers\TeacherController;
use App\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Routes::get('/', [HomeController::class, 'index']);
Routes::get('/dashboard', [HomeController::class, 'index']);
Routes::get('/teachers', [HomeController::class, 'teachers']);
Routes::get('/courses', [HomeController::class, 'courses']);

/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
*/

Routes::get('/wishlist', [UserController::class, 'wishlist']);
Routes::get('/mylearning', [UserController::class, 'myLearning']);
Routes::get('/accomplishments', [UserController::class, 'accomplishments']);
Routes::get('/myprofile', [UserController::class, 'myProfile']);

/*
|--------------------------------------------------------------------------
| Learning
|--------------------------------------------------------------------------
*/

Routes::get('/course/{course_id}', [LearningController::class, 'course']);
Routes::get('/course/resume/{course_id}', [LearningController::class, 'resume']);
Routes::post('/course/enroll', [LearningController::class, 'enroll']);

Routes::get('/material/{material_id}', [LearningController::class, 'material']);
Routes::get('/material/{material_id}/quiz', [LearningController::class, 'quiz']);

Routes::post('/quiz/submit', [LearningController::class, 'submitQuiz']);

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Routes::get('/login', [AuthController::class, 'login']);
Routes::post('/login', [AuthController::class, 'authenticate']);

Routes::get('/register', [AuthController::class, 'register']);
Routes::post('/register', [AuthController::class, 'store']);

Routes::get('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Teacher Pages
|--------------------------------------------------------------------------
*/

Routes::get('/teacher/dashboard', [TeacherController::class, 'dashboard']);
Routes::get('/teacher/courses', [TeacherController::class, 'courses']);
Routes::get('/teacher/course/{course_id}', [TeacherController::class, 'course']);
Routes::get('/teacher/archived', [TeacherController::class, 'archived']);
Routes::get('/teacher/archived/{course_id}', [TeacherController::class, 'archivedCourse']);
Routes::get('/teacher/students', [TeacherController::class, 'students']);
Routes::get('/teacher/ratings', [TeacherController::class, 'ratings']);
Routes::get('/teacher/quiz-score', [TeacherController::class, 'quizAttempts']);
Routes::get('/teacher/assignments', [TeacherController::class, 'assignments']);

Routes::get('/create/course', [TeacherController::class, 'createCourse']);
Routes::post('/create/course', [TeacherController::class, 'storeCourse']);

Routes::get('/edit/course/{course_id}', [TeacherController::class, 'editCourse']);
Routes::post('/edit/course/{course_id}', [TeacherController::class, 'editStoreCourse']);

Routes::get('/create/video/{course_id}', [TeacherController::class, 'createVideo']);
Routes::post('/create/video/{course_id}', [TeacherController::class, 'storeVideo']);
Routes::get('/edit/video/{material_id}', [TeacherController::class, 'editVideo']);
Routes::post('/edit/video/{material_id}', [TeacherController::class, 'editStoreVideo']);
Routes::get('/view/video/{material_id}', [TeacherController::class, 'viewVideo']);

Routes::get('/create/text/{course_id}', [TeacherController::class, 'createText']);
Routes::get('/edit/text/{material_id}', [TeacherController::class, 'editText']);
Routes::get('/view/text/{material_id}', [TeacherController::class, 'viewText']);
Routes::post('/create/text/{course_id}', [TeacherController::class, 'storeText']);
Routes::post('/edit/text/{material_id}', [TeacherController::class, 'editStoreText']);

Routes::get('/create/quiz/{course_id}', [TeacherController::class, 'createQuiz']);
Routes::get('/edit/quiz/{material_id}', [TeacherController::class, 'editQuiz']);
Routes::get('/view/quiz/{material_id}', [TeacherController::class, 'viewQuiz']);
Routes::post('/create/quiz/{course_id}', [TeacherController::class, 'storeQuiz']);
Routes::post('/edit/quiz/{material_id}', [TeacherController::class, 'editStoreQuiz']);

Routes::get('/create/assignment/{course_id}', [TeacherController::class, 'createAssignment']);
Routes::get('/edit/assignment/{material_id}', [TeacherController::class, 'editAssignment']);
Routes::get('/view/assignment/{material_id}', [TeacherController::class, 'viewAssignment']);
Routes::post('/create/assignment/{course_id}', [TeacherController::class, 'storeAssignment']);
Routes::post('/edit/assignment/{material_id}', [TeacherController::class, 'editStoreAssignment']);

Routes::post('delete/material/{material_id}', [TeacherController::class, 'deleteMaterial']);

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/

Routes::post('/api/course/save', [UserController::class, 'saveCourse']);
Routes::get('/api/questions/{quiz_id}', [LearningController::class, 'getQuestions']);