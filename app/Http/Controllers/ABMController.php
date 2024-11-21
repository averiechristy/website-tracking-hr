<?php

namespace App\Http\Controllers;

use App\Models\ABM;
use App\Models\LogActivity;
use App\Models\TrainingABM;
use Auth;
use Illuminate\Http\Request;

class ABMController extends Controller
{
    /**
     * Display a listing of the resource.
     */



     public function superadminindex(){

        $abm = ABM::orderBy('created_at','desc')->get();
        return view('superadmin.abm.index',[
            'abm' => $abm,
        ]);
     }


     public function superadmincreate(){
        return view('superadmin.abm.create');
     }


     public function superadminstore(Request $request){

      
        $namaabm = $request->nama_abm;
        $existingdata = ABM::where('nama_ABM', $namaabm)->first();

        if ($existingdata) {
            $request->session()->flash('error', "Nama ABM sudah terdaftar.");
            return redirect()->route('superadmin.abm.index');
        }

        ABM::create([
            'nama_ABM' => $namaabm,
            'created_by' => Auth::user()->nama,
        ]);

        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Menambahkan ABM',
            'description' => 'Berhasil menambahkan ABM ' . $request->nama_abm,
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
    ]);


    $request->session()->flash('success', 'ABM berhasil ditambahkan.');

    return redirect(route('superadmin.abm.index'));

     }


     public function superadminshow($id){
        $data = ABM::find($id);

        return view('superadmin.abm.edit',[
            'data' => $data
        ]);
     }


     public function superadminupdate(Request $request, $id){
        $data = ABM::find($id);
        $abm = $request->nama_abm;

        $existingdata = ABM::where('nama_ABM', $abm)->where('id', '<>', $id)->first();

        
        if ($existingdata) {
            $request->session()->flash('error', "ABM sudah terdaftar.");
            return redirect()->route('superadmin.abm.index');
        }

        $data->nama_ABM = $abm;
        $data->save();

        
        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Update ABM',
            'description' => 'Berhasil mengupdate ABM ' . $data->nama_ABM,
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
        ]);

        $request->session()->flash('success', 'ABM berhasil diubah.');

        return redirect(route('superadmin.abm.index'));

     }


    public function superadmindestroy(Request $request, $id){
        $abm = ABM::find($id);
        $namaabm = $abm->nama_ABM;
        $trainingabm = TrainingABM::where('abm_id', $id)->first();

        if($trainingabm){
            $request->session()->flash('error', 'Tidak bisa menghapus ABM, karena ada data training yang berhubungan');

            return redirect(route('superadmin.abm.index'));
        }

        $abm->delete();

        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Hapus ABM',
            'description' => "Berhasil menghapus $namaabm",
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
        ]);

        $request->session()->flash('success', 'ABM berhasil dihapus.');

        return redirect(route('superadmin.abm.index'));
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
