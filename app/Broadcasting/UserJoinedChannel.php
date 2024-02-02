<?php

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// class UserJoinedChannel
// {

//     /**
//      * Authenticate the user's access to the channel.
//      */
//     public function join(User $user): array|bool
//     {
//         if (!$user) {
//             return false;
//         }
//         $user->statusOn = 'outline'; 
    
//         $user->save();

//         return true;
//     }
// }
