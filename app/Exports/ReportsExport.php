<?php

namespace App\Exports;

use App\Models\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $from;
    protected $to;
    protected $status;
    protected $search;
    protected $departemen;


    // ✅ TERIMA FILTER
    public function __construct(
        $from = null,
        $to = null,
        $status = null,
        $search = null,
        $departemen = null
    ) {
        $this->from       = $from;
        $this->to         = $to;
        $this->status     = $status;
        $this->search     = $search;
        $this->departemen = $departemen;
    }


    // =============================
    // AMBIL DATA SESUAI FILTER
    // =============================
    public function collection()
    {
        $query = Report::query();


        // Filter tanggal
        if ($this->from) {
            $query->whereDate('tanggal_laporan', '>=', $this->from);
        }

        if ($this->to) {
            $query->whereDate('tanggal_laporan', '<=', $this->to);
        }


        // Filter status
        if ($this->status) {
            $query->where('status', $this->status);
        }


        // Filter departemen
        if ($this->departemen) {
            $query->where('departemen', $this->departemen);
        }


        // Search
        if ($this->search) {

            $query->where(function ($q) {

                $q->where('nama_pelapor', 'like', '%'.$this->search.'%')
                  ->orWhere('departemen', 'like', '%'.$this->search.'%')
                  ->orWhere('jenis_masalah', 'like', '%'.$this->search.'%')
                  ->orWhere('deskripsi', 'like', '%'.$this->search.'%');

            });
        }


        return $query->select(
            'id',
            'nama_pelapor',
            'departemen',
            'jenis_masalah',
            'deskripsi',
            'status',
            'tanggal_laporan',
            'tanggal_selesai',
            'catatan_teknisi',
            'foto'
        )
        ->get()
        ->map(function ($item) {

            // Convert foto → URL
            $item->foto = $item->foto
                ? asset('uploads/laporan/' . $item->foto)
                : '-';

            return $item;
        });
    }


    // =============================
    // HEADER EXCEL
    // =============================
    public function headings(): array
    {
        return [

            ['HELPDESK SURABRAJA – LAPORAN TICKETING IT'],

            ['Tanggal Export : ' . now()->format('d-m-Y')],

            ['Periode : ' . $this->getPeriodeText()],

            ['Divisi : ' . ($this->departemen ?? 'Semua')],

            [''],

            [
                'ID',
                'Nama Pelapor',
                'Departemen',
                'Jenis Masalah',
                'Deskripsi',
                'Status',
                'Tanggal Laporan',
                'Tanggal Selesai',
                'Catatan Teknisi',
                'Foto (URL)'
            ]
        ];
    }


    // =============================
    // STYLE
    // =============================
    public function styles(Worksheet $sheet)
    {
        // Merge Header
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');
        $sheet->mergeCells('A4:J4');


        // Judul
        $sheet->getStyle('A1')->applyFromArray([

            'font' => [
                'bold' => true,
                'size' => 16
            ],

            'alignment' => [
                'horizontal' => 'center'
            ]

        ]);


        // Subjudul
        $sheet->getStyle('A2:A4')->applyFromArray([

            'font' => [
                'bold' => true,
                'size' => 11
            ],

            'alignment' => [
                'horizontal' => 'left'
            ]

        ]);


        // Header tabel
        $sheet->getStyle('A6:J6')->getFont()->setBold(true);


        // Border
        $sheet->getStyle('A6:J' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );


        // Wrap
        $sheet->getStyle('D:J')
            ->getAlignment()
            ->setWrapText(true);


        return [];
    }


    // =============================
    // FORMAT PERIODE
    // =============================
    private function getPeriodeText()
    {
        if ($this->from && $this->to) {

            return date('d-m-Y', strtotime($this->from)) .
                   ' s/d ' .
                   date('d-m-Y', strtotime($this->to));
        }

        if ($this->from) {

            return 'Dari ' .
                   date('d-m-Y', strtotime($this->from));
        }

        if ($this->to) {

            return 'Sampai ' .
                   date('d-m-Y', strtotime($this->to));
        }

        return 'Semua Data';
    }
}
