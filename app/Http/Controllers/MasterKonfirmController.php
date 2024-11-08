<?php

namespace App\Http\Controllers;

use App\Models\DetailPosisi;
use App\Models\Kandidat;
use App\Models\LaporanPerformance;
use App\Models\MasterKonfirm;
use App\Models\Posisi;
use App\Models\Sumber;
use App\Models\User;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MasterKonfirmController extends Controller
{
    /**
     * Display a listing of the resource.
     */


     public function superadminindex()
     {
         $loggedInUser = auth()->user();
         $loggedid = $loggedInUser->id;
     
         if ($loggedInUser->role_id === 2) {
             // Get the assigned positions and regions for the logged-in user
             $detailPosisi = DetailPosisi::where('user_id', $loggedid)->get();
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
             
             // Query MasterKonfirm based on assigned positions and regions
             $data = MasterKonfirm::where(function ($query) use ($assignedPosisiIds, $assignedWilayahIds) {
                 foreach ($assignedPosisiIds as $posisiId) {
                     foreach ($assignedWilayahIds as $wilayahId) {
                         $query->orWhere(function ($query) use ($posisiId, $wilayahId) {
                             $query->where('posisi_id', $posisiId)
                                   ->where('wilayah_id', $wilayahId);
                         });
                     }
                 }
             })->get();
     
         } else {
             $data = MasterKonfirm::all();
         }
     
         return view('superadmin.masterkonfirm.index', [
             'data' => $data,
         ]);
     }
     

     public function superadmindestroy(Request $request, $id) {

        $konfirm = MasterKonfirm::find($id);

        $bulan = $konfirm->month;
        $tahun = $konfirm->year;


        $posisiid = $konfirm->posisi_id;

        $dataposisi = Posisi::find($posisiid);

        $namaposisi = $dataposisi->nama_posisi;

        $monthName = Carbon::createFromDate($tahun, $bulan, 1)->format('F');

        $laporanperform = LaporanPerformance::where('posisi_id', $posisiid)
        ->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->first();    

       
        $konfirm->delete();



        $request->session()->flash('success', "Data konfirmasi berhasil dihapus.");
 
        return redirect()->route('superadmin.masterkonfirm.index');
     }

     public function getJumlahUndang(Request $request)
     {
         $count = Kandidat::where('tanggal', $request->tanggal)
                         ->where('posisi_id', $request->posisi_id)
                         ->where('wilayah_id', $request->wilayah_id)
                         ->where('user_id', $request->sourcing_id)
                         ->count();
         
         return response()->json(['count' => $count]);
     }
     
     public function superadmincreate(){
        $posisi = Posisi::all();
        $wilayah = Wilayah::all();
        $sumber = Sumber::all();
        
        $user = User::all();

        $loggeduser = auth()->user();
     
        $loggedid = $loggeduser->id;
        
        $detailPosisi = DetailPosisi::where('user_id', auth()->id())->pluck('posisi_id')->toArray(); 
        

        // Ambil data Posisi berdasarkan kode-kode yang ada di DetailPosisi
        $filteredPosisi = Posisi::whereIn('id', $detailPosisi)->get();

        $posisi = $posisi->sortBy('nama_posisi'); 
        $wilayah = $wilayah->sortBy('nama_wilayah');
        $sumber = $sumber->sortBy('nama_sumber');

        return view('superadmin.masterkonfirm.create',[
           
            'posisi'=>$posisi,
            'wilayah' => $wilayah,
            'sumber' => $sumber,
            'detailPosisi' => $detailPosisi,
            'filteredPosisi' => $filteredPosisi,
            'user' => $user,            
            'loggedid' => $loggedid,
        ]);
     }


     public function superadminstore(Request $request){

        $tanggal = $request->tanggal;
        $sourcingid = $request->sourcing_id;
        $posisiid = $request->posisi_id;
        $wilayahid = $request->wilayah_id;
        $jumlahundang = $request->jumlah_undang_otomatis;
        $jumlahkonfirm = $request->jumlah_konfirm_manual;

        $existingdata = MasterKonfirm::where('tanggal', $tanggal)
        ->where('posisi_id', $posisiid)
        ->where('wilayah_id', $wilayahid)
        ->where('sourcing_id', $sourcingid)
        ->first();

        $datasourcing = User::find($sourcingid);
        $dataposisi = Posisi::find($posisiid);
        $datawilayah = Wilayah::find($wilayahid);
        
        $namasourcing = $datasourcing->nama;
        $namaposisi = $dataposisi->nama_posisi;
        $namawilayah = $datawilayah->nama_wilayah;

        if ($existingdata){
            $request->session()->flash('error', "Data konfirmasi PIC $namasourcing untuk $namaposisi $namawilayah pada tanggal $tanggal sudah terdaftar.");
            return redirect(route('superadmin.masterkonfirm.index'));
        }

        $bulan = date('m', strtotime($tanggal));

        // Memecahkan tanggal menjadi tahun
        $tahun = date('Y', strtotime($tanggal));
        $dataposisi = Posisi::find($posisiid);

        $namaposisi = $dataposisi->nama_posisi;

        $monthName = Carbon::createFromDate($tahun, $bulan, 1)->format('F');

        $laporanperform = LaporanPerformance::where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->where('posisi_id', $posisiid)->first();



        $date = Carbon::parse($tanggal);

        $hari = $date->day;
        $bulan = $date->month;
        $tahun = $date->year;

        $keterangan = $request->keterangan;
      

        MasterKonfirm::create([
            'tanggal' => $tanggal,
            'sourcing_id' => $sourcingid,
            'nama_sourcing' => $namasourcing,
            'posisi_id' => $posisiid,
            'nama_posisi' => $namaposisi,
            'wilayah_id' => $wilayahid,
            'nama_wilayah' => $namawilayah,
            'jumlah_undang_otomatis' => $jumlahundang,
            'jumlah_konfirm_manual' => $jumlahkonfirm,
            'day' => $hari,
            'month' => $bulan,
            'year'=> $tahun,
            'keterangan' => $keterangan,
        ]);

        $request->session()->flash('success', 'Data konfirmasi berhasil ditambahkan.');

        return redirect(route('superadmin.masterkonfirm.index'));

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
     */
    public function destroy(string $id)
    {
        //
    }
}
