<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use App\Services\BaseService;
class ProductSV extends BaseService
{
    protected function getQuery()
    {
        return Product::query()->with("category");
    }

    public function getAllProducts($params)
    {
        $query = $this->getQuery();

        return $this->getAll($query, $params);
    }

    public function createProduct($data){
       try {
            $query = $this->getQuery();
            $status = isset($data['status']) ? $data['status'] : 1;

            $product = $query->create([
                'product_name'     => $data['product_name'],
                'description'      => $data['description'],
                'price'            => $data['price'],
                'stock_qty'        => $data['stock_qty'],
                'brand_id'         => $data['brand_id'],
                'category_id'      => $data['category_id'],
                'status'           => $status,
                'image'            => isset($data['image']) ? $data['image'] : null,
            ]);
            
            return $product;
        } catch (\Exception $e) {
            throw new \Exception('Error creating product: ' . $e->getMessage(), 500);
        }
    }

 
    public function getProductById($id)
    {
        $query =  $this->getQuery();
        $product = $query->where('product_id', $id)->first();
        return $product;
    }                 

    public function deactivateProduct($id)
    {
        try {
            $product = $this->getQuery()->findOrFail($id);
            $newStatus = $product->status == 1 ? 0 : 1; // Toggle status

            $this->getQuery()->where('product_id', $id)->update(['status' => $newStatus]);
            $product->refresh(); // Refresh the product instance
            return $product;
        } catch (\Exception $e) {
            throw new \Exception('Error toggling user status: ' . $e->getMessage(), 500);
        }
    }

    public function updateProduct($data, $id){
       try {

            $query = $this->getQuery();
            $product = $query->where('product_id', $id)->first();
            if (!$product) {
                throw new \Exception('Product not found', 404);
            }
            $product->update($data);       
            return $product;
        } catch (\Exception $e) {
            throw new \Exception('Error updating product: ' . $e->getMessage(), 500);
        }
    }
 
}
