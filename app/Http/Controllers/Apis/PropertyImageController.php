<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PropertyImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        $validated = $request->validate([
            'images.*'  => 'nullable|image|mimes:jpeg,png,jpg,gif'
        ]);

        Log::info('Request files: ', $request->allFiles());
        $property = Property::find($id);

        if ($request->hasFile('images')) {
            $currentImages = $property->images ?? [];

            $newImagePaths = [];

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties', 'public');
                $url = 'storage/' . $path;

                $property->images()->create([
                    'image_path' => $url,
                    'image_name' => $image->getClientOriginalName(),
                    'is_primary' => false,
                    'sort_order' => count($currentImages) + $index,
                ]);

                $newImagePaths[] = $url;
            }

            $property->images = array_merge($currentImages, $newImagePaths);
            $property->save();
        }
        return response()->json([
            'message' => 'Images uploaded successfully',
            'images' => $property->images()->get()->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_path' => $image->image_path,
                    'is_primary' => $image->is_primary,
                ];
            }),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id,string $imageId)
    {
        PropertyImage::destroy($imageId);
        return response()->json(['message' => 'Property image deleted successfully']);
    }
}
