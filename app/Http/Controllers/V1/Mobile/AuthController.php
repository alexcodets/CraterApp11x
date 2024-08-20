<?php

namespace Crater\Http\Controllers\V1\Mobile;

use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AuthController", "login");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $request->validate([
            'username' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
        'type' => 'Bearer',
        'token' => $user->createToken($request->device_name)->plainTextToken,
        ], "message" => "login mobile"];
        LogsDev::finishLog($log, $res, $time, 'D', "login mobile");
        /////////////////////////////////////////

        /*  //Log::debug('$user->role----------');
         //Log::debug($user->role); */

        return response()->json([
            'type' => 'Bearer',
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'role' => $user->role
        ]);
    }

    public function logout(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AuthController", "logout");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo



        $request->user()->currentAccessToken()->delete();


        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "login mobile"];
        LogsDev::finishLog($log, $res, $time, 'D', "login mobile");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }
}
