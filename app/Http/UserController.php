<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Kreait\Firebase\Auth;

class UserController extends Controller
{

    public $auth;
  
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }
     
    public function index(Request $request)
    {
        $users = $this->auth->listUsers($defaultMaxResults = 1000, $defaultBatchSize = 1000);
   
        foreach ($users as $k => $v) {
            $response[$k] = $v;
        }
        echo json_encode($response);
     }
     
     public function update(Request $request, $uid)
     {   
         $this->validate($request, [
             'role' => 'present|string|max:20',
         ]);
         
         $customAttributes = [
           'role' => $request->role,
         ];
         
         $updatedUser = $this->auth->setCustomUserAttributes($uid, $customAttributes);
         
         
         
         return $this->auth->getUser($uid);
     }
    public function foo(Request $request, Guard $guard)
    {
        
        // Retrieve Firebase uid from id token via request
        $user = $request->user();
        $uid = $user->getAuthIdentifier();
        
        // Or, do the same thing using guard instead
        $user = $guard->user();
        $uid = $user->getAuthIdentifier();
        
        
        // Do something with the request for this user
    }

    public function bar()
  {
     //Check if logged in and retrieve user object and uid using Auth Facade
     $isLoggedIn = Auth::guard('api')->check();
     $userObject = Auth::guard('api')->user();
     $uid = Auth::guard('api')->id();
     
     //Alternatively, use auth() helper
     $isLoggedIn = auth('api')->check();
     $userObject = auth('api')->user();
     $uid = auth('api')->id();
     
  }
   
}
