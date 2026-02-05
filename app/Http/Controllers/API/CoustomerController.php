<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Coustomer;
use App\Models\ForogtPassword;
use Dotenv\Exception\ValidationException;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Notifications\RestPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Throwable;
class CoustomerController extends Controller
{
    function register(Request $request){
      $request->validate([
         'name'=>'required',
         'email'=>'required',
         'password'=>'required',
      ]);

      try {
        Coustomer::insert([
         'name'=>$request->name,
         'email'=>$request->email,
         'password'=>bcrypt($request->password),
        ]);
        return response()->json([
            'success'=>'Customer Register Successful',
        ]);
      }
      catch (\Throwable $th) {
        return response()->json([
            'error'=>'Something Went Wrong',
        ]);
      }


    }

    function login(Request $request){
      $customer = Coustomer::where('email', $request->email)->first();
      if (! $customer || ! Hash::check($request->password, $customer->password)) {
          return response()->json([
              'error' => 'The provided credentials are incorrect.',
          ], 401);
      }
      $token = $customer->createToken('token')->plainTextToken;

      $customerData = $customer->toArray();
      $customerData['user_type'] = 'customer';

      return response()->json([
          'message' => 'Login successful',
          'customer' => $customerData,
          'token' => $token,
      ]);
}

function logout(Request $request){
    if ($request->user()) {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'User Logged Out Successfully',
        ], 200);
    }
     else {
       return response()->json([
           'message' => 'Already Logged Out',
       ], 200);
   }
}

function user(Request $request)
{
    $user = $request->user();
    $userData = $user->toArray();
    $userData['user_type'] = 'customer';
    return response()->json($userData);
}

function update(Request $request, $id)
{
    if ($request->current_password == '') {
        Coustomer::find($id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
        ]);
        return response()->json([
            'success' => 'Coustomer Info Updated Successful',
        ]);
    } else {
        $request->validate([
            'password' => 'required',
            'current_password' => 'required',
            'password_confirmation' => 'required',
        ]);
        try {
            $customer = Coustomer::find($id);
            if (!$customer || !password_verify($request->current_password, $customer->password)) {
                return response()->json([
                    'wrong' => 'Customer Current Password not matched',
                ]);
            }
            Coustomer::find($id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'password' => bcrypt($request->password),
            ]);
            return response()->json([
                'success' => 'Coustomer Info Updated Successful',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something Went Wrong',
            ]);
        }
    }
}

function forgot_password(Request $request){
  if(Coustomer::where('email',$request->email)->exists()){
    $customer = Coustomer::where('email',$request->email)->first();
    $token =uniqid();

    ForogtPassword::insert([
        'coustomer_id'=>$customer->id,
        'token'=>$token,
    ]);

   Notification::send($customer, new ResetPassword($token));
    return response()->json([
    'token'=>'Request Sent Successfully',
   ]);
  }
  else{
    return response()->json([
    'notExists'=>'Coustomer Email Not Exists',
]);
}
}

function reset_password(Request $request){
    if(ForogtPassword::where('token',$request->token)->exists()){
   $coustomer =ForogtPassword::where('token',$request->token)->first();
Coustomer::find($coustomer->coustomer_id)->update([
    'password'=>bcrypt($request->new_password),
]);

$coustomer->delete();
return response()->json([
    'success'=>'Password Reset Successully',
]);
    }
else{
   return response()->json([
       'notExists'=>'Password does not exists',
   ]);
}

}

function google_auth(Request $request){
$request->validate([
   'id_token' => 'required',
]);

try {
   // Verify the Google ID token and get the user's email
   $clientId = env('GOOGLE_CLIENT_ID');
   
   if (!$clientId || $clientId === 'your-google-client-id.apps.googleusercontent.com') {
       return response()->json([
           'error' => 'Google OAuth not configured properly. Please contact support.',
       ], 500);
   }
   
   $client = new \Google_Client(['client_id' => $clientId]);
   $payload = $client->verifyIdToken($request->id_token);
 
   if (!$payload) {
       return response()->json([
           'error' => 'Invalid Google ID token',
       ], 401);
   }
 
   $email = $payload['email'];
   $name = $payload['name'] ?? 'Google User';
 
   // Check if the user already exists
   $customer = Coustomer::where('email', $email)->first();
 
   if (!$customer) {
       // Create a new user if they don't exist
       $randomPassword = bin2hex(random_bytes(8)); // Generate random password
       $customer = Coustomer::create([
           'name' => $name,
           'email' => $email,
           'password' => bcrypt($randomPassword), // Random password since they're using Google auth
       ]);
   }
 
   // Generate a token for the user
   $token = $customer->createToken('token')->plainTextToken;
 
   $customerData = $customer->toArray();
   $customerData['user_type'] = 'customer';
 
   return response()->json([
       'message' => 'Signed in with Google successfully',
       'customer' => $customerData,
       'token' => $token,
   ]);
} catch (\Exception $e) {
   return response()->json([
       'error' => 'Google authentication failed: ' . $e->getMessage(),
   ], 500);
}
}
}
