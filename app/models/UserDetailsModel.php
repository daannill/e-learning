<?php

namespace App\Models;

use Core\Model;

class UserDetailsModel extends Model {

    public function create(array $data): bool {
        return $this->insert('user_details', [
            'user_id' => $data['id'],
            'first_name' => $data['fname'],
            'last_name' => $data['lname'],
            'email' => $data['email'],
            'gender' => $data['gender'],
            'profile_picture' => $data['profile_picture'],
            'address' => $data['address']
        ]);
    }

    public function findUserDetailsById(string $id): ?array {
        return $this->findByOne(
            'user_details',
            [
                'user_id',
                "CONCAT(first_name, ' ', last_name) AS full_name",
                'email',
                'gender',
                'address',
                "DATE_FORMAT(created_at, '%M %Y') AS join_since",
                'update_at'
            ],
            ['user_id' => $id]
        );
    }
}