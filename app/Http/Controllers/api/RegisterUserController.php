<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterUserController extends Controller
{
    //

    public function new(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
           'email' => 'email',
           'password' => 'min:4|string'
        ]);

        try {
            $user = new User();
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->save();
        } catch (\Exception $e) {
            Log::channel('daily')->debug($e);
            return response()->json([
                'error' => true,
                'msg' => trans('messages.error_process_request')
            ]);
        }

        return response()->json([
            'error' => false,
            'msg' => 'El usuario se ha creado correctamente.'
        ]);

    }
}
