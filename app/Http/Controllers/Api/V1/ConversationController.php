<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Http\Requests\ConversationRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $data = [];
        // $conversations = Conversation::where('sender_id', auth()->user()->id)
        //     ->orWhere('receiver_id', auth()->user()->id)
        //     ->orderBy('created_at', 'DESC')
        //     ->get();
    
        // foreach ($conversations as $conversation) {
        //     $user = $this->getChatUser($conversation);
    
        //     $data[] = [
        //         'user' => new UserResource($user), // Les informations de l'utilisateur
        //         'conversation_id' => $conversation->id, // L'ID de la conversation
        //     ];
        // }
    
        // return response()->json($data);

        $data = [];
        $conversations = Conversation::where('sender_id', auth()->user()->id)
            ->orWhere('receiver_id', auth()->user()->id)
            ->orderBy('created_at', 'DESC')
            ->get();
    
        foreach ($conversations as $conversation) {
            $user = $this->getChatUser($conversation);
            $userData = new UserResource($user); // Formatage des données de l'utilisateur
            $userData->conversation_id = $conversation->id; // Ajout de conversation_id à l'objet UserResource
            $userData->receiver_id = $conversation->receiver_id; // Ajout de conversation_id à l'objet UserResource
            
            $data[] =
                $userData // Utilisation de l'objet UserResource modifié
            ;
        }
        //dd($data[0]['user']->phone);
    
        return response()->json($data);
    }


    public function getChatUser(Conversation $conversation){
        if ($conversation->sender_id == auth()->user()->id) {
            $receiver = User::firstWhere('id', $conversation->receiver_id);
        }else{
            $receiver  = User::firstWhere('id', $conversation->sender_id);
        }

        return $receiver;
    }

    public function con(User $user, Conversation $conversation)

    {
        //$users =  User::collection($conversation->sender, $conversation->receiver);
        //$users = collect([$conversation->sender, $conversation->receiver]);
        dd($conversation->sender->id, $conversation->receiver->id, $user->id);
        //return $users->contains($user);
    }


    public function selectConversation(int $conversation, int $receiverId){

        $selecConversation = Conversation::where('id', $conversation)->get();
        $receiver = User::find($receiverId);
        
        if (count($selecConversation) > 0) {
            return $this->loadMessage(Conversation::find($selecConversation[0]->id), $receiver);
           // dd($selecConversation);
        }else{
            $selecConversation2 = Conversation::where('receiver_id', auth()->user()->id)->where("sender_id", $receiver->id)->orWhere('receiver_id', $receiver->id)->where("sender_id", auth()->user()->id)->get();
            if (count($selecConversation2) > 0) {
                return $this->loadMessage(Conversation::find($selecConversation2[0]->id), $receiver);
                //dd($selecConversatio);
            }
            return [];
            //echo 'No conversation';
        }
       
    }

    public function loadMessage(Conversation $conversation, User $receiverId){

        //dd($conversation, $receiverId);

        $messages = Message::where('conversation_id', $conversation->id)->get();
        return MessageResource::collection($messages);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ConversationRequest $request)
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

            // if ($messages) {
                
                $createdconversation = Conversation::create([
                    'sender_id' => auth()->user()->id,//$validitedata['sender_id'],
                    'receiver_id' => $validitedata['receiver_id'],
                ]);

                // if ($request->hasFile('media')) {
                //     $validitedata['media'] = $request->file('media')->store('public/images/messages/media');
                // }

                // if ($request->hasFile('document')) {
                //     $validitedata['document'] = $request->file('document')->store('public/images/messages/documents');
                // }
                
                // $createdMessage = Message::create([
                //     'sender_id' => auth()->user()->id,
                //     'receiver_id' => $messages['receiver_id'],
                //     'type' => $messages['type'],
                //     'text' => $messages['text'],
                //     'media' => $messages['media'],
                //     'document' => $messages['document'],
                //     'conversation_id' =>$createdconversation->id,
                // ]);
                
                return response()->json([
                    'status' => true,
                    'message' => 'conversation est crée avec succès',
                    'data' => [
                        "conversations"=> ConversationResource::make($createdconversation),
                    ],
                ]);
            // }

           dd($checkConversation);
           dd("no conversation");
        }else if( count($checkConversation) >= 1){
            dd($checkConversation);
            dd("conversation existe");
        }
       
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Conversation $conversation)
    {
        //dd($conversation);
        return ConversationResource::make($conversation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Conversation $conversation)
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

        $conversation->update($validitedata);

        return response()->json([
            'status' => true,
            'message' => 'Conversation est modifier avec succès',
            'data' => [
                "conversations"=> ConversationResource::make($conversation),

            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conversation $conversation)
    {
        $conversation->delete();

        return response()->json([
            'status' => true,
            'message' => 'Conversation est modifier avec succès',
            'data' => [
                "conversations"=> ConversationResource::make($conversation),
            ],
        ]);
    }
}
