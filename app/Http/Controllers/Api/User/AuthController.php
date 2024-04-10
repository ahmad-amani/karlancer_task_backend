<?php

namespace App\Http\Controllers\Api\User;


use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $userService;
    private $userRepository;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->userRepository = new UserRepository();
    }

    public function register(Request $request)
    {
        $user = $this->userService->register(request()->all());
        return response()->json(["status" => "success", "user" => $user]);
    }

    public function login(Request $request)
    {
        $user = $this->userService->login(request()->all());
        return response()->json(["status" => "success", "user" => $user]);
    }

    public function verify(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(['status' => 'failed', 'message' => 'Invalid verification link'], 400);
        }


        $user = $this->userService->verify($id);
        return response()->json(["status" => "success", "user" => $user]);

    }

    public function logout()
    {
        $this->userService->logout(Auth::user());
        return response()->json(["status" => "success"]);
    }

}
