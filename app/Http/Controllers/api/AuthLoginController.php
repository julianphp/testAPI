<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
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
        $token = Auth::user()->createToken(23234234234);

        return response()->json([
            'error' => false,
            'token_2' => $token->accessToken
        ]);
    }
}
