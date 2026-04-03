<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
    'kode_laporan',
    'nama_pelapor',
    'departemen', // ✅ INI YANG PENTING
    'tanggal_laporan',
    'jenis_masalah',
    'deskripsi',
    'status',
    'tanggal_selesai',
    'catatan_teknisi',
    'foto',
    'similarity',
];
}
