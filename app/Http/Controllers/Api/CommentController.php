<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
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
        $comment = new Comment();
        $comment->text = $request->text;
        $comment->user()->associate(auth()->user());
        $comment->post()->associate(Post::findOrFail($request->post_id));
        $comment->save();
        $comment->user = $comment->user();
        return response()->json($comment, 201);
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
        $comment = Comment::findOrFail($id);
        if (auth()->user()->$id == $comment->user_id||auth()->user()->role=="admin"||auth()->user()->role=="moderator") {
            $comment->text = $request->text;
            $comment->update();
            return response()->json($comment, 200);
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
        $comment = Comment::findOrFail($id);
        if (auth()->user()->id == $comment->user_id||auth()->user()->role=="admin"||auth()->user()->role=="moderator") {
            $comment->delete();
            return response()->json(null, 204);
        }
        return response()->json(null,404);
    }
}
