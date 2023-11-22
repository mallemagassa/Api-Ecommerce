<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Http\Requests\ConversationRequest;
use App\Http\Resources\ConversationResource;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return MessageResource::collection(Message::all());
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
            
            $createdMessage = Message::create([
                'sender_id' => auth()->user()->id,
                'receiver_id' => $validitedata['receiver_id'],
                'type' => $validitedata['type'],
                'text' => isset($validitedata['text']) ? $validitedata['text'] : null,
                'media' => isset($validitedata['media']) ? $validitedata['media'] : null,
                'document' => isset($validitedata['document']) ? $validitedata['document'] : null,
                'conversation_id' =>$createdconversation->id,
            ]);
            
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
            
            $createdMessage = Message::create([
                'sender_id' => auth()->user()->id,
                'receiver_id' => $validitedata['receiver_id'],
                'type' => $validitedata['type'],
                'text' => isset($validitedata['text']) ? $validitedata['text'] : null,
                'media' => isset($validitedata['media']) ? $validitedata['media'] : null,
                'video' => isset($validitedata['video']) ? $validitedata['video'] : null,
                'document' => isset($validitedata['document']) ? $validitedata['document'] : null,
                'conversation_id' =>$checkConversation[0]->id,
            ]);

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
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        MessageResource::make($message);
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
    public function destroy(string $id)
    {
        //
    }
}
