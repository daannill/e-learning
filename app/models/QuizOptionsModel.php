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

    public function deleteByQuizId(int $quizId): bool {
        return $this->run(
            "
            DELETE qo
            FROM quiz_options qo
            INNER JOIN quiz_questions qq
                ON qq.question_id = qo.question_id
            WHERE qq.quiz_id = :quiz_id
            ",
            [
                ':quiz_id' => $quizId
            ]
        );
    }
}