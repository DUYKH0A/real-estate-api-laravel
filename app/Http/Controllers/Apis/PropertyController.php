<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::query();

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        $allowedSorts = ['price', 'area', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        $order = strtolower($order) == 'asc' ? 'asc' : 'desc';

        $query->orderBy($sort, $order);

        $properties = $query->paginate(5);

        $data = $properties->map(function ($property) {
            return [
                'id' => $property->id,
                'title' => $property->title,
                'price' => (float) $property->price,
                'city' => $property->city,
                'status' => $property->status,
                'images' => $property->images,
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'total' => $properties->total(),
            ],
        ]);
    }


    public function store(Request $request)
    {

        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'price'     => 'required|numeric|min:0',
            'area'      => 'required|numeric|min:0',
            'city'      => 'required|string|max:100',
            'district'  => 'required|string|max:100',
            'status'    => 'required|in:available,sold,rented,pending',
            'images.*'  => 'nullable|image|mimes:jpeg,png,jpg,gif'
        ]);

        $property = Property::create([
            'title'     => $validated['title'],
            'price'     => $validated['price'],
            'area'      => $validated['area'],
            'city'      => $validated['city'],
            'district'  => $validated['district'],
            'status'    => $validated['status'],
            'images'    => []
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties', 'public');
                $url = 'storage/' . $path;

                $property->images()->create([
                    'image_path' => $url,
                    'image_name' => $image->getClientOriginalName(),
                    'is_primary' => $index === 0,
                    'sort_order' => $index
                ]);

                $imagePaths[] = $url;
            }

            $property->images = $imagePaths;
            $property->save();
        }

        return response()->json([
            'message' => 'Property created successfully',
            'data'    => [
                'id'    => $property->id,
                'title' => $property->title
            ]
        ], 201);
    }

    public function show(string $id)
    {
        $property = Property::with(['images'])->find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        return response()->json([
            'id' => $property->id,
            'title' => $property->title,
            'description' => $property->description,
            'price' => (float) $property->price,
            'city' => $property->city,
            'district' => $property->district,
            'features' => $property->features,
            'status' => $property->status,
            'images' => $property->images()->get()->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_path' => $image->image_path,
                    'is_primary' => $image->is_primary,
                ];
            }),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $property = Property::find($id);

        $validated = $request->validate([
            'title'     => 'sometimes|string|max:255',
            'price'     => 'sometimes|numeric|min:0',
            'area'      => 'sometimes|numeric|min:0',
            'city'      => 'sometimes|string|max:100',
            'district'  => 'sometimes|string|max:100',
            'status'    => 'sometimes|in:available,sold,rented,pending',
            'images.*'  => 'sometimes|image|mimes:jpeg,png,jpg,gif'
        ]);

        Log::info($request->all());

        $property->fill($validated);
        $property->save();

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

        return response()->json(['message' => 'Property updated successfully']);
    }

    public function destroy(string $id)
    {
        $property = Property::find($id);
        $property->delete();

        return response()->json([
            'message' => 'Property deleted successfully'
        ]);
    }

    public function restore(string $id)
    {
        $property = Property::withTrashed()->find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        $property->restore();

        return response()->json(['message' => 'Property restored successfully']);
    }
}
