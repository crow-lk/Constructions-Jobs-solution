<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\Api\DiagnosticsController;
use App\Http\Middleware\Cors;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Apply CORS middleware to all API routes
Route::middleware([Cors::class])->group(function() {

// Debug endpoints
Route::get('/debug', function() {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working correctly',
        'server_time' => now()->toIso8601String(),
        'environment' => app()->environment(),
        'version' => app()->version(),
    ]);
});

Route::get('/diagnostics', [DiagnosticsController::class, 'apiDiagnostics']);

// Register diagnostics route - this will test the registration process without creating a user
Route::post('/register-test', function(Request $request) {
    Log::channel('daily')->info('Registration test endpoint hit', [
        'ip' => $request->ip(),
        'request_data' => $request->except(['password', 'password_confirmation']),
    ]);
    
    return response()->json([
        'status' => 'success',
        'message' => 'Registration test endpoint working',
        'received_data' => [
            'name' => $request->input('name', 'Not provided'),
            'email' => $request->input('email', 'Not provided'),
            'role' => $request->input('role', 'Not provided'),
            'has_password' => $request->has('password') ? 'Yes' : 'No',
            'has_password_confirmation' => $request->has('password_confirmation') ? 'Yes' : 'No',
            'has_business_registration_number' => $request->has('business_registration_number') ? 'Yes' : 'No',
            'has_business_registration_document' => $request->hasFile('business_registration_document') ? 'Yes' : 'No',
            'content_type' => $request->header('Content-Type'),
            'http_method' => $request->method(),
        ]
    ]);
});

// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.register');

// Public roles endpoint
Route::get('/roles', [RoleController::class, 'index']);

// Public category and subcategory endpoints
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/active', [CategoryController::class, 'active']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::get('/subcategories', [SubCategoryController::class, 'index']);
Route::get('/subcategories/active', [SubCategoryController::class, 'active']);
Route::get('/subcategories/{subCategory}', [SubCategoryController::class, 'show']);
Route::get('/categories/{category}/subcategories', [SubCategoryController::class, 'byCategory']);
Route::get('/categories/{category}/subcategories/active', [SubCategoryController::class, 'activeByCategory']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // User management
    Route::apiResource('users', UserController::class);
    
    // Role management
    Route::get('/roles/{role}', [RoleController::class, 'show']);
    
    // File upload management
    Route::prefix('files')->group(function () {
        Route::post('/business-registration-document/upload', [FileUploadController::class, 'uploadBusinessRegistrationDocument']);
        Route::delete('/business-registration-document/delete', [FileUploadController::class, 'deleteBusinessRegistrationDocument']);
        Route::get('/business-registration-document/get', [FileUploadController::class, 'getBusinessRegistrationDocument']);
        Route::get('/business-registration-document/download', [FileUploadController::class, 'downloadBusinessRegistrationDocument']);
    });
    
    // Add more API resources here as you create them
    // Route::apiResource('tasks', TaskController::class);
    // Route::apiResource('clients', ClientController::class);
}); 

// Close the CORS middleware group
});