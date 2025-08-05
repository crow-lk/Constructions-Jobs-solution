<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    /**
     * Upload business registration document
     */
    public function uploadBusinessRegistrationDocument(Request $request): JsonResponse
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'user_id' => 'required|exists:users,id',
        ], [
            'document.required' => 'Document is required.',
            'document.file' => 'Document must be a file.',
            'document.mimes' => 'Document must be a PDF, JPG, JPEG, or PNG file.',
            'document.max' => 'Document size cannot exceed 10MB.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'User not found.',
        ]);

        try {
            $file = $request->file('document');
            $userId = $request->user_id;
            
            // Generate unique filename
            $filename = 'business_registration_' . $userId . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Store file in business-registration-documents directory
            $path = $file->storeAs('business-registration-documents', $filename, 'public');
            
            // Update user record with document path
            $user = \App\Models\User::find($userId);
            $user->business_registration_document = $path;
            $user->save();
            
            return response()->json([
                'message' => 'Business registration document uploaded successfully',
                'file_path' => $path,
                'file_url' => Storage::url($path),
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete business registration document
     */
    public function deleteBusinessRegistrationDocument(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ], [
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'User not found.',
        ]);

        try {
            $user = \App\Models\User::find($request->user_id);
            
            if (!$user->business_registration_document) {
                return response()->json([
                    'message' => 'No document found for this user',
                ], 404);
            }
            
            // Delete file from storage
            if (Storage::disk('public')->exists($user->business_registration_document)) {
                Storage::disk('public')->delete($user->business_registration_document);
            }
            
            // Clear document path from user record
            $user->business_registration_document = null;
            $user->save();
            
            return response()->json([
                'message' => 'Business registration document deleted successfully',
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get business registration document
     */
    public function getBusinessRegistrationDocument(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ], [
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'User not found.',
        ]);

        try {
            $user = \App\Models\User::find($request->user_id);
            
            if (!$user->business_registration_document) {
                return response()->json([
                    'message' => 'No document found for this user',
                ], 404);
            }
            
            if (!Storage::disk('public')->exists($user->business_registration_document)) {
                return response()->json([
                    'message' => 'Document file not found',
                ], 404);
            }
            
            return response()->json([
                'file_path' => $user->business_registration_document,
                'file_url' => Storage::url($user->business_registration_document),
                'file_name' => basename($user->business_registration_document),
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download business registration document
     */
    public function downloadBusinessRegistrationDocument(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ], [
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'User not found.',
        ]);

        try {
            $user = \App\Models\User::find($request->user_id);
            
            if (!$user->business_registration_document) {
                return response()->json([
                    'message' => 'No document found for this user',
                ], 404);
            }
            
            if (!Storage::disk('public')->exists($user->business_registration_document)) {
                return response()->json([
                    'message' => 'Document file not found',
                ], 404);
            }
            
            return Storage::disk('public')->download($user->business_registration_document);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to download document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
