<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Display a listing of the resource Seller.
     */
    public function seller()
    {
        return UserResource::collection(User::where('isSeller', '1')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }


    public function createCompteSeller(Request $request)
    {
        try {
    
            $input = $request->all();

            //dd($input);
    
            $validator = FacadesValidator::make($input, [
                "phone" => 'phone:INTERNATIONAL,ML',
                "nameCom" => 'required|string|max:255',
                "status" => 'required|string|max:2000',
                "address" => 'required|string|max:255',
                "isSeller" => 'bool',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de Validation',
                    'errors' => $validator->errors(),
                ], 422);
            }
            
            
            $authUser = User::where('id', Auth::id())->first();
            $sellerValid = $validator->valid();
            if ($authUser) {
                $sellerValid['phone'] = $authUser->phone;
                $sellerValid['isSeller'] = true;

                $authUser->update($sellerValid);
        
                 return response()->json([
                    'status' => true,
                    'message' => 'Votre compte vendeur est créer avec succèe !',
                 ]);
            }else{
                return;
            }
            
             
    
        } catch (\Throwable $th) {
    
             return response()->json([
                 'status' => false,
                 'message' => $th->getMessage(),
             ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return UserResource::make($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        //dd($request->validated());
        $input = $request->all();
        $user->update($input);

        return UserResource::make($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return UserResource::make($user);
    }
}
