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
        return $this->delete('saved_courses', 'course_id = :course_id AND user_id = :user_id', [
            ':course_id' => $courseId,
            ':user_id' => $userId
        ]);
    }

    public function getAllSavedCourses(string $userId): array {
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
 
            WHERE sc.user_id = :user_id
            AND c.status = 'published'
 
            ORDER BY sc.saved_at DESC
        ", [
            ':user_id' => $userId
        ]);
    }
}