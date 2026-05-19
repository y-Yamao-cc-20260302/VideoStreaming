<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoticeResource;
use App\Models\Notice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NoticeController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $notices = Notice::active()
            ->orderByDesc('published_at')
            ->paginate(20);

        return NoticeResource::collection($notices);
    }

    public function show(int $id): JsonResponse
    {
        $notice = Notice::active()->findOrFail($id);

        return response()->json(new NoticeResource($notice));
    }
}
