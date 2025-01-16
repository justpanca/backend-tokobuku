<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Profile extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'profiles';

    protected $fillable = ['bio','age','image','user_id'];

    public function user()
    {
        return $this->belongsTo(Role::class, 'user_id');
    }
   
}
