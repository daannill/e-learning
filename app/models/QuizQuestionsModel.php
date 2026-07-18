<?php

namespace App\Models;

use Core\Model;

class QuizQuestionsModel extends Model {
    
    public function getAllQuestionsWithOptions(string $quizId): array {
        $rows = $this->many("
            SELECT
                qq.question_id,
                qq.question,
                qq.question_order,
                qo.option_id,
                qo.option_order,
                qo.option_text,
                qo.is_correct
 
            FROM quiz_questions qq
 
            JOIN quiz_options qo
                ON qq.question_id = qo.question_id
 
            WHERE qq.quiz_id = :quiz_id
 
            ORDER BY qq.question_order, qo.option_order
        ", [
            ':quiz_id' => $quizId
        ]);
 
        $questions = [];
 
        foreach ($rows as $row) {
            $questionId = $row['question_id'];
 
            if (!isset($questions[$questionId])) {
                $questions[$questionId] = [
                    'question_id' => $row['question_id'],
                    'question' => $row['question'],
                    'question_order' => $row['question_order'],
                    'options' => []
                ];
            }
 
            $questions[$questionId]['options'][] = [
                'option_id' => $row['option_id'],
                'option_order' => $row['option_order'],
                'option_text' => $row['option_text'],
                'is_correct' => $row['is_correct']
            ];
        }
 
        return array_values($questions);
    }

    public function create(array $data): int|false {
        $insert = $this->insert('quiz_questions', [
            'quiz_id' => $data['quiz_id'],
            'question_order' => $data['question_order'],
            'question' => $data['question']
        ]);

        if (!$insert) {
            return $insert;
        }

        return $this->lastInsertId();
    }

    public function deleteByQuizId(int $quizId): bool {
        return $this->run(
            "
            DELETE qq
            FROM quiz_questions qq
            WHERE qq.quiz_id = :quiz_id
            ",
            [
                ':quiz_id' => $quizId
            ]
        );
    }
}