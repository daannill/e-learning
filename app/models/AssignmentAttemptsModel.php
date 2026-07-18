<?php

namespace App\Models;

use Core\Model;

class AssignmentAttemptsModel extends Model {
    
    public function getSubmittedAssignments(string $teacherId): array {
        return $this->many("
            SELECT
                aa.assignment_attempt_id,
                CONCAT(
                    ud.first_name,
                    ' ',
                    ud.last_name
                ) AS student_name,
                m.title AS assignment_title,
                c.course_name

            FROM assignment_attempts aa

            JOIN assignments a
                ON aa.assignment_id = a.assignment_id

            JOIN user_progress up
                ON aa.user_progress_id = up.user_progress_id

            JOIN enrollments e
                ON up.enrollment_id = e.enrollment_id

            JOIN user_details ud
                ON e.user_id = ud.user_id

            JOIN materials m
                ON a.material_id = m.material_id

            JOIN courses c
                ON m.course_id = c.course_id

            WHERE c.instructor_id = :teacher_id
            AND aa.status = 'submitted'

            ORDER BY aa.created_at ASC

            LIMIT 3
        ", [
            ':teacher_id' => $teacherId
        ]);
    }

    public function countPendingAssignments(string $teacherId): int {
        $result = $this->one("
            SELECT
                COUNT(*) AS total

            FROM assignment_attempts aa

            JOIN assignments a
                ON aa.assignment_id = a.assignment_id

            JOIN materials m
                ON a.material_id = m.material_id

            JOIN courses c
                ON m.course_id = c.course_id

            WHERE c.instructor_id = :teacher_id
            AND aa.status = 'submitted'
        ", [
            ':teacher_id' => $teacherId
        ]);

        return (int) $result['total'];
    }

    public function getTeacherAssignmentAttempts(
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
            'oldest' => 'aa.created_at ASC',
            default => 'aa.created_at DESC'
        };

        return $this->many("
            SELECT
                aa.assignment_attempt_id,

                CONCAT(
                    ud.first_name,
                    ' ',
                    ud.last_name
                ) AS student_name,

                ud.email,

                c.course_id,
                c.course_name,

                m.material_id,
                m.title AS assignment_title,

                aa.score,
                aa.onTime,
                aa.status,
                aa.created_at

            FROM assignment_attempts aa

            JOIN user_progress up
                ON aa.user_progress_id = up.user_progress_id

            JOIN enrollments e
                ON up.enrollment_id = e.enrollment_id

            JOIN user_details ud
                ON e.user_id = ud.user_id

            JOIN courses c
                ON e.course_id = c.course_id

            JOIN assignments a
                ON aa.assignment_id = a.assignment_id

            JOIN materials m
                ON a.material_id = m.material_id

            WHERE " . implode(' AND ', $where) . "

            ORDER BY {$orderBy}

            LIMIT {$limit} OFFSET {$offset}
        ", $params);
    }

    public function countTeacherAssignmentAttempts(
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

            FROM assignment_attempts aa

            JOIN user_progress up
                ON aa.user_progress_id = up.user_progress_id

            JOIN enrollments e
                ON up.enrollment_id = e.enrollment_id

            JOIN user_details ud
                ON e.user_id = ud.user_id

            JOIN courses c
                ON e.course_id = c.course_id

            JOIN assignments a
                ON aa.assignment_id = a.assignment_id

            JOIN materials m
                ON a.material_id = m.material_id

            WHERE " . implode(' AND ', $where)
        , $params);

        return (int) $result['total_attempts'];
    }
}