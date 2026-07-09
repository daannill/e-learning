<?php

namespace App\Models;

use Core\Model;

class MaterialVideosModel extends Model {
    
    public function findMaterialVideoByMaterialId(string $materialId): ?array {
        return $this->findByOne(
            'material_videos',
            ['video_id', 'material_id', 'source_type', 'video_url'],
            ['material_id' => $materialId]
        );
    }
}