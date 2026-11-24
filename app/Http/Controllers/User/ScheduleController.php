<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Katalog jadwal: pencarian kota, filter rute & tanggal, pagination.
     */
    public function index(Request $request)
    {
        $schedules = Schedule::with('route')
            ->where('departure_at', '>=', now())          // hanya jadwal yang belum berangkat
            ->when($request->search, function ($q, $search) {
                $q->whereHas('route', function ($q) use ($search) {
                    $q->where('origin', 'like', "%{$search}%")
                      ->orWhere('destination', 'like', "%{$search}%");
                });
            })
            ->when($request->route_id, fn ($q, $id) => $q->where('route_id', $id))
            ->when($request->date, fn ($q, $date) => $q->whereDate('departure_at', $date))
            ->when($request->availability === 'available', fn ($q) => $q->where('available_seats', '>', 0))
            ->orderBy('departure_at')
            ->paginate(9)
            ->withQueryString();

        $routes = Route::orderBy('origin')->get();

        return view('user.schedules.index', compact('schedules', 'routes'));
    }
}