<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware(
            'auth:api'
        );
    }



    public function getUsersLimit20($id){
        $users = User::where('role', '!=', 'admin')
        ->where('id', '!=', $id)
        ->orderBy('created_at')
        ->limit(20)
        ->get(['id', 'user_name', 'name', 'photo']);
        return response()->json($users,200);
    }

    public function getUsersLimit20ByName(Request $request, $id){
        $users = User::where('role', '!=', 'admin')
            ->where('role', '!=', 'moderator')
            ->where('id', '!=', $id)
            ->where(function($query) use ($request) {
                $query->where('user_name', 'like', '%' . $request->user_name . '%')
                      ->orWhere('name', 'like', '%' . $request->user_name . '%');
            })
            ->orderBy('created_at')
            ->limit(20)
            ->get(['id', 'user_name', 'name', 'photo']);

        return response()->json($users, 200);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($id != 1 && $id) {
            $user = User::where("id", $id)->with([
                "posts" => function ($query) {
                    $query->orderBy('created_at', 'desc')->with(["type","payments", "user","likes", "comments" => function ($query1) {
                        $query1->orderBy('created_at', 'desc')->with("user");
                    }]);
                },
                "followers",
                "followed"
            ])->first();
            return response()->json($user, 200);
        } else {
            return response()->json(null, 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if (auth()->user()->id == $id || auth()->user()->role == "admin" || auth()->user()->role == "moderator") {
            $user->name = $request->name;
            $user->last_name = $request->last_name;
            $user->description = $request->description;
            $user->role = $request->role;
            $user->save();
            return response()->json($user);
        }
        return response()->json(null, 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if (auth()->user()->id == $id || auth()->user()->role == "admin" || auth()->user()->role == "moderator") {
            if ($user->photo != null) {
                Storage::disk('images')->delete($user->photo);
            }
            $user->delete();
            return response()->json(null, 204);
        }
        return response()->json(null, 404);
    }

    public function updateImage(Request $request)
    {
        $user = User::findOrFail(auth()->user()->id);
        if ($request->hasFile('photo')) {
            try {
                $request->validate([
                    'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
            } catch (ValidationException $e) {
                return response()->json(['error' => 'No es una imagen el fichero'], 500);
            }
            if ($user->photo != null) {
                Storage::disk('images')->delete($user->photo);
            }
            $user->photo = $request->photo->store('users', 'images');
        }
        $user->save();
        return response()->json($user, 200);
    }

    public function followUser(Request $request)
    {
        if (!Follow::where("user_id", auth()->user()->id)->where("user_followed_id", $request->user_followed_id)->exists()) {
            $follow = new Follow();
            $follow->followed()->associate(User::findOrFail(auth()->user()->id));
            $follow->followers()->associate(User::findOrFail($request->user_followed_id));
            $follow->save();
            return response()->json($follow, 200);
        }
        return response()->json(
            ['error' => 'User is already followed'],
            401
        );
    }

    public function wallet($amount){
        $user = User::findOrFail(auth()->user()->id);
        if($user->wallet+$amount<0){
           return response()->json(
            ['error' => 'Not enough money'],
            401
        );
        }
        $user->wallet = $user->wallet+$amount;
        $user->save();
        return response()->json($user,201);
    }

    public function unfollowUser($id){
        $follow = Follow::findOrFail($id);
        if(auth()->user()->id==$follow->user_id){
            $follow->delete();
            return response()->json(null,204);
        }else
            return response()->json(null,404);

    }
}
