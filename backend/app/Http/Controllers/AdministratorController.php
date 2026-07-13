<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRequest;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdministratorController extends Controller
{
    public function login(AdminRequest $request, AdminService $service)
    {
        $data = $request->validated();

        try {
            $admin = $service->loginUser($data);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(["error" => true, "message" => "An error occurred"], 500);
        }

        if (! $admin) {
            return response()->json([
                'message' => 'Invalid credentials.',
                'errors' => [
                    'email' => ['The supplied credentials are invalid.'],
                    'password' => ['The supplied credentials are invalid.'],
                ],
            ], 422);
        }

        $request->session()->regenerate();

        return response()->json(['admin' => $admin]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
