<?php

namespace App\Models;

use Core\Model;

class LoginUsersModel extends Model {
    
    public function create(array $data): bool {
        return $this->insert('login_users', [
            'user_id' => $data['id'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'status' => $data['status']
        ]);
    }

    public function findUserByEmail(string $email): ?array {
        return $this->findByOne(
            'login_users',
            ['user_id', 'email', 'password', 'role'],
            ['email' => $email]
        );
    }

}