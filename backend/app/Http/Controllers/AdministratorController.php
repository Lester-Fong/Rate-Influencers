<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdministratorController extends Controller
{
    public function login(Request $request) {
        $data = $request->validate(rules: [
            "email" => "required|email",
            "password" => "required"
        ]);

        $admin = Administrator::where("email", $data["email"])->first();
        if (!isset($admin) || !Hash::check($data["password"], $admin->password)) {
            return response()->json(["error" => true, "message" => "Invalid credentials"], 401);
        }

        $token = $admin->createToken($admin->id . "admin")->plainTextToken;

        return response()->json(["error" => false, "token" => $token, "admin" => $admin]);
    }
}
