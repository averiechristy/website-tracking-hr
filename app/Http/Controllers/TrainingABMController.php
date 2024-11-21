<?php

namespace App\Http\Controllers;

use App\Models\ABM;
use App\Models\DetailPosisi;
use App\Models\Kandidat;
use App\Models\LogActivity;
use App\Models\LogTahapan;
use App\Models\TrainingABM;
use Auth;
use Illuminate\Http\Request;

class TrainingABMController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function superadminindex() {
        $trainingabm = TrainingABM::all();
    
        return view('superadmin.trainingabm.index', [
            'trainingabm' => $trainingabm,
        ]);
    }
    
     public function superadmincreate() {
        $roleid = auth()->user()->role_id;
        $abm = ABM::all();

        // Check if the user role is 2
        if ($roleid == 2) {
            $userId = auth()->id();
            $detailPosisi = DetailPosisi::where('user_id', $userId)->get();
    
            $assignedPosisi = $detailPosisi->pluck('posisi_id')->unique();
            $assignedWilayah = $detailPosisi->pluck('wilayah_id')->unique();
    
            $assignedPosisiIds = [];
            $assignedWilayahIds = [];
    
            foreach ($assignedPosisi as $posisi) {
                $assignedPosisiIds = array_merge($assignedPosisiIds, explode(',', $posisi));
            }
    
            foreach ($assignedWilayah as $wilayah) {
                $assignedWilayahIds = array_merge($assignedWilayahIds, explode(',', $wilayah));
            }
    
            $assignedPosisiIds = array_unique($assignedPosisiIds);
            $assignedWilayahIds = array_unique($assignedWilayahIds);
    
            // Query Kandidat with filters for status_hire, blacklist, position, and region
            $kandidat = Kandidat::whereDoesntHave('blacklist')
            ->whereIn('posisi_id', $assignedPosisiIds)
            ->whereIn('wilayah_id', $assignedWilayahIds)
            ->whereHas('logtahapan', function($query) {
                $query->where('status_tahapan', 'Proses PKM')
                      ->where('hasil_status', 'Selesai Proses');
            })
            ->whereDoesntHave(relation: 'trainingabm')
            ->get();

        } else {
            // For other roles, only apply the basic filters
            $kandidat = Kandidat::whereDoesntHave('blacklist')
                ->whereHas('logtahapan', function($query) {
                    $query->where('status_tahapan', 'Proses PKM')
                          ->where('hasil_status', 'Selesai Proses');
                })
                ->whereDoesntHave('trainingabm')
                ->get();
        }

      
        return view('superadmin.trainingabm.create', [
            'kandidat' => $kandidat,
            'abm' => $abm,
        ]);

     }

     public function superadminstore(Request $request){
        $abmId = $request->input('abm_id');
        $tanggal = $request->tanggal;
        $dataabm = ABM::find($abmId);
        $kandidatIds = $request->input('kandidat_id');

        foreach ($kandidatIds as $kandidatId) {


            // Simpan ke model ABM

            $datakandidat=Kandidat::find($kandidatId);
            $posisiid = $datakandidat->posisi_id;
            $wilayahid = $datakandidat->wilayah_id;


            TrainingABM::create([
                'kandidat_id' => $kandidatId,
                'abm_id' => $abmId,
                'nama_kandidat' => $datakandidat->nama_kandidat,
                'nama_abm' => $dataabm->nama_ABM,
                'created_by' =>Auth::user()->nama,
                'tanggal' => $tanggal,
               
            ]);
    
            // Simpan ke log tahapan
            LogActivity::create([
                'user_id' => Auth::id(),
                'nama_user' =>  Auth::user()->nama,
                'activity' => 'Training ABM',
                'description' => 'Menambahkan ' . $datakandidat->nama_kandidat . 'ke dalam daftar training ABM.',
                'timestamp' => now(),
                'role_id' =>  Auth::user()->role_id,
               
            ]);
            $tanggalHari = date('d', strtotime($tanggal));
            $tanggalBulan = date('m', strtotime($tanggal));
            $tanggalTahun = date('Y', strtotime($tanggal));

            LogTahapan::create([
                'kandidat_id' => $kandidatId,
                'status_tahapan' => 'Training ABM',
                'tanggal' => $tanggal,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $posisiid,
                'wilayah_id' => $wilayahid,
                'hasil_status' => 'Training ABM',
                'flag_tahapan' => 'Training ABM',
                'abm_id' => $abmId,
                'nama_abm' => $dataabm->nama_ABM,
        ]);


        }


     $request->session()->flash('success', 'Kandidat berhasil ditambahkan ke dalam daftar training ABM.');
    
        return redirect(route('superadmin.trainingabm.index'));

     }

     public function superadmindestroy(Request $request, $id)
     {
         $trainingabm = TrainingABM::find($id);
 
         $kandidatid = $trainingabm->kandidat_id;
         
        $datakandidat = Kandidat::find($kandidatid);
        $namakandidat = $datakandidat->nama_kandidat;
         
         $logtahapan = LogTahapan::where('kandidat_id', $kandidatid)
         ->where('status_tahapan', 'Training ABM')
         ->first();
 
         $logtahapan->delete();
 
         $trainingabm->delete();

         LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Hapus Daftar Training ABM',
            'description' => "Berhasil menghapus $namakandidat dari daftar Training ABM",
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
        ]);
 
         $request->session()->flash('success', "Kandidat berhasil dihapus dalam daftar training ABM.");
 
         return redirect()->route('superadmin.trainingabm.index');
     }

    public function index()
    {

       
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
