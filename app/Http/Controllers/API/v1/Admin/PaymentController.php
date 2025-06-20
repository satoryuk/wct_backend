<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Models\Payment;
use App\Http\Requests\StoreProducRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Services\PaymentSV;
use App\Http\Controllers\API\v1\BaseAPI;

class PaymentController extends BaseAPI
{
    protected $paymentService;
    public function __construct()
    {
        $this->paymentService = new PaymentSV();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Payment::with('order')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreProducRequest $request)
    // {
    //     $data = $request->validated();
    //     $payment = $this->paymentService->createPayment($data);
    //     return $this->successResponse($payment, 'Payment created successfully');
    // }

    public function store(StoreProducRequest $request)
    {
        $data = $request->validated();
        $data['payment_date'] = $data['payment_date'] ?? now();
        $data['payment_status'] = $data['payment_status'] ?? 'pending';
        
        $payment = $this->paymentService->createPayment($data);
        
        return $this->successResponse($payment, 'Payment created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        return response()->json($payment->load('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $payment->update($request->validated());
        return response()->json($payment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return response()->json(['message' => 'Payment deleted successfully!', 204]);
    }
}
