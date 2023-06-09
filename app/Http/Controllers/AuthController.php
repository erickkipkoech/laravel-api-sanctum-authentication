<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //Create User
    public function register(Request $request){
        $fields=$request->validate([
            'name'=>'required|string',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|confirmed'
        ]);
        $user=User::create([
            'name'=>$fields['name'],
            'email'=>$fields['email'],
            'password'=>bcrypt($fields['password'])
        ]);

        $token=$user->createToken('myapptoken')->plainTextToken;

        $response=[
            'user'=>$user,
            'token'=>$token
        ];
        
        return response($response,201);
    }
//Login
public function login(Request $request){
    $fields=$request->validate([
        'email'=>'required|email',
        'password'=>'required'
    ]);
    //Check email
    $user=User::where('email',$fields['email'])->first();
    //Check password
    if(!$user || !Hash::check($fields['password'],$user->password)){
        return response([
            'message'=>'Invalid Credentials'
        ],401);
    }
    $token=$user->createToken('myapptoken')->plainTextToken;
    $response=[
        'user'=>$user,
        'token'=>$token
    ];
    return response($response,200);
    
}
//Logout
    public function logout(){
        auth()->user()->tokens()->delete();
        return [
            'message'=>'Logged out'
        ];
    }
}
