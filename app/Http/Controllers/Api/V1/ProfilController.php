<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Profil;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfilRequest;
use App\Http\Resources\ProfilResource;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProfilResource::collection(Profil::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProfilRequest $request)
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
            $validitedata['image'] = $request->file('image')->store('public/images/profils');
        }

        $profil = Profil::create($validitedata);
        //dd($profil);

        return response()->json([
            'status' => true,
            'message' => 'Profile est crée avec succès',
            'data' => [
                "profil"=> ProfilResource::make($profil),

            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Profil $profil)
    {
        return ProfilResource::make($profil);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Profil $profil)
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
            Storage::delete($profil->image);
            $validitedata['image'] = $request->file('image')->store('public/images/profils');
        }

        $profil = Profil::create($validitedata);
        //dd($profil);

        return response()->json([
            'status' => true,
            'message' => 'Profile est crée avec succès',
            'data' => [
                "profil"=> ProfilResource::make($profil),

            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profil $profil)
    {
        $profil->delete();
        Storage::delete($profil->image);

        return response()->json([
            'status' => true,
            'message' => 'Product est supprimer avec succès',
            'data' => [
                "products"=> ProfilResource::make($profil),

            ],
        ]);
    }
}
