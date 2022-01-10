<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthLoginController extends Controller
{
    //

    public function login(Request $req){

        if (!Auth::attempt(['email' => $req->input('email'),'password' => $req->input('password')])){
            return response()->json([
                'error' => true,
                'msg' => 'Las credenciales no son correctas'
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
}
