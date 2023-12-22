<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('restrictRole:1')->only('store', 'update', 'destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProductResource::collection(Product::all());
    }


    public function myProducts(){
        $myProducts = Product::where('user_id', auth()->user()->id)->get();

        $dataProduct = [];

        if (isset($myProducts)) {
            foreach ($myProducts as $myProduct) {
                $dataProduct[] = $myProduct;
            }
        }
        return $dataProduct;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
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

        if ($request->hasFile('image')) {
            $validitedata['image'] = $request->file('image')->store('public/images/products');
        }

        $validitedata['user_id'] = Auth::id();
        
        $product = Product::create($validitedata);

        return response()->json([
            'status' => true,
            'message' => 'Product est crée avec succès',
            'data' => [
                "products"=> ProductResource::make($product),

            ],
        ]);

    }

    public function sellerProduct(int $id)
    {
        return ProductResource::collection(Product::where('user_id', $id)->get());
    }

    public function getProductImage(String $url)
    {
        if (Product::where('image', 'public/images/products/'.$url)->first()->image) {
            return response()->file(storage_path('app/public/images/products/'.$url));
            //return response()->file(storage_path('app/public/images/products/'.$image));
        }
        return response()->file(storage_path('app/public/images/profils/defaultAvatar.jpg'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
       return  response()->json([
                'status' => true,
                'message' => 'Product est crée avec succès',
                'data' => [
                    "product"=> ProductResource::make($product),
                    "user"=> UserResource::make($product->user),

                ],
            ]);
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
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

        if ($request->hasFile('image')) {
            Storage::delete($product->image);
            $validitedata['image'] = $request->file('image')->store('public/images/products');
        }

        $validitedata['user_id'] = Auth::id();

        $product->update($validitedata);

        return response()->json([
            'status' => true,
            'message' => 'Product est modifier avec succès',
            'data' => [
                "products"=> ProductResource::make($product),

            ],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product est crée avec succès',
            'data' => [
                "products"=> ProductResource::make($product),

            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        Storage::delete($product->image);

        return response()->json([
            'status' => true,
            'message' => 'Product est supprimer avec succès',
            'data' => [
                "products"=> ProductResource::make($product),

            ],
        ]);
    }
}
