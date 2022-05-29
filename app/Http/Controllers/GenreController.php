<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Genre;
use App\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GenreController extends Controller
{
    public function getGenresList(){

        $genres = DB::table('genres')
        ->get();

        return response()->json([
            'message' => 'Žymų sąrašas sėkmingai gautas',
            'genres' => $genres
        ], 201);
    }

    public function getGenre($id){

        $genre = Genre::find($id);

        return response()->json([
            'message' => 'Žyma sėkmingai gauta',
            'genre' => $genre
        ], 201);
    }

    public function createGenre(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Neregistruotas naudotojas'], 401);
        }

        $permission = Permission::where('id', $user->group_id)->get();

        if(!$permission[0]->manage_genres){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        $genre = new Genre();

        $genre->name = $request['name'];

        $genre->save();

        return response()->json([
            'message' => 'Žyma sėkmingai pridėta',
            'genre' => $genre
        ], 201);
    }

    public function updateGenre(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|string|between:2,100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Neregistruotas naudotojas'], 401);
        }

        $permission = Permission::where('id', $user->group_id)->get();

        if(!$permission[0]->manage_genres){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        $genre = Genre::find($request['id']);

        $genre->name = $request['name'];

        $genre->save();

        return response()->json([
            'message' => 'Žyma sėkmingai atnaujinta',
            'genre' => $genre
        ], 201);
    }

    public function deleteGenre(Request $request){

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

        if(!$permission[0]->manage_genres){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        Genre::find($request['id'])
        ->delete();

        return response()->json([
            'message' => 'Žyma sėkmingai ištrinta'
        ], 201);
    }
}
