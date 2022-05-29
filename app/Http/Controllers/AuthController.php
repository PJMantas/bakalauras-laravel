<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Tokio naudotojo nėra, blogas E-pašas arba slaptažodis'], 401);
        }
        return $this->createNewToken($token);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,100|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'age' => 'nullable|numeric',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'avatar_url' => 'file|mimes:jpg,png,jpeg,gif|max:5120',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if ($request->hasFile('avatar_url'))
        {
            $path = $request->file('avatar_url')->store('users', ['disk' => 'users']);
        }
        else{
            $path = "users/default_profile.jpg";
        }

        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password),
                    'avatar_url' => $path]
                ));
        return response()->json([
            'message' => 'Naudotojas sėkmingai užregistruotas',
            'user' => $user
        ], 201);
    }

    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'Naudotojas sėkmingai atjungtas'], 200);
    }

    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    public function userProfile() {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Naudotojas neautentifikuotas'], 401);
        }
        return response()->json(auth()->user());
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}