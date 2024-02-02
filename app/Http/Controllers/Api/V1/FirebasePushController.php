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
        ]);
        return response()->json([
            'message' => 'Successfully Updated FCM Token'
        ]);
    }


    public  function notification(array $data)
    {
        $message = CloudMessage::withTarget('token',  $data['fcm_token'])
        ->withNotification(Notification::create($data['title'], $data['body']))
        ->withData([
            'receiver_id' => $data['receiver_id'],
            'conversation_id' => $data['conversation_id']
        ]);
        $this->notification->send($message);
    }
}
