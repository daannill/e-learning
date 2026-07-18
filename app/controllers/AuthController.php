<?php

namespace App\Controllers;

use Core\Controller;
use Core\Flash;
use Core\Redirect;
use Core\Str;
use Core\Transaction;
use Core\Validator;
use Core\Request;
use Core\Session;
use Core\File;

use App\Models\UserDetailsModel;
use App\Models\LoginUsersModel;

class AuthController extends Controller {

    private $loginUsersModel;
    private $userDetailsModel;

    protected array $middleware = [
        'guest' => [
            'except' => ['logout'],
        ],
        'auth' => [
            'only' => ['logout'],
        ],
    ];

    public function __construct() {
        $this->loginUsersModel = new LoginUsersModel();
        $this->userDetailsModel = new UserDetailsModel();
    }

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    public function login() {
        $this->view('auth/login');
    }

    public function authenticate() {
        $data = Request::post();

        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        Validator::validate($data, $rules);
        $this->failIf(Validator::fails(), '/login', Validator::errors());

        $user = $this->loginUsersModel->findUserByEmail($data['email']);

        $password = $user && password_verify($data['password'], $user['password']);
        $this->failIf(!$user || !$password, '/login', 'error', 'Email atau Password salah');

        $name = $this->userDetailsModel->findUserDetailsById($user['user_id'])['full_name'];

        Session::set('auth', [
            'id' => $user['user_id'],
            'name' => $name,
            'email' => $user['email'],
            'role' => $user['role'],
        ]);

        Flash::set('success', "Login Berhasil! Welcome $name");
        Redirect::to('/');
    }

    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */

    public function register() {
        $this->view('auth/register');
    }

    public function store() {
        $data = Request::post();

        $rules = [
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'gender' => 'required',
            'password1' => 'min:6',
            'password2' => 'min:6'
        ];

        $label = [
            'fname' => 'First name',
            'lname' => 'Last name',
            'password1' => 'Password',
            'password2' => 'Confirm password'
        ];

        Validator::validate($data, $rules, $label);

        Validator::validateFile(
        Request::file(),
        [
            'avatar' => 'image|mimes:jpg,jpeg,png|max:2'
        ]
    );

        Validator::check($this->loginUsersModel->findUserByEmail($data['email']), 'email', 'Email Sudah Digunakan');
        Validator::check($data['password1'] !== $data['password2'], [
            'password1' => 'Password Tidak Sama',
            'password2' => 'Password Tidak Sama',
        ]);

        $this->failIf(Validator::fails(), '/register', Validator::errors());

        $userId = Str::userId();
        $password = password_hash($data['password1'], PASSWORD_DEFAULT);

        $avatar = '';

        if (Request::hasFile('avatar')) {
            $avatar = File::upload(
                Request::file('avatar'),
                UPLOAD_PATH . '/profiles',
                $userId
            );

            $this->failIf(
                !$avatar,
                '/register',
                [
                    'photo' => 'Upload photo gagal.'
                ]
            );
        }

        $transaction = Transaction::run(function () use ($userId, $password, $data, $avatar) {
            $this->loginUsersModel->create([
                'id' => $userId,
                'email' => $data['email'],
                'role' => 'user',
                'password' => $password,
                'status' => 'active'
            ]);

            $this->userDetailsModel->create([
                'id' => $userId,
                'fname' => $data['fname'],
                'lname' => $data['lname'],
                'email' => $data['email'],
                'gender' => $data['gender'],
                'profile_picture' => $avatar,
                'address' => $data['address']
            ]);
        });

        if (!$transaction) {
            File::delete(
                UPLOAD_PATH . '/profiles/' . $avatar
            );

            $this->redirectIf(
                true,
                '/register',
                'error',
                'Register Gagal, Coba Lagi Nanti!'
            );
        }

        Flash::set('success', 'Register Berhasil, Silahkan Login!');
        Redirect::to('/login');
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout() {
        Session::destroy();
        Redirect::to('/');
    }
}