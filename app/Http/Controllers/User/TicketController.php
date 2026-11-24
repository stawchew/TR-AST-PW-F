<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Riwayat tiket milik user yang sedang login (cari + filter status + pagination).
     */
    public function index(Request $request)
    {
        $tickets = $request->user()->tickets()
            ->with('schedule.route')
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('ticket_code', 'like', "%{$search}%")
                      ->orWhereHas('schedule.route', function ($q) use ($search) {
                          $q->where('origin', 'like', "%{$search}%")
                            ->orWhere('destination', 'like', "%{$search}%");
                      });
                });
            })
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('user.tickets.index', compact('tickets'));
    }

    /**
     * Form pesan tiket untuk jadwal tertentu.
     */
    public function create(Schedule $schedule)
    {
        if ($error = $this->bookingError($schedule)) {
            return redirect()->route('user.schedules.index')->with('error', $error);
        }

        $schedule->load('route');

        return view('user.tickets.create', compact('schedule'));
    }

    public function store(Request $request, Schedule $schedule)
    {
        if ($error = $this->bookingError($schedule)) {
            return redirect()->route('user.schedules.index')->with('error', $error);
        }

        $data = $request->validate([
            'passenger_count' => ['required', 'integer', 'min:1', 'max:' . $schedule->available_seats],
            'payment_proof'   => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'passenger_count.max' => "Kursi tersisa hanya {$schedule->available_seats}.",
        ]);

        Ticket::create([
            'user_id'         => $request->user()->id,
            'schedule_id'     => $schedule->id,
            'passenger_count' => $data['passenger_count'],
            // total dihitung di server, bukan dari input user
            'total_price'     => $data['passenger_count'] * $schedule->price,
            'payment_proof'   => $request->file('payment_proof')->store('payment_proofs', 'public'),
            'status'          => 'pending',
        ]);

        return redirect()->route('user.tickets.index')
            ->with('success', 'Pesanan berhasil dibuat. Menunggu verifikasi admin.');
    }

    public function show(Ticket $ticket)
    {
        $this->authorizeOwner($ticket);
        $ticket->load('schedule.route');

        return view('user.tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $this->authorizeOwner($ticket);

        if ($ticket->status !== 'pending') {
            return redirect()->route('user.tickets.index')
                ->with('error', 'Hanya tiket berstatus Pending yang bisa diedit.');
        }

        $ticket->load('schedule.route');

        return view('user.tickets.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorizeOwner($ticket);

        if ($ticket->status !== 'pending') {
            return redirect()->route('user.tickets.index')
                ->with('error', 'Hanya tiket berstatus Pending yang bisa diedit.');
        }

        $schedule = $ticket->schedule;

        if ($schedule->departure_at->isPast()) {
            return redirect()->route('user.tickets.index')
                ->with('error', 'Jadwal ini sudah berangkat, tiket tidak bisa diubah.');
        }

        $data = $request->validate([
            'passenger_count' => ['required', 'integer', 'min:1', 'max:' . $schedule->available_seats],
            'payment_proof'   => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'passenger_count.max' => "Kursi tersisa hanya {$schedule->available_seats}.",
        ]);

        $update = [
            'passenger_count' => $data['passenger_count'],
            'total_price'     => $data['passenger_count'] * $schedule->price,
        ];

        if ($request->hasFile('payment_proof')) {
            // hapus bukti lama sebelum menyimpan yang baru
            if ($ticket->payment_proof) {
                Storage::disk('public')->delete($ticket->payment_proof);
            }
            $update['payment_proof'] = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        $ticket->update($update);

        return redirect()->route('user.tickets.index')->with('success', 'Tiket berhasil diperbarui.');
    }

    /**
     * Batalkan tiket (hanya saat Pending). Data tetap tersimpan sebagai riwayat.
     */
    public function destroy(Ticket $ticket)
    {
        $this->authorizeOwner($ticket);

        if ($ticket->status !== 'pending') {
            return redirect()->route('user.tickets.index')
                ->with('error', 'Hanya tiket berstatus Pending yang bisa dibatalkan.');
        }

        $ticket->update(['status' => 'cancelled']);

        return redirect()->route('user.tickets.index')->with('success', 'Tiket berhasil dibatalkan.');
    }

    /** Ownership check: user tidak boleh menyentuh tiket milik user lain. */
    private function authorizeOwner(Ticket $ticket): void
    {
        abort_if($ticket->user_id !== auth()->id(), 403);
    }

    /** Pesan error jika jadwal tidak bisa dipesan, null jika aman. */
    private function bookingError(Schedule $schedule): ?string
    {
        if ($schedule->departure_at->isPast()) {
            return 'Jadwal ini sudah berangkat.';
        }
        if ($schedule->available_seats < 1) {
            return 'Kursi untuk jadwal ini sudah penuh.';
        }
        return null;
    }
}