<?php

namespace App\Http\Controllers;

use App\Exports\TemplateExport;
use App\Imports\KandidatImport;
use App\Models\DetailPosisi;
use App\Models\Kandidat;
use App\Models\LaporanPerformance;
use App\Models\LogActivity;
use App\Models\LogTahapan;
use App\Models\Posisi;
use App\Models\Sumber;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class KandidatController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function superadminindex(Request $request) {
        $roleid = auth()->user()->role_id;
        $sumber = Sumber::all();

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
            
            // Initialize the query for Kandidat
            $query = Kandidat::query();

       
        
            // Filtering by position and region based on assigned positions and regions
            $query->where(function ($query) use ($assignedPosisiIds, $assignedWilayahIds) {
                foreach ($assignedPosisiIds as $posisiId) {
                    foreach ($assignedWilayahIds as $wilayahId) {
                        $query->orWhere(function ($query) use ($posisiId, $wilayahId) {
                            $query->where('posisi_id', $posisiId)
                                  ->where('wilayah_id', $wilayahId);
                        });
                    }
                }
            });
            
            // Filtering by user input
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $selectedPosisiId = $request->filter_posisi;
                $query->where('posisi_id', $selectedPosisiId);
        
                // Get assigned wilayah for the selected posisi
                $assignedWilayahIdsForPosisi = $detailPosisi->where('posisi_id', $selectedPosisiId)->pluck('wilayah_id')->toArray();
                $wilayah = Wilayah::whereIn('id', explode(',', implode(',', $assignedWilayahIdsForPosisi)))->get();
                
                // Apply wilayah filter if specified
                if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                    $query->where('wilayah_id', $request->filter_wilayah);
                }
            } else {
                // If no posisi is selected, no wilayah should be shown
                $wilayah = collect(); // Empty collection
            }
    
            // Apply filter for status_copy
            if ($request->has('filter_status_copy') && $request->filter_status_copy != '') {
                if ($request->filter_status_copy == 'Copied') {
                    $query->where('status_copy', 'Copied');
                } else if ($request->filter_status_copy == 'Not Copied') {
                    $query->whereNull('status_copy');
                }
            }


            if ($request->has('filter_tanggal_awal') && $request->filter_tanggal_awal != '' && 
            $request->has('filter_tanggal_akhir') && $request->filter_tanggal_akhir != '') {
            $tanggalAwal = $request->filter_tanggal_awal;
            $tanggalAkhir = $request->filter_tanggal_akhir;
        
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        }
            $kandidat = $query->orderBy('created_at', 'desc')->get();
        
            // Get assigned positions and regions for filters
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();

            
            // Return the view or data
            return view('superadmin.kandidat.index', [
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
                'selectedStatusCopy' => $request->filter_status_copy,
                'sumber' => $sumber
            ]);

        } else {
            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
    
            $query = Kandidat::query();
    
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            // Apply filter for status_copy
            if ($request->has('filter_status_copy') && $request->filter_status_copy != '') {
                if ($request->filter_status_copy == 'Copied') {
                    $query->where('status_copy', 'Copied');
                } else if ($request->filter_status_copy == 'Not Copied') {
                    $query->whereNull('status_copy');
                }
            }
            
            if ($request->has('filter_tanggal_awal') && $request->filter_tanggal_awal != '' && 
            $request->has('filter_tanggal_akhir') && $request->filter_tanggal_akhir != '') {
            $tanggalAwal = $request->filter_tanggal_awal;
            $tanggalAkhir = $request->filter_tanggal_akhir;
        
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        }
        
            $kandidat = $query->orderBy('created_at', 'desc')->get();
    
            return view('superadmin.kandidat.index', [
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
                'selectedStatusCopy' => $request->filter_status_copy,
                'sumber' => $sumber
            ]);
        }
    }
    
       
    public function detailtahapan($id){
        $data = Kandidat::find($id);

        $detail = LogTahapan::with('kandidat')->where('kandidat_id', $id)->get();

        return view('superadmin.kandidat.detail',[
            'data' => $data,
            'detail' => $detail,
        ]);
    }
    
    
    
    public function processNoHp(Request $request)
    {
        $noHpList = $request->input('no_hp');
        
    
        // Lakukan proses lain yang diperlukan dengan $noHpList
    
        return view('superadmin.kandidat.no_hp', compact('noHpList'));
    }

    // Add this method in your relevant controller



