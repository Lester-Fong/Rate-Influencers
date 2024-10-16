<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRequest;
use App\Models\Administrator;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdministratorController extends Controller
{
    public function login(AdminRequest $request, AdminService $service) {
        $data = $request->validated();

        try {
            $response = $service->loginUser($data);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(["error" => true, "message" => "An error occurred"], 500);
        }
        
        return response()->json($response);
    }
}
