<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register (Request $request){
        
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'

        ]);
        //confirmed -> use to make user enter the password 2 times (password,password_confirmation)
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token 
        ];
        //201 -> created
        return response($response,201);
    }

    public function login (Request $request){
        
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'

        ]);
        // first -> يعني أول واحد بس هو كدا كدا مافيش غير واحد فابيكون اسرع علشان مش بيكمل في الجدول
        $user = User::where('email', $fields['email'])->first();

        if(! $user || !Hash::check($fields['password'], $user->password)){
            return response([
                'massage' => 'Bad creds'
            ],401);
            //401 -> unauth
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token 
        ];
        //201 -> created
        return response($response,201);
    }

    public function logout(Request $request){
        auth()->user()->tokens()->delete();

        return [
            'massage' => 'logged out'
        ];

    }
}
