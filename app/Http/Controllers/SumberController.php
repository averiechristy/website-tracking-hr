<?php

namespace App\Http\Controllers;

use App\Models\Kandidat;
use App\Models\LogActivity;
use App\Models\Sumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SumberController extends Controller
{
    /**
     * Display a listing of the resource.
     */


     public function superadminindex(){

        $sumbers = Sumber::orderBy('created_at','desc')->get();
        
        return view('superadmin.sumber.index',[
            'sumbers' => $sumbers,
        ]);

     }

     public function superadmincreate(){

        return view('superadmin.sumber.create');

     }


     public function superadminstore(Request $request){

        $namasumber = $request->nama_sumber;

        $existingdata = Sumber::where('nama_sumber', $namasumber)->first();

        if ($existingdata) {
            $request->session()->flash('error', "Sumber sudah terdaftar.");
            return redirect()->route('superadmin.sumber.index');
        }

        Sumber::create([
            'nama_sumber' => $namasumber,
        ]);

        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Membuat Sumber',
            'description' => 'Berhasil membuat sumber ' . $request->nama_sumber,
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
    ]);


    $request->session()->flash('success', 'Sumber berhasil ditambahkan.');

    return redirect(route('superadmin.sumber.index'));

     }

     public function superadminshow($id){
        $data = Sumber::find($id);

        return view ('superadmin.sumber.edit',[
            'data' => $data,
        ]);
     }



     public function superadminupdate(Request $request, $id){

        $data = Sumber::find($id);

        $namasumber = $request->nama_sumber;

        $existingdata = Sumber::where('nama_sumber', $namasumber)->where('id', '<>', $id)->first();
       
        if ($existingdata) {
            $request->session()->flash('error', "Sumber sudah terdaftar.");
            return redirect()->route('superadmin.sumber.index');
        }

        $data->nama_sumber = $namasumber;
        $data->save();

        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Update Sumber',
            'description' => 'Berhasil mengupdate sumber ' . $data->nama_sumber,
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
        ]);


        $request->session()->flash('success', 'Sumber berhasil diubah.');

        return redirect(route('superadmin.sumber.index'));

     }

     public function superadmindestroy(Request $request, $id){
        $sumber = Sumber::find($id);

        $namasumber = $sumber->nama_sumber;

        if (Kandidat::where('sumber_id', $sumber->id)->exists()) {
            $request->session()->flash('error', "Tidak dapat menghapus sumber, karena masih ada data kandidat yang berhubungan.");
            return redirect()->route('superadmin.sumber.index');
        }

        $sumber->delete();

        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Hapus Sumber',
            'description' => "Berhasil menghapus sumber $namasumber",
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
        ]);

        $request->session()->flash('success', "Sumber berhasil dihapus.");
        return redirect()->route('superadmin.sumber.index');

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
