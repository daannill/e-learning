<?php

namespace App\Models;

use Core\Model;

class MaterialVideosModel extends Model {
    
    public function findMaterialVideo(string $materialId): ?array {
        return $this->findByOne(
            'material_videos',
            ['video_id', 'material_id', 'source_type', 'video_url'],
            ['material_id' => $materialId]
        );
    }

    public function create(array $data): bool {
        return $this->insert('material_videos', [
            'material_id' => $data['material_id'],
            'source_type' => $data['source_type'],
            'video_url' => $data['video_url']
        ]);
    }

    public function updateMaterialVideo(string $materialId, array $data): bool {
        return $this->update(
            'material_videos',
            [
                'source_type' => $data['source_type'],
                'video_url'   => $data['video_url']
            ],
            [
                'material_id' => $materialId
            ]
        );
    }

    public function deleteMaterialVideo(string $materialId): bool {
        return $this->delete(
            'material_videos',
            [
                'material_id' => $materialId
            ]
        );
    }
}