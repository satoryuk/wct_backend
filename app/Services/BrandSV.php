<?php

namespace App\Services;

use App\Models\Brand;
use Exception;
use App\Services\BaseService;
class BrandSV extends BaseService
{
    public function getQuery()
    {
        return Brand::query();
    }

    public function getAllBrands($params)
    {
        $query = $this->getQuery();

        return $this->getAll($query, $params);
    }

    public function createbrand($data){
       try {
            $query = $this->getQuery();
            // $status = isset($data['status']) ? $data['status'] : 1;

            $brand = $query->create([
                'brand_name'     => $data['brand_name'],
            ]);
            
            return $brand;
        } catch (\Exception $e) {
            throw new \Exception('Error creating brand: ' . $e->getMessage());
        }
    }


 
    public function getBrandById($id)
    {
        $query =  $this->getQuery();
        $brand = $query->where('brand_id', $id)->first();
        return $brand;
    }


    public function updateBrand($data, $id){
       try {
            $query = $this->getQuery();
            $brand = $query->where('brand_id', $id)->first();
            if (!$brand) {
                throw new \Exception('Brand not found', 404);
            }
           $brand->update($data);
            return $brand;
        } catch (\Exception $e) {
            throw new \Exception('Error updating brand: ' . $e->getMessage(), 500);
        }
    }
 
}
