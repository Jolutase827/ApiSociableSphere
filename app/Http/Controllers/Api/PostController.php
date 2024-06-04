<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Like;
use App\Models\Payment;
use App\Models\Post;
use App\Models\Type;
use App\Models\User;
use App\Models\View;
use Dotenv\Exception\ValidationException;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
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
        $type = Type::findOrFail($request->type);
        $post = new Post();
        $post->user()->associate(User::findOrFail(auth()->user()->id));
        $post->type()->associate($type);
        if ($type->has_footer) {
            if ($request->hasFile($type->content)) {
                try {
                    $request->validate([
                        $type->content => 'mimetypes:image/jpeg,image/png,image/jpg,image/gif,video/mp4,video/avi|max:30720',
                    ]);
                } catch (ValidationException $e) {
                    return response()->json(['error' => 'Error en la carga del fichero'], 500);
                }
                if ($type->content == "photo") {
                    $post->content = $request->photo->store('posts', "images");
                } else {
                    $post->content = $request->video->store('posts', "videos");
                }
            }
            $post->footer = $request->footer;
            if ($type->has_reward) {
                $post->reward = $request->reward;
                $post->cost = 0;
            } else if ($type->has_cost) {
                $post->cost = $request->cost;
                $post->reward = 0;
            } else {
                $post->cost = 0;
                $post->reward = 0;
            }
        } else {
            $post->footer = "null";
            $post->content = $request->content;
            $post->cost = 0;
            $post->reward = 0;
        }
        $post->save();
        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id)->whith("user")->with("likes")->whith("comments");
        return response()->json($post, 200);
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
        $post = Post::findOrFail($id);
        if (auth()->user()->id == $post->user_id || auth()->user()->role == "admin" || auth()->user()->role == "moderator") {
            $type = $post->type;
            if ($type->id == 1) {
                $post->content = $request->content;
            } else if ($type->id == 2 || $type->id == 3) {
                $post->footer = $request->footer;
            }
            $post->save();
            return response()->json($post, 200);
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
        $post = Post::findOrFail($id);
        if (auth()->user()->id == $post->user_id || auth()->user()->role == "admin" || auth()->user()->role == "moderator") {
            $type = $post->type;
            if ($type->has_footer) {
                if ($type->content == "photo") {
                    Storage::disk('images')->delete($post->content);
                } else {
                    Storage::disk('videos')->delete($post->content);
                }
            }
            $post->delete();
            return response()->json(null, 204);
        }
        return response()->json(null, 404);
    }


    public function addLike(Request $request)
    {
        if (Like::where("user_id", auth()->user()->id)->where("post_id", $request->post_id)->first()) {
            return response()->json(["error" => "Ya ha dado a like"], 401);
        }
        $post = Post::findOrFail($request->post_id);
        $user = auth()->user();
        $like = new Like();
        $like->user()->associate($user);
        $like->post()->associate($post);
        $like->save();
        return response()->json($like, 201);
    }

    public function deleteLike($id)
    {
        $like = Like::findOrFail($id);
        if (auth()->user()->id == $like->user_id) {
            $like->delete();
            return response()->json(null, 204);
        }
        return response()->json(null, 404);
    }

    public function getPostWithAlgoritm()
    {
        $user = User::findOrFail(auth()->user()->id);
        $followers = $user->followed()->pluck('user_followed_id');
        $postsFollowed = Post::whereIn('user_id', $followers)
            ->whereDoesntHave('views', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereNotIn('type_id', [4, 5])
            ->orderBy('created_at', 'DESC')
            ->with(["user", "payments", "type", "likes", "comments" => function ($query1) {
                $query1->orderBy('created_at', 'desc')->with("user");
            }])
            ->get();
        $postsOthers = Post::whereNotIn('user_id', $followers)
            ->whereDoesntHave('views', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereNotIn('type_id', [4, 5])
            ->where('user_id', "!=", $user->id)
            ->orderBy('created_at', 'DESC')
            ->with(["user", "payments", "type", "likes", "comments" => function ($query1) {
                $query1->orderBy('created_at', 'desc')->with("user");
            }])
            ->limit(5)
            ->get();
        $posts = ["postFollowed" => $postsFollowed, "postOthers" => $postsOthers];
        $postsToMarkAsViewed = $postsFollowed->merge($postsOthers);
        foreach ($postsToMarkAsViewed as $post) {
            $view = new View();
            $view->user_id = $user->id;
            $view->post_id = $post->id;
            $view->save();
        }
        return response()->json($posts, 200);
    }
    public function getAdds()
    {
        $user = User::findOrFail(auth()->user()->id);
        $adds = Post::where("user_id", "!=", $user->id)
            ->whereDoesntHave('claims', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereIn("type_id", [4, 5])
            ->where(function ($query) {
                $query->whereRaw('(SELECT COUNT(*) FROM claims WHERE claims.post_id = posts.id) < posts.reward');
            })
            ->orderBy("created_at", "DESC")
            ->limit(2)
            ->with("user", "claims", "type", "comments", "likes")
            ->get();
        return response()->json($adds, 200);
    }

    public function createClaim($post_id)
    {
        $claim = new Claim();
        $claim->user_id = auth()->user()->id;
        $claim->post_id = $post_id;
        $claim->save();
        return response()->json($claim, 201);
    }

    public function createPayment($post_id)
    {
        $payment = new Payment();
        $payment->user_id = auth()->user()->id;
        $payment->post_id = $post_id;
        $payment->save();
        return response()->json($payment, 201);
    }
}
