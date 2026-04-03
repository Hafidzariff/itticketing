<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Mail\ReportCreatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // 🟢 Form laporan
    public function create()
    {
        return view('reports.create');
    }

    // 🟢 Simpan laporan
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelapor'        => 'required',
            'departemen'          => 'required',
            'departemen_lainnya'  => 'required_if:departemen,Lainnya',
            'jenis_masalah'       => 'required',
            'deskripsi'           => 'required',
            'foto'                => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 🔥 FIX UTAMA DI SINI
        $departemen = $request->departemen;

        if ($request->departemen === 'Lainnya') {
            $departemen = $request->filled('departemen_lainnya')
                ? trim($request->departemen_lainnya)
                : 'Lainnya';
        }

       // Upload foto
        $filename = null;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');

            $filename = time() . '-' . $file->getClientOriginalName();

            // Path langsung ke public_html
            $destination = $_SERVER['DOCUMENT_ROOT'] . '/uploads/laporan';

            // Buat folder jika belum ada
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            // Upload file
            $file->move($destination, $filename);
        }
        // Simpan data
        $report = Report::create([
            'kode_laporan'     => $this->generateKode(),
            'nama_pelapor'     => $request->nama_pelapor,
            'departemen'       => $departemen,
            'tanggal_laporan'  => now(),
            'jenis_masalah'    => $request->jenis_masalah,
            'deskripsi'        => $request->deskripsi,
            'status'           => 'Baru',
            'foto'             => $filename,
        ]);

        // Kirim email
        Mail::to(env('ADMIN_EMAIL', 'admin@surabraja.co.id'))
            ->queue(new ReportCreatedMail($report));

        return redirect()->back()->with(
            'success',
            '✅ Laporan berhasil dikirim!
Kode laporan Anda: ' . $report->kode_laporan . '
(Simpan kode ini untuk cek status)'
        );
    }

    private function generateKode()
    {
        return 'LP-' . strtoupper(uniqid());
    }

    // 🟢 Form cek laporan
    public function cekForm()
    {
        return view('reports.cek');
    }

    // 🟢 Cek status laporan
    public function cekStatus(Request $request)
    {
        $request->validate([
            'kode_laporan' => 'required'
        ]);

        $report = Report::where('kode_laporan', $request->kode_laporan)->first();

        if (!$report) {
            return back()->with('error', '❌ Kode laporan tidak ditemukan');
        }

        return view('reports.cek-hasil', compact('report'));
    }

    // 🟢 List laporan
    public function index(Request $request)
    {
        $query = Report::orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('tanggal_laporan', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('tanggal_laporan', '<=', $request->to);
        }

        if ($request->filled('departemen')) {
            $query->where('departemen', $request->departemen);
        }

        if ($request->filled('search')) {
            $keyword = $request->search;

            $query->where(function ($q) use ($keyword) {
                $q->where('nama_pelapor', 'LIKE', "%$keyword%")
                  ->orWhere('departemen', 'LIKE', "%$keyword%")
                  ->orWhere('jenis_masalah', 'LIKE', "%$keyword%")
                  ->orWhere('deskripsi', 'LIKE', "%$keyword%");
            });
        }

        $reports = $query->paginate(5)->withQueryString();

        return view('reports.index', compact('reports'));
    }

    // 🟢 Update status
    public function update(Request $request, Report $report)
    {
        $report->update([
            'status'           => $request->status,
            'catatan_teknisi'  => $request->catatan_teknisi,
            'tanggal_selesai'  => $request->status === 'Selesai' ? now() : null,
        ]);

        return redirect()
            ->route('reports.index')
            ->with('success', '✅ Status laporan berhasil diperbarui.');
    }

    // 🟢 Hapus 1 laporan
    public function destroy(Report $report)
    {
        $report->delete();

        return redirect()
            ->route('reports.index')
            ->with('success', '🗑️ Laporan berhasil dihapus.');
    }

    // 🟢 Hapus semua
    public function destroyAll()
    {
        Report::truncate();

        return redirect()
            ->route('reports.index')
            ->with('success', '🗑️ Semua laporan berhasil dihapus.');
    }

    // 🟢 Export Excel
    public function export(Request $request)
    {
        return Excel::download(
            new ReportsExport(
                $request->from,
                $request->to,
                $request->status,
                $request->search,
                $request->departemen
            ),
            'laporan_export_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    // 🟢 Dashboard
    public function dashboard()
    {
        $total   = Report::count();
        $selesai = Report::where('status', 'Selesai')->count();
        $proses  = Report::where('status', 'Sedang Dikerjakan')->count();
        $baru    = Report::where('status', 'Baru')->count();

        $laporanBaru = Report::where('status', 'Baru')
            ->latest()
            ->take(3)
            ->get();

        $diagramHelpdesk = Report::select(
                'departemen',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('departemen')
            ->get();

        return view(
            'reports.dashboard',
            compact(
                'total',
                'selesai',
                'proses',
                'baru',
                'laporanBaru',
                'diagramHelpdesk'
            )
        );
    }
}