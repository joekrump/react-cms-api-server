<?php

namespace App\Api\V1\Controllers;

use JWTAuth;
use Validator;
use Config;
use Cache;
use Carbon\Carbon;
use App\User;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Exceptions\JWTException;
use Dingo\Api\Exception\ValidationHttpException;
use App\Helpers\UserHelper;
use App\Transformers\AuthTransformer;

class AuthController extends Controller
{
    use Helpers;

    /**
     * Handles receiving an expired token and then trying to generate a new one
     * @return [type] [description]
     */
    public function refresh_token() {
        $token = JWTAuth::getToken();
        if(!$token){
            throw new BadRequestHtttpException('Token not provided');
        }
        try{
            $token = JWTAuth::refresh($token);
        }catch(TokenInvalidException $e){
            throw new AccessDeniedHttpException('The token is invalid');
        }
        return $this->response->withArray(['token'=>$token]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        $validator = Validator::make($credentials, [
            'email' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->response->errorUnauthorized();
            }
        } catch (JWTException $e) {
            return $this->response->error('could_not_create_token', 500);
        }

        $user = JWTAuth::toUser($token);

        // Put an entry in cache saying that the user is online
        $expiresAt = Carbon::now()->addMinutes(5);
        Cache::put('user-is-online-' . $user->id, true, $expiresAt);

        $user->logged_in = true;
        $user->save();
        $user = User::where('id', $user->id)->with('roles.permissions')->first();
        $authTransformer = new AuthTransformer();

        return response()->json([
            'token' => $token,
            'user' => $authTransformer->transform($user)
        ]);
    }

    public function signup(Request $request)
    {
        $signupFields = Config::get('boilerplate.signup_fields');
        $hasToReleaseToken = Config::get('boilerplate.signup_token_release');

        $userData = $request->only($signupFields);

        $validator = Validator::make($userData, Config::get('boilerplate.signup_fields_rules'));

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        $user = new User($userData);

        if(!$user->save()) {
            return $this->response->error('could_not_create_user', 500);
        } else {
            // Because signup process should only be allowed to occur for first user, 
            // make that user an admin.
            $adminRole = Role::where('name', 'admin')->first();
            $user->roles()->attach($adminRole->id);
        }

        if($hasToReleaseToken) {
            return $this->login($request);
        }
        
        return $this->response->created();
    }

    public function recovery(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => 'required'
        ]);

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject(Config::get('boilerplate.recovery_email_subject'));
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return $this->response->noContent();
            case Password::INVALID_USER:
                return $this->response->errorNotFound();
        }
    }

    public function reset(Request $request)
    {
        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $validator = Validator::make($credentials, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }
        
        $response = Password::reset($credentials, function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                if(Config::get('boilerplate.reset_token_release')) {
                    return $this->login($request);
                }
                return $this->response->noContent();

            default:
                return $this->response->error('could_not_reset_password', 500);
        }
    }

    public function logout(Request $request) {
        $currentUser = JWTAuth::parseToken()->authenticate();
        $currentUser->logged_in = false;
        $currentUser->save();
    }
}