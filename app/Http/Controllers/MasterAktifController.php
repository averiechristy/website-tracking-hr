<?php

namespace App\Http\Controllers;

use App\Imports\KaryawanAktifImport;
use App\Models\LaporanPerformance;
use App\Models\MasterAktif;
use App\Models\Posisi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class MasterAktifController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function superadminindex() {
        // Retrieve all records from MasterAktif model
        $karyawanaktif = MasterAktif::all();
    
        // Pass the data to the view
        return view('superadmin.masteraktif.index', [
            'karyawanaktif' => $karyawanaktif,
        ]);  
    }
    

     public function import(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
        ]);
    
        $month = $request->input('month');
        $year = $request->input('year');
    
        try {
            $file = $request->file('file');

            $reader = Excel::toArray([], $file);
            $headingRow = $reader[0][1]; // Mengambil header dari baris ke-5 (indeks 4)
           
            $expectedHeaders = [
              'ID',
              'Nama Karyawan',
              'Departemen'
            ];

            if ($headingRow !== $expectedHeaders) {
                throw new \Exception("File tidak sesuai.");
            }

            $data = Excel::toCollection(new KaryawanAktifImport($month, $year), $file);
            
            if ($data->isEmpty() || $data->first()->isEmpty()) {
                throw new \Exception("File harus diisi.");
            }
    
            $hasData = false;
            foreach ($data->first() as $row) {
                if ($row->filter()->isNotEmpty()) {
                    $hasData = true;
                    break;
                }
            }
    
            if (!$hasData) {
                throw new \Exception("File harus diisi.");
            }

            // Import data with month and year parameters
            Excel::import(new KaryawanAktifImport($month, $year), $file);

            // If import is successful, display success message
            $request->session()->flash('success', "Karyawan aktif berhasil ditambahkan.");
        } catch (\Exception $e) {
            // If an exception occurs, catch and display error message
            $request->session()->flash('error', $e->getMessage());
        }
    
        return redirect()->route('superadmin.masteraktif.index');
    }
    

    public function superadmincreate(){
        return view ('superadmin.masteraktif.create');
    }

    public function superadmindestroy(Request $request, $id)
    {
        $dataaktif = MasterAktif::find($id);

        $bulan = $dataaktif ->bulan;
        $tahun = $dataaktif->tahun;
        $posisiid = $dataaktif->posisi_id;

        $dataposisi = Posisi::find($posisiid);

        $namaposisi = $dataposisi->nama_posisi;

        $monthName = Carbon::createFromDate($tahun, $bulan, 1)->format('F');

        $laporanperform = LaporanPerformance::where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->where('posisi_id', $posisiid)->first();
        
        $dataaktif->delete();

        $request->session()->flash('success', "Karyawan aktif berhasil dihapus.");
    
        return redirect()->route('superadmin.masteraktif.index');
        
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * 
     */
    public function destroy(string $id)
    {
        //
    }
}
