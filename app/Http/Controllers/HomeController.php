<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    /**
     * Fetch Instagram profile data
     */
    public function getProfile(Request $request)
    {
        $username = $request->get('username');

        if (!$username) {
            return response()->json([
                'success' => false,
                'message' => 'Username is required'
            ], 400);
        }

        try {
            // Call your existing API
            $apiUrl = "http://localhost/instagram-video/profile.php?username=" . urlencode($username);
            $response = Http::timeout(30)->get($apiUrl);

            if (!$response->successful()) {
                throw new \Exception('API request failed');
            }

            $data = $response->json();

            if (!$data || !isset($data['success']) || !$data['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile not found or private'
                ], 404);
            }

            // Return the data as-is since your API already has all the data
            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Proxy for Instagram images to bypass CORS
     */
    public function proxyImage(Request $request)
    {
        $imageUrl = $request->get('url');

        if (!$imageUrl) {
            return response('Image URL is required', 400);
        }

        // Validate that it's an Instagram URL
        if (!str_contains($imageUrl, 'instagram.') && !str_contains($imageUrl, 'fbcdn.net')) {
            return response('Invalid image URL', 400);
        }

        try {
            // Set proper headers to mimic browser request
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'image/webp,image/apng,image/*,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Cache-Control' => 'no-cache',
                'Pragma' => 'no-cache',
                'Sec-Fetch-Dest' => 'image',
                'Sec-Fetch-Mode' => 'no-cors',
                'Sec-Fetch-Site' => 'cross-site'
            ])->timeout(30)->get($imageUrl);

            if (!$response->successful()) {
                return response('Failed to fetch image', 404);
            }

            $contentType = $response->header('Content-Type') ?: 'image/jpeg';

            return response($response->body(), 200, [
                'Content-Type' => $contentType,
                'Cache-Control' => 'public, max-age=86400', // Cache for 24 hours
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET',
                'Access-Control-Allow-Headers' => 'Content-Type'
            ]);

        } catch (\Exception $e) {
            return response('Image not found', 404);
        }
    }

    /**
     * Download Instagram media
     */
    public function downloadMedia(Request $request)
    {
        $mediaUrl = $request->get('url');
        $filename = $request->get('filename', 'instagram_media');

        if (!$mediaUrl) {
            return response('Media URL is required', 400);
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept' => '*/*',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Cache-Control' => 'no-cache',
                'Pragma' => 'no-cache'
            ])->timeout(60)->get($mediaUrl);

            if (!$response->successful()) {
                return response()->json(['success' => false, 'message' => 'Failed to download media'], 404);
            }

            $contentType = $response->header('Content-Type') ?: 'application/octet-stream';
            $extension = $this->getExtensionFromContentType($contentType);

            return response($response->body(), 200, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '.' . $extension . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Download failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get file extension from content type
     */
    private function getExtensionFromContentType($contentType)
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            'video/quicktime' => 'mov'
        ];
        return $extensions[$contentType] ?? 'jpg';
    }
}