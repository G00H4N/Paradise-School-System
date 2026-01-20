<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Student;
use App\Models\FeeInvoice;
use Illuminate\Support\Facades\Hash;

// 1. Mobile Login API
Route::post('/login', function (Request $request) {
    $request->validate(['email' => 'required', 'password' => 'required']);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Token generate karein
    $token = $user->createToken('MobileApp')->plainTextToken;

    return response()->json([
        'token' => $token,
        'role' => $user->role,
        'id' => $user->id
    ]);
});

// 2. Protected Routes (Login ke baad)
Route::middleware('auth:sanctum')->group(function () {

    // Student Profile
    Route::get('/student/{id}', function ($id) {
        return Student::with('schoolClass')->findOrFail($id);
    });

    // Fee History
    Route::get('/student/{id}/fees', function ($id) {
        return FeeInvoice::where('student_id', $id)->get();
    });
});