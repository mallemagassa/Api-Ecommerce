<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Events\MessageWasPosted;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessPendingMessages;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Exception\Messaging\NotFound;
use App\Http\Controllers\Api\V1\FirebasePushController;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Message::where('conversation_id', 0)->get();
        //dd(count(Message::where('conversation_id', 2)->get()));
        return MessageResource::collection(Message::all());
    }

    public function getImageMessage(String $url)
    {
        if (file_exists(storage_path() . '/app/public/images/messages/media/'.$url)) {
            return response()->file(storage_path('app/public/images/messages/media/'.$url));
            //return response()->file(storage_path('app/public/images/products/'.$image));
        }
        return response()->file(storage_path('app/public/images/profils/defaultAvatar.jpg'));
    }
    
    public function getImageProductM(String $url)
    {
        if (file_exists(storage_path() . '/app/public/images/products/'.$url)) {
            return response()->file(storage_path('app/public/images/products/'.$url));
            //return response()->file(storage_path('app/public/images/products/'.$image));
        }
        return response()->file(storage_path('app/public/images/profils/defaultAvatar.jpg'));
    }


    public function deleteImageMessage(String $url)
    {
        if (file_exists(storage_path() . '/app/public/images/messages/media/'.$url)) {
            Storage::delete('public/images/messages/media/'.$url);
            return response()->json([
                'status' => true,
                'message' => 'Image est supprimer avec succes',
            ], 422);
            //response()->file();
            //return response()->file(storage_path('app/public/images/products/'.$image));
        }
        return null; //response()->file(storage_path('app/public/images/profils/defaultAvatar.jpg'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MessageRequest $request)
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

        $checkConversation = Conversation::where('receiver_id', auth()->user()->id)->where("sender_id", $validitedata['receiver_id'])->orWhere('receiver_id', $validitedata['receiver_id'])->where("sender_id", auth()->user()->id)->get(); //$validitedata['$validitedata'])
        $firebasePushController = new FirebasePushController();
        if (count($checkConversation) == 0) {
                
            $createdconversation = Conversation::create([
                'sender_id' => auth()->user()->id,//$validitedata['sender_id'],
                'receiver_id' => $validitedata['receiver_id'],
            ]);

            if ($request->hasFile('media')) {
                $validitedata['media'] = $request->file('media')->store('public/images/messages/media');
            }
            if ($request->hasFile('video')) {
                $validitedata['video'] = $request->file('video')->store('public/images/messages/video');
            }

            if ($request->hasFile('document')) {
                $validitedata['document'] = $request->file('document')->store('public/images/messages/documents');
            }

            if (isset($validitedata['text']) && isset($validitedata['media'])) {

                $filename = $validitedata['numOrder'].$validitedata['media'];

                if (!Storage::exists('public/images/messages/media/')) {
                    Storage::makeDirectory('public/images/messages/media/');
                }
                if (Storage::exists('public/images/messages/media/') && Storage::exists('public/images/orders/'.$validitedata['media'])) {
                    Storage::copy('public/images/orders/'.$validitedata['media'], 'public/images/messages/media/' . $filename);
                } else {
                   echo 'non file';
                }
                
                $validitedata['media'] = "public/images/messages/media/".$validitedata['numOrder'].$validitedata['media'];

                $createdMessage = Message::create([
                    'sender_id' => auth()->user()->id,
                    'receiver_id' => $validitedata['receiver_id'],
                    'type' => $validitedata['type'],
                    'text' => isset($validitedata['text']) ? $validitedata['text'] : null,
                    'media' => isset($validitedata['media']) ? $validitedata['media'] : null,
                    'video' => isset($validitedata['video']) ? $validitedata['video'] : null,
                    'document' => isset($validitedata['document']) ? $validitedata['document'] : null,
                    'numOrder' => isset($validitedata['numOrder']) ? $validitedata['numOrder'] : null,
                    'conversation_id' =>$checkConversation[0]->id,
                ]);
                
                event(new MessageWasPosted($createdMessage));
                
            }else{
                $createdMessage = Message::create([
                    'sender_id' => auth()->user()->id,
                    'receiver_id' => $validitedata['receiver_id'],
                    'type' => $validitedata['type'],
                    'text' => isset($validitedata['text']) ? $validitedata['text'] : null,
                    'media' => isset($validitedata['media']) ? $validitedata['media'] : null,
                    'video' => isset($validitedata['video']) ? $validitedata['video'] : null,
                    'document' => isset($validitedata['document']) ? $validitedata['document'] : null,
                    'numOrder' => isset($validitedata['numOrder']) ? $validitedata['numOrder'] : null,
                    'conversation_id' =>$checkConversation[0]->id,
                ]);
                
                event(new MessageWasPosted($createdMessage));
            }

            //ProcessPendingMessages::dispatch($createdMessage);

            try {
                // Vérification du token avant d'envoyer le message
                $user = User::find($createdMessage->receiver_id);
    
                if ($user && $user->fcm_token) {
                    // Envoyer le message car le token est valide
                    if ($createdMessage->media != null) {
                        $firebasePushController->notification([
                            'fcm_token' => $user->fcm_token,
                            'title' => 'Message',
                            'body' => $createdMessage->media,
                            'receiver_id' => $createdMessage->receiver_id,
                            'conversation_id' => $createdMessage->conversation_id,
                        ]);
                    } else {
                        $firebasePushController->notification([
                            'fcm_token' => $user->fcm_token,
                            'title' => 'Message',
                            'body' => $createdMessage->text,
                            'receiver_id' => $createdMessage->receiver_id,
                            'conversation_id' => $createdMessage->conversation_id,
                        ]);
                    }
                } else {
                   echo response()->json([
                        'status' => true,
                        'message' => 'Token introuvable',
                        'data' => [
                            "message"=> MessageResource::make($createdMessage),
                        ],
                    ]);
                }
            } catch (NotFound $e) {
                // Gérer l'exception, par exemple, enregistrer le message dans les logs
                Log::error('Erreur Firebase : ' . $e->getMessage());
                // ...
            }
            
            //$this->sendNotificationToReceiver($createdMessage);

            return response()->json([
                'status' => true,
                'message' => 'Message est crée avec succès',
                'data' => [
                    "message"=> MessageResource::make($createdMessage),
                ],
            ]);

        }else if (count($checkConversation) >= 1) {
            //dd($checkConversation[0]->id);
            if ($request->hasFile('media')) {
                $validitedata['media'] = $request->file('media')->store('public/images/messages/media');
            }

            if ($request->hasFile('video')) {
                $validitedata['video'] = $request->file('video')->store('public/images/messages/video');
            }

            if ($request->hasFile('document')) {
                $validitedata['document'] = $request->file('document')->store('public/images/messages/documents');
            }

            if ($checkConversation[0]->is_sender_delete == 1 || $checkConversation[0]->is_receiver_delete == 1 ) {
                $checkConversation[0]->is_sender_delete = false;
                $checkConversation[0]->is_receiver_delete = false;
    
                $checkConversation[0]->save();
            }
            
            if (isset($validitedata['text']) && isset($validitedata['media'])) {

                $filename = $validitedata['numOrder'].$validitedata['media'];

                if (!Storage::exists('public/images/messages/media/')) {
                    Storage::makeDirectory('public/images/messages/media/');
                }
                if (Storage::exists('public/images/messages/media/') && Storage::exists('public/images/orders/'.$validitedata['media'])) {
                    Storage::copy('public/images/orders/'.$validitedata['media'], 'public/images/messages/media/' . $filename);
                } else {
                   echo 'non file';
                }
                
                $validitedata['media'] = "public/images/messages/media/".$validitedata['numOrder'].$validitedata['media'];

                $createdMessage = Message::create([
                    'sender_id' => auth()->user()->id,
                    'receiver_id' => $validitedata['receiver_id'],
                    'type' => $validitedata['type'],
                    'text' => isset($validitedata['text']) ? $validitedata['text'] : null,
                    'media' => isset($validitedata['media']) ? $validitedata['media'] : null,
                    'video' => isset($validitedata['video']) ? $validitedata['video'] : null,
                    'document' => isset($validitedata['document']) ? $validitedata['document'] : null,
                    'numOrder' => isset($validitedata['numOrder']) ? $validitedata['numOrder'] : null,
                    'conversation_id' =>$checkConversation[0]->id,
                ]);
                
                event(new MessageWasPosted($createdMessage));
                
            }else{
                $createdMessage = Message::create([
                    'sender_id' => auth()->user()->id,
                    'receiver_id' => $validitedata['receiver_id'],
                    'type' => $validitedata['type'],
                    'text' => isset($validitedata['text']) ? $validitedata['text'] : null,
                    'media' => isset($validitedata['media']) ? $validitedata['media'] : null,
                    'video' => isset($validitedata['video']) ? $validitedata['video'] : null,
                    'document' => isset($validitedata['document']) ? $validitedata['document'] : null,
                    'numOrder' => isset($validitedata['numOrder']) ? $validitedata['numOrder'] : null,
                    'conversation_id' =>$checkConversation[0]->id,
                ]);
                
                event(new MessageWasPosted($createdMessage));
            }


            try {
                $user = User::find($createdMessage->receiver_id);
    
                if ($user && $user->fcm_token) {

                    if ($createdMessage->media != null) {
                        $firebasePushController->notification([
                            'fcm_token' => $user->fcm_token,
                            'title' => 'Message',
                            'body' => $createdMessage->media,
                            'receiver_id' => $createdMessage->receiver_id,
                            'conversation_id' => $createdMessage->conversation_id,
                        ]);
                    } else {
                        $firebasePushController->notification([
                            'fcm_token' => $user->fcm_token,
                            'title' => 'Message',
                            'body' => $createdMessage->text,
                            'receiver_id' => $createdMessage->receiver_id,
                            'conversation_id' => $createdMessage->conversation_id,
                        ]);
                    }
                } else {
                   echo response()->json([
                        'status' => false,
                        'message' => 'Token introuvable',
                        'data' => [
                            "message"=> MessageResource::make($createdMessage),
                        ],
                    ]);
                }
            } catch (NotFound $e) {
                Log::error('Erreur Firebase : ' . $e->getMessage());
            }


            //dd($createdMessage->conversation->id);
           
            
           // ProcessPendingMessages::dispatch($createdMessage);

            //$this->sendNotificationToReceiver($createdMessage);

            return response()->json([
                'status' => true,
                'message' => 'Message est crée avec succès',
                'data' => [
                    "message"=> MessageResource::make($createdMessage),
                ],
            ]);
        }
    }

    /**
     * Delete the Message.
     */
    // public function deleteMessage(Message $message){
    //     dd($message);

    // }
    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        return MessageResource::make($message);
    }


     /**
     * Send notification to other users
     *
     * @param Message $message
     */
    private function sendNotificationToReceiver(Message $message) : void {

        // TODO move this event broadcast to observer
        broadcast(new MessageWasPosted($message))->toOthers();

        $user = auth()->user();
        $userId = $user->id;

        $message = Message::where('id', $message->id)->get();
        
        if(count($message) > 0){
            //dd($message[0]->receiver_id);
            $receiver = User::where('id',$message[0]->receiver_id)->first();

            //$otherUser = User::where('id',$otherUserId)->first();
            $receiver->sendNewMessageNotification([
                'messageData'=>[
                    'senderName'=>$user->nomEom,
                    'message'=>$message[0]->text,
                    'message_id'=>$message[0]->id
                ]
            ]);

        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        if ($message->sender_id == auth()->user()->id) {
            $message->is_sender_delete = true;

            $message->save();
        }else{
            $message->is_receiver_delete = true;

            $message->save();
        }
    }
}
