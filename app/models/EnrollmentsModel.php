<?php

namespace App\Models;

use Core\Model;

class EnrollmentsModel extends Model {
    
    public function isEnrolled(string $userId, string $courseId): bool {
        return $this->exists('enrollments', ['user_id' => $userId, 'course_id' => $courseId]);
    }

    public function create(array $data): bool {
        return $this->insert('enrollments', [
            'enrollment_id' => $data['enrollment_id'],
            'user_id' => $data['user_id'],
            'course_id' => $data['course_id'],
            'last_material' => $data['last_material']
        ]);
    }

    public function findEnrollment(string $userId, string $courseId): ?array {
        return $this->one("
            SELECT
                enrollment_id,
                user_id,
                course_id,
                last_material,
                total_completed,
                last_open,
                enrolled_at
 
            FROM enrollments
 
            WHERE user_id = :user_id
            AND course_id = :course_id
 
            LIMIT 1
        ", [
            ':user_id' => $userId,
            ':course_id' => $courseId
        ]);
    }

    public function updateLastMaterialAndTotalCompleted(string $enrollmentId, string $materialId) {
        $this->run("
            UPDATE enrollments
            SET
                last_material = :material_id,
                completed = COALESCE(completed, 0) + 1
            WHERE enrollment_id = :enrollment_id
        ", [
            ':enrollment_id' => $enrollmentId,
            ':material_id' => $materialId
        ]);
    }

    public function getUserStats(string $userId): ?array {
        return $this->one("
            SELECT
                COUNT(e.enrollment_id) AS enrolled_courses,
 
                COUNT(
                    CASE
                        WHEN e.total_completed >= c.total_materials
                        THEN 1
                    END
                ) AS completed_courses,
 
                ROUND(
                    AVG(
                        (e.total_completed / c.total_materials) * 100
                    )
                ) AS avg_progress,
 
                COUNT(
                    CASE
                        WHEN e.total_completed > 0
                        AND e.total_completed < c.total_materials
                        THEN 1
                    END
                ) AS in_progress_courses,
 
                SUM(e.total_completed) AS completed_materials
 
            FROM enrollments e
 
            JOIN courses c
                ON c.course_id = e.course_id
 
            WHERE e.user_id = :user_id

            LIMIT 1
        ", [
            ':user_id' => $userId
        ]);
    }

    public function getAllEnrolledCoursesExceptCourse(string $userId, string $currentCourseId): array {
        return $this->many("
            SELECT
                c.course_id,
                c.course_name,
                c.thumbnail,
                c.short_description,
                cat.category_name,
                e.total_completed,
                c.total_materials,
                CONCAT(ud.first_name, ' ', ud.last_name) AS teacher_name,
 
                CASE
                    WHEN e.total_completed >= c.total_materials
                    THEN 1
                    ELSE 0
                END AS is_completed,
 
                ROUND(
                    (e.total_completed / c.total_materials) * 100
                ) AS avg_progress
 
            FROM enrollments e
 
            JOIN courses c
                ON c.course_id = e.course_id
 
            JOIN categories cat
                ON cat.category_id = c.category_id
 
            JOIN user_details ud
                ON c.instructor_id = ud.user_id
 
            WHERE e.user_id = :user_id
            AND e.course_id != :current_course_id
 
            ORDER BY e.last_open DESC
        ", [
            ':user_id' => $userId,
            ':current_course_id' => $currentCourseId
        ]);
    }

    public function findLastOpenedCourse(string $userId): ?array {
        return $this->one("
            SELECT
                c.course_id,
                c.course_name,
                CONCAT(ud.first_name, ' ', ud.last_name) AS teacher_name,
                m.material_id,
                m.title,
                e.total_completed,
                c.total_materials,

                ROUND(
                    (e.total_completed / c.total_materials) * 100
                ) AS avg_progress,

                up.opened_at

            FROM enrollments e

            JOIN user_progress up
                ON e.enrollment_id = up.enrollment_id

            JOIN courses c
                ON e.course_id = c.course_id

            JOIN materials m
                ON up.material_id = m.material_id

            JOIN user_details ud
                ON c.instructor_id = ud.user_id

            WHERE e.user_id = :user_id

            ORDER BY up.opened_at DESC

            LIMIT 1
        ", [
            ':user_id' => $userId
        ]);
    }
    
    public function getTeacherDashboardActivity(string $teacherId): array {
        return $this->many("
            SELECT
                activity_type,
                user_name,
                course_name,
                rating,
                created_at

            FROM (

                SELECT
                    'enroll' AS activity_type,
                    CONCAT(
                        ud.first_name,
                        ' ',
                        ud.last_name
                    ) AS user_name,
                    c.course_name,
                    NULL AS rating,
                    e.enrolled_at AS created_at

                FROM enrollments e

                JOIN courses c
                    ON e.course_id = c.course_id

                JOIN user_details ud
                    ON e.user_id = ud.user_id

                WHERE c.instructor_id = :teacher_id


                UNION ALL


                SELECT
                    'completed' AS activity_type,
                    CONCAT(
                        ud.first_name,
                        ' ',
                        ud.last_name
                    ) AS user_name,
                    c.course_name,
                    NULL AS rating,
                    e.last_open AS created_at

                FROM enrollments e

                JOIN courses c
                    ON e.course_id = c.course_id

                JOIN user_details ud
                    ON e.user_id = ud.user_id

                WHERE c.instructor_id = :teacher_id
                AND c.total_materials > 0
                AND e.total_completed = c.total_materials


                UNION ALL


                SELECT
                    'review' AS activity_type,
                    CONCAT(
                        ud.first_name,
                        ' ',
                        ud.last_name
                    ) AS user_name,
                    c.course_name,
                    r.rating,
                    r.created_at

                FROM ratings r

                JOIN enrollments e
                    ON r.enrollment_id = e.enrollment_id

                JOIN courses c
                    ON e.course_id = c.course_id

                JOIN user_details ud
                    ON e.user_id = ud.user_id

                WHERE c.instructor_id = :teacher_id

            ) activities

            ORDER BY created_at DESC

            LIMIT 4
        ", [
            ':teacher_id' => $teacherId
        ]);
    }
}