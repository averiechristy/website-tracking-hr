<?php

namespace App\Exports;
use App\Models\Posisi;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use Maatwebsite\Excel\Concerns\FromCollection;

class BackupLporan implements WithMultipleSheets
{
    protected $tahun, $posisi;
    
    public function __construct($tahun, $posisi)
    {
        $this->tahun = $tahun;
        $this->posisi = $posisi;
    }

    public function sheets(): array
    {
        $sheets = [];

        if ($this->posisi == 'All') {
            // Untuk setiap posisi, buat sheet terpisah
            $positions = Posisi::all();
            foreach ($positions as $position) {
                // Menggunakan nama posisi sebagai judul sheet
                $sheets[] = new LaporanPerPosisiSheet($this->tahun, $position->id, $position->nama_posisi);
            }
        } else {
            // Untuk posisi tunggal
            $posisi = Posisi::find($this->posisi);
            $sheets[] = new LaporanPerPosisiSheet($this->tahun, $this->posisi, $posisi->nama_posisi);
        }

        return $sheets;
    }
}
