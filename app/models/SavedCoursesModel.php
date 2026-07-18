<?php

namespace App\Models;

use Core\Model;

class SavedCoursesModel extends Model {

    public function create(array $data): bool {
        return $this->insert('saved_courses', [
            'course_id' => $data['course_id'],
            'user_id' => $data['user_id']
        ]);
    }

    public function findSavedCourse(string $userId, string $courseId): ?array {
        return $this->findByOne(
            'saved_courses',
            ['course_id', 'user_id', 'saved_at'],
            ['course_id' => $courseId, 'user_id' => $userId]
        );
    }

    public function deleteSavedCourse(string $userId, string $courseId): bool {
        return $this->delete(
            'saved_courses',
            [
                'course_id' => $courseId,
                'user_id' => $userId
            ]
        );
    }

    public function getAllSavedCourses(
        string $userId,
        string $category = 'all',
        string $sort = 'newest',
        string $search = '',
        int $limit = 8,
        int $offset = 0
    ): array {

        $where = [
            'sc.user_id = :user_id',
            "c.status = 'published'"
        ];

        $params = [
            ':user_id' => $userId
        ];

        if ($category !== 'all') {
            $where[] = 'c.category_id = :category_id';
            $params[':category_id'] = $category;
        }

        if ($search !== '') {
            $where[] = 'c.course_name LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        $orderBy = match ($sort) {
            'oldest' => 'sc.saved_at ASC',
            default => 'sc.saved_at DESC'
        };

        return $this->many("
            SELECT
                c.course_id,
                cat.category_name,

                CONCAT(
                    ud.first_name,
                    ' ',
                    ud.last_name
                ) AS teacher_name,

                tp.job_title,

                c.course_name,
                c.short_description,
                c.thumbnail,
                c.difficulty,
                c.total_materials,
                c.total_students,
                c.average_rating,
                c.created_at,

                (e.course_id IS NOT NULL) AS is_enrolled,
                1 AS is_saved

            FROM saved_courses sc

            JOIN courses c
                ON c.course_id = sc.course_id

            JOIN categories cat
                ON c.category_id = cat.category_id

            JOIN user_details ud
                ON c.instructor_id = ud.user_id

            JOIN teacher_profiles tp
                ON c.instructor_id = tp.user_id

            LEFT JOIN enrollments e
                ON c.course_id = e.course_id
                AND e.user_id = :user_id

            WHERE " . implode(' AND ', $where) . "

            ORDER BY {$orderBy}

            LIMIT {$limit} OFFSET {$offset}
        ", $params);
    }

    public function countSavedCourses(
        string $userId,
        string $category = 'all',
        string $search = ''
    ): int {

        $where = [
            'sc.user_id = :user_id',
            "c.status = 'published'"
        ];

        $params = [
            ':user_id' => $userId
        ];

        if ($category !== 'all') {
            $where[] = 'c.category_id = :category_id';
            $params[':category_id'] = $category;
        }

        if ($search !== '') {
            $where[] = 'c.course_name LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        $result = $this->one("
            SELECT
                COUNT(*) AS total_courses

            FROM saved_courses sc

            JOIN courses c
                ON c.course_id = sc.course_id

            WHERE " . implode(' AND ', $where)
        , $params);

        return (int) $result['total_courses'];
    }
}