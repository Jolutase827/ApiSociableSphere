<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function followers()
    {
        return $this->hasMany(Follow::class,"user_followed_id","id");
    }
    public function followed()
    {
        return $this->hasMany(Follow::class,"user_id","id");
    }
    public function views(){
        return $this->hasMany(View::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function likes(){
        return $this->hasMany(Like::class);
    }
    public function comments(){
        return $this->hasMany(Comment::class);
    }
    public function chats(){
        return $this->hasMany(Chat::class);
    }
    public function messages(){
        return $this->hasMany(Message::class);
    }
    public function claims(){
        return $this->hasMany(Claim::class);
    }
    public function payments(){
        return $this->hasMany(Payment::class);
    }
}
