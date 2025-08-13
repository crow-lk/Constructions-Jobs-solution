<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ];

            // Add business registration number if provided
            if ($request->filled('business_registration_number')) {
                $userData['business_registration_number'] = $request->business_registration_number;
            }

            // Handle business registration document file upload
            if ($request->hasFile('business_registration_document')) {
                $file = $request->file('business_registration_document');
                
                // Validate file is valid
                if ($file->isValid()) {
                    $filename = 'business_registration_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
                    
                    // Store file in public disk
                    $path = $file->storeAs('business-registration-documents', $filename, 'public');
                    $userData['business_registration_document'] = $path;
                } else {
                    return response()->json([
                        'message' => 'Invalid file upload',
                        'errors' => ['business_registration_document' => ['The uploaded file is invalid.']]
                    ], 422);
                }
            }

            $user = User::create($userData);

            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'message' => 'Registration successful',
                'user' => new UserResource($user),
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            // Clean up uploaded file if user creation fails
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }
}