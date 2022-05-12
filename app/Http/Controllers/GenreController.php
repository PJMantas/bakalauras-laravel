<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Genre;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GenreController extends Controller
{
    public function getGenresList(){

        $genres = DB::table('genres')
        ->get();

        return response()->json([
            'message' => 'Genre list successfully returned:',
            'genres' => $genres
        ], 201);
    }

    public function getGenre($id){

        $genre = Genre::find($id);

        return response()->json([
            'message' => 'Genre successfully returned:',
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

        $genre = new Genre();

        $genre->name = $request['name'];

        $genre->save();

        return response()->json([
            'message' => 'Genre successfully created',
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

        $genre = Genre::find($request['id']);

        $genre->name = $request['name'];

        $genre->save();

        return response()->json([
            'message' => 'Genre successfully updated',
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

        $genre = Genre::find($request['id']);

        $genre->delete();

        return response()->json([
            'message' => 'Genre successfully deleted',
            'genre' => $genre
        ], 201);
    }
}
