<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OccupationResource;
use App\Models\Occupation;
use Illuminate\Http\JsonResponse;

class OccupationController extends Controller
{
    public function index(): JsonResponse
    {
        $occupations = Occupation::orderBy('id')->get();

        return response()->json(['data' => OccupationResource::collection($occupations)]);
    }
}
