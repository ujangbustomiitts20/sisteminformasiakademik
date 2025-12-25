<?php

namespace App\Http\Controllers;

use App\Models\KalenderAkademik;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class KalenderAkademikController extends Controller
{
    /**
     * Display calendar
     */
    public function index()
    {
        $tahunAkademiks = TahunAkademik::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();
        
        return view('kalender.index', compact('tahunAkademiks'));
    }

    /**
     * Get events for calendar (JSON)
     */
    public function events(Request $request)
    {
        $query = KalenderAkademik::active();
        
        // If filtering by tahun akademik, skip date filter to show all events in that academic year
        if ($request->has('tahun_akademik_id') && $request->tahun_akademik_id) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        } elseif ($request->has('start') && $request->has('end')) {
            // Only apply date filter when not filtering by tahun akademik
            $start = $request->start;
            $end = $request->end;
            
            $query->where(function($q) use ($start, $end) {
                // Event starts within range OR ends within range OR spans the entire range
                $q->whereBetween('tanggal_mulai', [$start, $end])
                  ->orWhereBetween('tanggal_selesai', [$start, $end])
                  ->orWhere(function($q2) use ($start, $end) {
                      $q2->where('tanggal_mulai', '<=', $start)
                         ->where('tanggal_selesai', '>=', $end);
                  });
            });
        }
        
        $events = $query->get()->map(function($item) {
            return [
                'id' => Hashids::encode($item->id),
                'title' => $item->judul,
                'start' => $item->tanggal_mulai->format('Y-m-d'),
                'end' => $item->tanggal_selesai ? $item->tanggal_selesai->addDay()->format('Y-m-d') : null,
                'color' => $item->warna,
                'description' => $item->deskripsi,
                'jenis' => $item->jenis,
                'allDay' => true
            ];
        });
        
        return response()->json($events);
    }

    /**
     * Show create form (admin only)
     */
    public function create()
    {
        $tahunAkademiks = TahunAkademik::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();
        
        return view('kalender.create', compact('tahunAkademiks'));
    }

    /**
     * Store new event (admin only)
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'warna' => 'nullable|string|max:7',
            'jenis' => 'required|in:akademik,libur,ujian,pendaftaran,lainnya',
            'tahun_akademik_id' => 'nullable|exists:tahun_akademik,id'
        ]);

        KalenderAkademik::create([
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'warna' => $request->warna ?? '#007bff',
            'jenis' => $request->jenis,
            'is_active' => true
        ]);

        return redirect()->route('kalender.index')
            ->with('success', 'Event kalender berhasil ditambahkan.');
    }

    /**
     * Show edit form (admin only)
     */
    public function edit($id)
    {
        $decoded = Hashids::decode($id);
        if (empty($decoded)) {
            abort(404);
        }
        
        $event = KalenderAkademik::findOrFail($decoded[0]);
        $tahunAkademiks = TahunAkademik::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();
        
        return view('kalender.edit', compact('event', 'tahunAkademiks'));
    }

    /**
     * Update event (admin only)
     */
    public function update(Request $request, $id)
    {
        $decoded = Hashids::decode($id);
        if (empty($decoded)) {
            abort(404);
        }
        
        $event = KalenderAkademik::findOrFail($decoded[0]);
        
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'warna' => 'nullable|string|max:7',
            'jenis' => 'required|in:akademik,libur,ujian,pendaftaran,lainnya',
            'tahun_akademik_id' => 'nullable|exists:tahun_akademik,id'
        ]);

        $event->update([
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'warna' => $request->warna ?? '#007bff',
            'jenis' => $request->jenis
        ]);

        return redirect()->route('kalender.index')
            ->with('success', 'Event kalender berhasil diperbarui.');
    }

    /**
     * Delete event (admin only)
     */
    public function destroy($id)
    {
        $decoded = Hashids::decode($id);
        if (empty($decoded)) {
            abort(404);
        }
        
        $event = KalenderAkademik::findOrFail($decoded[0]);
        $event->delete();

        return redirect()->route('kalender.index')
            ->with('success', 'Event kalender berhasil dihapus.');
    }

    /**
     * Get list view (admin only)
     */
    public function list()
    {
        $events = KalenderAkademik::with('tahunAkademik')
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(20);
        
        return view('kalender.list', compact('events'));
    }
}
