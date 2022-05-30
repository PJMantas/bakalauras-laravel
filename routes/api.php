<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\GenreRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PermissionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']); 
    
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'admin'
], function ($router) {
    Route::get('/users-list', [AdminController::class, 'getUsersList']); 
    Route::post('/add-user', [AdminController::class, 'addUser']);
    Route::delete('/delete-user', [AdminController::class, 'deleteUser']);
    Route::patch('/admin-update-user', [AdminController::class, 'adminUpdateUser']);

    Route::get('/permissions-list', [PermissionController::class, 'getPermissionsList']);
    Route::post('/add-permission', [PermissionController::class, 'addPermission']);
    Route::delete('/delete-permission', [PermissionController::class, 'deletePermission']);
    Route::patch('/update-permission', [PermissionController::class, 'updatePermission']);
    Route::get('/get-permission', [PermissionController::class, 'getPermission']);

    Route::get('/genre-requests-list', [GenreRequestController::class, 'getGenreRequestsList']);
    Route::patch('/approve-genre-request', [GenreRequestController::class, 'approveGenreRequest']);
    Route::delete('/delete-genre-request', [GenreRequestController::class, 'deleteGenreRequest']);
    Route::patch('/reject-genre-request', [GenreRequestController::class, 'rejectGenreRequest']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'user'
], function ($router) {
    Route::get('/get-user', [UserController::class, 'getUserById']); 
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::get('/get-user-permissions', [PermissionController::class, 'getAuthUserPermissions']);
    Route::post('/add-genre-request', [GenreRequestController::class, 'addGenreRequest']);
    Route::get('/get-user-genre-requests-list', [GenreRequestController::class, 'getUserGenreRequestsList']);
    Route::get('/get-auth-user-permissions', [PermissionController::class, 'getAuthUserPermissions']);
    Route::delete('/delete-profile', [UserController::class, 'deleteProfile']);
    
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'video'
], function ($router) {
    Route::post('/create-video', [VideoController::class, 'createVideo']); 
    Route::get('/get-video', [VideoController::class, 'getVideoById']); 
    Route::delete('/delete-video', [VideoController::class, 'deleteVideo']); 
    Route::get('/get-videos-list', [VideoController::class, 'getVideosList']);
    Route::patch('/update-video', [VideoController::class, 'updateVideo']); 
    Route::get('/get-user-videos-list', [VideoController::class, 'getUserVideosList']);
    Route::post('/add-video-view', [VideoController::class, 'addVideoView']); 
    Route::post('/react-to-video', [VideoController::class, 'reactToVideo']); 
    Route::get('search-video', [VideoController::class, 'searchVideos']);
    Route::get('get-videos-by-genre', [VideoController::class, 'getVideosByGenre']);
    Route::post('get-ordered-videos-by-genre', [VideoController::class, 'getOrderedVideosByGenre']);
    Route::get('get-recomended-videos', [VideoController::class, 'getRecomendedVideos']);
    Route::get('get-most-viewed-genre-videos', [VideoController::class, 'getMostPopularGanreRecomendedVideos']);
    
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'comment'
], function($router) {
    Route::post('/create-comment', [CommentController::class, 'createComment']);
    Route::post('/create-comment-reply', [CommentController::class, 'createCommentReply']);
    Route::get('/get-video-comments', [CommentController::class, 'getVideoComments']);
    Route::delete('/delete-comment', [CommentController::class, 'deleteComment']);
    Route::patch('/edit-comment', [CommentController::class, 'editComment']);
    
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'report'
], function($router) {
    Route::get('/get-system-report', [ReportController::class, 'getSystemReport']);
    Route::get('/get-user-report', [ReportController::class, 'getUserReport']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'genre'
], function($router) {
    Route::get('/get-genres-list', [GenreController::class, 'getGenresList']);
    Route::get('/get-genre', [GenreController::class, 'getGenre']);
    Route::post('/create-genre', [GenreController::class, 'createGenre']);
    Route::delete('/delete-genre', [GenreController::class, 'deleteGenre']);
    Route::patch('/update-genre', [GenreController::class, 'updateGenre']);
});
