<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{

    public function __construct()
    {
        $this->middleware(
            'auth:api'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_message = auth()->user();
        $chat = Chat::findOrFail($request->chat_id);
        $message = new Message();
        $message->text = $request->text;
        $message->chat()->associate($chat);
        $message->user()->associate($user_message);
        $message->save();
        return response()->json($message,201);
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

        $message = Message::findOrFail($id);
        if(auth()->user()->$id==$message->user()->id){
            $message->text = $request->text;
            $message->update();
            return response()->json($message,200);
        }
        return response()->json(null,404);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message =Message::findOrFail($id);
        if(auth()->user()->$id==$message->user()->id){

            return response()->json(null,204);
        }
        return response()->json(null,404);
    }

}
