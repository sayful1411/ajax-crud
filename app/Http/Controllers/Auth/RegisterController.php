<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreRegisterRequest;
use App\Models\User;

class RegisterController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    public function store(StoreRegisterRequest $request)
    {
        $validatedData = $request->validated();

        User::create($validatedData);

        return redirect()->route('login')->with('success', 'Registration Success');
    }
}
