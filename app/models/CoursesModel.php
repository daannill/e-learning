<?php

namespace App\Models;

use Core\Model;

class CoursesModel extends Model{

    public function getAllPublishedCourses(?string $userId = null): array {
        $userColumns = '';
        $userJoins = '';
        $params = [];
 
        if ($userId !== null) {
            $userColumns = ",
                (e.course_id IS NOT NULL) AS is_enrolled,
                (sc.course_id IS NOT NULL) AS is_saved";
 
            $userJoins = "
                LEFT JOIN enrollments e
                    ON c.course_id = e.course_id
                    AND e.user_id = :user_id
 
                LEFT JOIN saved_courses sc
                    ON c.course_id = sc.course_id
                    AND sc.user_id = :user_id";
 
            $params[':user_id'] = $userId;
        }
 
        return $this->many("
            SELECT
                c.course_id,
                cat.category_name,
                CONCAT(ud.first_name, ' ', ud.last_name) AS teacher_name,
                tp.job_title,
                c.course_name,
                c.short_description,
                c.thumbnail,
                c.difficulty,
                c.total_materials,
                c.total_students,
                c.average_rating,
                c.created_at
                $userColumns
 
            FROM courses c
 
            JOIN categories cat
                ON c.category_id = cat.category_id
 
            JOIN user_details ud
                ON c.instructor_id = ud.user_id
 
            JOIN teacher_profiles tp
                ON c.instructor_id = tp.user_id
            $userJoins
 
            WHERE c.status = 'published'
 
            ORDER BY c.created_at DESC
        ", $params);
    }

    public function create(array $data): bool {
        return $this->insert('courses', [
            'course_id' => $data['course_id'],
            'instructor_id' => $data['instructor_id'],
            'course_name' => $data['course_name'],
            'category_id' => $data['category_id'],
            'short_description' => $data['short_description'],
            'description' => $data['description'],
            'thumbnail' => $data['thumbnail'],
            'difficulty' => $data['difficulty'],
            'status' => $data['status'],
            'total_materials' => $data['total_materials'],
            'total_students' => $data['total_students'],
            'total_duration' => $data['total_duration'],
            'average_rating' => $data['average_rating'],
            'total_reviews' => $data['total_reviews']
        ]);
    }

    public function findPublishedCourseDetail(string $id): ?array {
        return $this->one("
            SELECT
                c.course_id,
                c.course_name,
                c.description,
                c.total_materials,
                c.total_students,
                c.average_rating,
                c.created_at,
                cat.category_name,
                c.difficulty,
                c.thumbnail,
                CONCAT(ud.first_name, ' ', ud.last_name) AS teacher_name,
                tp.job_title
 
            FROM courses c
 
            JOIN categories cat
                ON c.category_id = cat.category_id
 
            JOIN user_details ud
                ON c.instructor_id = ud.user_id
 
            JOIN teacher_profiles tp
                ON c.instructor_id = tp.user_id
 
            WHERE c.course_id = :id
            AND c.status = 'published'
 
            LIMIT 1
        ", [
            ':id' => $id
        ]);
    }

    public function isCoursePublish(string $courseId): bool {
        return $this->exists('courses', ['course_id' => $courseId, 'status' => 'published']);
    }

