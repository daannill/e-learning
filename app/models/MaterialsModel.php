<?php

namespace App\Models;

use Core\Model;

class MaterialsModel extends Model {
    
    public function getAllMaterials(string $courseId, ?string $enrollmentId = null): array {
        $progressColumn = '';
        $progressJoin = '';
        $params = [':course_id' => $courseId];
 
        if ($enrollmentId !== null) {
            $progressColumn = ",
                (up.material_id IS NOT NULL) AS is_completed";
 
            $progressJoin = "
                LEFT JOIN user_progress up
                    ON up.material_id = m.material_id
                    AND up.enrollment_id = :enrollment_id
                    AND up.is_completed = 1";
 
            $params[':enrollment_id'] = $enrollmentId;
        }
 
        return $this->many("
            SELECT
                m.material_id,
                m.title,
                m.type,
                m.order_index,
                m.created_at,
                m.updated_at
                $progressColumn
 
            FROM materials m
            $progressJoin
 
            WHERE m.course_id = :course_id
 
            ORDER BY m.order_index ASC
        ", $params);
    }

    public function findMaterial(string $materialId): ?array {
        return $this->findByOne(
            'materials',
            ['material_id', 'course_id', 'title', 'type', 'order_index', 'created_at', 'updated_at'],
            ['material_id' => $materialId]
        );
    }

     public function findMaterialIdByCourseIdAndOrderIndex(string $courseId, int $orderIndex): ?string {
        $result = $this->findByOne(
            'materials',
            ['material_id'],
            ['course_id' => $courseId, 'order_index' => $orderIndex]
        );
 
        return $result['material_id'] ?? null;
    }

    public function findPrevAndNextMaterialId(string $courseId, string $materialId): ?array {
        return $this->one("
            SELECT
                COALESCE(
                    (
                        SELECT material_id
                        FROM materials
                        WHERE course_id = :course_id
                        AND order_index < current_material.order_index
                        ORDER BY order_index DESC
                        LIMIT 1
                    ), 0
                ) AS previous_material_id,
 
                COALESCE(
                    (
                        SELECT material_id
                        FROM materials
                        WHERE course_id = :course_id
                        AND order_index > current_material.order_index
                        ORDER BY order_index ASC
                        LIMIT 1
                    ), 0
                ) AS next_material_id
 
            FROM materials current_material
 
            WHERE current_material.material_id = :material_id
 
            LIMIT 1
        ", [
            ':course_id' => $courseId,
            ':material_id' => $materialId
        ]);
    }
}