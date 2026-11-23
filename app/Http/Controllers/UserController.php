<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string|unique:users,id',
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'role' => ['required', Rule::in(['manager', 'procurement', 'store_keeper', 'site_manager', 'operation_manager', 'operator', 'security_site', 'supervisor', 'data_analyst'])],
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:home,dashboard,inventory,inventory_add,inventory_edit,inventory_delete,inventory_restock,inventory_actions,distribution,distribution_issue,distribution_receive,distribution_shaft_fuel,assets,assets_add,assets_edit,assets_delete,assets_assign,stations,stations_add,stations_edit,stations_delete,users,users_create,users_edit,users_delete,permissions_manage,activity_logs,reports,reports_export,operations,operations_issue_stock,operations_borrow_stock,operations_assign_asset,operations_requests,shaft_operations,shaft_register_db,shaft_view_db,shaft_register_deliveries,shaft_fuel,shaft_daily_report',
            'station_id' => 'nullable|string',
            'password' => 'nullable|string',
        ]);

        if (empty($validated['password'])) {
            $validated['password'] = 'password';
        }
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'role' => ['sometimes', Rule::in(['manager', 'procurement', 'store_keeper', 'site_manager', 'operation_manager', 'operator', 'security_site', 'supervisor', 'data_analyst'])],
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:home,dashboard,inventory,inventory_add,inventory_edit,inventory_delete,inventory_restock,inventory_actions,distribution,distribution_issue,distribution_receive,distribution_shaft_fuel,assets,assets_add,assets_edit,assets_delete,assets_assign,stations,stations_add,stations_edit,stations_delete,users,users_create,users_edit,users_delete,permissions_manage,activity_logs,reports,reports_export,operations,operations_issue_stock,operations_borrow_stock,operations_assign_asset,operations_requests,shaft_operations,shaft_register_db,shaft_view_db,shaft_register_deliveries,shaft_fuel,shaft_daily_report',
            'station_id' => 'nullable|string',
            'password' => 'nullable|string',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