    public function incrementTotalStudents(string $courseId): bool {
        return $this->run("
            UPDATE courses
            SET total_students = total_students + 1
            WHERE course_id = :course_id
        ", [
            ':course_id' => $courseId
        ]);
    }

    public function findCourseCategoryAndTotalMaterials(string $courseId): ?array {
        return $this->one("
            SELECT
                c.course_name,
                cat.category_name,
                c.total_materials
 
            FROM courses c
 
            JOIN categories cat
                ON c.category_id = cat.category_id
 
            WHERE c.course_id = :course_id
 
            LIMIT 1
        ", [
            ':course_id' => $courseId
        ]);
    }

    public function getTeacherStat(string $teacherId): ?array {
        return $this->one("
            SELECT
                COUNT(*) AS total_courses,

                COALESCE(
                    SUM(total_students),
                    0
                ) AS total_enrolled,

                COALESCE(
                    ROUND(
                        SUM(average_rating * total_reviews) /
                        NULLIF(SUM(total_reviews), 0),
                        1
                    ),
                    0
                ) AS average_rating,

                COALESCE(
                    SUM(status = 'pending'),
                    0
                ) AS pending_courses

            FROM courses

            WHERE instructor_id = :teacher_id
                AND status = 'published';
        ", [
            ':teacher_id' => $teacherId
        ]);
    }

    public function getLatestCoursesTeacher(string $teacherId): array {
        return $this->many("
            SELECT
                c.course_id,
                cat.category_name,
                c.course_name,
                c.thumbnail,
                c.short_description,
                c.difficulty,
                c.total_materials,
                c.total_students,
                c.average_rating,
                c.status,
                c.created_at

            FROM courses c

            JOIN categories cat
                ON c.category_id = cat.category_id

            WHERE c.instructor_id = :teacher_id
                AND c.status <> 'archived'

            ORDER BY c.created_at DESC

            LIMIT 4
        ", [
            ':teacher_id' => $teacherId
        ]);
    }

    public function getLatestRejectedCourses(string $teacherId): array {
        return $this->many("
            SELECT
                course_id,
                course_name,
                updated_at

            FROM courses

            WHERE instructor_id = :teacher_id
            AND status = 'rejected'

            ORDER BY updated_at DESC

            LIMIT 5
        ", [
            ':teacher_id' => $teacherId
        ]);
    }

    public function countRejectedCourses(string $teacherId): int {
        $result = $this->one("
            SELECT
                COUNT(*) AS total

            FROM courses

            WHERE instructor_id = :teacher_id
            AND status = 'rejected'
        ", [
            ':teacher_id' => $teacherId
        ]);

        return (int) $result['total'];
    }

    public function getTeacherCourses(
        string $teacherId,
        string $status = 'all',
        string $sort = 'newest',
        string $search = '',
        int $limit = 10,
        int $offset = 0
    ): array {

        $where = [
            'c.instructor_id = :teacher_id'
        ];

        $params = [
            ':teacher_id' => $teacherId
        ];

        if ($status !== 'all') {
            $where[] = 'c.status = :status';
            $params[':status'] = $status;
        }

        if ($search !== '') {
            $where[] = 'c.course_name LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        $orderBy = match ($sort) {
            'oldest' => 'c.created_at ASC',
            'most_students' => 'c.total_students DESC',
            'most_reviews' => 'c.total_reviews DESC',
            'highest_rating' => 'c.average_rating DESC',
            default => 'c.created_at DESC'
        };

        return $this->many("
            SELECT
                c.course_id,
                cat.category_name,
                c.course_name,
                c.thumbnail,
                c.short_description,
                c.difficulty,
                c.total_materials,
                c.total_students,
                c.average_rating,
                c.total_reviews,
                c.status,
                c.created_at

            FROM courses c

            JOIN categories cat
                ON c.category_id = cat.category_id

            WHERE " . implode(' AND ', $where) . "

            ORDER BY {$orderBy}

            LIMIT {$limit} OFFSET {$offset}
        ", $params);
    }

    public function getTeacherArchivedCourses(
        string $teacherId,
        string $sort = 'newest',
        string $search = '',
        int $limit = 10,
        int $offset = 0
    ): array {

        $where = [
            'c.instructor_id = :teacher_id',
            'c.status = :status'
        ];

        $params = [
            ':teacher_id' => $teacherId,
            ':status' => 'archived'
        ];

        if ($search !== '') {
            $where[] = 'c.course_name LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        $orderBy = match ($sort) {
            'oldest' => 'c.created_at ASC',
            'most_students' => 'c.total_students DESC',
            'most_reviews' => 'c.total_reviews DESC',
            'highest_rating' => 'c.average_rating DESC',
            default => 'c.created_at DESC'
        };

        return $this->many("
            SELECT
                c.course_id,
                cat.category_name,
                c.course_name,
                c.thumbnail,
                c.short_description,
                c.difficulty,
                c.total_materials,
                c.total_students,
                c.average_rating,
                c.total_reviews,
                c.status,
                c.created_at

            FROM courses c

            JOIN categories cat
                ON c.category_id = cat.category_id

            WHERE " . implode(' AND ', $where) . "

            ORDER BY {$orderBy}

            LIMIT {$limit} OFFSET {$offset}
        ", $params);
    }

    public function countTeacherCourses(
        string $teacherId,
        string $status = 'all',
        string $search = ''
    ): int {

        $where = [
            'c.instructor_id = :teacher_id'
        ];

        $params = [
            ':teacher_id' => $teacherId
        ];

        if ($status !== 'all') {
            $where[] = 'c.status = :status';
            $params[':status'] = $status;
        }

        if ($search !== '') {
            $where[] = 'c.course_name LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        $result = $this->one("
            SELECT
                COUNT(*) AS total_courses

            FROM courses c

            WHERE " . implode(' AND ', $where)
        , $params);

        return (int) $result['total_courses'];
    }

    public function countTeacherArchivedCourses(
        string $teacherId,
        string $search = ''
    ): int {

        $where = [
            'c.instructor_id = :teacher_id',
            'c.status = :status'
        ];

        $params = [
            ':teacher_id' => $teacherId,
            ':status' => 'archived'
        ];

        if ($search !== '') {
            $where[] = 'c.course_name LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        $result = $this->one("
            SELECT
                COUNT(*) AS total_courses

            FROM courses c

            WHERE " . implode(' AND ', $where)
        , $params);

        return (int) $result['total_courses'];
    }

    public function findCourse(string $courseId): ?array {
        return $this->one("
            SELECT
                c.course_id,
                c.course_name,
                c.instructor_id,
                c.description,
                c.total_materials,
                c.total_students,
                c.average_rating,
                c.created_at,
                cat.category_name,
                c.difficulty,
                c.thumbnail,
                c.status,
                CONCAT(
                    ud.first_name,
                    ' ',
                    ud.last_name
                ) AS teacher_name,
                tp.job_title

            FROM courses c

            JOIN categories cat
                ON c.category_id = cat.category_id

            JOIN user_details ud
                ON c.instructor_id = ud.user_id

            JOIN teacher_profiles tp
                ON c.instructor_id = tp.user_id

            WHERE c.course_id = :course_id

            LIMIT 1
        ",[
            'course_id' => $courseId
        ]);
    }

    public function findCourseForEdit(string $courseId): ?array {
        return $this->one("
            SELECT
                course_id,
                course_name,
                category_id,
                difficulty,
                short_description,
                description,
                thumbnail
            FROM courses
            WHERE course_id = :course_id
            LIMIT 1
        ", [
            'course_id' => $courseId
        ]);
    }

    public function teacherOwnsCourse(string $courseId, string $teacherId): bool {
        return $this->exists(
            'courses',
            [
                'course_id' => $courseId,
                'instructor_id' => $teacherId
            ]
        );
    }

    public function updateCourse(string $courseId, array $data): bool {
        return $this->update(
            'courses',
            [
                'course_name' => $data['title'],
                'category_id' => $data['category'],
                'difficulty' => $data['difficulty'],
                'short_description' => $data['short_description'],
                'description' => $data['description'],
                'thumbnail' => $data['thumbnail'],
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'course_id' => $courseId
            ]
        );
    }
}