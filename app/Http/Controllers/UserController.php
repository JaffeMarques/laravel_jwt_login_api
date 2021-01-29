<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ResponseService;
use Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\User\StoreUser;
use App\Transformers\User\UserResource;
use App\Transformers\User\UserResourceCollection;
use App\Models\User;
use JWTAuth;

class UserController extends Controller
{       
    private $user;
    
    /**
     * Set users to api consume
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(User $user){
        $this->user = $user;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\User\StoreUser  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUser $request)
    {
        try{   
            $isValidUser = ResponseService::validateUser($request);
            if(empty($isValidUser)){
                $user = $this->user->create($request->all());
            } else {
                return response()->json([
                    'message'   => $isValidUser,
                ], 400);
            } 
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('users.store', null, $e);
        }

        return new UserResource($user, array('type' => 'store', 'route' => 'users.store'));
    }

    
    /**
     * Try to login user on the system
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $data = $request->only('email', 'password');
        try {
            //try setting login data
            $token = $this->user->login($data);
        } catch (\Throwable|\Exception $e) {
            //Login exception
            return ResponseService::exception('users.login', null, $e);
        }
        return response()->json(compact('token'));
    }

    /**
     * Logout of system
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        try {
            $this->user->logout($request->input('token'));
        } catch (\Throwable|\Exception $e) {
            return ResponseService::exception('users.logout', null, $e);
        }

        return response(['status' => true,'msg' => 'Você foi deslogado #tchau'], 200);
    }

    /**
     * Get autenticate user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUser(Request $request)
    {
        //Check user permissions
        $currentUser = JWTAuth::toUser($request->input('token'));
        return response()->json(compact('currentUser'));
    }


    /**
     * Search by name and Career
     * Apply filter by bornDate
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function searchByNameAndCareer(Request $request)
    {
        //separate words, this help whit 2 or more spaces cases and avoid a lot problems
        $searchValues = preg_split('/\s+/', $request->search, -1, PREG_SPLIT_NO_EMPTY); 

        //i know that's a lot cases, but that way, we can see all the possibilities  
        if(isset($request->day) && isset($request->month) && isset($request->year)) {
            $results = User::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->orWhere('name', 'like', "%{$value}%")
                ->orWhere('career', 'like', "%{$value}%");
            }})->whereYear('born_date', '=', $request->year)
            ->whereMonth('born_date', '=', $request->month)
            ->whereDay('born_date', '=', $request->day)
            ->orderBy('born_date', 'DESC')->get();
            
        } else if(isset($request->day) && isset($request->month)) {
            $results = User::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->orWhere('name', 'like', "%{$value}%")
                ->orWhere('career', 'like', "%{$value}%");
            }})->whereDay('born_date', '=', $request->day)
            ->whereMonth('born_date', '=', $request->month)
            ->orderBy('born_date', 'DESC')->get();

        } else if(isset($request->day) && isset($request->year)) {
            $results = User::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->orWhere('name', 'like', "%{$value}%")
                ->orWhere('career', 'like', "%{$value}%");
            }})->whereYear('born_date', '=', $request->year)
            ->whereDay('born_date', '=', $request->day)
            ->orderBy('born_date', 'DESC')->get();   

        } else if(isset($request->day)) {
            $results = User::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->orWhere('name', 'like', "%{$value}%")
                ->orWhere('career', 'like', "%{$value}%");
            }})->whereDay('born_date', '=', $request->day)
            ->orderBy('born_date', 'DESC')->get();
            

        } else if(isset($request->month) && isset($request->year)) {
            $results = User::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->orWhere('name', 'like', "%{$value}%")
                ->orWhere('career', 'like', "%{$value}%");
            }})->whereMonth('born_date', '=', $request->month)
            ->whereYear('born_date', '=', $request->year)
            ->orderBy('born_date', 'DESC')->get();
            
        } else if(isset($request->year)) {
            $results = User::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->orWhere('name', 'like', "%{$value}%")
                ->orWhere('career', 'like', "%{$value}%");
            }})->whereYear('born_date', $request->year)
            ->orderBy('born_date', 'DESC')
            ->get();
    
        } else if(isset($request->month)) {
            $results = User::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->orWhere('name', 'like', "%{$value}%")
                ->orWhere('career', 'like', "%{$value}%");
            }})->whereMonth('born_date', $request->month)
            ->orderBy('born_date', 'DESC')->get();

        } else {
            $results = User::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->orWhere('name', 'like', "%{$value}%")
                ->orWhere('career', 'like', "%{$value}%");
            }})->orderBy('born_date', 'DESC')->get();    
                    
        }
        return response()->json(compact('results'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = User::find($request->id);
        if(!$user) {
            return response()->json([
                'message'   => 'Usuário não encontrado',
            ], 404);
        }
        //Check user permissions
        $currentUser = JWTAuth::toUser($request->input('token'));
    
        if($currentUser->id != $request->id){
            return response()->json([
                'message'   => "Você não tem permissão para fazer essa mudança",
            ], 405);
        }
        try{            
            $isValidUser = ResponseService::validateUpdatedUser($request->all());
            if($isValidUser == 1){
                $request->born_date = date_create_from_format("Y-m-d", $request->born_date);
                $user->fill([
                    "name" => $request->name,
                    "email" => $request->email,
                    "password" => Hash::make($request->password),
                    "career" => $request->career,
                    "born_date" => $request->born_date,
                    "pet" => $request->pet,
                    "cpf" => $request->cpf
                ]);
                $user->save();
                return response()->json($user);
            } else {
                return response()->json([
                    'message'   => $isValidUser,
                ], 400);
            } 
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('users.store', null, $e);
        }
        return new UserResource($user, array('type' => 'store', 'route' => 'users.store'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $currentUser = JWTAuth::toUser($request->input('token'));
    
        if($currentUser->delete()){
            try {
                $this->user->logout($request->input('token'));
            } catch (\Throwable|\Exception $e) {
                return ResponseService::exception('users.logout', null, $e);
            }
            return response()->json([
                'message'   => "Seu usuário foi excluído, esperamos te ver de novo",
            ], 200);
        }
    }
}
