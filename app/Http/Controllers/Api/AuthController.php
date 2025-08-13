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
use Illuminate\Support\Facades\Log;
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
        Log::channel('daily')->info('Registration attempt started', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'email' => $request->email,
            'role' => $request->role,
        ]);
        
        try {
            // Log request validation success
            Log::channel('daily')->info('Request validation passed for registration');
            
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ];
            
            Log::channel('daily')->info('User data prepared', ['data' => array_diff_key($userData, ['password' => ''])]);

            // Add business registration number if provided
            if ($request->filled('business_registration_number')) {
                Log::channel('daily')->info('Business registration number provided', [
                    'business_registration_number' => $request->business_registration_number
                ]);
                $userData['business_registration_number'] = $request->business_registration_number;
            }

            // Handle business registration document file upload
            if ($request->hasFile('business_registration_document')) {
                Log::channel('daily')->info('Business registration document provided', [
                    'original_name' => $request->file('business_registration_document')->getClientOriginalName(),
                    'mime_type' => $request->file('business_registration_document')->getMimeType(),
                    'size' => $request->file('business_registration_document')->getSize()
                ]);
                
                $file = $request->file('business_registration_document');
                
                // Validate file is valid
                if ($file->isValid()) {
                    $filename = 'business_registration_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
                    Log::channel('daily')->info('File is valid, generating filename', ['filename' => $filename]);
                    
                    try {
                        // Store file in public disk
                        $path = $file->storeAs('business-registration-documents', $filename, 'public');
                        Log::channel('daily')->info('File stored successfully', ['path' => $path]);
                        $userData['business_registration_document'] = $path;
                    } catch (\Exception $e) {
                        Log::channel('daily')->error('File storage failed', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw $e;
                    }
                } else {
                    Log::channel('daily')->warning('Invalid file upload', [
                        'errors' => ['business_registration_document' => ['The uploaded file is invalid.']]
                    ]);
                    return response()->json([
                        'message' => 'Invalid file upload',
                        'errors' => ['business_registration_document' => ['The uploaded file is invalid.']]
                    ], 422);
                }
            }

            Log::channel('daily')->info('Attempting to create user in database');
            $user = User::create($userData);
            Log::channel('daily')->info('User created successfully', ['user_id' => $user->id]);

            try {
                $token = $user->createToken('mobile-app')->plainTextToken;
                Log::channel('daily')->info('Token created successfully');
            } catch (\Exception $e) {
                Log::channel('daily')->error('Token creation failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            Log::channel('daily')->info('Registration completed successfully', ['user_id' => $user->id]);
            return response()->json([
                'message' => 'Registration successful',
                'user' => new UserResource($user),
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            Log::channel('daily')->error('Registration failed with exception', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Clean up uploaded file if user creation fails
            if (isset($path) && Storage::disk('public')->exists($path)) {
                try {
                    Storage::disk('public')->delete($path);
                    Log::channel('daily')->info('Cleaned up uploaded file after failure', ['path' => $path]);
                } catch (\Exception $cleanupEx) {
                    Log::channel('daily')->warning('Failed to clean up file after error', [
                        'path' => $path,
                        'error' => $cleanupEx->getMessage()
                    ]);
                }
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