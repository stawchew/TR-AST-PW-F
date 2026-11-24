<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $routes = Route::withCount('schedules')
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('origin', 'like', "%{$search}%")
                      ->orWhere('destination', 'like', "%{$search}%");
                });
            })
            ->when($request->origin, fn ($q, $origin) => $q->where('origin', $origin))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $origins = Route::select('origin')->distinct()->orderBy('origin')->pluck('origin');

        return view('admin.routes.index', compact('routes', 'origins'));
    }

    public function create()
    {
        return view('admin.routes.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('routes', 'public');
        }

        Route::create($data);

        return redirect()->route('admin.routes.index')->with('success', 'Rute berhasil ditambahkan.');
    }

    public function edit(Route $route)
    {
        return view('admin.routes.edit', compact('route'));
    }

    public function update(Request $request, Route $route)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            // hapus file lama sebelum menyimpan yang baru
            if ($route->image) {
                Storage::disk('public')->delete($route->image);
            }
            $data['image'] = $request->file('image')->store('routes', 'public');
        }

        $route->update($data);

        return redirect()->route('admin.routes.index')->with('success', 'Rute berhasil diperbarui.');
    }

    public function destroy(Route $route)
    {
        if ($route->schedules()->whereHas('tickets')->exists()) {
            return back()->with('error', 'Rute tidak bisa dihapus karena sudah ada tiket pada jadwalnya.');
        }

        if ($route->image) {
            Storage::disk('public')->delete($route->image);
        }

        $route->delete(); // jadwal ikut terhapus (cascadeOnDelete)

        return redirect()->route('admin.routes.index')->with('success', 'Rute berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'origin'           => ['required', 'string', 'max:100'],
            'destination'      => ['required', 'string', 'max:100', 'different:origin'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'image'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }
}