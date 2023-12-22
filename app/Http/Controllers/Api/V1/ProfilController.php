<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Profil;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfilRequest;
use App\Http\Resources\ProfilResource;
use Illuminate\Support\Facades\Auth;
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

    public function getProfilImage()
    {
        //dd(Profil::where('user_id', Auth::id())->first()->image);
        if (Profil::where('user_id', Auth::id())->first() != null) {
            $image = substr(Profil::where('user_id', Auth::id())->first()->image, 22);
            return response()->file(storage_path('app/public/images/profils/'.$image));
        }
        return response()->file(storage_path('app/public/images/profils/defaultAvatar.jpg'));
    }


    public function getProfilUser(String $url)
    {
        if (isset(Profil::where('image', 'public/images/profils/'.$url)->first()->image)) {
            return response()->file(storage_path('app/public/images/profils/'.$url));
            //return response()->file(storage_path('app/public/images/products/'.$image));
        }
        return response()->file(storage_path('app/public/images/profils/defaultAvatar.jpg'));
    }


    // public function getProfilUser(int $id)
    // {
    //     if (Profil::where('user_id', $id)->first()->image) {
    //         return response()->file(storage_path('app/public/images/profils/'.substr(Profil::where('user_id', $id)->first()->image, 22)));
    //         //return response()->file(storage_path('app/public/images/products/'.$image));
    //     }
    //     return response()->file(storage_path('app/public/images/profils/defaultAvatar.jpg'));
    // }

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

        $validitedata['user_id'] = Auth::id(); 

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
     * if profil alydear
     */

    public function verifyImge(Request $request){
        if (Profil::where('user_id', Auth::id())->first()) {
            return response()->json([
                'status' => true,
                'message' => 'Cool !!!',
                'id' => Profil::where('user_id', Auth::id())->first()->id
            ]);

        }else{
            return response()->json([
                'status' => false,
                'message' => 'no !!!',
            ]);
        }
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
    public function update(ProfilRequest $request, Profil $profil)
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
        $validitedata['user_id'] = Auth::id(); 

        $profil->update($validitedata);
        //dd($profil);

        return response()->json([
            'status' => true,
            'message' => 'Profile est modifier avec succès',
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
