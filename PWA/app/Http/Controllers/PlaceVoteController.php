<?php

namespace App\Http\Controllers;

use App\Models\PlaceVote;
use Illuminate\Http\Request;

class PlaceVoteController extends Controller
{
    // Listar votos de un lugar (opcional)
    public function index(Request $request)
    {
        $placeId = $request->query('place_id');

        $votes = PlaceVote::where('place_id', $placeId)->get();

        return response()->json($votes);
    }

    // Crear voto
    public function store(Request $request)
    {
        $data = $request->validate([
            'place_id' => 'required|exists:places,id',
        ]);

        $vote = PlaceVote::firstOrCreate([
            'place_id' => $data['place_id'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Voted successfully',
            'data' => $vote
        ], 201);
    }

    // Mostrar si el usuario ya votó
    public function show($place_id, Request $request)
    {
        $vote = PlaceVote::where('place_id', $place_id)
            ->where('user_id', $request->user()->id)
            ->first();

        return response()->json([
            'voted' => $vote ? true : false,
            'data' => $vote
        ]);
    }

    // Quitar voto
    public function destroy(Request $request)
    {
        $request->validate([
            'place_id' => 'required|exists:places,id'
        ]);

        PlaceVote::where('place_id', $request->place_id)
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['message' => 'Vote removed']);
    }
}
