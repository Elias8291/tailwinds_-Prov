<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoogleMapsProxyController extends Controller
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_key', 'AIzaSyCUqfgNQ2Q4AVy8OTNMfogJceDbA0FHZKs');
    }

    public function getScript()
    {
        $libraries = request('libraries', 'places');
        $version = request('v', 'weekly');
        $callback = request('callback', 'initGoogleMaps');
        
        $url = "https://maps.googleapis.com/maps/api/js";
        $params = http_build_query([
            'key' => $this->apiKey,
            'libraries' => $libraries,
            'v' => $version,
            'callback' => $callback,
            'language' => 'es',
            'region' => 'MX'
        ]);

        return response()
            ->view('scripts.google-maps', ['url' => "{$url}?{$params}"])
            ->header('Content-Type', 'application/javascript');
    }
} 