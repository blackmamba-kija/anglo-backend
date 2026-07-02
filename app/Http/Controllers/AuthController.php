<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Authenticate a user by email and password.
     * Returns the user record on success (no token — simple session-less auth for demo).
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Support plain-text passwords stored during seeding (legacy) AND hashed passwords
        $passwordOk = Hash::check($request->password, $user->password)
            || $request->password === $user->password;

        if (!$passwordOk) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->role,
            'station_id' => $user->station_id,
        ]);
    }
}
