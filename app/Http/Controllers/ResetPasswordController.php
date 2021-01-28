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

class ResetPasswordController extends Controller
{
    /**
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function renew(Request $request)
    {
        $token = DB::table('password_resets')->where('token', $request->token)->first();
        if(!$token) {
            return response()->json(['status','Link expirado'],404);
        }
        $atual = Carbon::now();
        $old = Carbon::parse($token->created_at);
        if($atual->diffInMinutes($old, true) > 10){
            return response()->json(['status','Link expirado'],404);
        }
        if($request->password != $request->passwordb){
            return response()->json(['status','As senhas devem ser iguais'],400);
        }
        if(preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/", $request->password) < 1){
            return   response()->json(['status', 'Deve conter pelo menos 8 caracteres, 1 caractere especial e 1  número'], 400);
        }
        $user = User::where('email', $token->email)->first();
        $user->fill([
            "password" => Hash::make($request->password)
        ]);
        $user->save();
        DB::table('password_resets')->where('token', $request->token)->delete();
        return response()->json(['status','Nova senha salva!'], 200);
    }
}