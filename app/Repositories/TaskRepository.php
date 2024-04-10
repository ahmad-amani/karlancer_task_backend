<?php

namespace App\Repositories;

use App\Exceptions\APIException;
use App\Models\Task;
use App\Services\ValidationService;
use Carbon\Carbon;

class TaskRepository
{

    public function create($data): Task
    {
        $validatedData = ValidationService::validate($data, [
            'text' => 'required|string',
            'due_date' => 'date',
            'user_id' => 'required|exists:users,id'
        ]);

        if (isset($validatedData['due_date'])) $validatedData['due_date'] = Carbon::parse($validatedData['due_date'] . ' UTC')->timezone('Asia/Tehran');
        $task = Task::create($validatedData);
        $task = $this->findOrFail($task->id, $data['user_id']);
        if (!$task)
            throw new APIException('Failed to create the task!', 500);

        return $task;
    }

    public function findOrFail($id, $user_id = false)
    {
        if ($user_id) {
            return Task::where('user_id', $user_id)->findOrFail($id);
        } else {
            return Task::findOrFail($id);
        }
    }

    public function update($task, $data): Task
    {
        $validatedData = ValidationService::validate($data, [
            'text' => 'string',
            'due_date' => 'date',
        ]);
        if (isset($validatedData['due_date'])) $validatedData['due_date'] = Carbon::parse($validatedData['due_date'] . ' UTC')->timezone('Asia/Tehran');

        $updated = $task->update($validatedData);

        if (!$updated)
            throw new APIException('Failed to update the task!', 500);

        return $this->findOrFail($task->id,$data['user_id']);
    }

    public function delete($task): Task
    {
        $deleted = $task->delete();

        if (!$deleted)
            throw new APIException('Failed to delete the task!', 500);

        return $task;
    }

    public function list($user_id)
    {
        return Task::where('user_id', $user_id)->orderBy('id', 'desc')->get();
    }
}


?>
