<?php
namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // GET /reviews
    public function index()
    {
        return Review::with('user', 'place')->get();
    }

    // POST /reviews
    public function store(Request $request)
    {
        $data = $request->validate([
            'place_id' => 'required|exists:places,id',
            'rating'   => 'required|integer|min:1|max:5',
            'comment'  => 'nullable|string'
        ]);

        $data['user_id'] = $request->user()->id;

        return Review::create($data);
    }

    // GET /reviews/{id}
    public function show($id)
    {
        $review = Review::with('user')->find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        return $review;
    }

    // PUT/PATCH /reviews/{id}
    public function update(Request $request, $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        if ($review->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'rating'  => 'integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $review->update($data);

        return $review;
    }

    // DELETE /reviews/{id}
    public function destroy(Request $request, $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        if ($review->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted']);
    }
}
