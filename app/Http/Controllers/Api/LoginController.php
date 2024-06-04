<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('user_name', $request->login)->first();
        if(!$user)
        $user = User::where('email', $request->login)->first();
        if (
            !$user ||
            !Hash::check($request->password, $user->password)
        ) {
            return response()->json(
                ['error' => 'Credenciales no válidas'],
                401
            );
        } else {
            $user->api_token = Str::random(60);
            $user->save();
            return response()->json(['user' => $user],200);
        }
    }
    public function register(Request $request)
    {
        if(User::where('user_name',$request->user_name)->get()->first()!=null){
            return response()->json(
                ['error' => 'User name already exists',"code"=>"name"],
                401
            );
        }
        if(User::where('email',$request->email)->get()->first()!=null){
            return response()->json(
                ['error' => 'Email already exists',"code"=>"email"],
                401
            );
        }
        $user = new User();
        $user->user_name= $request->user_name;
        $user->email= $request->email;
        $user->name= $request->name;
        $user->last_name= $request->last_name;
        $user->password = bcrypt($request->password);
        $user->role = "normal";
        $user->wallet = 0;
        $user->api_token = Str::random(60);
        $user->save();
        return response()->json(['user' => $user],201);
    }
}
