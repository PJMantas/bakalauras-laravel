<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Reaction;
use App\Models\Comment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function createVideo(Request $request){

    	$validator = Validator::make($request->all(), [
            'title' => 'required|string|between:4,100',
            'video_url' => 'required|file|mimetypes:video/mp4',
            'description' => 'required|string|between:4,255',
            'genre' => 'required|numeric',
            'thumbnail_url' => 'file|mimes:jpg,png,jpeg,gif|max:5120',
            
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        
        $user = auth()->user();

        $video = new Video();

        $video->title = $request['title'];
        //$video->video_url = $request['video_url'];
        if ($request->hasFile('video_url'))
        {
            $path = $request->file('video_url')->store('videos', ['disk' => 'videos']);
            $video->video_url = $path;
        }

        if ($request->hasFile('thumbnail_url'))
        {
            $path = $request->file('thumbnail_url')->store('thumbnails', ['disk' => 'thumbnails']);
            $video->thumbnail_url = $path;
        }

        $video->description = $request['description'];
        $video->genre = $request['genre'];
        $video->creator_id = $user->id;
        
        $video->save();


        return response()->json([
            'message' => 'Video successfully created',
            'video' => $video,
            'path' => $path
        ], 201);
    }

    public function updateVideo(Request $request){
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|numeric', 
            'title' => 'required|string|between:4,100',
            'description' => 'required|string|between:4,255',
            'genre' => 'required|numeric',
            'thumbnail_url' => 'file|mimes:jpg,png,jpeg,gif,svg|max:5120',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $video = Video::find($request['video_id']);

        $video->title = $request['title'];
        $video->description = $request['description'];
        $video->genre = $request['genre'];

        if ($request->hasFile('thumbnail_url'))
        {
            $path = $request->file('thumbnail_url')->store('thumbnails', ['disk' => 'thumbnails']);
            $video->thumbnail_url = $path;
        }

        $video->save();

        return response()->json([
            'message' => 'Video edited',
            'video' => $video
            ], 201);
    }

    public function addVideoView(Request $request){
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|numeric', 
            'genre' => 'numeric',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $video = Video::find($request['video_id']);

        $video->increment('clicks');

        $video->save();

        return response()->json([
            'message' => 'Video count added',
            'video' => $video
            ], 201);
    }

    public function reactToVideo(Request $request){
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|numeric', 
            'reaction_type' => 'required|string|in:true,false',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $video = Video::find($request['video_id']);
        $isLiked = $request['reaction_type'] === 'true';
        $user = auth()->user();

        $like = Reaction::where('user_id', $user->id)->where('video_id', $video->id)->first();

        if($like){
            if($like->reaction_type == $isLiked){
                if ($isLiked) {           
                    $like->delete();
                    $video->decrement('likes');
                } else {
                    $video->decrement('dislikes');
                    $like->delete();
                }
            } 
            else {
                if ($isLiked) {
                    $like->reaction_type = $isLiked;
                    $like->save();
                    $video->increment('likes');
                    $video->decrement('dislikes');
                }
                else {
                    $like->reaction_type = $isLiked;
                    $like->save();
                    $video->increment('dislikes');
                    $video->decrement('likes');
                }
            }
        }
        else
        {
            $reaction = new Reaction();
            $reaction->user_id = $user->id;
            $reaction->video_id = $video->id;
            $reaction->reaction_type = $isLiked;
            $reaction->save();

            if ($isLiked)
            {
                $video->increment('likes');
            }
            else
            {
                $video->increment('dislikes');
            }
        }

        $video->save();

        if ($isLiked) {
            return response()->json([
                'message' => 'Video liked',
                'video' => $video
            ], 201);
        }
        else{
            return response()->json([
                'message' => 'Video disliked',
                'video' => $video
            ], 201);
        }   
    }


    public function getVideoById(Request $request){
    	$validator = Validator::make($request->all(), [
            'id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //$video = DB::select('select * from videos where id=' . $request['id'] . ' LIMIT 1');
        $video = Video::where('id', $request['id'])->get();

        return response()->json([
            'message' => 'Retrieved Video ID: ' . $request['id'],
            'video' => $video[0]
        ], 200);
    }

    public function deleteVideo(Request $request){
        $validator = Validator::make($request ->all(), [
            'id' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $video = Video::find($request['id']);

        Comment::where('video_id', $video->id)->delete();
        Reaction::where('video_id', $video->id)->delete();

        $videoPath = public_path().'/'.$video->video_url;
        $thumbnailPath = public_path().'/'.$video->thumbnail_url;

        if ($video->thumbnail_url != 'thumbnails/default_thumbnail.jpg') {
            File::delete($thumbnailPath);
        }

        File::delete($videoPath);

        Video::where('id', $request['id'])->delete();
        
        return response()->json(200);
        
    }

    public function getVideosList(){
        $videos = DB::select('select * from videos');

        return response()->json([
            'message' => 'Retrieved Video List',
            'videos' => $videos
        ], 200);
    }

    public function getUserVideosList(){

        //$videos = DB::select('select * from videos where creator_id=' . auth()->user()->id);
        $videos = Video::where('creator_id', auth()->user()->id)->get();
        return response()->json([
            'message' => 'Retrieved Video List',
            'videos' => $videos
        ], 200);
    }

    public function searchVideos(Request $request){
        $validator = Validator::make($request->all(), [
            'search' => 'between:0,100'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $videos = Video::where('title', 'like', '%' . $request['search'] . '%')->get();

        return response()->json([
            'message' => 'Retrieved Video List',
            'videos' => $videos
        ], 201);
    }

    public function getOrderedVideosByGenre(Request $request){
        $validator = Validator::make($request->all(), [
            'genre' => 'required|numeric',
            'orderField' => 'required|string|between:2,100',
            'orderType' => 'required|string|between:2,100'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if ($request['genre'] != -1)
        {
            //$videos = DB::select('select * from videos where genre=' . $request['genre'] . ' order by ' . $request['orderField'] . ' ' . $request['orderType']);
            $videos = Video::Where('genre', $request['genre'])
                    ->orderBy($request['orderField'], $request['orderType'])
                    ->get();
        }
        else
        {
            //$videos = DB::select('select * from videos order by ' . $request['orderField'] . ' ' . $request['orderType']);
            $videos = Video::orderBy($request['orderField'], $request['orderType'])->get();
        }

        return response()->json([
            'message' => 'Retrieved Video List',
            'videos' => $videos
        ], 201);
    }

    public function getVideosByGenre(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'genre' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $videos = DB::select('select * from videos where genre="' . $request['genre'] . '"');

        return response()->json([
            'message' => 'Retrieved Video List',
            'videos' => $videos
        ], 201);
    }

    public function getRecomendedVideos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'genre' => 'required',
            'videoId' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $videos = Video::where('genre', $request['genre'])
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->where('id', '!=', $request['videoId'])
            ->orderBy('clicks', 'desc')
            ->orderBy('likes', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'message' => 'Retrieved Video Recomendation List',
            'videos' => $videos
        ], 201);

    }

    public function getMostPopularGanreRecomendedVideos(Request $request)
    {


        $popularGenres = DB::select('select sum(clicks) as totalViews, genre  from videos group by genre order by totalViews desc limit 3');
        $videos1 = Video::where('genre', $popularGenres[0]->genre)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('clicks', 'desc')
            ->orderBy('likes', 'desc')
            ->take(5)
            ->get();

        $videos2 = Video::where('genre', $popularGenres[1]->genre)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('clicks', 'desc')
            ->orderBy('likes', 'desc')
            ->take(5)
            ->get();
        
        $videos3 = Video::where('genre', $popularGenres[2]->genre)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('clicks', 'desc')
            ->orderBy('likes', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'message' => 'Retrieved Video Recomendation List',
            'videos1' => $videos1,
            'videos2' => $videos2,
            'videos3' => $videos3
        ], 201);
    }


    
}
