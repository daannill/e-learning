<?php

namespace App\Models;

use Core\Model;

class AssignmentsModel extends Model {

    public function create(array $data): bool {
        return $this->insert('assignments', [
            'material_id'    => $data['material_id'],
            'description'    => $data['description'],
            'passing_score'  => $data['passing_score'],
            'deadline_at'    => $data['deadline_at']
        ]);
    }

    public function findAssignment(string $materialId): ?array {
        return $this->findByOne(
            'assignments',
            ['*'],
            ['material_id' => $materialId]
        );
    }
}