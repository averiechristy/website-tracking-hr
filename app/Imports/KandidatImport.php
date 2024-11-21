<?php

namespace App\Imports;

use App\Models\Kandidat;
use App\Models\LogActivity;
use App\Models\Posisi;
use App\Models\Wilayah;
use Auth;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KandidatImport implements ToCollection, WithHeadingRow
{
    protected $tanggal;
    private $allowedposisi = [];

    private $allowedwilayah = [];
    private $errors = [];
    private $rowNumber;
    protected $sumber;

    public function __construct($tanggal, $sumber)
    {
        $this->tanggal = $tanggal;
        $this->sumber = $sumber;
        $this->rowNumber = 1;
        $this->allowedposisi = Posisi::pluck('nama_posisi')->toArray();
        $this->allowedwilayah = Wilayah::pluck('nama_wilayah')->toArray();
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
       
        foreach ($rows as $row) {
            
         

            $this->rowNumber++;

            if (
                empty($row['posisi']) &&
                empty($row['wilayah']) &&
                empty($row['nama_kandidat']) && 
                empty($row['no_handphone']) &&
                empty($row['email'])
            ) {
                continue; 
            }

            $tanggallamar = $this->tanggal;
            list($tahun, $bulan, $hari) = explode("-", $tanggallamar);

            
            $posisi = $row['posisi'];
            $wilayah = $row['wilayah'];

            $contact = $row['no_handphone'];

            if (substr($contact, 0, 3) === '+62') {
                // Ganti "+62" dengan "0"
                $contact = '0' . substr($contact, 3);
            }

            $namakandidat = $row['nama_kandidat'];
            $contactedited = preg_replace('/^\+62/', '0', $contact);

            $existingKandidat = Kandidat::where('nama_kandidat', $namakandidat)
            ->where('no_hp', $contactedited)
            ->first();
            
            $errors = []; 
            
            if ($existingKandidat) {

                $existingDate = Carbon::parse($existingKandidat->tanggal);
                $monthsDifference = $existingDate->diffInMonths($tanggallamar);
                
                if ($monthsDifference < 3) {
                    $errors[] = "Kandidat pada baris {$this->rowNumber} sudah terdaftar dalam waktu kurang dari 3 bulan.";
                }
        
            }

            if ($posisi !== null) {
                if (!in_array(strtolower(trim($posisi)), array_map('strtolower', $this->allowedposisi))) {
                    $errors[] = "Posisi pada baris {$this->rowNumber} tidak valid, sesuaikan dengan master data posisi.";
                }
            }
            
            if ($wilayah !== null) {
                if (!in_array(strtolower(trim($wilayah)), array_map('strtolower', $this->allowedwilayah))) {
                    $errors[] = "Wilayah pada baris {$this->rowNumber} tidak valid, sesuaikan dengan master data wilayah.";
                }
            }
            
        
            if (!empty($errors)) {
                $this->errors = array_merge($this->errors, $errors);
                continue; 
            }
            
            $dataposisi = Posisi::where('nama_posisi', $posisi)->first();

            $posisiid = $dataposisi->id;
           
            $datawilayah = Wilayah::where('nama_wilayah', $wilayah)->first();
            $wilayahid = $datawilayah->id;

            $loggedInUser = auth()->user();
            $loggedInUsername = $loggedInUser->nama; 
            $userid = $loggedInUser->id;

            Kandidat::create([
                'tanggal' => $tanggallamar,
                'hari' => $hari,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'nama_kandidat' => $row['nama_kandidat'],
                'posisi' => $posisi,
                'no_hp' => $contact,
                'email' => $row['email'],
                'posisi_id' => $posisiid,
                'sumber_id' => $this->sumber,
                'wilayah_id' => $wilayahid,
                'status_hire' => "Belum Diproses",
                'created_by' => $loggedInUsername,
                'user_id' => $userid,
            ]);

            LogActivity::create([
                'user_id' => Auth::id(),
                'nama_user' =>  Auth::user()->nama,
                'activity' => 'Tambah Kandidat',
                'description' => 'Berhasil menambahkan kandidat ' . $row['nama_kandidat'],
                'timestamp' => now(),
                'role_id' =>  Auth::user()->role_id,
            ]);
        }

        if (!empty($this->errors)) {
            $errorMessages = implode('<br>', $this->errors);
            throw new Exception($errorMessages);
        }

    }
}
