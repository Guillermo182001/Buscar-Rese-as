<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReviewController extends Controller
{
    // Obtener reseñas de Google Places
    public function getGoogleReviews(Request $request)
    {
        $placeId = $request->input('place_id');
        $apiKey = config('services.google_places.api_key');

        $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
            'place_id' => $placeId,
            'key' => $apiKey,
            'fields' => 'reviews'
        ]);

        $reviews = collect($response->json()['result']['reviews'] ?? [])->map(function ($review) {
            return [
                'author_name' => $review['author_name'] ?? 'Anónimo',
                'profile_photo_url' => $review['profile_photo_url'] ?? null,
                'rating' => $review['rating'] ?? 0,
                'text' => $review['text'] ?? 'Sin comentario',
                'relative_time_description' => $review['relative_time_description'] ?? '',
            ];
        });

        return response()->json($reviews);
    }

    // Obtener reseñas de Facebook Graph API
    public function getFacebookReviews(Request $request)
    {
        $pageId = $request->input('page_id');
        $accessToken = config('services.facebook.access_token');

        $response = Http::get("https://graph.facebook.com/v18.0/{$pageId}/ratings", [
            'access_token' => $accessToken,
            'fields' => 'reviewer{name,picture},rating,review_text,created_time'
        ]);

        $reviews = collect($response->json()['data'] ?? [])->map(function ($review) {
            return [
                'reviewer_name' => $review['reviewer']['name'] ?? 'Anónimo',
                'reviewer_picture' => $review['reviewer']['picture']['data']['url'] ?? null,
                'rating' => $review['rating'] ?? 0,
                'review_text' => $review['review_text'] ?? 'Sin comentario',
                'created_time' => $review['created_time'] ?? '',
            ];
        });

        return response()->json($reviews);
    }
    
    public function testApis()
    {
        $googleApiKey = config('services.google_places.api_key');
        $facebookToken = config('services.facebook.access_token');
    
        return response()->json([
            'google_api_key' => $googleApiKey ? 'Funciona' : 'No encontrado',
            'facebook_access_token' => $facebookToken ? 'Funciona' : 'No encontrado',
        ]);
    }

}
