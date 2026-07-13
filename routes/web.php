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
Routes::get('/teacher/create/course', [TeacherController::class, 'createCourse']);

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/

Routes::post('/api/course/save', [UserController::class, 'saveCourse']);
Routes::get('/api/questions/{quiz_id}', [LearningController::class, 'getQuestions']);