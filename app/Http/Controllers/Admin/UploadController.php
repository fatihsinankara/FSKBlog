<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function image(Request $request, ImageProcessor $processor): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,webp,gif', 'max:4096'],
        ]);

        $path = $processor->process(
            $request->file('image'),
            'posts/content',
        );

        return response()->json([
            'url' => asset('storage/'.$path),
        ]);
    }
}
