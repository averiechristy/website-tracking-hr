<?php

namespace App\Http\Controllers;

use App\Imports\KaryawanTidakAktifImport;
use App\Models\LaporanPerformance;
use App\Models\MasterTidakAktif;
use App\Models\Posisi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MasterTidakAktifController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
              'Departemen',
              'Keterangan'
            ];

            if ($headingRow !== $expectedHeaders) {
                throw new \Exception("File tidak sesuai.");
            }

            $data = Excel::toCollection(new KaryawanTidakAktifImport($month, $year), $file);
            

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
            Excel::import(new KaryawanTidakAktifImport($month, $year), $file);

            // If import is successful, display success message
            $request->session()->flash('success', "Karyawan tidak aktif berhasil ditambahkan.");
        } catch (\Exception $e) {
            // If an exception occurs, catch and display error message
            $request->session()->flash('error', $e->getMessage());
        }
    
        return redirect()->route('superadmin.mastertidakaktif.index');
    }

    public function superadminindex(){

        $karyawantidakaktif = MasterTidakAktif::all(); 
        return view('superadmin.mastertidakaktif.index',[
            'karyawantidakaktif' => $karyawantidakaktif,
        ]);
    }


    public function superadmindestroy(Request $request, $id){
        $datatidakaktif = MasterTidakAktif::find($id);

        $bulan = $datatidakaktif->bulan;
        $tahun = $datatidakaktif->tahun;

        $posisiid = $datatidakaktif->posisi_id;

        $dataposisi = Posisi::find($posisiid);

        $namaposisi = $dataposisi->nama_posisi;

        $monthName = Carbon::createFromDate($tahun, $bulan, 1)->format('F');

        $laporanperform = LaporanPerformance::where('posisi_id', $posisiid)
        ->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->first();    
        
        $datatidakaktif ->delete();

        $request->session()->flash('success', "Karyawan tidak aktif berhasil dihapus.");
 
        return redirect()->route('superadmin.mastertidakaktif.index');

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
     */
    public function destroy(string $id)
    {
        //
    }
}
