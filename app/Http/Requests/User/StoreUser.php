<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUser extends FormRequest
{
    /**
     * Check if the user is authorized
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Getter for validate requisition
     * Check if the email is unique and if name and password are != null
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'email'     => 'unique:users,email|email|required',
            'name'      => 'required',
            'cpf'       => 'required',
            'born_date' => 'required',
            'password'  => 'required'
        ];
    }

    /**
     * Test validation
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'msg'    => 'Você precisa preencher todos os campos corretamente',
                'status' => false,
                'errors' => $validator->errors(),
                'url'    => route('users.store')
            ], 400));
       }
    }
}