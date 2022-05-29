<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Genre;
use App\Models\Permission;
use App\Models\GenreRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GenreRequestController extends Controller
{
    public function addGenreRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:2,20',
            'description' => 'required|string|between:2,100',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Neregistruotas naudotojas'], 401);
        }

        $genreRequest = new GenreRequest();

        $genreRequest->title = $request['title'];
        $genreRequest->description = $request['description'];
        $genreRequest->user_id = $user->id;
        $genreRequest->status = 'Pateiktas';
        $genreRequest->username = $user->username;

        $genreRequest->save();

        return response()->json([
            'message' => 'Žymos prašymas sėkmingai pateiktas',
            'genreRequest' => $genreRequest,
        ], 201);
    }

    public function getUserGenreRequestsList()
    {
        $user = auth()->user();

        if (!$user){
            return response()->json(['message' => 'Neregistruotas naudotojas'], 401);
        }

        $genreRequests = DB::table('genre_requests')
            ->where('user_id', $user->id)
            ->get();

        return response()->json([
            'message' => 'Žymų prašymų sąrašas sėkmingai gautas',
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

        $user = auth()->user();

        if (!$user){
            return response()->json(['message' => 'Neregistruotas naudotojas'], 401);
        }

        $genreRequest = GenreRequest::find($request['id']);

        if ($genreRequest == null) {
            return response()->json([
                'message' => 'Žymos prašymas nerastas',
            ], 404);
        }

        $genreRequest->delete();

        return response()->json([
            'message' => 'Žymos prašymas sėkmingai ištrintas',
        ], 200);
    }

    public function getGenreRequestsList(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Neregistruotas naudotojas'], 401);
        }

        $permission = Permission::where('id', $user->group_id)->get();

        if(!$permission[0]->is_admin){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        $genreRequests = DB::table('genre_requests')
            ->get();

        return response()->json([
            'message' => 'Žymos prašymų sąrašas sėkmingai gautas',
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

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Neregistruotas naudotojas'], 401);
        }

        $permission = Permission::where('id', $user->group_id)->get();

        if(!$permission[0]->is_admin){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        $genreRequest = GenreRequest::find($request['id']);

        if ($genreRequest == null) {
            return response()->json([
                'message' => 'Žymos prašymas nerastas',
            ], 404);
        }

        $genreRequest->status = 'Atmestas';

        $genreRequest->save();

        return response()->json([
            'message' => 'Žymos prašymas sėkmingai atmestas',
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

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Neregistruotas naudotojas'], 401);
        }

        $permission = Permission::where('id', $user->group_id)->get();

        if(!$permission[0]->is_admin){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        $genreRequest = GenreRequest::find($request['id']);

        if ($genreRequest == null) {
            return response()->json([
                'message' => 'Žymos prašymas nerastas',
            ], 404);
        }

        $genreRequest->status = 'Patvirtintas';

        $genreRequest->save();

        return response()->json([
            'message' => 'Žymos prašymas sėkmingai patvirtintas',
            'genreRequest' => $genreRequest,
        ], 200);
    }

}
