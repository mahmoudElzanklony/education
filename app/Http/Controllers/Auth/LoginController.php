<?php

namespace App\Http\Controllers\Auth;

use App\Actions\DefaultAddress;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\roles;
use App\Models\User;
use App\Services\Messages;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    //
    public function login()
    {
        $data = ['phone'=>request('phone'),'password'=>request('password')];
        if(auth()->attempt($data)){
            // check ip
            $user = User::query()->where('phone',$data['phone'])->first();
            if($user->type == 'client'){
                if($user->otp_secret == null){
                    $user->otp_secret = request()->ip();
                    $user->save();
                }else if($user->otp_secret != request()->ip()){
                    return Messages::error('هذا الجهاز ليس الجهاز الاول الذي قمت بالدخول الي التطبيق من خلاله');
                }
            }
            $user['token'] = $user->createToken($data['phone'])->plainTextToken;
            return Messages::success(__('messages.login_successfully'),UserResource::make($user));
        }else{
            return Messages::error(__('errors.email_or_password_is_not_correct'));
        }
    }

    public function logout()
    {
        auth()->logout();
        return Messages::success(__('messages.logout_successfully'));
    }

    public function get_user_by_token(){
        if(request()->hasHeader('token')) {
            $token = request()->header('token');
            request()->headers->set('token', (string)$token, true);
            request()->headers->set('Authorization', 'Bearer ' . $token, true);
            try {
                $token = JWTAuth::parseToken();
                $user = $token->authenticate();

                if ($user == false) {
                    return Messages::error(['invalid credential']);
                }
                // token
                $user['token'] =  request()->header('token');

                return Messages::success('',UserResource::make($user));


            } catch (\Exception $e) {
                return Messages::error([$e->getMessage()]);
            }
        }
    }

}
