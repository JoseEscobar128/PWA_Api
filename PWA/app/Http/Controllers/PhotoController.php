<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    // ============================
    // GET /photos -> listar fotos
    // ============================
    public function index(Request $request)
    {
        // Si quieres filtrar por place_id:
        if ($request->has('place_id')) {
            return Photo::where('place_id', $request->place_id)->get();
        }

        return Photo::all();
    }

    // ============================
    // POST /photos -> subir foto
    // ============================
    public function store(Request $request)
    {
        $data = $request->validate([
            'place_id' => 'required|exists:places,id',
            'photo' => 'required|image'
        ]);

        $path = $request->file('photo')->store('places', 'public');

        return Photo::create([
            'place_id' => $data['place_id'],
            'user_id' => $request->user()->id,
            'url' => $path
        ]);
    }

    // ============================
    // GET /photos/{id}
    // ============================
    public function show($id)
    {
        $photo = Photo::find($id);

        if (!$photo) {
            return response()->json(['message' => 'Photo not found'], 404);
        }

        return $photo;
    }

    // ============================
    // PUT /photos/{id} -> actualizar foto
    // ============================
    public function update(Request $request, $id)
    {
        $photo = Photo::find($id);

        if (!$photo) {
            return response()->json(['message' => 'Photo not found'], 404);
        }

        $data = $request->validate([
            'photo' => 'required|image'
        ]);

        // Borrar imagen vieja
        if (Storage::disk('public')->exists($photo->url)) {
            Storage::disk('public')->delete($photo->url);
        }

        // Subir nueva
        $path = $request->file('photo')->store('places', 'public');

        $photo->update([
            'url' => $path
        ]);

        return $photo;
    }

    // ============================
    // DELETE /photos/{id}
    // ============================
    public function destroy($id)
    {
        $photo = Photo::find($id);

        if (!$photo) {
            return response()->json(['message' => 'Photo not found'], 404);
        }

        // borrar archivo físico
        if (Storage::disk('public')->exists($photo->url)) {
            Storage::disk('public')->delete($photo->url);
        }

        $photo->delete();

        return response()->json(['message' => 'Photo deleted']);
    }
}
