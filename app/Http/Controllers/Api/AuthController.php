<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => ['required', 'string', 'email', 'max:255',],
            'password' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (auth()->attempt($request->all())) {
            return response([
                'user' => auth()->user(),
                'access_token' => auth()->user()->createToken('Laravel9PassportAuth')->accessToken,

            ], Response::HTTP_OK);
        }

        return response([
            'message' => 'This User does not exist.'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
            ]);
            $token = $user->createToken('Laravel9PassportAuth')->accessToken;
            return response()->json(['token ' => $token],Response::HTTP_OK);

        } catch (Exception $e) {
            return response()->json(['message' => 'Terjadi Kesalahan.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
