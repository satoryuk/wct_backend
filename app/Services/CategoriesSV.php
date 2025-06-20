<?php

namespace App\Services;

use App\Models\Category;
use Exception;
use App\Services\BaseService;
class CategoriesSV extends BaseService
{
    public function getQuery()
    {
        return Category::query();
    }

    public function getAllCategorys($params)
    {
        $query = $this->getQuery();

        return $this->getAll($query, $params);
    }

    public function createCategory($data){
       try {
            $query = $this->getQuery(); 
            
            // $active = isset($data['active']) ? $data['active'] : 1;

            $category = $query->create([
                'category_name'     => $data['category_name'],
            ]);
            
            return $category;
        } catch (\Exception $e) {
            throw new \Exception('Error creating category: ' . $e->getMessage());
        }
    }


 
    public function getCategoryById($id)
    {
        $query =  $this->getQuery();
        $category = $query->where('category_id', $id)->first();
        return $category;
    }


    public function updateCategory($data, $id){
       try {
            $query = $this->getQuery();
            $category = $query ->where('category_id', $id)->first();
            if (!$category) {
                throw new \Exception('Category', 404);
            }
           $category->update($data);
            return $category;
        } catch (\Exception $e) {
            throw new \Exception('Error updating Category: ' . $e->getMessage(), 500);
        }
    }
}