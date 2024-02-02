<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "type" => 'required|string|max:255',
            "text" => 'string|max:955',
            "media" => 'string',
            "document" => 'file|mimes:ppt,pdf,pptx,doc,docx,xlsx|max:204800,csv,txt,xlx,xls,pdf|max:2048',
            "video" => 'mimes:mp4,x-flv,x-mpegURL,MP2T,3gpp,quicktime,x-msvideo,x-ms-wmv',
            "numOrder" => 'string|max:255',
            //video:m4v,avi,flv,mp4,mov,mimes:mp4,ogg | max:20000,qt | max:20000,mimetypes:video/x-ms-asf,ogx,oga,ogv,webm
            //"sender_id" => 'exists:App\Models\User,id',
            "receiver_id" => 'exists:App\Models\User,id',
            //"conversation_id" => 'exists:App\Models\User,id',
        ];
    }
}
