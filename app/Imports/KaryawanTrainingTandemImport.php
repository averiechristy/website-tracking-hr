<?php

namespace App\Imports;

use App\Models\LaporanPerformance;
use App\Models\MasterAktif;
use App\Models\MasterTidakAktif;
use App\Models\MasterTrainingTandem;
use App\Models\Overtime;
use App\Models\Karyawan;
use App\Models\Posisi;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class KaryawanTrainingTandemImport implements ToCollection, WithHeadingRow, WithStartRow
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
        $this->rowNumber = 1;
        $this->month = $month;
        $this->year = $year;
        $this->allowedposisi = Posisi::pluck('nama_posisi')->toArray();
    }

    public function startRow(): int
    {
        return 2;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
     
        foreach ($rows as $row) {
            $this->rowNumber++;
            
            if (
                empty($row['nama']) &&
                empty($row['posisi']) &&
                empty($row['domisili']) &&
                empty($row['kelas_training']) && 
                empty($row['tanggal_training']) &&
                empty($row['status']) &&
                empty($row['reason']) 
            ) {
                continue; 
            }
            
            $posisi = $row['posisi'];

            if ($posisi !== null && !in_array(strtolower($posisi), array_map('strtolower', $this->allowedposisi))) {
                $this->errors[] = "Posisi pada baris {$this->rowNumber} tidak valid, sesuaikan dengan master data posisi.";
                continue;
            }   

            $dataposisi = Posisi::where('nama_posisi', $posisi)->first();

            $posisiid = $dataposisi->id;

            $bulan = $this->month;
            $tahun = $this->year;
            $dataposisi = Posisi::find($posisiid);

            $namaposisi = $dataposisi->nama_posisi;
    

            $laporanperformance = LaporanPerformance::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('posisi_id', $posisiid)->first();

            
            $monthName = Carbon::createFromDate($tahun, $bulan, 1)->format('F');

         
            $existingkaryawan = MasterTrainingTandem::where('nama_karyawan', $row['nama'] )->first();

            $namakaryawan = $row['nama'];
         
            $tanggal = $row['tanggal_training'];

            if (is_numeric($tanggal)) {
                // Excel date serial number format

                $tanggalcarbon = Carbon::createFromDate(1900, 1, 1)->addDays($tanggal - 2);
                $formattedDate = $tanggalcarbon->format('Y-m-d');
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
                // Text date format (YYYY-MM-DD)
                try {
                    $tanggalcarbon = Carbon::createFromFormat('Y-m-d', $tanggal);
                    $formattedDate = $tanggalcarbon->format('Y-m-d');
                } catch (Exception $e) {
                    $this->errors[] = "Format tanggal tidak valid pada baris {$this->rowNumber}.";
                    continue;
                }
            } else {
                $this->errors[] = "Format tanggal tidak valid pada baris {$this->rowNumber}.";
                continue;
            }
            
            if ($existingkaryawan) {
                $this->errors[] = "Data karyawan {$namakaryawan} sudah terdaftar pada baris {$this->rowNumber}.";
                continue;
            }
                        
            MasterTrainingTandem::create(attributes: [
                'id' => ++$this->lastId,
                'nama_karyawan' => $row['nama'],
                'posisi' => $row['posisi'],
                'domisili' => $row['domisili'],
                'kelas_training' => $row['kelas_training'],
                'tanggal_training' => $formattedDate,
                'status' => $row['status'],
                'reason' => $row['reason'],
                'bulan' => $this->month,
                'tahun'=> $this->year,
                'posisi_id' => $posisiid,
                
            ]);

        }

        if (!empty($this->errors)) {
            $errorMessages = implode('<br>', $this->errors);
            throw new Exception($errorMessages);
        }

    }
}
