<?php

namespace App\Models;

use Core\Model;

class QuizOptionsModel extends Model {
    
    public function create(array $data): bool {
        return $this->insert('quiz_options', [
            'question_id' => $data['question_id'],
            'option_order' => $data['option_order'],
            'option_text' => $data['option_text'],
            'is_correct' => $data['is_correct']
        ]);
    }
}