public function updateStatus(Request $request)
{
    $ids = $request->input('ids');
    $status = $request->input('status');

    Kandidat::whereIn('id', $ids)->update(['status_copy' => $status]);

    return response()->json(['success' => true]);
}


// KandidatController.php



     public function superadmincreate(){
        $posisi = Posisi::all();
        $wilayah = Wilayah::all();
        $sumber = Sumber::all();
        
        
        $detailPosisi = DetailPosisi::where('user_id', auth()->id())->pluck('posisi_id')->toArray(); 
        

        // Ambil data Posisi berdasarkan kode-kode yang ada di DetailPosisi
        $filteredPosisi = Posisi::whereIn('id', $detailPosisi)->get();

        $posisi = $posisi->sortBy('nama_posisi'); 
        $wilayah = $wilayah->sortBy('nama_wilayah');
        $sumber = $sumber->sortBy('nama_sumber');

        return view('superadmin.kandidat.create',[
           
            'posisi'=>$posisi,
            'wilayah' => $wilayah,
            'sumber' => $sumber,
            'detailPosisi' => $detailPosisi,
            'filteredPosisi' => $filteredPosisi,
            
        ]);
     }
     public function download()
     {
         // Panggil class export Anda, sesuaikan dengan struktur data Anda
         return Excel::download(new TemplateExport(), 'templatekandidat.xlsx');
     }

     public function getWilayahByPosisi(Request $request)
     {
         $posisi_id = $request->input('posisi_id');
         $user_id = $request->input('user_id');
     
         // Fetch the relevant wilayah based on posisi_id and user_id
         $wilayah = DetailPosisi::where('posisi_id', $posisi_id)
                     ->where('user_id', $user_id)
                     ->get();
                     
     
         return response()->json($wilayah);
     }

     public function getWilayahById(Request $request)
    {
    $wilayahIds = explode(',', $request->input('wilayah_ids'));
    
    // Fetch wilayah details based on the given IDs
    $wilayah = Wilayah::whereIn('id', $wilayahIds)->get();

    return response()->json($wilayah);
    }
   
    public function superadminstore(Request $request)
    {
        $tanggal = $request->tanggal;
        
        $loggedInUser = auth()->user();
        $loggedInUsername = $loggedInUser->nama; 
        $userid = $loggedInUser->id;
    
        $date = Carbon::parse($tanggal);
    
        $hari = $date->day;
        $bulan = $date->month;
        $tahun = $date->year;
    
        $namakandidat = $request->nama_kandidat;
        $posisi = $request->posisi_id;
        $nohp = $request->no_hp;
        $email = $request->email;
        $wilayah = $request->wilayah_id;
        $sumber = $request->sumber_id;
    
        // Check for existing candidates
        $existingKandidat = Kandidat::where('nama_kandidat', $namakandidat)
            ->where('no_hp', $nohp)
            ->first();
    
        if ($existingKandidat) {
            // Calculate the difference in months
            $existingDate = Carbon::parse($existingKandidat->tanggal);
            $monthsDifference = $existingDate->diffInMonths($request->tanggal);

            
    
            if ($monthsDifference < 3) {
                // If the existing candidate's date is less than 3 months old
                $request->session()->flash('error', 'Kandidat dengan nama dan nomor hp yang sama sudah terdaftar dalam waktu kurang dari 3 bulan.');
                return redirect(route('superadmin.kandidat.index'));
            }
        }

        $laporanperform = LaporanPerformance::where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->where('posisi_id', $posisi)
        ->first();

        $dataposisi = Posisi::find($posisi);

        $namaposisi = $dataposisi->nama_posisi;

        $monthName = Carbon::createFromDate($tahun, $bulan, 1)->format('F');

      

    
        Kandidat::create([
            'tanggal' => $tanggal,
            'hari' => $hari,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'nama_kandidat' => $namakandidat,
            'posisi' => $posisi,
            'no_hp' => $nohp,
            'email' => $email,
            'posisi_id' => $posisi,
            'sumber_id' => $sumber,
            'wilayah_id' => $wilayah,
            'status_hire' => "Belum Diproses",
            'created_by' => $loggedInUsername,
            'user_id' => $userid,
        ]);
    
        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Menambahkan Kandidat',
            'description' => 'Berhasil menambahkan kandidat ' . $request->nama_kandidat,
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
        ]);
    
        $request->session()->flash('success', 'Kandidat berhasil ditambahkan.');
    
        return redirect(route('superadmin.kandidat.index'));
    }
    

     public function superadminshow($id){
        $data = Kandidat::find($id);
        $posisi = Posisi::all();
        $wilayah = Wilayah::all();
        $sumber = Sumber::all();

        $detailPosisi = DetailPosisi::where('user_id', auth()->id())->pluck('posisi_id')->toArray(); 
        

        // Ambil data Posisi berdasarkan kode-kode yang ada di DetailPosisi
        $filteredPosisi = Posisi::whereIn('id', $detailPosisi)->get();

        $posisi = $posisi->sortBy('nama_posisi'); 
        $wilayah = $wilayah->sortBy('nama_wilayah');
        $sumber = $sumber->sortBy('nama_sumber');
        return view('superadmin.kandidat.edit',[
            'data' => $data,
            'posisi' => $posisi,
            'wilayah' => $wilayah,
            'sumber'=>$sumber,
            'detailPosisi' => $detailPosisi,
            'filteredPosisi' => $filteredPosisi,
        ]);
     }

     public function superadminupdate(Request $request, $id){

        $tanggal = $request->tanggal;

        $date = Carbon::parse($tanggal);

        $hari = $date->day;
        $bulan = $date->month;
        $tahun = $date->year;

        $namakandidat = $request->nama_kandidat;
        $posisi = $request->posisi_id;
        $nohp = $request->no_hp;
        $email = $request->email;
        $wilayah = $request->wilayah_id;
        $sumber = $request->sumber_id;

        $existingKandidat = Kandidat::where('nama_kandidat', $namakandidat)
        ->where('no_hp', $nohp)
        ->where('id', '<>', $id)
        ->first();

        if ($existingKandidat) {
            // Calculate the difference in months
            $existingDate = Carbon::parse($existingKandidat->tanggal);
            $monthsDifference = $existingDate->diffInMonths($request->tanggal);
           
            if ($monthsDifference < 3) {
                // If the existing candidate's date is less than 3 months old
                $request->session()->flash('error', 'Kandidat dengan nama dan nomor hp yang sama sudah terdaftar dalam waktu kurang dari 3 bulan.');
                return redirect(route('superadmin.kandidat.index'));
            }
            
        }
       
        $laporanperform = LaporanPerformance::where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->where('posisi_id', $posisi)
        ->first();

        $dataposisi = Posisi::find($posisi);

        $namaposisi = $dataposisi->nama_posisi;

        $monthName = Carbon::createFromDate($tahun, $bulan, 1)->format('F');

        

        $data = Kandidat::find($id);
        $data->tanggal = $tanggal;
        $data->hari = $hari;
        $data->bulan = $bulan;
        $data->tahun = $tahun;
        $data->nama_kandidat = $namakandidat;
        $data->posisi_id = $posisi;
        $data->no_hp = $nohp;
        $data->email = $email;
        $data->wilayah_id = $wilayah;
        $data -> sumber_id = $sumber;

        $data->save();

        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Update Kandidat',
            'description' => 'Berhasil mengupdate kandidat ' . $data->nama_kandidat,
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
        ]);

        $request->session()->flash('success', 'Kandidat berhasil diubah.');

        return redirect(route('superadmin.kandidat.index'));
     }

     
     public function superadmindestroy(Request $request, $id){

        $kandidat = Kandidat::find($id);
        $namakandidat = $kandidat->nama_kandidat;

        $kandidat->delete();

        LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Hapus Kandidat',
            'description' => "Berhasil menghapus kandidat $namakandidat",
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
        ]);
 
         $request->session()->flash('success', "Kandidat berhasil dihapus.");
 
         return redirect()->route('superadmin.kandidat.index');

     }




   
     public function import(Request $request)
     {
         // Validasi file upload dan tanggal
         $request->validate([
            'tanggal' => 'required|date',
            'sumber' => 'required', // validasi sumber
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
    
        // Import data menggunakan KandidatImport dengan sumber
        Excel::import(new KandidatImport($request->tanggal, $request->sumber), $request->file('file'));
        $request->session()->flash('success', "Kandidat berhasil ditambahkan.");
    } catch (\Exception $e) {
        // If an exception occurs, catch and display error message
        $request->session()->flash('error', $e->getMessage());
    }

    return redirect()->route('superadmin.kandidat.index');
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
