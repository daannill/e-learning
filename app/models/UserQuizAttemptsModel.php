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

    public function getTeacherQuizAttempts(
        string $teacherId,
        string $sort = 'newest',
        string $search = '',
        int $limit = 10,
        int $offset = 0
    ): array {

        $where = [
            'c.instructor_id = :teacher_id',
            "c.status = 'published'"
        ];

        $params = [
            ':teacher_id' => $teacherId
        ];

        if ($search !== '') {
            $where[] = "(
                CONCAT(
                    ud.first_name,
                    ' ',
                    ud.last_name
                ) LIKE :search

                OR c.course_name LIKE :search

                OR m.title LIKE :search
            )";

            $params[':search'] = '%' . $search . '%';
        }

        $orderBy = match ($sort) {
            'oldest' => 'uqa.submitted_at ASC',
            default => 'uqa.submitted_at DESC'
        };

        return $this->many("
            SELECT
                uqa.attempt_id,

                CONCAT(
                    ud.first_name,
                    ' ',
                    ud.last_name
                ) AS student_name,

                ud.email,

                c.course_id,
                c.course_name,

                m.material_id,
                m.title AS quiz_title,

                uqa.correct_answers,
                uqa.score,
                uqa.is_passed,
                uqa.submitted_at

            FROM user_quiz_attempts uqa

            JOIN user_progress up
                ON uqa.user_progress_id = up.user_progress_id

            JOIN enrollments e
                ON up.enrollment_id = e.enrollment_id

            JOIN user_details ud
                ON e.user_id = ud.user_id

            JOIN courses c
                ON e.course_id = c.course_id

            JOIN quizzes q
                ON uqa.quiz_id = q.quiz_id

            JOIN materials m
                ON q.material_id = m.material_id

            WHERE " . implode(' AND ', $where) . "

            ORDER BY {$orderBy}

            LIMIT {$limit} OFFSET {$offset}
        ", $params);
    }

    public function countTeacherQuizAttempts(
        string $teacherId,
        string $search = ''
    ): int {

        $where = [
            'c.instructor_id = :teacher_id',
            "c.status = 'published'"
        ];

        $params = [
            ':teacher_id' => $teacherId
        ];

        if ($search !== '') {
            $where[] = "(
                CONCAT(
                    ud.first_name,
                    ' ',
                    ud.last_name
                ) LIKE :search

                OR c.course_name LIKE :search

                OR m.title LIKE :search
            )";

            $params[':search'] = '%' . $search . '%';
        }

        $result = $this->one("
            SELECT
                COUNT(*) AS total_attempts

            FROM user_quiz_attempts uqa

            JOIN user_progress up
                ON uqa.user_progress_id = up.user_progress_id

            JOIN enrollments e
                ON up.enrollment_id = e.enrollment_id

            JOIN user_details ud
                ON e.user_id = ud.user_id

            JOIN courses c
                ON e.course_id = c.course_id

            JOIN quizzes q
                ON uqa.quiz_id = q.quiz_id

            JOIN materials m
                ON q.material_id = m.material_id

            WHERE " . implode(' AND ', $where)
        , $params);

        return (int) $result['total_attempts'];
    }
}