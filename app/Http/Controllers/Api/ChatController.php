<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    public function __construct()
    {
        $this->middleware(
            'auth:api'
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $chats = Chat::where("user1_id",$user->id)->orWhere("user2_id",$user->id)
            ->with("user_1", "user_2", "messages")
            ->get();

        return response()->json($chats, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth()->user();
        $chat = Chat::findOrFail($id)->with("messages", "user_1", "user_2");
        if ($user->id == $chat->user1_id || $user->id == $chat->user2_id) {
            return response()->json($chat, 200);
        }
        return response()->json(null, 404);
    }

    public function showOrCreateChat(Request $request)
    {
        if (isset($request->chat_id)) {
            $chat = Chat::with("user_1", "user_2", "messages")->findOrFail($request->chat_id);
        } else {
            $chat = Chat::where(function ($query) use ($request) {
                $query->where("user1_id", auth()->user()->id)
                    ->where("user2_id", $request->user_id);
            })->orWhere(function ($query) use ($request) {
                $query->where("user1_id", $request->user_id)
                    ->where("user2_id", auth()->user()->id);
            })->with("user_1", "user_2", "messages")->get()->first();
            if ($chat == null) {
                $chat = new Chat();
                $chat->user_1()->associate(User::findOrFail(auth()->user()->id));
                $chat->user_2()->associate(User::findOrFail($request->user_id));
                $chat->save();
                $chat->messages = [];
            }
        }
        return response()->json($chat, 200);
    }
}
