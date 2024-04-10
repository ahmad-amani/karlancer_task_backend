<?php

namespace App\Repositories;

use App\Models\User;
use App\Services\ValidationService;
use App\Exceptions\APIException;
use Illuminate\Support\Facades\Hash;

class UserRepository
{

    public function create($data): User
    {

        $validatedData = ValidationService::validate($data, [
            'password' => ['required'],
            'email' => ['required', 'unique:users'],
        ]);


        $user = (new User())->create([
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password'])
        ]);

        if (!$user)
            throw new APIException('Failed to create the user!',500);

        return $user;


    }

    public function update($user, $data): User
    {
        $validatedData = ValidationService::validate($data, [
            'password' => 'min:8',
            'email' => 'unique:users',
            'card' => 'min:16|max:16'
        ]);

        $user->update($validatedData);

        if (!$user)
            throw new APIException('Failed to update the user!',500);

        return $user;
    }

    public function findOrFail($id)
    {
        return User::findOrFail($id);
    }
}


?>
