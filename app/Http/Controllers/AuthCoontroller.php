<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
//use Tymon\JwtAuth\Facades\JwtAuth;
use Laravel\Passport\TokenRepository;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Carbon\Carbon;

class AuthCoontroller extends Controller
{
    public $successStatus = true;
    public $failedStatus = false;


    public function Login()
    {
        try {
            //Login to account

                $credentials = request(['email', 'password']);

                Passport::tokensExpireIn(Carbon::now()->addDays(3));
                Passport::refreshTokensExpireIn(Carbon::now()->addDays(3));

            if (! auth()->attempt($credentials)) {
                return response()->json([
                    'status'=>$this->failedStatus,
                    'error' => 'Unauthorized'
                ], 401);
            }

            $token = auth()->user()->createToken('API Token')->accessToken;
    
    
            Auth::guard('api')->check();
    
        return response()->json([
            "status" => $this->successStatus,
            'message' => "login Successfully",
            'user' => auth()->user()->load(['locations']), 
            'token' => $token,
            'expiresIn' => Auth::guard('api')->check(),
        ],200);
        } catch (Exception $e) {
            return response()->json([
                'status' => $this->failedStatus,
                'message'    => 'Error',
                'errors' => $e->getMessage(),
            ], 401);
        }
    }


    public function logout()
    {
        auth()->logout();

        return response()->json(['status' => $this->successStatus,'message' => 'Successfully logged out'],200);
    }

    public function createNewToken($token){
        return response()->json(
            [
                'status' => $this->successStatus,
                'expiresIn' => auth('api')->factory()->getTTL()*60*60*3,
                'user'=> auth()->user(),
                'tokenType' =>'Bearer',
                'accessToken' => $token,
                
            ],200
            );
       
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|max:100|unique:users',
            'phone' => 'required|string',
            'location' => 'required|string',
            'role' => 'required|string',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if($validator->fails()){
            return response()->json(['status' => $this->failedStatus,$validator->errors()->toJson()], 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => Hash::make($request->password)]
                ));
                $token = $user->createToken('API Token')->accessToken;
        return response()->json([
            'status' => $this->successStatus,
            'message' => 'User successfully registered',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function refresh()
    {
        return $this->createToken(auth()->refresh());
    }

    public function deviceId(Request $request)
    {
        $deviceId = User::find(Auth::id());
        $deviceId->update(['device_id' => $request->device_id]);
        return response()->json([
            'status' => $this->successStatus,
            'message' => 'DeviceId Updated',
            "data" => $deviceId->device_id
        ],200);
    }
    
    
}
