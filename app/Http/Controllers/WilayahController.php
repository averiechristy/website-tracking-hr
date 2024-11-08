<?php

namespace App\Http\Controllers;

use App\Models\DetailPosisi;
use App\Models\Kandidat;
use App\Models\LogActivity;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WilayahController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function superadminindex(){
        $wilayah = Wilayah::orderBy('created_at','desc')->get();
        return view('superadmin.wilayah.index',[
            'wilayah' => $wilayah,
        ]);
     }


     public function superadmincreate(){
        return view ('superadmin.wilayah.create');
     }

     public function superadminstore(Request $request){

        $wilayah = $request->nama_wilayah;

        $existingdata = Wilayah::where('nama_wilayah', $wilayah)->first();

        if ($existingdata) {
            $request->session()->flash('error', "Wilayah sudah terdaftar.");
            return redirect()->route('superadmin.wilayah.index');
        }

        Wilayah::create([
            'nama_wilayah' => $wilayah,
        ]);

        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Membuat Wilayah',
            'description' => 'Berhasil membuat wilayah ' . $request->nama_wilayah,
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
    ]);

        $request->session()->flash('success', 'Posisi berhasil ditambahkan.');

        return redirect(route('superadmin.wilayah.index'));
     }

     public function superadminshow($id){

        $data = Wilayah::find($id);

        return view('superadmin.wilayah.edit',[
            'data' => $data,
        ]);

     }


     public function superadminupdate(Request $request, $id){

        $data = Wilayah::find($id);
        

        $wilayah = $request->nama_wilayah;

        $existingdata = Wilayah::where('nama_wilayah', $wilayah)->where('id', '<>', $id)->first();
       

      
        if ($existingdata) {
            $request->session()->flash('error', "Wilayah sudah terdaftar.");
            return redirect()->route('superadmin.wilayah.index');
        }

        $data->nama_wilayah = $wilayah;
        $data->save();

        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Update Wilayah',
            'description' => 'Berhasil mengupdate wilayah ' . $data->nama_wilayah,
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
        ]);


 $request->session()->flash('success', 'Wilayah berhasil diubah.');

        return redirect(route('superadmin.wilayah.index'));
     }


     public function superadmindestroy(Request $request, $id){

        $wilayah = Wilayah::find($id);
        $namawilayah = $wilayah->nama_wilayah;

        if (Kandidat::where('wilayah_id', $wilayah->id)->exists()) {
            $request->session()->flash('error', "Tidak dapat menghapus wilayah, karena masih ada data kandidat yang berhubungan.");
            return redirect()->route('superadmin.wilayah.index');
        }

        $detailPosisi = DetailPosisi::whereRaw('FIND_IN_SET(?, wilayah_id) > 0', [$wilayah->id])->exists();
       

    if ($detailPosisi) {
        $request->session()->flash('error', "Tidak dapat menghapus wilayah, karena masih ada data di user yang berhubungan.");
        return redirect()->route('superadmin.wilayah.index');
    }

        $wilayah->delete();

        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Hapus Wilayah',
            'description' => "Berhasil menghapus wilayah $namawilayah",
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
        ]);
 
        $request->session()->flash('success', "Wilayah berhasil dihapus.");

        return redirect()->route('superadmin.wilayah.index');

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
