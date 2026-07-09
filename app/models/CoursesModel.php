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
                c.created_at$userColumns
 
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
        return $this->exists('courses', ['course_id' => $courseId]);
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
}