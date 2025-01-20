<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Carbon\Carbon;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'role_id',

    ];
    public function getJWTIdentifier() {
        return $this->getKey();
    }
     /** 
    * 
    *@return array
    */

    public function getJWTCustomClaims() {
        return [];
    }
    public static function boot()
    {
        parent::boot();
        static::created(function($model){
            $model->generate_otp();
        });
    }
    public function generate_otp()
    {
     do {
        $randomNumber = mt_rand(100000, 999999);
        $check = OtpCode::where('otp', $randomNumber)->first();
     }   while ($check);

     $now = Carbon::now();

     OtpCode::updateOrCreate(
         ['user_id' => $this->id],
         ['otp' => $randomNumber, 'valid_until' => $now->addMinutes(5)]
     );
    }    

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function otpcode()
    {
        return $this->hasOne(OtpCode::class, 'user_id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
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
    ];
}
