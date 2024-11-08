<?php

namespace App\Imports;

use App\Models\LaporanPerformance;
use App\Models\MasterAktif;
use App\Models\MasterTidakAktif;
use App\Models\Overtime;
use App\Models\Karyawan;
use App\Models\Posisi;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class KaryawanTidakAktifImport implements ToCollection, WithHeadingRow, WithStartRow
{
    private $lastId;
    private $rowNumber;
    private $allowedKaryawan = [];
    private $processedRows = [];
    private $errors = [];
    private $invalidKaryawan = [];
    private $month;
    private $year;

    private $allowedposisi = [];

    public function __construct($month, $year)
    {
        $this->rowNumber = 2;
        $this->month = $month;
        $this->year = $year;
        $this->allowedposisi = Posisi::pluck('nama_posisi')->toArray();
    }

    public function startRow(): int
    {
        return 3;
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
      
        foreach ($rows as $row) {
            $this->rowNumber++;
            
            if (
                empty($row['id']) &&
                empty($row['nama_karyawan']) &&
                empty($row['departemen']) &&
                empty($row['keterangan'])
            ) {
                continue; 
            }


            $departemen = $row['departemen'];

            if ($departemen !== null && !in_array(strtolower($departemen), array_map('strtolower', $this->allowedposisi))) {
                $this->errors[] = "Departemen pada baris {$this->rowNumber} tidak valid, sesuaikan dengan master data posisi.";
                continue;
            }

            
            $dataposisi = Posisi::where('nama_posisi', $departemen)->first();

            $posisiid = $dataposisi->id;


            $bulan = $this->month;
            $tahun = $this->year;
            $dataposisi = Posisi::find($posisiid);

            $namaposisi = $dataposisi->nama_posisi;
    

            $laporanperformance = LaporanPerformance::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('posisi_id', $posisiid)->first();

            

            $monthName = Carbon::createFromDate($tahun, $bulan, 1)->format('F');

           

            
            $existingkaryawan = MasterTidakAktif::where('karyawan_id', $row['id'])->first();

            $namakaryawan = $row['nama_karyawan'];
            $karyawanid = $row['id'];

            
            if ($existingkaryawan) {
                $this->errors[] = "Data karyawan {$namakaryawan} ($karyawanid) sudah terdaftar pada baris {$this->rowNumber}.";
                continue;
            }

            $status = $row['keterangan'];

            if (strcasecmp($status, "Batal Join") == 0) {
                $ket = "Batal Join";
            } else {
               $ket = "Resign";
            }
            
                
            MasterTidakAktif::create(attributes: [
                'id' => ++$this->lastId,
                'karyawan_id' =>  $row['id'],
                'nama_karyawan' => $row['nama_karyawan'],
                'jabatan' => $row['departemen'],
                'keterangan' => $row['keterangan'],
                'flag_ket' => $ket,
                'bulan' => $this->month,
                'tahun'=> $this->year,
                'posisi_id' => $posisiid,
                'nama_posisi' =>  $row['departemen'],
            ]);

        }

        if (!empty($this->errors)) {
            $errorMessages = implode('<br>', $this->errors);
            throw new Exception($errorMessages);
        }

    }
}
