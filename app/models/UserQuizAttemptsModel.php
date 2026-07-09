<?php

namespace App\Models;

use Core\Model;

class UserQuizAttemptsModel extends Model {

    public function create(array $data): bool {
        return $this->insert('user_quiz_attempts', [
            'attempt_id' => $data['attempt_id'],
            'user_progress_id' => $data['user_progress_id'],
            'quiz_id' => $data['quiz_id'],
            'correct_answers' => $data['correct_answers'],
            'score' => $data['score'],
            'is_passed' => $data['is_passed']
        ]);
    }

    public function getQuizAttemptsProgress(string $enrollmentId, string $materialId): ?array {
        return $this->one("
            SELECT
                uq.attempt_id,
                uq.user_progress_id,
                uq.quiz_id,
                uq.correct_answers,
                uq.score,
                uq.is_passed,
                uq.submitted_at,
 
                (
                    SELECT MAX(score)
                    FROM user_quiz_attempts
                    WHERE user_progress_id = uq.user_progress_id
                ) AS best_score
 
            FROM user_quiz_attempts uq
 
            JOIN user_progress up
                ON up.user_progress_id = uq.user_progress_id
 
            WHERE up.enrollment_id = :enrollment_id
            AND up.material_id = :material_id
 
            ORDER BY uq.submitted_at DESC
 
            LIMIT 1
        ", [
            ':enrollment_id' => $enrollmentId,
            ':material_id' => $materialId
        ]);
    }
}