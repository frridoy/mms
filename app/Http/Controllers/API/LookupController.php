<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lookup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LookupController extends Controller
{
    public function index()
    {
        $lookups = Lookup::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Lookups fetched successfully',
            'data' => $lookups
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:lookups,name',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $lookup = Lookup::create([
            'name' => $request->name,
            'is_active' => $request->is_active ?? 1
        ]);

        return response()->json([
            'status' => true,
            'data' => $lookup
        ]);
    }

    public function show($id)
    {
        $lookup = Lookup::find($id);

        if (!$lookup) {
            return response()->json([
                'status' => false,
                'message' => 'Lookup not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Lookup found',
            'data' => $lookup
        ]);
    }

    public function update(Request $request, Lookup $lookup)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:lookups,name,' . $lookup->id,
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $lookup->update([
            'name' => $request->name,
            'is_active' => $request->is_active ?? $lookup->is_active
        ]);

        return response()->json([
            'status' => true,
            'data' => $lookup
        ]);
    }

    public function destroy(Lookup $lookup)
    {
        $lookup->delete();

        return response()->json([
            'status' => true,
            'message' => 'Lookup deleted successfully'
        ]);
    }
}
