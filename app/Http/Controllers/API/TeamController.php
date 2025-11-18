<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    public function index()
    {
        $authId = Auth::id();

        $teams = Team::with(['user:id,name', 'creator:id,name'])
                 ->where('created_by', $authId)
                 ->orWhere('user_id', $authId)
                 ->orderBy('created_at', 'desc')
                 ->get();

        return response()->json([
            'status' => true,
            'message' => 'Teams fetched successfully',
            'data' => $teams
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('teams')->where(function ($query) use ($request) {
                    return $query->where('created_by', Auth::id());
                }),
            ],
            'users' => 'required|array|min:1',
            'users.*.user_id' => 'required|exists:users,id',
            'users.*.is_manager' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $createdTeams = [];

        $teamNumber = Team::max('team_number') ?? 1;

        foreach ($request->users as $user) {

            $team = Team::create([
                'name' => $request->name,
                'team_number' => $teamNumber,
                'user_id' => $user['user_id'],
                'is_manager' => $user['is_manager'] ?? 0,
                'is_active' => 1,
                'created_by' => Auth::id(),
            ]);

            $createdTeams[] = $team;
        }

        return response()->json([
            'status' => true,
            'message' => 'Team created successfully',
            'data' => $createdTeams
        ]);
    }
}
