<?php

namespace App\Models;

use Core\Model;

class RatingsModel extends Model {

    public function getTeacherRatings(
        string $teacherId,
        string $sort = 'newest',
        string $search = '',
        int $limit = 10,
        int $offset = 0
    ): array {

        $where = [
            'c.instructor_id = :teacher_id',
            'c.status = :course_status'
        ];

        $params = [
            ':teacher_id' => $teacherId,
            ':course_status' => 'published'
        ];

        if ($search !== '') {
            $where[] = "(
                CONCAT(
                    ud.first_name,
                    ' ',
                    ud.last_name
                ) LIKE :search
                OR c.course_name LIKE :search
            )";

            $params[':search'] = '%' . $search . '%';
        }

        $orderBy = match ($sort) {
            'oldest' => 'r.created_at ASC',
            default => 'r.created_at DESC'
        };

        return $this->many("
            SELECT
                r.rating_id,
                r.rating,
                r.created_at,

                c.course_id,
                c.course_name,
                c.thumbnail,
                c.difficulty,

                CONCAT(
                    ud.first_name,
                    ' ',
                    ud.last_name
                ) AS student_name,

                ud.email

            FROM ratings r

            JOIN enrollments e
                ON r.enrollment_id = e.enrollment_id

            JOIN courses c
                ON e.course_id = c.course_id

            JOIN user_details ud
                ON e.user_id = ud.user_id

            WHERE " . implode(' AND ', $where) . "

            ORDER BY {$orderBy}

            LIMIT {$limit} OFFSET {$offset}
        ", $params);
    }

    public function countTeacherRatings(
        string $teacherId,
        string $search = ''
    ): int {

        $where = [
            'c.instructor_id = :teacher_id',
            'c.status = :course_status'
        ];

        $params = [
            ':teacher_id' => $teacherId,
            ':course_status' => 'published'
        ];

        if ($search !== '') {
            $where[] = "(
                CONCAT(
                    ud.first_name,
                    ' ',
                    ud.last_name
                ) LIKE :search
                OR c.course_name LIKE :search
            )";

            $params[':search'] = '%' . $search . '%';
        }

        $result = $this->one("
            SELECT
                COUNT(*) AS total_ratings

            FROM ratings r

            JOIN enrollments e
                ON r.enrollment_id = e.enrollment_id

            JOIN courses c
                ON e.course_id = c.course_id

            JOIN user_details ud
                ON e.user_id = ud.user_id

            WHERE " . implode(' AND ', $where)
        , $params);

        return (int) $result['total_ratings'];
    }
}