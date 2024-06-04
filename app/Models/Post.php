<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function views(){
        return $this->hasMany(View::class);
    }
    public function type()
    {
        return $this->belongsTo(Type::class);
    }
    public function likes(){
        return $this->hasMany(Like::class);
    }
    public function comments(){
        return $this->hasMany(Comment::class);
    }
    public function claims(){
        return $this->hasMany(Claim::class);
    }
    public function payments(){
        return $this->hasMany(Payment::class);
    }
}
