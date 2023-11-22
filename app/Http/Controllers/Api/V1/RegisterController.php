<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class RegisterController extends Controller
{
    public function register(Request $request){
        try {
    
             $input = $request->all();
    
             $validator = FacadesValidator::make($input, [
                "phone"=> 'required|string|max:255',
             ]);
             
             if ($validator->fails()) {
                 return response()->json([
                     'status' => false,
                     'message' => 'Erreur de Validation',
                     'errors' => $validator->errors(),
                 ], 422);
             }
    
             $input['password'] = Hash::make($request->password);
             
             $user = User::create($input);
    
             return response()->json([
                 'status' => true,
                 'message' => 'Utilisateur créer avec succèe',
                 'info_user' => UserResource::make($user),
                 'data' => [
                     "token"=> $user->createToken('auth_user')->plainTextToken,
                     "token_type"=> "Bearer",
    
                 ],
             ]);
    
        } catch (\Throwable $th) {
    
             return response()->json([
                 'status' => false,
                 'message' => $th->getMessage(),
             ], 500);
        }

    }

    public function logout(Request $request){
          
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Vous êtes déconnecter !',
            
        ]);
     }

}
