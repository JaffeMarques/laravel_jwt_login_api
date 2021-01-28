<?php

namespace App\Transformers\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\ResponseService;

class UserResource extends JsonResource
{
    /**
     * @var
     */
    private $config;

    /**
     * New resource instance
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource, $config = array())
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);

        $this->config = $config;
    }
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'career' => $this->career,
            'pet' => $this->pet,
            'cpf' => $this->cpf,
            'born_date' => $this->born_date,
            'password' => '*********'
        ];
    }

    /**
     * additional data
     *
     * @param \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return ResponseService::default($this->config, $this->id);
    }

    /**
     * outgoing resource
     *
     * @param  \Illuminate\Http\Request
     * @param  \Illuminate\Http\Response
     * @return void
     */
    public function withResponse($request, $response)
    {
        //Success return
        $response->setStatusCode(200);
    }
}