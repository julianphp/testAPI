<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterUserController extends Controller
{
    //

    public function new(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
           'email' => 'email',
           'password' => 'min:4|string'
        ]);

        $user = new User();
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return response()->json([
            'error' => false,
            'msg' => 'El usuario se ha creado correctamente.'
        ]);

    }
}