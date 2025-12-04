<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class PlaceController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return Place::with(['photos', 'reviews', 'votes'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'address' => 'nullable',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'category' => 'nullable',
        ]);

        $data['user_id'] = $request->user()->id;

        return Place::create($data);
    }

    public function show(Place $place)
    {
        return $place->load(['photos', 'reviews', 'votes']);
    }

    public function update(Request $request, Place $place)
    {
        $this->authorize('update', $place);

        $place->update($request->all());
        return $place;
    }

    public function destroy(Place $place)
    {
        $this->authorize('delete', $place);
        $place->delete();

        return response()->json(['message' => 'Place deleted']);
    }
}
