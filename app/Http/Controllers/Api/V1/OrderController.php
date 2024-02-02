<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Product;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return OrderResource::collection(Order::all());
    }

    public function getOrderAuth(int $id){
        $myProduits = Product::where('user_id', $id)->get();

        $dataOrder = [];
        
        if (isset($myProduits) && $myProduits->isNotEmpty()) {
            foreach ($myProduits as $myProduit) {
                if (isset($myProduit->order[0])) {
                    $dataOrders = $myProduit->order[0]->get() ?? [];
                    foreach ($dataOrders as $value) {
                        if ($value['user_id'] == auth()->user()->id && $value->product->user_id == $id) {
                            $dataOrder[] = $value;
                        }
                    }
                }
                
            }
            
        }else {
            $dataOrder = [];
        }
        
        //dd($dataOrder);
        $uniqueArray = array_unique($dataOrder);

        $uniqueArray = collect($uniqueArray)
        ->groupBy('numOrder')
        ->map(function ($group) {
            
            $count = $group->count();

            return $group->map(function ($item) use ($count) {
                $item['count'] = $count; // Nombre d'éléments dans ce groupe spécifique
                return $item;
            });
        })
        ->flatten(1) // Aplatit la collection d'un niveau
        ->values()
        ->all();

        $uniqueArray = collect($dataOrder)
        ->unique(function ($item) {
            return $item['numOrder'];
        })->values()->all();
        //dd($uniqueArray);
        return OrderResource::collection($uniqueArray);
    }
    
    public function getOrderAuthReceirve(int $id){
        $myProduits = Product::where('user_id', auth()->user()->id)->get();

        $dataOrder = [];
        
        if (isset($myProduits) && $myProduits->isNotEmpty()) {
            foreach ($myProduits as $myProduit) {
                if (isset($myProduit->order[0])) {
                    $dataOrders = $myProduit->order[0]->get() ?? [];
                    foreach ($dataOrders as $value) {
                        if ($value['user_id'] == $id && $value->product->user_id == auth()->user()->id ) {
                            $dataOrder[] = $value;
                        }
                    }
                }
                
            }
            
        }else {
            $dataOrder = [];
        }
        
        //dd($dataOrder);
        $uniqueArray = array_unique($dataOrder);

        $uniqueArray = collect($uniqueArray)
        ->groupBy('numOrder')
        ->map(function ($group) {
            
            $count = $group->count();

            return $group->map(function ($item) use ($count) {
                $item['count'] = $count; // Nombre d'éléments dans ce groupe spécifique
                return $item;
            });
        })
        ->flatten(1) // Aplatit la collection d'un niveau
        ->values()
        ->all();
        

        $uniqueArray = collect($dataOrder)->unique(function ($item) {
            return $item['numOrder'];
        })->values()->all();

        //dd($dataUser);
        return OrderResource::collection($uniqueArray);
    }

    public function getOrderWithUser(){
        $myOrders = Order::where('user_id', auth()->user()->id)->get();

        $dataUser = [];

        if (isset($myOrders)) {
            foreach ($myOrders as $myOrder) {
                $dataUser[] = $myOrder->product->user;
            }
        }
        $uniqueArray = array_unique($dataUser);
        return UserResource::collection($uniqueArray);
    }
    
    public function getOrderReceived(){
        $myProducts = Product::where('user_id', auth()->user()->id)->get();

        $dataUser = [];

        if (isset($myProducts)) {
            foreach ($myProducts as $myProduct) {
                foreach ($myProduct->order as $value) {
                    $dataUser[] = $value->user;
                }
            }
        }
        $uniqueArray = array_unique($dataUser);
        return UserResource::collection($uniqueArray);
    }

    public function getOrderDetail(){
        $myProduits = Product::where('user_id', auth()->user()->id)->get();

        $dataUser = [];
        
        if (isset($myProduits)) {
            foreach ($myProduits as $myProduit) {
                $dataUser[] = $myProduit->order[0];
            }
        }
        
        // $uniqueArray = array_unique($dataUser);
        //dd($dataUser);
        return OrderResource::collection($dataUser);
    }

    public function getImageOrderM(String $url)
    {
        if (file_exists(storage_path() . '/app/public/images/orders/'.$url)) {
            return response()->file(storage_path('app/public/images/orders/'.$url));
            //return response()->file(storage_path('app/public/images/products/'.$image));
        }
        return response()->file(storage_path('app/public/images/profils/defaultAvatar.jpg'));
    }

    public function getOrderImage(String $url)
    {
        if (Order::where('imageUrl', 'public/images/orders/'.$url)->first()->imageUrl != null) {
            return response()->file(storage_path('app/public/images/orders/'.$url));
            //return response()->file(storage_path('app/public/images/products/'.$image));
        }
        return response()->file(storage_path('app/public/images/profils/defaultAvatar.jpg'));
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

        //$validitedata['numOrder'] = 'C'.date('Ymd').$this->generateUniqueCode();
        $validitedata['user_id'] = auth()->user()->id;
        $filename = $validitedata['numOrder'].$validitedata['imageUrl'];

        if (!Storage::exists('public/images/orders/')) {
            Storage::makeDirectory('public/images/orders/');
        }
        if (Storage::exists('public/images/orders/') && Storage::exists('public/images/products/'.$validitedata['imageUrl'])) {
            Storage::copy('public/images/products/'.$validitedata['imageUrl'], 'public/images/orders/' . $filename);
        } else {
           echo 'non file';
        }
        $validitedata['imageUrl'] = "public/images/orders/".$validitedata['numOrder'].$validitedata['imageUrl'];
        $order = Order::create($validitedata);

        
        return response()->json([
            'status' => true,
            'message' => 'Commande est crée avec succès',
            'orders' => OrderResource::make($order)
            // [
            //     "order" => OrderResource::make($order),

            // ],
        ]);
    }

    /**
     * Write referal_code on Method
     *
     * @return response()
    */

    // public function generateUniqueCode()
    // {
    //     do {
    //         $numOrder = random_int(1000, 9999);
    //     } while (Order::where("numOrder", "=", $numOrder)->first());

    //      return $numOrder;

    // }

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
