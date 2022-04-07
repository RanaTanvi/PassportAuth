<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
class PassportAuthController extends Controller
{
    /**
        * handle user registration request
     */
    /**
         * @OA\Post(
         * path="/register",
         * summary="Sign up",
         * description="register user",
         * operationId="authLogin",
         * tags={"auth"},
         * @OA\RequestBody(
         *    required=true,
         *    description="Pass user credentials",
         *    @OA\JsonContent(
         *       required={"name","email","password"},
         *    @OA\Property(property="name", type="string",  example="test"),
         *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
         *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
         * 
         *    ),
         * ),
         * @OA\Response(
             *          response=200,
             *          description="Successful operation",
             *        
             *       ),
             *      @OA\Response(
             *          response=401,
             *          description="Unauthenticated",
             *      ),
             *      @OA\Response(
             *          response=403,
             *          description="Forbidden"
             *      )
         * )
 */

    public function registerUser(Request $request)
    { 
            $data = $request->all();
            $validator = Validator::make($data, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:7',
        
        ]);
        if($validator->fails()){
            return response(['error' => $validator->errors(), 
            'Validation Error']);
        }

    
            $user= User::create([
                'name' =>$request->name,
                'email'=>$request->email,
                'password'=>bcrypt($request->password)
            ]);
        
            $access_token_example = $user->createToken('PassportExample@Section.io')->accessToken;
            //return the access token we generated in the above step
        
            return response()->json(['token'=>$access_token_example],200);
    }
     /**
     * login user to our application
     */
     /**
         * @OA\Post(
         * path="/login",
         * summary="Sign in",
         * description="login user",
         * operationId="authLogin",
         * tags={"auth"},
         * @OA\RequestBody(
         *    required=true,
         *    description="Pass user credentials",
         *    @OA\JsonContent(
         *       required={"email","password"},
         *  
         *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
         *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
         * 
         *    ),
         * ),
         * @OA\Response(
             *          response=200,
             *          description="Successful operation",
             *        
             *       ),
             *      @OA\Response(
             *          response=401,
             *          description="Unauthenticated",
             *      ),
             *      @OA\Response(
             *          response=403,
             *          description="Forbidden"
             *      )
         * )
 */
    public function loginUser(Request $request){
        $login_credentials=[
            'email'=>$request->email,
            'password'=>$request->password,
        ];
  
        if(auth()->attempt($login_credentials)){
            //generate the token for the user
            $user_login_token= auth()->user()->createToken('PassportExample@Section.io')->accessToken;
            //now return this token on success login attempt
            return response()->json(['token' => $user_login_token], 200);
        }
        else{
            //wrong login credentials, return, user not authorised to our system, return error code 401
            return response()->json(['error' => 'UnAuthorised Access'], 401);
        }
    }
}
