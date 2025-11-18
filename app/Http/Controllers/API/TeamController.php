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
            ->orderBy('id', 'desc')
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
                'team_number' => $teamNumber + 1,
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

    public function show($id)
    {
        $team = Team::with(['user:id,name', 'creator:id,name'])->find($id);

        if (!$team) {
            return response()->json([
                'status' => false,
                'message' => 'Team not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Team details fetched',
            'data' => $team
        ]);
    }

    public function update(Request $request, Team $team)
    {
        if ($team->created_by !== Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => 'You are not the creator of this team.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'users' => 'required|array|min:1',
            // 'users.*.user_id' => 'required|exists:users,id',
            'users.*.is_manager' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $teamNumber = $team->team_number;

        $newUsers = collect($request->users)->keyBy('user_id');
        $newUserIds = $newUsers->keys()->toArray();

        $existingRows = Team::where('team_number', $teamNumber)->get();
        $existingUserIds = $existingRows->pluck('user_id')->toArray();

        Team::where('team_number', $teamNumber)->update([
            'name' => $request->name
        ]);

        $usersToRemove = array_diff($existingUserIds, $newUserIds);

        if (!empty($usersToRemove)) {
            Team::where('team_number', $teamNumber)
                ->whereIn('user_id', $usersToRemove)
                ->delete();
        }

        $usersToAdd = array_diff($newUserIds, $existingUserIds);

        foreach ($usersToAdd as $userId) {
            Team::create([
                'name' => $request->name,
                'team_number' => $teamNumber,
                'user_id' => $userId,
                'is_manager' => $newUsers[$userId]['is_manager'] ?? 0,
                'is_active' => 1,
                'created_by' => Auth::id(),
            ]);
        }

        foreach ($existingUserIds as $userId) {
            if (isset($newUsers[$userId])) {
                Team::where('team_number', $teamNumber)
                    ->where('user_id', $userId)
                    ->update([
                        'is_manager' => $newUsers[$userId]['is_manager'] ?? 0,
                    ]);
            }
        }

        $updatedTeamData = Team::where('team_number', $teamNumber)->get();

        return response()->json([
            'status' => true,
            'message' => 'Team updated successfully',
            'data' => $updatedTeamData
        ]);
    }
}
