<?php

namespace App\Models;

use Core\Model;

class UserQuizAnswersModel extends Model {
    
    public function create(array $data): bool {
        return $this->insert('user_quiz_answers', [
            'attempt_id' => $data['attempt_id'],
            'question_id' => $data['question_id'],
            'selected_option_id' => $data['selected_option_id'],
            'correct' => $data['correct']
        ]);
    }
}