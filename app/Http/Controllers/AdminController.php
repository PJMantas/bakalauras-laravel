<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
//use DB;

class AdminController extends Controller
{
    public function getUsersList(Request $request){

        $users = DB::select('select id, username, email, first_name, last_name, age, city, country, group_id from users ');

        return response()->json([
            'message' => 'Users list successfully returned:',
            'users' => $users
        ], 201);

    }

    public function addUser(Request $request){

    	$validator = Validator::make($request->all(), [
            
            'username' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'first_name' => 'string|between:2,100',
            'last_name' => 'string|between:2,100',
            'age' => 'numeric',
            'country' => 'string|between:2,100',
            'city' => 'string|between:2,100',
            'group_id' => 'required|numeric',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = new User();

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        $user->email = $request['email'];
        $user->username = $request['username'];
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->password = $request['password'];
        $user->age = $request['age'];
        $user->country = $request['country'];
        $user->city = $request['city'];
        $user->group_id = $request['group_id'];

        $user->save();


        return response()->json([
            'message' => 'User successfully added',
            'user' => $user
        ], 201);
    }

    public function deleteUser(Request $request){
        $validator = Validator::make($request ->all(), [
            'id' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::table('users')->where(
            'id', $request['id'])
            ->delete();
        
        return response()->json(200);
        
    }

    public function adminUpdateUser(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric', 
            'first_name' => 'string|between:2,100',
            'last_name' => 'string|between:2,100',
            'age' => 'numeric',
            'country' => 'string|between:2,100',
            'city' => 'string|between:2,100',
            'group_id' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::find($request['user_id']);

        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->age = $request['age'];
        $user->country = $request['country'];
        $user->city = $request['city'];
        $user->group_id = $request['group_id'];

        $user->save();

        return response()->json([
            'message' => 'User edited',
            'user' => $user
            ], 201);
    }

    
}
