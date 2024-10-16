<?php

namespace App\Services;

use App\Models\Comment;

class CommentService
{
    
    public function addComment($data, $influencer) {

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
}
