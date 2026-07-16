<?php

namespace App\Models;

use Core\Model;

class CategoriesModel extends Model {
    
    public function getAllCategories() {
        return $this->findMany('categories', ['category_id', 'category_name']);
    }
}