<?php

namespace App\Services;
use App\Http\Requests\User\StoreUser;


Class ResponseService{
    
  /**
   * Default Responses for failed and success jobs
   *
   * @return void
   */
  public static function default($config = array(), $id = null){
    $route = $config['route'];

    switch($config['type']){
      case 'store':
        return [
          'status' => true,
          'msg'    => 'Cadastrado com sucesso',
          'url'    => route($route)
        ];
        break;
      case 'show':
        return [
          'status' => true,
          'msg'    => 'Requisição realizada com sucesso',
          'url'    => $id != null ? route($route,$id) : route($route)
        ];
        break;
      case 'update':
        return [
          'status' => true,
          'msg'    => 'Dados Atualizado com sucesso',
          'url'    => $id != null ? route($route,$id) : route($route)
        ];
        break;
      case 'destroy':
        return [
          'status' => true,
          'msg'    => 'Dado excluido com sucesso',
          'url'    => $id != null ? route($route,$id) : route($route)
        ];
        break;
    }
  }

  /**
   * Register services.
   *
   * @return void
   */
  public static function exception($route, $id = null, $e)
  {
    switch($e->getCode()){
      case -403:
        return response()->json([
          'status' => false,
          'statusCode' => 403,
          'error'  => $e->getMessage(),
          'url'    => $id != null ? route($route, $id) : route($route)
        ], 403);
        break;
      case -404:
        return response()->json([
          'status' => false,
          'statusCode' => 404,
          'error'  => $e->getMessage(),
          'url'    => $id != null ? route($route, $id) : route($route)
        ], 404);
        break;
      default:
        if (app()->bound('sentry')) {
          $sentry = app('sentry');
          $user = auth()->user();
          if($user){
              $sentry->user_context(['id' => $user->id, 'name' => $user->name]);
          }
          $sentry->captureException($e);
        }
        return response()->json([
          'status' => false,
          'statusCode' => 500,
          'error'  => $e->getMessage(),
          'url'    => $id != null ? route($route, $id) : route($route)
        ], 500);
        break;
    }
  }

  /**
   * Test validation whit customers warnings
   *
   * @param  array $request
   * @return json
   */
  public static function validateUser(StoreUser $request){
    if($request->password != $request->passwordb){
      return 'As senhas não conferem';
    }
    if(preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/", $request->password) < 1){
      return  "Deve conter pelo menos 8 caracteres, 1 caractere especial e 1  número";
    }
    if(empty($request->name) || (preg_match('/\s/', $request->name) < 1) ){
      return 'Preencha com o nome e sobrenome';
    }
    if(empty($request->career)){
      return 'Campo obrigatório';
    }
    if(preg_match('/^\d{3}\x2E\d{3}\x2E\d{3}\x2D\d{2}$/', $request->cpf) < 1){
      return 'Preencha com um CPF válido';
    }
    if(preg_match('/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/', $request->born_date) < 1){
      return 'Preencha com um formato válido: dd/mm/aaaa';
    }
    if (time() < strtotime('+18 years', strtotime($request->born_date))) {
      return 'Você precisa ter mais de 18 anos';
    }
    return 1;
  }

  /**
   * Test validation whit customers warnings
   *
   * @param  \Illuminate\Http\Request $request
   * @return json
   */
  public static function validateUpdatedUser(Array $request){
    if($request['password'] != $request['passwordb']){
      return 'As senhas não conferem';
    }
    if(preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/", $request['password']) < 1){
      return  "Deve conter pelo menos 8 caracteres, 1 caractere especial e 1  número";
    }
    if(empty($request['name']) || (preg_match('/\s/', $request['name']) < 1) ){
      return 'Preencha com o nome e sobrenome';
    }
    if(empty($request['career'])){
      return 'Campo obrigatório';
    }
    if(preg_match('/^\d{3}\x2E\d{3}\x2E\d{3}\x2D\d{2}$/', $request['cpf']) < 1){
      return 'Preencha com um CPF válido';
    }
    if(preg_match('/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/', $request['born_date']) < 1){
      return 'Preencha com um formato válido: dd/mm/aaaa';
    }
    if (time() < strtotime('+18 years', strtotime($request['born_date']))) {
      return 'Você precisa ter mais de 18 anos';
    }
    return 1;
  }
}