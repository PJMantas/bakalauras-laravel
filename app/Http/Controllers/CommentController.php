<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function createComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_text' => 'required|string|between:2,100',
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

        $permission = Permission::where('id', $user->group_id)->get();

        if(!$permission[0]->comment_create){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->video_id = $request['video_id'];
        $comment->comment_parent_id = $request['comment_parent_id'];
        $comment->username = $user->username;
        $comment->comment_text = $request['comment_text'];
        $comment->save();

        return response()->json([
            'message' => 'Komentaras sėkmingai pridėtas',
            'comment' => $comment,
        ], 201);
    }

    public function createCommentReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_text' => 'required|string|between:2,100',
            'video_id' => 'required|numeric',
            'comment_parent_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Neregistruotas naudotojas'], 401);
        }

        $permission = Permission::where('id', $user->group_id)->get();

        if(!$permission[0]->comment_create){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->video_id = $request['video_id'];
        $comment->comment_parent_id = $request['comment_parent_id'];
        $comment->username = $user->username;
        $comment->comment_text = $request['comment_text'];
        $comment->save();

        return response()->json([
            'message' => 'Komentaro atsakymas sėkmingai pridėtas',
            'comment' => $comment,
        ], 201);
    }

    public function editComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_text' => 'required|string|between:2,100',
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Neregistruotas naudotojas'], 401);
        }

        $permission = Permission::where('id', $user->group_id)->get();

        if(!$permission[0]->comment_edit){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        $comment = Comment::find($request['id']);
        $comment->comment_text = $request['comment_text'];

        $comment->save();

        return response()->json([
            'message' => 'Komentaras sėkmingai atnaujintas',
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
            'message' => 'Komentarai sėkmingai gauti',
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

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Neregistruotas naudotojas'], 401);
        }

        $permission = Permission::where('id', $user->group_id)->get();

        if(!$permission[0]->comment_delete){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        Comment::where('comment_parent_id', $request['id'])->delete();
        Comment::where('id', $request['id'])->delete();
        
        return response()->json([
            'message' => 'Komentaras sėkmingai ištrintas',
            'comments' => $request['id'],
        ], 200);
        
    }

}
