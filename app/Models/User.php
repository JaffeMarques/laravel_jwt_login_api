<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
/**
 * imports for jwt
 */
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
/**
 * End JWT
 */

use Hash;
use Illuminate\Notifications\Notifiable;

//using JWT interface to login autenticate
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'career', 'pet', 'cpf', 'born_date',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function create($fields)
    {   
        return parent::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'career' => $fields['career'],
            'pet' => $fields['pet'],
            'cpf' => $fields['cpf'],
            'born_date' => date_create_from_format("Y-m-d", $fields['born_date'])->format("Y-m-d"),
        ]);
    }

    public function logout($token){
        if (!JWTAuth::invalidate($token)) {
            throw new \Exception('Tente novamente', -404);
        }
    }

    public function login($data){
        if (!$token = JWTAuth::attempt($data)) {
         throw new \Exception('Email ou senha incorretos, tente novamente', -404);
        }
        return $token;
    }
    
    /**
     * Getters 
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
