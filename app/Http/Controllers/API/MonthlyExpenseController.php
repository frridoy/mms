<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MonthlyExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MonthlyExpenseController extends Controller
{
    public function index(Request $request)
    {
        $authId = Auth::id();

        if (!$authId) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $currentMonth = date('n');
        $currentYear  = date('Y');

        $query = MonthlyExpense::with(['lookup:id,name', 'team:id,name,team_number'])
            ->where('created_by', $authId);

        if (!$request->month && !$request->year) {
            $query->where('month', $currentMonth)
                ->where('year', $currentYear);
        }

        if ($request->month) {
            $query->where('month', $request->month);
        }

        if ($request->year) {
            $query->where('year', $request->year);
        }

        if ($request->lookup_id) {
            $query->where('lookup_id', $request->lookup_id);
        }

        if ($request->team_id) {
            $query->where('team_id', $request->team_id);
        }

        $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        $data = $query->paginate(20);

        return response()->json([
            'status' => true,
            'message' => 'Monthly expenses list',
            'data' => $data
        ]);

        // $monthlyExpenses = MonthlyExpense::with([
        //     'lookup:id,name',
        //     'team:id,name,team_number',
        //     'team.members:id,name,team_number,is_manager',
        //     'team.manager:id,name,team_number,is_manager'
        // ])->get();

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lookup_id' => 'required|exists:lookups,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'amount' => 'required|numeric|min:0',
            'team_id' => 'nullable|exists:teams,id',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'error' => $validator->errors()->all(),
            ], 401);
        }

        try {

            $monthlyExpense = MonthlyExpense::create([
                'lookup_id' => $request->lookup_id,
                'month' => $request->month,
                'year' => $request->year,
                'amount' => $request->amount,
                'team_id' => $request->team_id,
                'description' => $request->description,
                'created_by' => Auth::id(),
                'expense_date' => now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Monthly expense created successfully',
                'data' => $monthlyExpense
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create monthly expense',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
