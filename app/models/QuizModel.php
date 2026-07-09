<?php

namespace App\Models;

use Core\Model;

class QuizzesModel extends Model {

    public function findQuiz(string $quizId): ?array {
        return $this->findByOne(
            'quizzes',
            ['quiz_id', 'material_id', 'minimum_correct', 'total_questions', 'max_attempts', 'reset_minutes', 'timer'],
            ['quiz_id' => $quizId]
        );
    }


    public function getQuizUser(string $materialId, string $enrollmentId): ?array {
        return $this->one("
            SELECT
                q.quiz_id,
                q.max_attempts,
                q.timer,
                q.reset_minutes,
                q.total_questions,
 
                ROUND(
                    (q.minimum_correct / q.total_questions) * 100
                ) AS passing_score,
 
                COUNT(
                    CASE
                        WHEN qa.submitted_at >= DATE_SUB(
                            NOW(),
                            INTERVAL q.reset_minutes MINUTE
                        )
                        THEN 1
                    END
                ) AS attempts_used,
 
                COALESCE(
                    UNIX_TIMESTAMP(
                        DATE_ADD(
                            MAX(qa.submitted_at),
                            INTERVAL q.reset_minutes MINUTE
                        )
                    ),
                    0
                ) AS next_attempt_at,
 
                COALESCE(MAX(qa.score), 0) AS best_score,
 
                (
                    SELECT qa2.score
                    FROM user_quiz_attempts qa2
                    WHERE qa2.user_progress_id = up.user_progress_id
                    ORDER BY qa2.submitted_at DESC
                    LIMIT 1
                ) AS latest_score,
 
                (
                    SELECT MIN(qa3.submitted_at)
                    FROM user_quiz_attempts qa3
                    WHERE qa3.user_progress_id = up.user_progress_id
                    AND qa3.submitted_at >= DATE_SUB(
                        NOW(),
                        INTERVAL q.reset_minutes MINUTE
                    )
                ) AS oldest_active_attempt
 
            FROM quizzes q
 
            LEFT JOIN user_progress up
                ON up.material_id = q.material_id
                AND up.enrollment_id = :enrollment_id
 
            LEFT JOIN user_quiz_attempts qa
                ON qa.user_progress_id = up.user_progress_id
 
            WHERE q.material_id = :material_id
 
            GROUP BY
                q.quiz_id,
                q.max_attempts,
                q.reset_minutes,
                up.user_progress_id
 
            LIMIT 1
        ", [
            ':material_id' => $materialId,
            ':enrollment_id' => $enrollmentId
        ]);
    }
}