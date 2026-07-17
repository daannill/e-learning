<?php

namespace App\Models;

use Core\Model;

class MaterialTextsModel extends Model {

    public function findMaterialText(string $materialId): ?array {
        return $this->findByOne(
            'material_texts',
            ['text_id', 'material_id', 'content'],
            ['material_id' => $materialId]
        );
    }

    public function create(array $data): bool {
        return $this->insert('material_texts', [
            'material_id' => $data['material_id'],
            'content' => $data['content']
        ]);
    }
}