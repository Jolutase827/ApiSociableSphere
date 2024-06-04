<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    public function messages(){
        return $this->hasMany(Message::class);
    }
    
    public function user_1()
    {
        return $this->belongsTo(User::class,"user1_id");
    }
    public function user_2()
    {
        return $this->belongsTo(User::class,"user2_id");
    }
}
