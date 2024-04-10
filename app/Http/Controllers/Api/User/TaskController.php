<?php

namespace App\Http\Controllers\Api\User;


use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{

    public function __construct()
    {

    }


    public function index(TaskRepository $taskRepository)
    {
        $tasks = $taskRepository->list(Auth::id());
        return response()->json(['status' => 'success', 'tasks' => $tasks]);
    }


    public function store(Request $request, TaskRepository $taskRepository)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $task = $taskRepository->create($data);
        return response()->json(['status' => 'success', 'task' => $task]);
    }


    public function update($id, Request $request, TaskRepository $taskRepository)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $task = $taskRepository->update($taskRepository->findOrFail($id, Auth::id()), $data);
        return response()->json(['status' => 'success', 'task' => $task]);
    }


    public function destroy($id, TaskRepository $taskRepository)
    {
        $taskRepository->delete($taskRepository->findOrFail($id, Auth::id()));
        return response()->json(['status' => 'success']);

    }


}
