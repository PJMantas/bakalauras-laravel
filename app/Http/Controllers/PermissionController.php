<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function getAuthUserPermissions(){
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $permissions = new Permission();

        $userGroup = $user->group_id;

        $permissions = DB::select('select * from permissions where id = ?', [$userGroup]);

        return response()->json([
            'message' => 'Naudotojo teisės sėkmingai gautos',
            'permissions' => $permissions[0]
        ], 200);
    }

    public function getPermissionsList(){

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Neregistruotas naudotojas'], 401);
        }

        $permission = Permission::where('id', $user->group_id)->get();

        if(!$permission[0]->manage_permissions){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        $permissions = new Permission();

        $permissions = DB::select('select * from permissions');

        return response()->json([
            'message' => 'Visos teisių grupės sėkmingai gautos',
            'permissions' => $permissions
        ], 200);
    }

    public function getPermission(Request $request){

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

        if(!$permission[0]->manage_permissions){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        $permission = Permission::where('id', $request['id'])->get();

        return response()->json([
            'message' => 'Teisių grupė sėkmingai gauta',
            'permission' => $permission[0]
        ], 200);
    }

    public function addPermission(Request $request){
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string|between:2,100',
            'video_create' => 'boolean',
            'video_edit' => 'boolean',
            'video_delete' => 'boolean',

            'reaction_create' => 'boolean',
            'comment_create' => 'boolean',
            'comment_edit' => 'boolean',
            'comment_delete' => 'boolean',

            // administratoriaus teisės
            'is_admin' => 'boolean',
            'manage_users' => 'boolean',
            'manage_permissions' => 'boolean',
            'manage_genres' => 'boolean',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Neregistruotas naudotojas'], 401);
        }

        $permission = Permission::where('id', $user->group_id)->get();

        if(!$permission[0]->manage_permissions){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        $permission = new Permission();

        $permission->group_name = $request['group_name'];
        $permission->video_create = $request['video_create'];
        $permission->video_edit = $request['video_edit'];
        $permission->video_delete = $request['video_delete'];

        $permission->reaction_create = $request['reaction_create'];
        $permission->comment_create = $request['comment_create'];
        $permission->comment_edit = $request['comment_edit'];
        $permission->comment_delete = $request['comment_delete'];

        // administratoriaus teisės
        $permission->is_admin = $request['is_admin'];
        $permission->manage_users = $request['manage_users'];
        $permission->manage_permissions = $request['manage_permissions'];
        $permission->manage_genres = $request['manage_genres'];

        $permission->save();

        return response()->json([
            'message' => 'Teisių grupė sėkmingai sukurta',
            'permission' => $permission
        ], 201);

    }

    public function updatePermission(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'group_name' => 'required|string|between:2,100',
            'video_create' => 'boolean',
            'video_edit' => 'boolean',
            'video_delete' => 'boolean',

            'reaction_create' => 'boolean',
            'comment_create' => 'boolean',
            'comment_edit' => 'boolean',
            'comment_delete' => 'boolean',

            // administratoriaus teisės
            'is_admin' => 'boolean',
            'manage_users' => 'boolean',
            'manage_permissions' => 'boolean',
            'manage_genres' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Neregistruotas naudotojas'], 401);
        }

        $permission = Permission::where('id', $user->group_id)->get();

        if(!$permission[0]->manage_permissions){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        $permission = new Permission();

        $permission = Permission::find($request['id']);

        if(!$permission){
            return response()->json([
                'message' => 'Teisių grupė nerasta'
            ], 404);
        }
        
        $permission->group_name = $request['group_name'];
        $permission->video_create = $request['video_create'];
        $permission->video_edit = $request['video_edit'];
        $permission->video_delete = $request['video_delete'];

        $permission->reaction_create = $request['reaction_create'];
        $permission->comment_create = $request['comment_create'];
        $permission->comment_edit = $request['comment_edit'];
        $permission->comment_delete = $request['comment_delete'];
        
        // administratoriaus teisės
        $permission->is_admin = $request['is_admin'];
        $permission->manage_users = $request['manage_users'];
        $permission->manage_permissions = $request['manage_permissions'];
        $permission->manage_genres = $request['manage_genres'];

        $permission->save();

        return response()->json([
            'message' => 'Teisių grupė sėkmingai atnaujinta',
            'permission' => $permission
        ], 200);
    }

    public function deletePermission(Request $request){
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

        if(!$permission[0]->manage_permissions){
            return response()->json(['error' => 'Nėra teisių'], 401);
        }

        Permission::where('id', $request['id'])->delete();

        return response()->json([
            'message' => 'Teisių grupė sėkmingai ištrinta'
        ], 200);
    }
    
}
