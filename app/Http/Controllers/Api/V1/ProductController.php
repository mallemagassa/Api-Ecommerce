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

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProductResource::collection(Product::all());
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
        
        $product = Product::create($validitedata);

        return response()->json([
            'status' => true,
            'message' => 'Product est crée avec succès',
            'data' => [
                "products"=> ProductResource::make($product),

            ],
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
       return ProductResource::make($product);
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
