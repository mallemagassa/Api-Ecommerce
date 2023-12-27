<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Order;
use App\Models\Profil;
use App\Models\Product;
use App\Models\Conversation;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\MessageSent;;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'phone',
        'nameCom',
        'status',
        'address',
        'fcm_token',
        'isSeller',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function profil(): HasOne
    {
        return $this->hasOne(Profil::class);
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function product(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class);
    }

    public function routeNotificationForOneSignal() : array{
        return ['tags'=>['key'=>'user_id','relation'=>'=', 'value'=>(string)($this->id)]];
    }

    public function sendNewMessageNotification(array $data) : void {
        $this->notify(new MessageSent($data));
    }

}
