<?php

namespace App\Models;

use Core\Model;

class UserProgressModel extends Model {

    public function findUserProgress(string $enrollmentId, string $materialId): ?array {
        return $this->findByOne(
            'user_progress',
            ['user_progress_id'],
            ['enrollment_id' => $enrollmentId, 'material_id' => $materialId]
        );
    }
    
    public function create(array $data): bool {
        return $this->run("
            INSERT IGNORE INTO user_progress (
                enrollment_id,
                material_id,
                is_completed,
                completed_at
            ) VALUES (
                :enrollment_id,
                :material_id,
                0,
                NULL
            )
        ", [
            ':enrollment_id' => $data['enrollment_id'],
            ':material_id' => $data['material_id']
        ]);
    }

    public function markCompleted(string $enrollmentId): bool {
        return $this->run("
            UPDATE user_progress
            SET
                is_completed = 1,
                completed_at = NOW()
            WHERE enrollment_id = :enrollment_id
        ", [
            ':enrollment_id' => $enrollmentId
        ]);
    }

    public function getEnrollmentIdByUserProgressId(string $userProgressId): ?array {
        return $this->findByOne(
            'user_progress',
            ['enrollment_id'],
            ['user_progress_id' => $userProgressId] 
        );
    }

    public function updateLastOpenedMaterial(string $enrollmentId, string $materialId): bool {
        return $this->run("
            UPDATE user_progress
            SET opened_at = NOW()
            WHERE enrollment_id = :enrollment_id
            AND material_id = :material_id
        ", [
            ':enrollment_id' => $enrollmentId,
            ':material_id' => $materialId
        ]);
    }

    public function getAllRecentActivity(string $userId, int $limit = 3): array {
        return $this->many("
            SELECT
                course_id,
                course_name,
                material_id,
                title,
                opened_at
 
            FROM (
                SELECT
                    c.course_id,
                    c.course_name,
                    m.material_id,
                    m.title,
                    up.opened_at,
 
                    ROW_NUMBER() OVER (
                        PARTITION BY c.course_id
                        ORDER BY up.opened_at DESC
                    ) AS rn
 
                FROM user_progress up
 
                JOIN materials m
                    ON m.material_id = up.material_id
 
                JOIN courses c
                    ON c.course_id = m.course_id
 
                JOIN enrollments e
                    ON e.enrollment_id = up.enrollment_id
 
                WHERE e.user_id = :user_id
            ) recent
 
            WHERE rn = 1
 
            ORDER BY opened_at DESC
 
            LIMIT $limit
        ", [
            ':user_id' => $userId
        ]);
    }
}