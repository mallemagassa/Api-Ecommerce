<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class RegisterController extends Controller
{
    public function register(Request $request){
        try {
    
            $input = $request->all();
    
             $validator = FacadesValidator::make($input, [
                "phone" => 'required|phone:INTERNATIONAL,ML',
             ]);
             
             if ($validator->fails()) {
                 return response()->json([
                     'status' => false,
                     'message' => 'Erreur de Validation',
                     'errors' => $validator->errors(),
                 ], 422);
             }
            
             
            $user = User::create($input);
    
             return response()->json([
                 'status' => true,
                 'message' => 'Utilisateur créer avec succèe',
                 'info_user'  => UserResource::make($user),
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

    public function verifyNumber(Request $request) {
        $input = $request->all();

        if (User::where('phone', $input['phone'])->first()) {
            return response()->json([
                'status' => false,
                'message' => 'Cette numéro existe déjá essayer de vous authentifiez',
            ]);

        }else{
            return response()->json([
                'status' => true,
                'message' => 'Cool',
            ]);
        }
    }

    public function logout(Request $request){
          
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Vous êtes déconnecter !',
            
        ]);
     }


    public function profile(){
        return response()->json([
            "status" => "true",
            "message" => "Bienvenue à votre Profile",
            "data" => [UserResource::make(Auth::user())]
        ]);
    }

}
