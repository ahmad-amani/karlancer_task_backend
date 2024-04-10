<?php

namespace App\Services;

use App\Exceptions\APIException;
use App\Mail\VerificationCode;
use App\Models\User;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class UserService
{
    protected $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function register($data): User
    {

        $user = $this->userRepository->create($data);
        $user->verification_url = $this->getTemporarySignedRoute($user);
        $this->sendVerificationEmail($user);
        $this->createTempTask($user);
        return $user;
    }

    public function getTemporarySignedRoute(User $user): string
    {
        return URL::temporarySignedRoute(
            'verification.verify', now()->addDay(), ['id' => $user->id]);
    }

    public function sendVerificationEmail(User $user): void
    {
        Mail::to($user->email)->send(new VerificationCode(env('FRONTEND_VERIFY_EMAIL_URL') . urlencode($user->verification_url)));
    }

    public function login($data): User
    {
        $validatedData = ValidationService::validate($data, [
            'password' => ['required'],
            'email' => ['required'],
        ]);


        if (Auth::attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']])) {
            $user = Auth::user();
            if ($user->email_verified_at) {
                $user->token = $this->getPlainTextToken($user);
            } else {
                $user->verification_url = $this->getTemporarySignedRoute($user);
            }
            return $user;
        }

        throw new APIException('Invalid credential', 401);

    }

    public function getPlainTextToken(Authenticatable $user, $tokenName = 'karlancer')
    {
        return $user->createToken($tokenName)->plainTextToken;
    }

    public function verify($id): User
    {
        $user = $this->userRepository->findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            throw new APIException('Email already verified', 400);
        }

        if ($user->markEmailAsVerified()) {
            $user->token = $this->getPlainTextToken($user);
            return $user;
        } else {
            throw new APIException('Cant verify email', 400);
        }
    }

    public function createTempTask(User $user)
    {
        (new TaskRepository())->create(['text'=>'Lorem ipsum dolor sit amet consectetur adipisicing elit. Corporis quis eligendi magnam blanditiis totam vero maxime suscipit repudiandae, quaerat reiciendis quibusdam similique expedita minus molestiae ut saepe fugit mollitia inventore?','user_id'=>$user->id,'due_date'=>now()]);
    }

    public function logout($user)
    {
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
    }


}


?>
