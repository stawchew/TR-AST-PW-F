<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $schedules = Schedule::with('route')
            ->when($request->search, function ($q, $search) {
                $q->whereHas('route', function ($q) use ($search) {
                    $q->where('origin', 'like', "%{$search}%")
                      ->orWhere('destination', 'like', "%{$search}%");
                });
            })
            ->when($request->route_id, fn ($q, $id) => $q->where('route_id', $id))
            ->when($request->status, function ($q, $status) {
                match ($status) {
                    'upcoming' => $q->where('departure_at', '>=', now()),
                    'past'     => $q->where('departure_at', '<', now()),
                    'full'     => $q->where('available_seats', 0),
                    default    => null,
                };
            })
            ->orderBy('departure_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $routes = Route::orderBy('origin')->get();

        return view('admin.schedules.index', compact('schedules', 'routes'));
    }

    public function create()
    {
        $routes = Route::orderBy('origin')->get();
        return view('admin.schedules.create', compact('routes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'route_id'     => ['required', 'exists:routes,id'],
            'departure_at' => ['required', 'date', 'after:now'],
            'price'        => ['required', 'integer', 'min:0'],
            'total_seats'  => ['required', 'integer', 'min:1', 'max:60'],
        ]);

        $data['available_seats'] = $data['total_seats']; // awalnya semua kursi kosong

        Schedule::create($data);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Schedule $schedule)
    {
        $routes = Route::orderBy('origin')->get();
        return view('admin.schedules.edit', compact('schedule', 'routes'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $data = $request->validate([
            'route_id'     => ['required', 'exists:routes,id'],
            'departure_at' => ['required', 'date'],
            'price'        => ['required', 'integer', 'min:0'],
            'total_seats'  => ['required', 'integer', 'min:1', 'max:60'],
        ]);

        // kursi yang sudah terpesan tidak boleh "hilang"
        $booked = $schedule->total_seats - $schedule->available_seats;

        if ($data['total_seats'] < $booked) {
            return back()->withInput()->withErrors([
                'total_seats' => "Total kursi tidak boleh kurang dari kursi yang sudah terpesan ({$booked}).",
            ]);
        }

        $data['available_seats'] = $data['total_seats'] - $booked;

        $schedule->update($data);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        if ($schedule->tickets()->exists()) {
            return back()->with('error', 'Jadwal tidak bisa dihapus karena sudah memiliki tiket.');
        }

        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}