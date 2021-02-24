<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Hash;

class AuthController extends Controller
{

	public function index(){

		return User::all();

	}
	public function register(Request $request){
		$request->validate([
			'name'=> 'required',
			'email' => 'email|required',
			'password' => 'required'
		  ]);
		$user=new User();
		$user->name=$request->name;
		$user->email=$request->email;
		$user->password=bcrypt($request->password);
		$user->save();
		return response()->json(['status_code'=> 200, 'message'=>'User created successfully!']);

	}



    public function login(Request $request)
	{
	  try {
	    $request->validate([
	      'email' => 'email|required',
	      'password' => 'required'
	    ]);
	    $credentials = request(['email', 'password']);
	    if (!Auth::attempt($credentials)) {
	      return response()->json([
	        'status_code' => 500,
	        'message' => 'Unauthorized'
	      ]);
	    }
	    $user = User::where('email', $request->email)->first();
	    if ( ! Hash::check($request->password, $user->password, [])) {
	       throw new \Exception('Error in Login');
	    }
	    $tokenResult = $user->createToken('authToken')->plainTextToken;
	    return response()->json([
	      'status_code' => 200,
	      'access_token' => $tokenResult,
	      'token_type' => 'Bearer',
	    ]);
	  } catch (Exception $error) {
	    return response()->json([
	      'status_code' => 500,
	      'message' => 'Error in Login',
	      'error' => $error,
	    ]);
	  }
	}
	 public function logout(Request $request){

		$request->user()->currentAccessToken()->delete();
		return response()->json([
			'status_code' => 200,
			'message' => 'Token deleted successfully.',
			
		  ]);
	 }
}
