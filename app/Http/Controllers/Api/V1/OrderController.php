<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Requests\UpdateOrderRequest;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return OrderResource::collection(Order::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        $validitedata = $request->validated();

        //dd($validitedata);

        if (!$validitedata) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur de Validation',
                'errors' => $request->errors(),
            ], 422);
        }

        $validitedata['numOrder'] = 'C'.date('Ymd').$this->generateUniqueCode();
        $product = Order::create($validitedata);

        return response()->json([
            'status' => true,
            'message' => 'Commande est crée avec succès',
            'data' => [
                "orders"=> OrderResource::make($product),

            ],
        ]);
    }

    /**
     * Write referal_code on Method
     *
     * @return response()
    */

    public function generateUniqueCode()
    {
    do {
        $numOrder = random_int(1000, 9999);
    } while (Order::where("numOrder", "=", $numOrder)->first());

    return $numOrder;

    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return OrderResource::make($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $validitedata = $request->validated();

        //dd($validitedata);

        if (!$validitedata) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur de Validation',
                'errors' => $request->errors(),
            ], 422);
        }
        
        //$validitedata['numOrder'] = 'C'.date('Ymd').$this->generateUniqueCode();
        $order->update($validitedata);

        return response()->json([
            'status' => true,
            'message' => 'Commande est modifier avec succès',
            'data' => [
                "orders"=> OrderResource::make($order),

            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'status' => true,
            'message' => 'Commande est supprimer avec succès',
            'data' => [
                "orders"=> OrderResource::make($order),

            ],
        ]);
    }
}
