<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function createAdmin(Request $request){
    	$fields = $request->validate([
    		'name' => 'required|string',
    		'email' => 'required|string|unique:users,email',
    		'password' => 'required|string',
    		'user_type' => 'required|string'
    	]);

    	$user = User::create([
    		'name' => $fields['name'],
    		'email' => $fields['email'],
    		'password' => bcrypt($fields['password']),
    		'user_type' => $fields['user_type'],
    	]);

    	$token = $user->createToken('testapptoken')->plainTextToken;

    	$response = [
    		'user' => $user,
    		'token' => $token,
    	];

    	return response($response, 201);
    }

    public function logout(Request $request){
    	auth()->user()->tokens()->delete();

    	return response([
    		'message' => 'Logged out'
    	]);
    }

    public function login(Request $request){
    	$fields = $request->validate([
    		'email' => 'required|string',
    		'password' => 'required|string'
    	]);

    	// Checking email
    	$user = User::where('email', $fields['email'])->first();

    	//Checking password
    	if(!$user || Hash::check($fields['password'], $user->password)){
    		return response(['message' => 'Invalid email or password'], 401);
    	}else {
    		$token = $user->createToken('testapptoken')->plainTextToken;

    		$response = [
    			'user' => $user,
    			'token' => $token,
    		];

    		return response($response, 201);
    	}
    }
}
