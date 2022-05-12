<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function createComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_text' => 'required|string|max:255',
            'video_id' => 'required|numeric',
            'comment_parent_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Neregistruotas naudotojas'], 401);
        }

        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->video_id = $request['video_id'];
        $comment->comment_parent_id = $request['comment_parent_id'];
        $comment->username = $user->username;
        $comment->comment_text = $request['comment_text'];
        $comment->save();

        return response()->json([
            'message' => 'Comment successfully created',
            'comment' => $comment,
        ], 201);
    }

    public function createCommentReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_text' => 'required|string|max:255',
            'video_id' => 'required|numeric',
            'comment_parent_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();

        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->video_id = $request['video_id'];
        $comment->comment_parent_id = $request['comment_parent_id'];
        $comment->username = $user->username;
        $comment->comment_text = $request['comment_text'];
        $comment->save();

        return response()->json([
            'message' => 'Comment reply successfully created',
            'comment' => $comment,
        ], 201);
    }

    public function editComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_text' => 'required|string|max:255',
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $comment = Comment::find($request['id']);
        $comment->comment_text = $request['comment_text'];

        $comment->save();

        return response()->json([
            'message' => 'Comment successfully edited',
            'comment' => $comment,
        ], 201);
    }

    public function getVideoComments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|numeric',
            'count' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $comments = DB::select('select * from comments where video_id = ? order by created_at desc LIMIT ?', [$request['video_id'], $request['count']]);
        //$comments = Comment::where('video_id', $request['video_id'])
        //                ->orderBy('created_at', 'desc')
        //                ->take($request['count'])
        //                ->get();


        return response()->json([
            'message' => 'Comments successfully fetched',
            'comments' => $comments,
        ], 200);
    }

    public function deleteComment(Request $request){
        $validator = Validator::make($request ->all(), [
            'id' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::table('comments')->where(
            'id', $request['id'])
            ->delete();
        
        return response()->json([
            'message' => 'Comment successfully deleted',
            'comments' => $request['id'],
        ], 200);
        
    }

}
