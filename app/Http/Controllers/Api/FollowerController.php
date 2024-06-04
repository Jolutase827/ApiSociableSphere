<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowerController extends Controller
{

    public function __construct()
    {
        $this->middleware(
            'auth:api'
        );
    }

    public function followedUsers($id){
        $follows = Follow::where("user_followed_id",$id)->with("followed")->get();
        return response()->json($follows,200);
    }
    public function followsUser($id){
        $follows = Follow::where("user_id",$id)->with("followers")->get();
        return response()->json($follows,200);
    }
}
