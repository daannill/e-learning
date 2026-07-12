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
}