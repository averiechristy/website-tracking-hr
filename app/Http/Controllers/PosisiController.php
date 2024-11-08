<?php

namespace App\Http\Controllers;

use App\Models\DetailPosisi;
use App\Models\Kandidat;
use App\Models\LogActivity;
use App\Models\Posisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */


     public function superadminindex(){
        $posisis = Posisi::orderBy('created_at','desc')->get();
        return view('superadmin.posisi.index',[
            'posisis' => $posisis,
        ]);
     }


     public function superadmincreate(){
        return view ('superadmin.posisi.create');
     }

     public function superadminstore(Request $request){

        $posisi = $request->nama_posisi;

        $existingdata = Posisi::where('nama_posisi', $posisi)->first();

        if ($existingdata) {
            $request->session()->flash('error', "Posisi sudah terdaftar.");
            return redirect()->route('superadmin.posisi.index');
        }

        Posisi::create([
            'nama_posisi' => $posisi,
        ]);

        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Membuat Posisi',
            'description' => 'Berhasil membuat posisi ' . $request->nama_posisi,
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
    ]);

        $request->session()->flash('success', 'Posisi berhasil ditambahkan.');

        return redirect(route('superadmin.posisi.index'));
     }


     public function superadminshow($id){

        $data = Posisi::find($id);

        return view('superadmin.posisi.edit',[
            'data' => $data,
        ]);

     }

     public function superadminupdate(Request $request, $id){

        $data = Posisi::find($id);
        

        $posisi = $request->nama_posisi;

        $existingdata = Posisi::where('nama_posisi', $posisi)->where('id', '<>', $id)->first();
       

      
        if ($existingdata) {
            $request->session()->flash('error', "Posisi sudah terdaftar.");
            return redirect()->route('superadmin.posisi.index');
        }


        $data->nama_posisi = $posisi;
        $data->save();

        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Update Posisi',
            'description' => 'Berhasil mengupdate posisi ' . $data->nama_posisi,
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
        ]);



 $request->session()->flash('success', 'Posisi berhasil diubah.');

        return redirect(route('superadmin.posisi.index'));
     }

     public function superadmindestroy(Request $request, $id){

        $posisi = Posisi::find($id);
        $namaposisi = $posisi->nama_posisi;

        if (Kandidat::where('posisi_id', $posisi->id)->exists()) {
            $request->session()->flash('error', "Tidak dapat menghapus posisi, karena masih ada data kandidat yang berhubungan.");
            return redirect()->route('superadmin.posisi.index');
        }

        if (DetailPosisi::where('posisi_id', $posisi->id)->exists()) {
            $request->session()->flash('error', "Tidak dapat menghapus user, karena masih ada data user yang berhubungan.");
            return redirect()->route('superadmin.user.index');
        }

        $posisi->delete();

        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Hapus Posisi',
            'description' => "Berhasil menghapus posisi $namaposisi",
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
        ]);
 
        $request->session()->flash('success', "Posisi berhasil dihapus.");

        return redirect()->route('superadmin.posisi.index');

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
