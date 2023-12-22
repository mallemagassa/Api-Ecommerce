<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Messaging\CloudMessage;

class SendVerificationCode extends Controller
{
    // public function sendVerificationCode(Request $request)
    // {

    //     $phoneNumber = $request->all();

    //     $serviceAccountPath = storage_path('app/config/firebase/otp-phone-d6ab0-firebase-adminsdk-5zguv-3ded9b0c79.json');

    //     $phoneNumber = $request->input('phone_number');

    //     $firebase = (new Factory())
    //         ->withServiceAccount($serviceAccountPath)
    //         ->createAuth();

    //     $auth = $firebase->Auth();

    //     try {
    //         // Envoi du code OTP au numéro de téléphone spécifié
    //         $auth->startVerification($phoneNumber, [
    //             'phoneNumber' => $phoneNumber,
    //             'recaptchaToken' => null, // Facultatif : si vous utilisez Recaptcha
    //             'app' => [
    //                 'apiKey' => config('services.firebase.api_key'),
    //                 'appId' => config('services.firebase.app_id'),
    //                 'projectId' => config('services.firebase.project_id'),
    //             ],
    //         ]);

    //         return response()->json(['message' => 'OTP sent successfully']);
    //     } catch (\Exception $e) {
    //         // Gestion des erreurs lors de l'envoi du code OTP
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }
}
