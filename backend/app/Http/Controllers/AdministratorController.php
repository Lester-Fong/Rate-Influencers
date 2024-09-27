<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdministratorController extends Controller
{
    public function login(Request $request): void {
        $data = $request->validate(rules: [
            "email" => "required|email",
            "password" => "required"
        ]);

        Log::debug(message: print_r(value: $data, return: true));
        dd(vars: 'test');
    }
}
