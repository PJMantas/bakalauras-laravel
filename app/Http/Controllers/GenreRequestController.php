<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Genre;
use App\Models\GenreRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GenreRequestController extends Controller
{
    public function addGenreRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:2,100',
            'description' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();

        $genreRequest = new GenreRequest();

        $genreRequest->title = $request['title'];
        $genreRequest->description = $request['description'];
        $genreRequest->user_id = $user->id;
        $genreRequest->status = 'Pateiktas';
        $genreRequest->username = $user->username;

        $genreRequest->save();

        return response()->json([
            'message' => 'Genre request successfully created',
            'genreRequest' => $genreRequest,
        ], 201);
    }

    public function getUserGenreRequestsList()
    {
        $user = auth()->user();

        $genreRequests = DB::table('genre_requests')
            ->where('user_id', $user->id)
            ->get();

        return response()->json([
            'message' => 'Genre requests successfully retrieved',
            'genreRequests' => $genreRequests,
        ], 200);
    }

    public function deleteGenreRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $genreRequest = GenreRequest::find($request['id']);

        if ($genreRequest == null) {
            return response()->json([
                'message' => 'Genre request not found',
            ], 404);
        }

        $genreRequest->delete();

        return response()->json([
            'message' => 'Genre request successfully deleted',
        ], 200);
    }

    public function getGenreRequestsList(Request $request)
    {
        $genreRequests = DB::table('genre_requests')
            ->get();

        return response()->json([
            'message' => 'Genre requests successfully retrieved',
            'genreRequests' => $genreRequests,
        ], 200);
    }

    public function rejectGenreRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $genreRequest = GenreRequest::find($request['id']);

        if ($genreRequest == null) {
            return response()->json([
                'message' => 'Genre request not found',
            ], 404);
        }

        $genreRequest->status = 'Atmestas';

        $genreRequest->save();

        return response()->json([
            'message' => 'Genre request successfully rejected',
            'genreRequest' => $genreRequest,
        ], 200);
    }

    public function approveGenreRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $genreRequest = GenreRequest::find($request['id']);

        if ($genreRequest == null) {
            return response()->json([
                'message' => 'Genre request not found',
            ], 404);
        }

        $genreRequest->status = 'Patvirtintas';

        $genreRequest->save();

        return response()->json([
            'message' => 'Genre request successfully approved',
            'genreRequest' => $genreRequest,
        ], 200);
    }

}
