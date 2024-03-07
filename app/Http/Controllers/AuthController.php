<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function register()
    {
            $validator = Validator::make(request()->all(),[
                'name'=>'required',
                'email'=>'required|email|unique:users',
                'password' => 'required',
            ]);
    
            if($validator->fails()){
                return response()->json([
                    "success" => false,
                    "code" => 400,
                    "message" => $validator->messages()->all()
                ], 400);
            }
            
            $user = User::create([
                'name'=> request('name'),
                'email'=> request('email'),
                'password'=> Hash::make(request('password')),
            ]);

    
            if($user){
                return response()->json([
                    'success' => true,
                    'message' => 'Pendaftaran Berhasil']);
                    
                }else{
                    return response()->json([
                        'message' => 'Pendaftaran Gagal',
                        'success' => false
                ]);
                }
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

   
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
