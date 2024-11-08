<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
use App\Models\DetailPosisi;
use App\Models\Kandidat;
use App\Models\LogTahapan;
use Illuminate\Http\Request;

class BlacklistController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function superadminindex()
     {
         $roleid = auth()->user()->role_id;
     
         // Check if the user role is 2
         if ($roleid == 2) {
             $userId = auth()->id();
             $detailPosisi = DetailPosisi::where('user_id', $userId)->get();
     
             // Get assigned position and region IDs
             $assignedPosisiIds = $detailPosisi->pluck('posisi_id')->unique()->toArray();
             $assignedWilayahIds = $detailPosisi->pluck('wilayah_id')->unique()->toArray();
     
             // Filter blacklist entries based on assigned positions and regions
             $blacklist = Blacklist::whereHas('kandidat', function ($query) use ($assignedPosisiIds, $assignedWilayahIds) {
                     $query->whereIn('posisi_id', $assignedPosisiIds)
                           ->whereIn('wilayah_id', $assignedWilayahIds);
                 })
                 ->get();
         } else {
             // For other roles, retrieve all blacklist entries
             $blacklist = Blacklist::all();
         }
     
         return view('superadmin.blacklist.index', [
             'blacklist' => $blacklist,
         ]);
     }
     


    public function superadmincreate()
{
    $roleid = auth()->user()->role_id;

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
        $kandidat = Kandidat::where(function($query) {
                $query->where('status_hire', 'Stop Proses')
                      ->orWhere('status_hire', 'Tidak Lolos');
            })
            ->whereDoesntHave('blacklist')
            ->whereIn('posisi_id', $assignedPosisiIds)
            ->whereIn('wilayah_id', $assignedWilayahIds)
            ->get();
    } else {
        // For other roles, only apply the basic filters
        $kandidat = Kandidat::where(function($query) {
                $query->where('status_hire', 'Stop Proses')
                      ->orWhere('status_hire', 'Tidak Lolos');
            })
            ->whereDoesntHave('blacklist')
            ->get();
    }

    return view('superadmin.blacklist.create', [
        'kandidat' => $kandidat,
    ]);
}

    

    public function superadminstore(Request $request){

        foreach ($request->kandidat_id as $index => $kandidatId) {
            Blacklist::create([
                'kandidat_id' => $kandidatId,
                'keterangan' => $request->keterangan[$index] ?? null,
            ]);

            $datakandidat = Kandidat::find($kandidatId);
          

            $posisiid = $datakandidat->posisi_id;
            $wilayahid = $datakandidat->wilayah_id;
            $now = now();  // Mengambil waktu saat ini
            $bulan = $now->format('m'); // Mendapatkan bulan
            $tahun = $now->format('Y'); // Mendapatkan tahun

           
        LogTahapan::create([
                'kandidat_id' => $kandidatId,
                'status_tahapan' => 'Blacklist',
                'tanggal' => $now,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'posisi_id' => $posisiid,
                'wilayah_id' => $wilayahid,
                'hasil_status' => 'Blacklist',
                'flag_tahapan' => 'Blacklist',
        ]);

        }

        $request->session()->flash('success', 'Kandidat berhasil ditambahkan dalam daftar blacklist.');

        return redirect(route('superadmin.blacklist.index'));

    }
    public function superadmindestroy(Request $request, $id)
    {
        $blacklist = Blacklist::find($id);

        $kandidatid = $blacklist->kandidat_id;
        
        $logtahapan = LogTahapan::where('kandidat_id', $kandidatid)
        ->where('status_tahapan', 'Blacklist')
        ->first();

        $logtahapan->delete();

        $blacklist->delete();

        $request->session()->flash('success', "Kandidat berhasil dihapus dalam daftar blacklist.");

        return redirect()->route('superadmin.blacklist.index');
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
