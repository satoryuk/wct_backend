<?php

namespace App\Services;

use App\Models\Inventory;
use Exception;
use App\Services\BaseService;
class InventorySV extends BaseService
{
    public function getQuery()
    {
        return Inventory::query();
    }

    public function getAllInventory($params)
    {
        $query = $this->getQuery();

        return $this->getAll($query, $params);
    }

    // public function getAllInventory($params)
    // {
    //     // Example implementation
    //     $query = Inventory::query();

    //     if (!empty($params['filterBy'])) {
    //         foreach ($params['filterBy'] as $field => $value) {
    //             $query->where($field, $value);
    //         }
    //     }

    //     return $query->paginate($params['perPage'] ?? 10);
    // }

    public function createInventory($data){
       try {
            $query = $this->getQuery(); 
            
            // $active = isset($data['active']) ? $data['active'] : 1;

            $inventory = $query->create([
                'Inventory_name'     => $data['Inventory_name'],
            ]);
            
            return $inventory;
        } catch (\Exception $e) {
            throw new \Exception('Error creating Inventory: ' . $e->getMessage());
        }
    }


 
    public function getInventoryById($id)
    {
        $query =  $this->getQuery();
        $Inventory = $query->where('Inventory_id', $id)->first();
        return $Inventory;
    }


    public function updateInventory($data, $id){
       try {
            $query = $this->getQuery();
            $Inventory = $query ->where('Inventory_id', $id)->first();
            if (!$Inventory) {
                throw new \Exception('Inventory', 404);
            }
           $Inventory->update($data);
            return $Inventory;
        } catch (\Exception $e) {
            throw new \Exception('Error updating Inventory: ' . $e->getMessage(), 500);
        }
    }
}