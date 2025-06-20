<?php

namespace App\Services;

use App\Models\Payment;
use Exception;
use App\Services\BaseService;
class PaymentSV extends BaseService
{
    public function getQuery()
    {
        return Payment::query();
    }

    public function getAllPayments($params)
    {
        $query = $this->getQuery();

        return $this->getAll($query, $params);
    }

    public function createPayment($data){
       try {
            $query = $this->getQuery();
            $status = isset($data['status']) ? $data['status'] : 'pending';
            
            $payment = $query->create([
                'order_id' => $data['order_id'],
                'payment_date' => isset($data['payment_date']) ? $data['payment_date'] : now(),
                'status' => isset($data['status']) ? $data['status'] : 'pending',
                'payment_method' => isset($data['payment_method']) ? $data['payment_method'] : 'credit_card',
            ]);
            
            return $payment;
        } catch (Exception $e) {
            throw new Exception('Error creating payment: ' . $e->getMessage());
        }
    }


 
    public function getPaymentById($id)
    {
        try {
            $query = $this->getQuery();
            $payment = $query->where('id', $id)->get();
            return $payment;
        } catch (Exception $e) {
            throw new Exception('Error get payment: ' . $e->getMessage());
        }
    }


    public function updatePayment($id,$data){
       try {

            $query = $this->getQuery();
            $payment = $query->update($data, $id);
           
            return $payment;
        } catch (\Exception $e) {
            throw new \Exception('Error updating payment: ' . $e->getMessage(), 500);
        }
    }
 
}
