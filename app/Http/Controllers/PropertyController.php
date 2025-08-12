<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

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

    public function create()
    {
        return view('upload-form');
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
                $url = asset('storage/' . $path);

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

    public function show(Property $property)
    {
        //
    }


    public function edit(Property $property)
    {
        //
    }


    public function update(Request $request, Property $property)
    {
        //
    }


    public function destroy(Property $property)
    {
        //
    }
}
