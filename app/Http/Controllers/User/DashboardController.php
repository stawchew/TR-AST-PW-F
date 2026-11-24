<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total'     => $user->tickets()->count(),
            'pending'   => $user->tickets()->where('status', 'pending')->count(),
            'verified'  => $user->tickets()->where('status', 'verified')->count(),
            'rejected'  => $user->tickets()->where('status', 'rejected')->count(),
            'cancelled' => $user->tickets()->where('status', 'cancelled')->count(),
            // total uang yang sudah terverifikasi (tiket resmi)
            'spent'     => $user->tickets()->where('status', 'verified')->sum('total_price'),
        ];

        $latestTickets = $user->tickets()
            ->with('schedule.route')
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact('stats', 'latestTickets'));
    }
}