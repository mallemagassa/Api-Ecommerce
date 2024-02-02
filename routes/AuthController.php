<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{

    public function login(Request $request){

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
            
            $user = User::where('phone', $request->phone)->first();

            if ($user) {
                Auth::login($user);

                return response()->json([
                    'status' => true,
                    'message' => 'Connexion Réussis !',
                    'info_user'  => UserResource::make(Auth::user()),
                    'data' => [
                        "token"=> $user->createToken('auth_user')->plainTextToken,
                        "token_type"=> "Bearer",
                    ],
                ]);
            }
            
            
            return response()->json([
                'status' => false,
                'message' => 'Essayer de vous s\'inscrire votre numéro n\'exigiste pas',
                'errors' => $validator->errors(),
            ], 401);
 
 
        } catch (\Throwable $th) {
 
             return response()->json([
                 'status' => false,
                 'message' => $th->getMessage(),
             ], 500);
        }
        
    }

    public function verifyNumberAuth(Request $request) {
        $input = $request->all();

        if (!User::where('phone', $input['phone'])->first()) {
            return response()->json([
                'status' => false,
                'message' => 'Cette numéro ne existe pas essayer de vous Inscrire',
            ]);

        }else{
            return response()->json([
                'status' => true,
                'message' => 'Cool',
            ]);
        }
    }
   
}
