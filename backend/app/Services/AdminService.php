<?php

namespace App\Services;

use App\Models\Administrator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function loginUser(array $data): ?Administrator
    {

        $admin = Administrator::where("email", $data["email"])->first();
        if (!isset($admin) || !Hash::check($data["password"], $admin->password)) {
            return null;
        }

        Auth::guard('web')->login($admin);

        return $admin;
    }
}
