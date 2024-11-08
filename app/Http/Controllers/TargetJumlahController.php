<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use App\Models\TargetJumlah;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TargetJumlahController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function superadminindex(){
        
        $target = TargetJumlah::orderBy('created_at','desc')->get();
        return view('superadmin.targetjumlah.index',[
            'target' => $target,
        ]);

     }

     public function superadmincreate(){
        

        $posisi = Posisi::all();

        return view('superadmin.targetjumlah.create',[
            'posisi' => $posisi,
        ]);

     }

     public function superadminstore(Request $request){
        $monthYear = $request->input('month'); 
        
        $posisiid = $request->posisi_id;
        
        $targetmpp = $request->target_mpp;

        $mitra = $request->mitra_existing;

        $targetjoin = $request->target_join;

        list($year, $month) = explode('-', $monthYear);

        $month = ltrim($month, '0');

        $dataposisi = Posisi::find($posisiid);

        $namaposisi = $dataposisi->nama_posisi;

        $existingdata = TargetJumlah::where('bulan', $month)
        ->where('tahun', $year)
        ->where('posisi_id', $posisiid)
        ->first();

        $monthName = Carbon::createFromDate($year, $month, 1)->format('F');

        if($existingdata){
            $request->session()->flash('error', "Data target sudah terdaftar untuk posisi $namaposisi pada bulan $monthName $year.");
            return redirect()->route('superadmin.targetjumlah.index');
        }

        TargetJumlah::create([
            'bulan' => $month,
            'posisi_id' => $posisiid,
            'tahun' => $year,
            'target_mpp' => $targetmpp,
            'jumlah_mitra' => $mitra,
            'target_join' => $targetjoin,
        ]);

        $request->session()->flash('success', "Data target berhasil ditambahkan.");
        return redirect()->route('superadmin.targetjumlah.index');

     }

     public function superadminshow($id){
        $data = TargetJumlah::find($id);
        $posisi = Posisi::all();

$bulan = str_pad($data->bulan, 2, '0', STR_PAD_LEFT); // Mengubah bulan jadi dua digit
$tahun = $data->tahun;
$monthValue = $tahun . '-' . $bulan;

        return view('superadmin.targetjumlah.edit',[
            'data'=>$data,
            'posisi' => $posisi,
            'monthValue' => $monthValue,
        ]);

     }


     public function superadminupdate (Request $request, $id){

        $data = TargetJumlah::find($id);

        $monthYear = $request->input('month'); 
        list($year, $month) = explode('-', $monthYear);
        $month = ltrim($month, '0');
        $monthName = Carbon::createFromDate($year, $month, 1)->format('F');

        $posisiid = $request->posisi_id;
        $dataposisi = Posisi::find($posisiid);
        $namaposisi = $dataposisi->nama_posisi;

        $targetmpp = $request->target_mpp;

        $mitra = $request->mitra_existing;

        $targetjoin = $request->target_join;

        $existingdata = TargetJumlah::where('posisi_id', $posisiid)
        ->where('bulan', $month)
        ->where('tahun', $year)
        ->where('id', '<>', $id)
        ->first();

        if($existingdata){
            $request->session()->flash('error', "Data target sudah terdaftar untuk posisi $namaposisi pada bulan $monthName $year.");
            return redirect()->route('superadmin.targetjumlah.index');
        }

        $data -> posisi_id = $posisiid;
        $data -> bulan = $month;
        $data -> tahun = $year;
        $data -> target_mpp = $targetmpp;
        $data -> jumlah_mitra = $mitra;
        $data->target_join = $targetjoin;
        $data -> save();

        $request->session()->flash('success', "Data target berhasil diubah.");
        return redirect()->route('superadmin.targetjumlah.index');

     }

     public function superadmindestroy(Request $request, $id){

        $data = TargetJumlah::find($id);

        $data->delete();

        $request->session()->flash('success', "Data target berhasil dihapus.");
        return redirect()->route('superadmin.targetjumlah.index');

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
