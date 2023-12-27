<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebasePushController extends Controller
{
    protected $notification;
    
    public function __construct()
    {
        $this->notification = Firebase::messaging();
    }

    public function setToken(Request $request)
    {
        $token = $request->input('fcm_token');
        $request->user()->update([
            'fcm_token' => $token
        ]); //Get the currrently logged in user and set their token
        return response()->json([
            'message' => 'Successfully Updated FCM Token'
        ]);
    }


    public  function notification(array $data)
    {
        //$FcmToken = auth()->user()->fcm_token;
        $notifications = Notification::create($data['title'], $data['body']);
        
        // 'token' => $FcmToken,
        $message = CloudMessage::new()
        ->withNotification($notifications)
        ->withTarget('token',  $data['fcm_token'])
        ->withData([
            'receiver_id' => $data['receiver_id'],
            'conversation_id' => $data['conversation_id']
        ]);

        $this->notification->send($message, $data['fcm_token']);
    }
}
