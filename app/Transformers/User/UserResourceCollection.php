<?php

namespace App\Transformers\User;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Services\ResponseService;

class UserResourceCollection extends ResourceCollection
{
  /**
   * New instance of resource
   *
   * @param  mixed  $resource
   * @return void
  */
  public function toArray($request)
  {  
    return ['data' => $this->collection];
  }

  /**
   * Additional data
   *
   * @param \Illuminate\Http\Request  $request
   * @return array
  */
  public function with($request)
  {
    return [
      'status' => true,
      'msg'    => 'Listando dados',
      'url'    => route('users.index')
    ];
  }

  /**
   * Customize the outgoing response for the resource.
   *
   * @param  \Illuminate\Http\Request
   * @param  \Illuminate\Http\Response
   * @return void
   */
  public function withResponse($request, $response)
  {
    //success requisition
    $response->setStatusCode(200);
  }
}