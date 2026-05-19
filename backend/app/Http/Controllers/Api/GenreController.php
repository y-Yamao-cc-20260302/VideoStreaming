<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;

class GenreController extends Controller
{
    public function index(): JsonResponse
    {
        $genres = Genre::orderBy('name')->get();

        return response()->json(['data' => GenreResource::collection($genres)]);
    }
}
