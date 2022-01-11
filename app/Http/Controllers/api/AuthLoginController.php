<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthLoginController extends Controller
{
    //

    /**
     * @param Request $req
     * @return JsonResponse
     */
    public function login(Request $req): JsonResponse
    {

        if (!Auth::attempt(['email' => $req->input('email'),'password' => $req->input('password')])){
            return response()->json([
                'error' => true,
                'msg' => trans('messages.user_incorrect')
            ]);
        }
        $tokenResult = Auth::user()->createToken(Auth::user()->email);
        $token  = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeek();
        $token->save();
        return response()->json([
            'error' => false,
            'token' => $tokenResult->accessToken
        ]);
    }

    public function logout(): JsonResponse
    {

        Auth::user()->token()->revoke();

        return response()->json([
            'error' =>false,
            'status' => trans('messages.user_logout_success')
        ]);
    }
}
