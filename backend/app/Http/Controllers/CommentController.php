<?php

namespace App\Http\Controllers;

use App\Services\CommentService;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Influencer;
use Illuminate\Http\Request;

class CommentController extends Controller
{


    public function store(CommentRequest $request, CommentService $commentService, string $slug)
    {
        $influencer = Influencer::where('slug', $slug)->first();
        if (isset($influencer)) {
            $data = $request->validated();
            try {
                $response = $commentService->addComment($data, $influencer);
                return $response;
            } catch (\Throwable $th) {
                return response()->json(['error'=> true, 'message'=> 'Internal Server Error'], 500);
            }
        } else {
            return response()->json(['error'=> true, 'message' => 'This influencer is not yet recognized.'], 404);
        }
    }
}
