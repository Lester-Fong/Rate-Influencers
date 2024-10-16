<?php

namespace App\Services;

use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function loginUser($data) {

        $admin = Administrator::where("email", $data["email"])->first();
        if (!isset($admin) || !Hash::check($data["password"], $admin->password)) {
            return response()->json(["error" => true, "message" => "Invalid credentials"], 401);
        }

        $token = $admin->createToken($admin->id . "admin")->plainTextToken;
        return ["token" => $token, "admin" => $admin];
    }
}
