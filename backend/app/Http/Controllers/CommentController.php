<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Influencer;
use Illuminate\Http\Request;

class CommentController extends Controller
{


    public function store(Request $request, string $slug)
    {
        $influencer = Influencer::where('slug', $slug)->first();
        if (isset($influencer)) {
            $data = $request->validate([
                'name' => 'required|string',
                'influencer_rating' => 'required|int',
                'comment' => 'required|string'
            ]);

            $comment = new Comment();
            $comment->name = $data['name'];
            $comment->influencer_rating = $data['influencer_rating'];
            $comment->comment = $data['comment'];
            $comment->is_approved = false;
            $comment->influencer_id = $influencer->id;
           
            if ($comment->save()) {
                return response()->json(['error'=> false, 'message' => 'Comment has been saved.'], 201);
            } else {
                return response()->json(['error'=> true, 'message'=> 'Internal Server Error'], 500);
            }
        }

        return response()->json(['error'=> true, 'message' => 'This influencer is not yet recognized.'], 404);
    }
}
