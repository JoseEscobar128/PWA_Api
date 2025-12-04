<?php

namespace App\Http\Controllers;

use App\Models\PlaceVote;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\ApiResponse;

class PlaceVoteController extends Controller
{
    use ApiResponse;

    /**
     * List votes for a place (optional `place_id`).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $placeId = $request->query('place_id');

            $votes = PlaceVote::when($placeId, function ($q, $placeId) {
                return $q->where('place_id', $placeId);
            })->get();

            return $this->success($votes);
        } catch (\Exception $e) {
            return $this->error('Failed to list votes', $e->getMessage(), 500);
        }
    }

    /**
     * Create a vote (one per user/place).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'place_id' => 'required|exists:places,id',
            ]);

            $vote = PlaceVote::firstOrCreate([
                'place_id' => $data['place_id'],
                'user_id' => $request->user()->id,
            ]);

            return $this->success($vote, 'Voted successfully', 201);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return $this->error('Validation failed', $ve->errors(), 422);
        } catch (\Exception $e) {
            return $this->error('Failed to vote', $e->getMessage(), 500);
        }
    }

    /**
     * Show whether the authenticated user has voted for the given place.
     *
     * @param int $place_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($place_id, Request $request)
    {
        try {
            $vote = PlaceVote::where('place_id', $place_id)
                ->where('user_id', $request->user()->id)
                ->first();

            return $this->success([
                'voted' => (bool) $vote,
                'data' => $vote
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to check vote', $e->getMessage(), 500);
        }
    }

    /**
     * Remove authenticated user's vote for a place.
     *
     * @param int $place_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($place_id, Request $request)
    {
        try {
            PlaceVote::where('place_id', $place_id)
                ->where('user_id', $request->user()->id)
                ->delete();

            return $this->success(null, 'Vote removed');
        } catch (\Exception $e) {
            return $this->error('Failed to remove vote', $e->getMessage(), 500);
        }
    }
}
