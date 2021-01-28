<?php

namespace App\Http\Controllers;

use App\Transformers\User\UserResourceCollection;
use Illuminate\Support\Facades\Validator;
use App\Transformers\User\UserResource;
use App\Http\Requests\User\StoreUser;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use JWTAuth;
use Hash;
use DB;

class ForgotPasswordController extends Controller
{
    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getResetToken(Request $request)
    {
        $user = User::where('email', $request->email)->where('cpf', $request->cpf)->first();
        if (!$user) {
            return response()->json(['Usuário não encontrado'], 404);
        }
        $token = $request->email.$request->cpf.Carbon::now()->toDateTimeString();
        $token = Hash::make($token);
        $token = preg_replace('/[^A-Za-z0-9\-]/', '', $token);
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token'  => $token,
            'created_at' => Carbon::now()
        ]);
        //sendemail
        return response()->json(compact('token'));
    }
}