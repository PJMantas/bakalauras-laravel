<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{
    public function getSystemReport(){
       
        $usersCount = DB::select('select count(*) as count from users');

        $videoCount = DB::select('select count(*) as count from videos');

        $videoSums = DB::select('select sum(clicks) as clicks, sum(likes) as likes, sum(dislikes) as dislikes from videos');

        $mostLikedVideos = DB::select('select * from videos order by likes desc limit 5');
        
        $mostDislikedVideos = DB::select('select * from videos order by dislikes desc limit 5');
        
        $mostViewedVideos = DB::select('select * from videos order by clicks desc limit 5');

        $commentCount = DB::select('select count(*) as count from comments');

        $mostCommentedVideos = DB::select('select videos.id as video_id, videos.title as title, count(comments.id) as comments from videos
                                        inner join comments on videos.id = comments.video_id 
                                            group by videos.id, videos.title
                                            order by comments desc limit 5');

        $users = User::select('id', 'created_at')
        ->get()
        ->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('m');
        });

        $userMonthCount = [];
        $userArr = [];

        foreach ($users as $key => $value) {
            $userMonthCount[(int)$key] = count($value);
        }

        for($i = 1; $i <= 12; $i++){
            if(!empty($userMonthCount[$i])){
                $userArr[$i] = $userMonthCount[$i];    
            }else{
                $userArr[$i] = 0;    
            }
        }

        return response()->json([
            'message' => 'Gauti sistemos ataskaitos duomenys',
            'RegisteredUsers' => $usersCount,
            'Videos' => $videoCount,
            'VideoSums' => $videoSums,
            'MostLikedVideos' => $mostLikedVideos,
            'MostDislikedVideos' => $mostDislikedVideos,
            'MostViewedVideos' => $mostViewedVideos,
            'MostCommentedVideos' => $mostCommentedVideos,
            'Comments' => $commentCount,
            'UserCountPerMonth' => $userArr

        ], 201);
    }

    public function getUserReport(){
        
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Neregistruotas naudotojas'], 401);
        }

        $permission = Permission::where('id', $user->group_id)->get();

        if(!$permission[0]->is_admin){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        $userid = $user->id;

        $userVideoCount = DB::select('select count(*) as count from videos where creator_id = ?', [$userid]);

        $userVideoSums = DB::select('select sum(clicks) as clicks, sum(likes) as likes, sum(dislikes) as dislikes from videos where creator_id = ?', [$userid]);
     
        $mostCommentedVideos = DB::select('select videos.id as video_id, videos.title as title, count(comments.id) as comments from videos
                                            inner join comments on videos.id = comments.video_id 
                                                where videos.creator_id = ?
                                                group by videos.id, videos.title
                                                order by comments desc limit 3', [$userid]);

        $mostLikedVideos = DB::select('select * from videos where creator_id = ? order by likes desc limit 3', [$userid]);

        $mostLikedVideosByDate = DB::select('select * from videos where creator_id = ? order by created_at', [$userid]);

        $mostViewedVideos = DB::select('select * from videos where creator_id = ? order by clicks desc limit 3', [$userid]);
        
        $mostDislikedVideos = DB::select('select * from videos where creator_id = ? order by dislikes desc limit 3', [$userid]);

        $mostDislikedVideosByDate = DB::select('select * from videos where creator_id = ? order by created_at', [$userid]);
        
        $userVideoCommentCount = DB::select('select count(comments.id) as commentCount from videos
                                            inner join comments on videos.id = comments.video_id 
                                                where videos.creator_id = ? ', [$userid]);

        return response()->json([
            'message' => 'Gauti naudotojo ataskaitos duomenys',
            'VideoCount' => $userVideoCount,
            'MostCommentedVideos' => $mostCommentedVideos,
            'MostLikedVideos' => $mostLikedVideos,
            'MostViewedVideos' => $mostViewedVideos,
            'VideoSums' => $userVideoSums,
            'MostDislikedVideos' => $mostDislikedVideos,
            'CommentCount' => $userVideoCommentCount,
            'MostLikedVideosByDate' => $mostLikedVideosByDate,
            'MostDislikedVideosByDate' => $mostDislikedVideosByDate
           
        ], 201);
    }

    
}
