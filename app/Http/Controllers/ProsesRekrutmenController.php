<?php

namespace App\Http\Controllers;

use App\Models\DetailPosisi;
use App\Models\Kandidat;
use App\Models\LogTahapan;
use App\Models\Posisi;
use App\Models\Wilayah;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use Validator;

class ProsesRekrutmenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function jadwalindex(Request $request)
    {
        $ids = explode(',', $request->query('ids'));
        $candidates = Kandidat::whereIn('id', $ids)->get();
    
        return view('superadmin.belumproses.penjadwalan',[
            'candidates' => $candidates
        ]);
    }


    public function jadwalstore(Request $request){
        
      
        $request->validate([
            'status' => 'required',
            'date' => 'required|date',
        ]);
    
        // Ambil input status dan tanggal yang akan diterapkan pada semua kandidat
        $statusTahapan = $request->status;
        $tanggal = $request->date;
        $tanggalHari = date('d', strtotime($tanggal));
        $tanggalBulan = date('m', strtotime($tanggal));
        $tanggalTahun = date('Y', strtotime($tanggal));
    
        // Proses setiap kandidat menggunakan ID yang dikirim
        foreach ($request->candidates as $kandidatId) {
            $datakandidat = Kandidat::find($kandidatId);
            $posisiid = $datakandidat->posisi_id;
            $wilayahid = $datakandidat->wilayah_id;
            $namakandidat = $datakandidat->nama_kandidat;
    
            // Cek apakah sudah ada jadwal untuk kandidat ini

    
            // Update status hire kandidat
            $datakandidat->status_hire = "$statusTahapan Sudah Dijadwalkan";
            $datakandidat->save();
    
            // Simpan log tahapan baru
            LogTahapan::create([
                'kandidat_id' => $kandidatId,
                'status_tahapan' => $statusTahapan,
                'tanggal' => $tanggal,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $posisiid,
                'wilayah_id' => $wilayahid,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Belum Proses',
                'requester' => Auth::user()->nama,
                'waktu' => now(),
            ]);
        }
    
        $request->session()->flash('success', 'Penjadwalan kandidat berhasil dibuat.');
        return redirect(route('superadmin.belumproses.index'));
    }


    public function showjadwal($id){
      
        
        $data = LogTahapan::where('kandidat_id', $id)->get();
        $kandidat = Kandidat::find($id);
        $nama = $kandidat->nama_kandidat;

        return view('superadmin.belumproses.edit',[
            'data' => $data,
            'nama' => $nama,
        ]);
     
    }
    
    public function updateStatus(Request $request)
    {

        $roleid = auth()->user()->role_id;
        $item = LogTahapan::find($request->id);
        $statustahapan = $item->status_tahapan;
        $kandidatid = $item->kandidat_id;
        $kandidat = Kandidat::find($kandidatid);
        
        if ($item) {

            if($roleid === 3) {

          
                if($request->status === 'Tidak Hadir'){
                    $item->hasil_status = 'Tidak Hadir Belum Rescheduled';
                    $item->save();
        }else {
            $item->hasil_status = $request->status;
            $item->save();
        }

            $status = $request->status;

            if($status === 'Lolos'){
                $kandidat->status_hire = $statustahapan;
                $kandidat->save();
            }

            if($status === 'Selesai Proses'){
                $kandidat->status_hire = 'PKM Selesai';
                $kandidat->save();
            }

            if($roleid !== 1 && $roleid !== 2) {
                if($status === 'Tidak Hadir') {
                    $item->flag_kehadiran = 'Tidak Hadir';
                    $item->save();
                }
            }
        

            if($status === 'Tidak Lolos'){
                $kandidat->status_hire = $status;
                $kandidat -> save();
            }

            if($status === 'Simpan Kandidat') {
                $kandidat->status_hire = $status;
                $kandidat->save();
            }

            if($status === 'Stop Proses') {
                $kandidat->status_hire = $status;
                $kandidat->save();
            }
    
            return response()->json(['success' => true, 'message' => 'Status berhasil diubah.']);

        }
            else {
                
            $item->hasil_status = $request->status;
            $item->save();

            $status = $request->status;

            if($status === 'Lolos'){
                $kandidat->status_hire = $statustahapan;
                $kandidat->save();
            }

            if($status === 'Selesai Proses'){
                $kandidat->status_hire = 'PKM Selesai';
                $kandidat->save();
            }

            if($roleid !== 1 && $roleid !== 2) {
                if($status === 'Tidak Hadir') {
                    $item->flag_kehadiran = 'Tidak Hadir';
                    $item->save();
                }
            }
        

            if($status === 'Tidak Lolos'){
                $kandidat->status_hire = $status;
                $kandidat -> save();
            }

            if($status === 'Simpan Kandidat') {
                $kandidat->status_hire = $status;
                $kandidat->save();
            }

            if($status === 'Stop Proses') {
                $kandidat->status_hire = $status;
                $kandidat->save();
            }
    
            return response()->json(['success' => true, 'message' => 'Status berhasil diubah.']);
            }
        }
    
        return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan.']);
    }

    public function createLogTahapan(Request $request)
    {
        $item = LogTahapan::find($request->id);
    
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Log tahapan tidak ditemukan.']);
        }
    
        $tanggallama = Carbon::parse($item->tanggal);
        $tanggalbaru = Carbon::parse($request->input('tanggal'));
    
        // Validasi: tanggal baru tidak boleh sama atau lebih kecil dari tanggal lama
        if ($tanggalbaru->equalTo($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh sama dengan tanggal lama.']);
        }
    
        if ($tanggalbaru->lessThan($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh kurang dari tanggal lama.']);
        }
    
        // Update hasil status menjadi 'Tidak Hadir'
        $item->hasil_status = 'Tidak Hadir';
        $item->flag_schedule = 'Rescheduled';
        $item->save();
    
        $kandidatId = $item->kandidat_id;
        $tanggalBulan = $tanggalbaru->month;
        $tanggalTahun = $tanggalbaru->year;

        $statuslama = $item->status_tahapan;
    
        $kandidat = Kandidat::find($kandidatId);
        if ($kandidat) {
            LogTahapan::create([
                'kandidat_id' => $kandidat->id,
                'status_tahapan' => $statuslama,
                'tanggal' => $tanggalbaru,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $kandidat->posisi_id,
                'wilayah_id' => $kandidat->wilayah_id,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Belum Proses',
                
            ]);
    
            return response()->json(['success' => true, 'message' => 'Penjadwalan ulang berhasil dibuat.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan.']);
    }
    
     public function belumprosesindex(Request $request){

        $roleid = auth()->user()->role_id;

        if($roleid == 2){

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

            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Belum Diproses')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();

           return view('superadmin.belumproses.index',[
            'kandidat' => $kandidat,
            'posisi' => $posisi,
            'wilayah' => $wilayah,
            'selectedPosisi' => $request->filter_posisi,
            'selectedWilayah' => $request->filter_wilayah,
         
           ]);


        }else {

            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = Kandidat::query();
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Belum Diproses')->get();
            return view('superadmin.belumproses.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
              
               ]);
        }

     }

     public function belumprosesafterindex(Request $request) {
        $roleid = auth()->user()->role_id;
    
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
    
            // Initialize the query for LogTahapan
           $query = LogTahapan::query()
           ->where('flag_tahapan', 'Belum Proses')
           ->whereNot('hasil_status','Simpan Kandidat')
          ->whereHas('kandidat', function ($query) {
        $query->where('status_hire', 'Belum Diproses Sudah Dijadwalkan');
    });

    
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
    
            // Get all log tahapan that match the criteria
            $logTahapan = $query->orderBy('created_at', 'desc')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
    
            return view('superadmin.belumproses.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
    
        } else {
            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
           $query = LogTahapan::query()
           ->where('flag_tahapan', 'Belum Proses')
           ->whereNot('hasil_status','Simpan Kandidat')
    ->whereHas('kandidat', function ($query) {
        $query->where('status_hire', 'Belum Diproses Sudah Dijadwalkan');
    });

    
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $logTahapan = $query->orderBy('created_at', 'desc')->get();

            
            return view('superadmin.belumproses.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
        }
    }
    
     public function process(Request $request)
     {
         $data = $request->all();

         $checkedIds = explode(',', $data['checked_ids']);
        
         $status = $data['status'];
         
         foreach ($checkedIds as $id) {
            
             $kandidat = Kandidat::find($id);
             $posisiid = $kandidat->posisi_id;
             $wilayahid = $kandidat ->wilayah_id;

             if ($kandidat) {
                $now = now();  
                $bulan = $now->format('m'); 
                $tahun = $now->format('Y'); 
                 $kandidat->status_hire = $status;
                 $kandidat->save();

                 LogTahapan::create([
                    'kandidat_id' => $id,
                    'status_tahapan' => $status, 
                    'tanggal' => $now,  
                    'bulan' => $bulan,  
                    'tahun' => $tahun,  
                    'posisi_id' => $posisiid,
                    'wilayah_id' => $wilayahid
                ]);
             }
         }
         
         $request->session()->flash('success', 'Status kandidat berhasil diubah.');

         return redirect(route('superadmin.belumproses.index'));
     }
     

     public function psikotesindex(Request $request){

        $roleid = auth()->user()->role_id;

     

        if($roleid == 2){

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

            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Psikotes')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
            return view('superadmin.psikotes.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
           ]);


        }else {

            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = Kandidat::query();
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Psikotes')->get();
            return view('superadmin.psikotes.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
              
               ]);
        }

     }

     public function psikotesesafterindex(Request $request) {
        $roleid = auth()->user()->role_id;
    
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
    
            // Initialize the query for LogTahapan
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Psikotes')
            ->where(function ($query) {
                // Kondisi untuk hasil status Dijadwalkan dan Tidak Hadir
                $query->where('hasil_status', 'Dijadwalkan')
                    ;
            })
            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Psikotes Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Psikotes Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
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
    
            // Get all log tahapan that match the criteria
            $logTahapan = $query->orderBy('created_at', 'desc')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
    
            return view('superadmin.psikotes.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
    
        } else {
            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Psikotes')
            ->where(function ($query) {
                // Kondisi untuk hasil status Dijadwalkan dan Tidak Hadir
                $query->where('hasil_status', 'Dijadwalkan')
                   ;
            })
            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Psikotes Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Psikotes Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $logTahapan = $query->orderBy('created_at', 'desc')->get();

            
            return view('superadmin.psikotes.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
        }
    }


    public function createLogTahapanpsikotes(Request $request)
    {
        $item = LogTahapan::find($request->id);
    
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Log tahapan tidak ditemukan.']);
        }
    
        $tanggallama = Carbon::parse($item->tanggal);
        $tanggalbaru = Carbon::parse($request->input('tanggal'));
    
        // Validasi: tanggal baru tidak boleh sama atau lebih kecil dari tanggal lama
        if ($tanggalbaru->equalTo($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh sama dengan tanggal lama.']);
        }
    
        if ($tanggalbaru->lessThan($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh kurang dari tanggal lama.']);
        }
    
        // Update hasil status menjadi 'Tidak Hadir'
        $item->hasil_status = 'Tidak Hadir';
        $item->flag_schedule = 'Rescheduled';
        $item->save();
    
        $kandidatId = $item->kandidat_id;
        $tanggalBulan = $tanggalbaru->month;
        $tanggalTahun = $tanggalbaru->year;

        $statuslama = $item->status_tahapan;
    
        $kandidat = Kandidat::find($kandidatId);
        if ($kandidat) {
            LogTahapan::create([
                'kandidat_id' => $kandidat->id,
                'status_tahapan' => $statuslama,
                'tanggal' => $tanggalbaru,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $kandidat->posisi_id,
                'wilayah_id' => $kandidat->wilayah_id,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Psikotes',
                
            ]);
    
            return response()->json(['success' => true, 'message' => 'Penjadwalan ulang berhasil dibuat.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan.']);
    }
    public function jadwalindexpsikotes(Request $request)
    {
        $ids = explode(',', $request->query('ids'));
        $candidates = Kandidat::whereIn('id', $ids)->get();
    
        return view('superadmin.psikotes.penjadwalan',[
            'candidates' => $candidates
        ]);
    }


    public function jadwalstorepsikotes(Request $request){
        
        $request->validate([
            'status' => 'required',
            'date' => 'required|date',
        ]);
    
        // Ambil input status dan tanggal yang akan diterapkan pada semua kandidat
        $statusTahapan = $request->status;
        $tanggal = $request->date;
        $tanggalHari = date('d', strtotime($tanggal));
        $tanggalBulan = date('m', strtotime($tanggal));
        $tanggalTahun = date('Y', strtotime($tanggal));
    
        // Proses setiap kandidat menggunakan ID yang dikirim
        foreach ($request->candidates as $kandidatId) {
            $datakandidat = Kandidat::find($kandidatId);
            $posisiid = $datakandidat->posisi_id;
            $wilayahid = $datakandidat->wilayah_id;
            $namakandidat = $datakandidat->nama_kandidat;
    
            // Cek apakah sudah ada jadwal untuk kandidat ini
            
    
            // Update status hire kandidat
            $datakandidat->status_hire = "$statusTahapan Sudah Dijadwalkan";
            $datakandidat->save();
    
            // Simpan log tahapan baru
            LogTahapan::create([
                'kandidat_id' => $kandidatId,
                'status_tahapan' => $statusTahapan,
                'tanggal' => $tanggal,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $posisiid,
                'wilayah_id' => $wilayahid,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Psikotes',
                'requester' => Auth::user()->nama,
                'waktu' => now(),
            ]);
        }
    
        $request->session()->flash('success', 'Penjadwalan kandidat berhasil dibuat.');
        return redirect(route('superadmin.psikotes.index'));
    }


     public function psikotesprocess(Request $request)
     {
         $data = $request->all();
         
         $checkedIds = explode(',', $data['checked_ids']);
         
         $status = $data['status'];
         
        
         foreach ($checkedIds as $id) {
            
             $kandidat = Kandidat::find($id);
             $posisiid = $kandidat->posisi_id;
             $wilayahid = $kandidat ->wilayah_id;

             if ($kandidat) {
                $now = now();  // Mengambil waktu saat ini
                $bulan = $now->format('m'); // Mendapatkan bulan
                $tahun = $now->format('Y'); // Mendapatkan tahun
                 $kandidat->status_hire = $status;
                 $kandidat->save();

                LogTahapan::create([
                    'kandidat_id' => $id,
                    'status_tahapan' => $status, 
                    'tanggal' => $now,  // Menyimpan waktu lengkap
                    'bulan' => $bulan,  // Menyimpan bulan
                    'tahun' => $tahun,  // Menyimpan tahun
                    'posisi_id' => $posisiid,
                    'wilayah_id' => $wilayahid
                ]);

             }
         }
                 
         $request->session()->flash('success', 'Status kandidat berhasil diubah.');

         return redirect(route('superadmin.psikotes.index'));
     }


     public function itvhrindex(Request $request){

        $roleid = auth()->user()->role_id;

        if($roleid == 2){

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
            
            
            $query = Kandidat::query();
                    
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
             
              if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $selectedPosisiId = $request->filter_posisi;
                $query->where('posisi_id', $selectedPosisiId);
        
                
                $assignedWilayahIdsForPosisi = $detailPosisi->where('posisi_id', $selectedPosisiId)->pluck('wilayah_id')->toArray();
                $wilayah = Wilayah::whereIn('id', explode(',', implode(',', $assignedWilayahIdsForPosisi)))->get();
                
                
                if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                    $query->where('wilayah_id', $request->filter_wilayah);
                }
            } else {
                
                $wilayah = collect(); 
            }

            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Interview HR')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
            return view('superadmin.itvhr.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
           ]);


        }else {

            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = Kandidat::query();
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Interview HR')->get();
            return view('superadmin.itvhr.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
              
               ]);
        }

     }




     public function jadwalindexitvhr(Request $request)
     {
         $ids = explode(',', $request->query('ids'));
         $candidates = Kandidat::whereIn('id', $ids)->get();
     
         return view('superadmin.itvhr.penjadwalan',[
             'candidates' => $candidates
         ]);
     }
 
     public function jadwalstoreitvhr(Request $request){
        
     
        $request->validate([
            'status' => 'required',
            'date' => 'required|date',
        ]);
    
        // Ambil input status dan tanggal yang akan diterapkan pada semua kandidat
        $statusTahapan = $request->status;
        $tanggal = $request->date;
        $tanggalHari = date('d', strtotime($tanggal));
        $tanggalBulan = date('m', strtotime($tanggal));
        $tanggalTahun = date('Y', strtotime($tanggal));
    
        // Proses setiap kandidat menggunakan ID yang dikirim
        foreach ($request->candidates as $kandidatId) {
            $datakandidat = Kandidat::find($kandidatId);
            $posisiid = $datakandidat->posisi_id;
            $wilayahid = $datakandidat->wilayah_id;
            $namakandidat = $datakandidat->nama_kandidat;
    
            // Cek apakah sudah ada jadwal untuk kandidat ini
            
    
            // Update status hire kandidat
            $datakandidat->status_hire = "$statusTahapan Sudah Dijadwalkan";
            $datakandidat->save();
    
            // Simpan log tahapan baru
            LogTahapan::create([
                'kandidat_id' => $kandidatId,
                'status_tahapan' => $statusTahapan,
                'tanggal' => $tanggal,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $posisiid,
                'wilayah_id' => $wilayahid,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Interview HR',
                'requester' => Auth::user()->nama,
                'waktu' => now(),
            ]);
        }
    
        $request->session()->flash('success', 'Penjadwalan kandidat berhasil dibuat.');
        return redirect(route('superadmin.itvhr.index'));
    }


     public function itvhrprocess(Request $request)
     {
         $data = $request->all();
         $checkedIds = explode(',', $data['checked_ids']);
         $status = $data['status'];
     
         foreach ($checkedIds as $id) {
             $kandidat = Kandidat::find($id);
             $posisiid = $kandidat->posisi_id;
             $wilayahid = $kandidat ->wilayah_id;

             if ($kandidat) {
                 $now = now();  // Mengambil waktu saat ini
                 $bulan = $now->format('m'); // Mendapatkan bulan
                 $tahun = $now->format('Y'); // Mendapatkan tahun
                 $kandidat->status_hire = $status;
                 $kandidat->save();
     
                 // Create a new log for the current stage
                 LogTahapan::create([
                     'kandidat_id' => $id,
                     'status_tahapan' => $status, 
                     'tanggal' => $now,  // Menyimpan waktu lengkap
                     'bulan' => $bulan,  // Menyimpan bulan
                     'tahun' => $tahun,  // Menyimpan tahun
                     'posisi_id' => $posisiid,
                     'wilayah_id' => $wilayahid
                 ]);
     
                 // If the current status is Interview User, Training, Tandem, or Lolos,
                 // find the "Interview HR" log and update its flag_lolos to "Yes"
                 if (in_array($status, ['Interview User', 'Training', 'Tandem', 'Lolos'])) {
                     $logInterviewHR = LogTahapan::where('kandidat_id', $id)
                         ->where('status_tahapan', 'Interview HR')
                         ->first();
     
                     if ($logInterviewHR) {
                         $logInterviewHR->flag_lolos = 'Yes';
                         $logInterviewHR->save();
                     }
                 }
             }
         }
     
         $request->session()->flash('success', 'Status kandidat berhasil diubah.');
         return redirect(route('superadmin.itvhr.index'));
     }
     


     
     public function itvuserindex(Request $request){

        $roleid = auth()->user()->role_id;

     

        if($roleid == 2){

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

            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Interview User')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
            return view('superadmin.itvuser.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
           ]);


        }else {

            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = Kandidat::query();
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Interview User')->get();
            return view('superadmin.itvuser.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
              
               ]);
        }

     }

     public function itvhrafterindex(Request $request) {
        $roleid = auth()->user()->role_id;
    
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
    
            // Initialize the query for LogTahapan
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Interview HR')
            ->where(function ($query) {
                // Kondisi untuk hasil status Dijadwalkan dan Tidak Hadir
                $query->where('hasil_status', 'Dijadwalkan')
                    ;
            })
            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Psikotes Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Interview HR Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
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
    
            // Get all log tahapan that match the criteria
            $logTahapan = $query->orderBy('created_at', 'desc')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
    
            return view('superadmin.itvhr.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
    
        } else {
            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Interview HR')
            ->where(function ($query) {
                // Kondisi untuk hasil status Dijadwalkan dan Tidak Hadir
                $query->where('hasil_status', 'Dijadwalkan')
                    ;
            })
            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Interview HR Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Interview HR Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $logTahapan = $query->orderBy('created_at', 'desc')->get();

            
            return view('superadmin.itvhr.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
        }
    }


    public function createLogTahapanitvhr(Request $request)
    {
        $item = LogTahapan::find($request->id);
    
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Log tahapan tidak ditemukan.']);
        }
    
        $tanggallama = Carbon::parse($item->tanggal);
        $tanggalbaru = Carbon::parse($request->input('tanggal'));
    
        // Validasi: tanggal baru tidak boleh sama atau lebih kecil dari tanggal lama
        if ($tanggalbaru->equalTo($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh sama dengan tanggal lama.']);
        }
    
        if ($tanggalbaru->lessThan($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh kurang dari tanggal lama.']);
        }
    
        // Update hasil status menjadi 'Tidak Hadir'
        $item->hasil_status = 'Tidak Hadir';
        $item->flag_schedule = 'Rescheduled';
        $item->save();
    
        $kandidatId = $item->kandidat_id;
        $tanggalBulan = $tanggalbaru->month;
        $tanggalTahun = $tanggalbaru->year;

        $statuslama = $item->status_tahapan;
    
        $kandidat = Kandidat::find($kandidatId);
        if ($kandidat) {
            LogTahapan::create([
                'kandidat_id' => $kandidat->id,
                'status_tahapan' => $statuslama,
                'tanggal' => $tanggalbaru,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $kandidat->posisi_id,
                'wilayah_id' => $kandidat->wilayah_id,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Interview HR',
                
            ]);
    
            return response()->json(['success' => true, 'message' => 'Penjadwalan ulang berhasil dibuat.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan.']);
    }

     public function itvuserprocess(Request $request)
     {

         $data = $request->all();

         $checkedIds = explode(',', $data['checked_ids']);
         
         $status = $data['status'];
         
        
         foreach ($checkedIds as $id) {
            
             $kandidat = Kandidat::find($id);
             $posisiid = $kandidat->posisi_id;
             $wilayahid = $kandidat ->wilayah_id;

             
             if ($kandidat) {
                $now = now();  // Mengambil waktu saat ini
                $bulan = $now->format('m'); // Mendapatkan bulan
                $tahun = $now->format('Y'); // Mendapatkan tahun
                 $kandidat->status_hire = $status;
                 $kandidat->save();

                 LogTahapan::create([
                    'kandidat_id' => $id,
                    'status_tahapan' => $status, 
                    'tanggal' => $now,  // Menyimpan waktu lengkap
                    'bulan' => $bulan,  // Menyimpan bulan
                    'tahun' => $tahun,  // Menyimpan tahun
                    'posisi_id' => $posisiid,
                    'wilayah_id' => $wilayahid
                ]);
             }
         }
         
         $request->session()->flash('success', 'Status kandidat berhasil diubah.');

         return redirect(route('superadmin.itvuser.index'));
     }

     public function jadwalindexitvuser(Request $request)
     {
         $ids = explode(',', $request->query('ids'));
         $candidates = Kandidat::whereIn('id', $ids)->get();
     
         return view('superadmin.itvuser.penjadwalan',[
             'candidates' => $candidates
         ]);
     }



     public function jadwalstoreitvuser(Request $request){
        
        
        $request->validate([
            'status' => 'required',
            'date' => 'required|date',
        ]);
    
        // Ambil input status dan tanggal yang akan diterapkan pada semua kandidat
        $statusTahapan = $request->status;
        $tanggal = $request->date;
        $tanggalHari = date('d', strtotime($tanggal));
        $tanggalBulan = date('m', strtotime($tanggal));
        $tanggalTahun = date('Y', strtotime($tanggal));
    
        // Proses setiap kandidat menggunakan ID yang dikirim
        foreach ($request->candidates as $kandidatId) {
            $datakandidat = Kandidat::find($kandidatId);
            $posisiid = $datakandidat->posisi_id;
            $wilayahid = $datakandidat->wilayah_id;
            $namakandidat = $datakandidat->nama_kandidat;
    
            // Cek apakah sudah ada jadwal untuk kandidat ini
            
    
            // Update status hire kandidat
            $datakandidat->status_hire = "$statusTahapan Sudah Dijadwalkan";
            $datakandidat->save();
    
            // Simpan log tahapan baru
            LogTahapan::create([
                'kandidat_id' => $kandidatId,
                'status_tahapan' => $statusTahapan,
                'tanggal' => $tanggal,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $posisiid,
                'wilayah_id' => $wilayahid,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Interview User',
                'requester' => Auth::user()->nama,
                'waktu' => now(),
            ]);
        }
    
        $request->session()->flash('success', 'Penjadwalan kandidat berhasil dibuat.');
        return redirect(route('superadmin.itvuser.index'));
    }


    public function itvuserafterindex(Request $request) {
        $roleid = auth()->user()->role_id;
    
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
    
            // Initialize the query for LogTahapan
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Interview User')
            ->where(function ($query) {
                // Kondisi untuk hasil status Dijadwalkan dan Tidak Hadir
                $query->where('hasil_status', 'Dijadwalkan')
                    ;
            })
            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Interview User Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Interview User Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
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
    
            // Get all log tahapan that match the criteria
            $logTahapan = $query->orderBy('created_at', 'desc')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
    
            return view('superadmin.itvuser.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
    
        } else {
            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');

          $query = LogTahapan::query()
    ->where('status_tahapan', 'Interview User')
    ->where(function ($query) {
        // Kondisi untuk hasil status Dijadwalkan dan Tidak Hadir
        $query->where('hasil_status', 'Dijadwalkan')
            ;
    })
    ->where(function ($query) {
        // Kondisi untuk status_hire yang harus Interview User Sudah Dijadwalkan
        $query->whereHas('kandidat', function ($query) {
            $query->where('status_hire', 'Interview User Sudah Dijadwalkan');
        });
    })
    ->whereNot('hasil_status', 'Simpan Kandidat');

    
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $logTahapan = $query->orderBy('created_at', 'desc')->get();

            
            return view('superadmin.itvuser.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
        }
    }



    public function createLogTahapanitvuser(Request $request)
    {
        $item = LogTahapan::find($request->id);
    
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Log tahapan tidak ditemukan.']);
        }
    
        $tanggallama = Carbon::parse($item->tanggal);
        $tanggalbaru = Carbon::parse($request->input('tanggal'));
    
        // Validasi: tanggal baru tidak boleh sama atau lebih kecil dari tanggal lama
        if ($tanggalbaru->equalTo($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh sama dengan tanggal lama.']);
        }
    
        if ($tanggalbaru->lessThan($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh kurang dari tanggal lama.']);
        }
    
        // Update hasil status menjadi 'Tidak Hadir'
        $item->hasil_status = 'Tidak Hadir';
        $item->flag_schedule = 'Rescheduled';
        $item->save();
    
        $kandidatId = $item->kandidat_id;
        $tanggalBulan = $tanggalbaru->month;
        $tanggalTahun = $tanggalbaru->year;

        $statuslama = $item->status_tahapan;
    
        $kandidat = Kandidat::find($kandidatId);
        if ($kandidat) {
            LogTahapan::create([
                'kandidat_id' => $kandidat->id,
                'status_tahapan' => $statuslama,
                'tanggal' => $tanggalbaru,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $kandidat->posisi_id,
                'wilayah_id' => $kandidat->wilayah_id,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Interview User',
                
            ]);
    
            return response()->json(['success' => true, 'message' => 'Penjadwalan ulang berhasil dibuat.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan.']);
    }




    public function itvuserindexdua(Request $request){

        $roleid = auth()->user()->role_id;

     

        if($roleid == 2){

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

            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Interview User 2')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
            return view('superadmin.itvuserdua.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
           ]);


        }else {

            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = Kandidat::query();
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Interview User 2')->get();
            return view('superadmin.itvuserdua.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
              
               ]);
        }

     }


     public function jadwalindexitvuserdua(Request $request)
     {
         $ids = explode(',', $request->query('ids'));
         $candidates = Kandidat::whereIn('id', $ids)->get();
     
         return view('superadmin.itvuserdua.penjadwalan',[
             'candidates' => $candidates
         ]);
     }


     public function jadwalstoreitvuserdua(Request $request){
        
      
        $request->validate([
            'status' => 'required',
            'date' => 'required|date',
        ]);
    
        // Ambil input status dan tanggal yang akan diterapkan pada semua kandidat
        $statusTahapan = $request->status;
        $tanggal = $request->date;
        $tanggalHari = date('d', strtotime($tanggal));
        $tanggalBulan = date('m', strtotime($tanggal));
        $tanggalTahun = date('Y', strtotime($tanggal));
    
        // Proses setiap kandidat menggunakan ID yang dikirim
        foreach ($request->candidates as $kandidatId) {
            $datakandidat = Kandidat::find($kandidatId);
            $posisiid = $datakandidat->posisi_id;
            $wilayahid = $datakandidat->wilayah_id;
            $namakandidat = $datakandidat->nama_kandidat;
    
            // Cek apakah sudah ada jadwal untuk kandidat ini
           
    
            // Update status hire kandidat
            $datakandidat->status_hire = "$statusTahapan Sudah Dijadwalkan";
            $datakandidat->save();
    
            // Simpan log tahapan baru
            LogTahapan::create([
                'kandidat_id' => $kandidatId,
                'status_tahapan' => $statusTahapan,
                'tanggal' => $tanggal,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $posisiid,
                'wilayah_id' => $wilayahid,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Interview User 2',
                'requester' => Auth::user()->nama,
                'waktu' => now(),
            ]);
        }
    
        $request->session()->flash('success', 'Penjadwalan kandidat berhasil dibuat.');
        return redirect(route('superadmin.itvuserdua.index'));
    }



    public function itvuserafterindexdua(Request $request) {
        $roleid = auth()->user()->role_id;
    
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
    
            // Initialize the query for LogTahapan
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Interview User 2')
            ->where(function ($query) {
                // Kondisi untuk hasil status Dijadwalkan dan Tidak Hadir
                $query->where('hasil_status', 'Dijadwalkan')
                    ;
            })
            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Interview User 2 Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Interview User 2 Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
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
    
            // Get all log tahapan that match the criteria
            $logTahapan = $query->orderBy('created_at', 'desc')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
    
            return view('superadmin.itvuserdua.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
    
        } else {
            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Interview User 2')
            ->where(function ($query) {
                // Kondisi untuk hasil status Dijadwalkan dan Tidak Hadir
                $query->where('hasil_status', 'Dijadwalkan')
                    ;
            })
            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Interview User 2 Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Interview User 2 Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $logTahapan = $query->orderBy('created_at', 'desc')->get();

            
            return view('superadmin.itvuserdua.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
        }
    }


    public function createLogTahapanitvuserdua(Request $request)
    {
        $item = LogTahapan::find($request->id);
    
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Log tahapan tidak ditemukan.']);
        }
    
        $tanggallama = Carbon::parse($item->tanggal);
        $tanggalbaru = Carbon::parse($request->input('tanggal'));
    
        // Validasi: tanggal baru tidak boleh sama atau lebih kecil dari tanggal lama
        if ($tanggalbaru->equalTo($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh sama dengan tanggal lama.']);
        }
    
        if ($tanggalbaru->lessThan($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh kurang dari tanggal lama.']);
        }
    
        // Update hasil status menjadi 'Tidak Hadir'
        $item->hasil_status = 'Tidak Hadir';
        $item->flag_schedule = 'Rescheduled';
        $item->save();
    
        $kandidatId = $item->kandidat_id;
        $tanggalBulan = $tanggalbaru->month;
        $tanggalTahun = $tanggalbaru->year;

        $statuslama = $item->status_tahapan;
    
        $kandidat = Kandidat::find($kandidatId);
        if ($kandidat) {
            LogTahapan::create([
                'kandidat_id' => $kandidat->id,
                'status_tahapan' => $statuslama,
                'tanggal' => $tanggalbaru,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $kandidat->posisi_id,
                'wilayah_id' => $kandidat->wilayah_id,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Interview User 2',
                
            ]);
    
            return response()->json(['success' => true, 'message' => 'Penjadwalan ulang berhasil dibuat.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan.']);
    }



    public function itvuserindextiga(Request $request){

        $roleid = auth()->user()->role_id;

     

        if($roleid == 2){

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

            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Interview User 3')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
            return view('superadmin.itvusertiga.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
           ]);


        }else {

            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = Kandidat::query();
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Interview User 3')->get();
            return view('superadmin.itvusertiga.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
              
               ]);
        }

     }

     public function jadwalindexitvusertiga(Request $request)
     {
         $ids = explode(',', $request->query('ids'));
         $candidates = Kandidat::whereIn('id', $ids)->get();
     
         return view('superadmin.itvusertiga.penjadwalan',[
             'candidates' => $candidates
         ]);
     }


     public function jadwalstoreitvusertiga(Request $request){
        
        
        $request->validate([
            'status' => 'required',
            'date' => 'required|date',
        ]);
    
        // Ambil input status dan tanggal yang akan diterapkan pada semua kandidat
        $statusTahapan = $request->status;
        $tanggal = $request->date;
        $tanggalHari = date('d', strtotime($tanggal));
        $tanggalBulan = date('m', strtotime($tanggal));
        $tanggalTahun = date('Y', strtotime($tanggal));
    
        // Proses setiap kandidat menggunakan ID yang dikirim
        foreach ($request->candidates as $kandidatId) {
            $datakandidat = Kandidat::find($kandidatId);
            $posisiid = $datakandidat->posisi_id;
            $wilayahid = $datakandidat->wilayah_id;
            $namakandidat = $datakandidat->nama_kandidat;
    
            // Cek apakah sudah ada jadwal untuk kandidat ini

    
            // Update status hire kandidat
            $datakandidat->status_hire = "$statusTahapan Sudah Dijadwalkan";
            $datakandidat->save();
    
            // Simpan log tahapan baru
            LogTahapan::create([
                'kandidat_id' => $kandidatId,
                'status_tahapan' => $statusTahapan,
                'tanggal' => $tanggal,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $posisiid,
                'wilayah_id' => $wilayahid,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Interview User 3',
                'requester' => Auth::user()->nama,
                'waktu' => now(),
            ]);
        }
    
        $request->session()->flash('success', 'Penjadwalan kandidat berhasil dibuat.');
        return redirect(route('superadmin.itvusertiga.index'));
    }


    public function itvuserafterindextiga(Request $request) {
        $roleid = auth()->user()->role_id;
    
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
    
            // Initialize the query for LogTahapan
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Interview User 3')
            ->where(function ($query) {
                // Kondisi untuk hasil status Dijadwalkan dan Tidak Hadir
                $query->where('hasil_status', 'Dijadwalkan')
                    ;
            })
            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Interview User 3 Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Interview User 3 Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
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
    
            // Get all log tahapan that match the criteria
            $logTahapan = $query->orderBy('created_at', 'desc')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
    
            return view('superadmin.itvusertiga.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
    
        } else {
            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Interview User 3')
            ->where(function ($query) {
                // Kondisi untuk hasil status Dijadwalkan dan Tidak Hadir
                $query->where('hasil_status', 'Dijadwalkan')
                    ;
            })
            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Interview User 3 Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Interview User 3 Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $logTahapan = $query->orderBy('created_at', 'desc')->get();

            
            return view('superadmin.itvusertiga.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
        }
    }


    public function createLogTahapanitvusertiga(Request $request)
    {
        $item = LogTahapan::find($request->id);
    
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Log tahapan tidak ditemukan.']);
        }
    
        $tanggallama = Carbon::parse($item->tanggal);
        $tanggalbaru = Carbon::parse($request->input('tanggal'));
    
        // Validasi: tanggal baru tidak boleh sama atau lebih kecil dari tanggal lama
        if ($tanggalbaru->equalTo($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh sama dengan tanggal lama.']);
        }
    
        if ($tanggalbaru->lessThan($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh kurang dari tanggal lama.']);
        }
    
        // Update hasil status menjadi 'Tidak Hadir'
        $item->hasil_status = 'Tidak Hadir';
        $item->flag_schedule = 'Rescheduled';
        $item->save();
    
        $kandidatId = $item->kandidat_id;
        $tanggalBulan = $tanggalbaru->month;
        $tanggalTahun = $tanggalbaru->year;

        $statuslama = $item->status_tahapan;
    
        $kandidat = Kandidat::find($kandidatId);
        if ($kandidat) {
            LogTahapan::create([
                'kandidat_id' => $kandidat->id,
                'status_tahapan' => $statuslama,
                'tanggal' => $tanggalbaru,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $kandidat->posisi_id,
                'wilayah_id' => $kandidat->wilayah_id,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Interview User 3',
                
            ]);
    
            return response()->json(['success' => true, 'message' => 'Penjadwalan ulang berhasil dibuat.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan.']);
    }



    public function jadwalindextraining(Request $request)
    {
        $ids = explode(',', $request->query('ids'));
        $candidates = Kandidat::whereIn('id', $ids)->get();
    
        return view('superadmin.training.penjadwalan',[
            'candidates' => $candidates
        ]);
    }




    public function jadwalstoretraining(Request $request){
        
      
        $request->validate([
            'status' => 'required',
            'date' => 'required|date',
        ]);
    
        // Ambil input status dan tanggal yang akan diterapkan pada semua kandidat
        $statusTahapan = $request->status;
        $tanggal = $request->date;
        $tanggalHari = date('d', strtotime($tanggal));
        $tanggalBulan = date('m', strtotime($tanggal));
        $tanggalTahun = date('Y', strtotime($tanggal));
    
        // Proses setiap kandidat menggunakan ID yang dikirim
        foreach ($request->candidates as $kandidatId) {
            $datakandidat = Kandidat::find($kandidatId);
            $posisiid = $datakandidat->posisi_id;
            $wilayahid = $datakandidat->wilayah_id;
            $namakandidat = $datakandidat->nama_kandidat;
    
            // Cek apakah sudah ada jadwal untuk kandidat ini
           
    
            // Update status hire kandidat
            $datakandidat->status_hire = "$statusTahapan Sudah Dijadwalkan";
            $datakandidat->save();
    
            // Simpan log tahapan baru
            LogTahapan::create([
                'kandidat_id' => $kandidatId,
                'status_tahapan' => $statusTahapan,
                'tanggal' => $tanggal,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $posisiid,
                'wilayah_id' => $wilayahid,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Training',
                'requester' => Auth::user()->nama,
                'waktu' => now(),
            ]);
        }
    
        $request->session()->flash('success', 'Penjadwalan kandidat berhasil dibuat.');
        return redirect(route('superadmin.training.index'));
    }




    public function trainingafterindex(Request $request) {
        $roleid = auth()->user()->role_id;
    
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
    
            // Initialize the query for LogTahapan
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Training')
            ->where(function ($query) {
                // Kondisi untuk hasil status Dijadwalkan dan Tidak Hadir
                $query->where('hasil_status', 'Dijadwalkan')
                    ->orWhere('hasil_status', 'Tidak Hadir Belum Rescheduled');
            })
            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Training Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Training Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
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
    
            // Get all log tahapan that match the criteria
            $logTahapan = $query->orderBy('created_at', 'desc')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
    
            return view('superadmin.training.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
    
        } else {
            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Training')
            ->where(function ($query) {
                // Kondisi untuk hasil status Dijadwalkan dan Tidak Hadir
                $query->where('hasil_status', 'Dijadwalkan')
                ->orWhere('hasil_status', 'Tidak Hadir Belum Rescheduled'); ;
            })
            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Training Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Training Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $logTahapan = $query->orderBy('created_at', 'desc')->get();

            
            return view('superadmin.training.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
        }
    }

    public function createJadwalTandem(Request $request)
    {

        $item = LogTahapan::find($request->id);
        $tanggalbaru = Carbon::parse($request->input('tanggal'));
        $tanggalBulan = $tanggalbaru->month;
        $tanggalTahun = $tanggalbaru->year;

        $tgltraining = Carbon::parse($item->tanggal);
        $tanggalnew = Carbon::parse($request->input('tanggal'));
    
        if ($tanggalnew->equalTo($tgltraining)) {
            return response()->json(['success' => false, 'message' => 'Tanggal tandem tidak boleh sama dengan tanggal training.']);
        }
    
        $kandidatId = $item->kandidat_id;
        $datakandidat = Kandidat::find($kandidatId);
        $posisiid = $datakandidat->posisi_id;
        $wilayahid = $datakandidat->wilayah_id;

        $item -> hasil_status = "Lolos";
        $item -> save();

        $datakandidat->status_hire = "Tandem Sudah Dijadwalkan";
        $datakandidat -> save();

        LogTahapan::create([
            'kandidat_id' => $kandidatId,
            'status_tahapan' => "Tandem",
            'tanggal' => $tanggalbaru,
            'bulan' => $tanggalBulan,
            'tahun' => $tanggalTahun,
            'posisi_id' => $posisiid,
            'wilayah_id' => $wilayahid,
            'hasil_status' => 'Dijadwalkan',
            'flag_tahapan' => 'Tandem',
        ]);

    
    return response()->json(['success' => true, 'message' => 'Jadwal tandem berhasil dibuat.']);

    }

    public function createLogTahapantraining(Request $request)
    {
        $item = LogTahapan::find($request->id);
    
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Log tahapan tidak ditemukan.']);
        }
    
        $tanggallama = Carbon::parse($item->tanggal);
        $tanggalbaru = Carbon::parse($request->input('tanggal'));
    
        // Validasi: tanggal baru tidak boleh sama atau lebih kecil dari tanggal lama
        if ($tanggalbaru->equalTo($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh sama dengan tanggal lama.']);
        }
    
        if ($tanggalbaru->lessThan($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh kurang dari tanggal lama.']);
        }
    
        // Update hasil status menjadi 'Tidak Hadir'
        $item->hasil_status = 'Tidak Hadir';
        $item->flag_schedule = 'Rescheduled';
        $item->save();
    
        $kandidatId = $item->kandidat_id;
        $tanggalBulan = $tanggalbaru->month;
        $tanggalTahun = $tanggalbaru->year;

        $statuslama = $item->status_tahapan;
    
        $kandidat = Kandidat::find($kandidatId);
        if ($kandidat) {
            LogTahapan::create([
                'kandidat_id' => $kandidat->id,
                'status_tahapan' => $statuslama,
                'tanggal' => $tanggalbaru,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $kandidat->posisi_id,
                'wilayah_id' => $kandidat->wilayah_id,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Training',
                
            ]);
    
            return response()->json(['success' => true, 'message' => 'Penjadwalan ulang berhasil dibuat.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan.']);
    }

    public function createLogTahapantandem(Request $request)
    {
        $item = LogTahapan::find($request->id);
    
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Log tahapan tidak ditemukan.']);
        }
    
        $tanggallama = Carbon::parse($item->tanggal);
        $tanggalbaru = Carbon::parse($request->input('tanggal'));
    
        // Validasi: tanggal baru tidak boleh sama atau lebih kecil dari tanggal lama
        if ($tanggalbaru->equalTo($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh sama dengan tanggal lama.']);
        }
    
        if ($tanggalbaru->lessThan($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh kurang dari tanggal lama.']);
        }
    
        // Update hasil status menjadi 'Tidak Hadir'
        $item->hasil_status = 'Tidak Hadir';
        $item->flag_schedule = 'Rescheduled';
        $item->save();
    
        $kandidatId = $item->kandidat_id;
        $tanggalBulan = $tanggalbaru->month;
        $tanggalTahun = $tanggalbaru->year;

        $statuslama = $item->status_tahapan;
    
        $kandidat = Kandidat::find($kandidatId);
        if ($kandidat) {
            LogTahapan::create([
                'kandidat_id' => $kandidat->id,
                'status_tahapan' => $statuslama,
                'tanggal' => $tanggalbaru,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $kandidat->posisi_id,
                'wilayah_id' => $kandidat->wilayah_id,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Tandem',
                
            ]);
    
            return response()->json(['success' => true, 'message' => 'Penjadwalan ulang berhasil dibuat.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan.']);
    }
    public function createLogTahapansave(Request $request)
    {
        $item = LogTahapan::find($request->id);
    
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Log tahapan tidak ditemukan.']);
        }
    
        $tanggallama = Carbon::parse($item->tanggal);
        $tanggalbaru = Carbon::parse($request->input('tanggal'));
    
        // Validasi: tanggal baru tidak boleh sama atau lebih kecil dari tanggal lama
        if ($tanggalbaru->equalTo($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh sama dengan tanggal lama.']);
        }
    
        if ($tanggalbaru->lessThan($tanggallama)) {
            return response()->json(['success' => false, 'message' => 'Tanggal baru tidak boleh kurang dari tanggal lama.']);
        }
    
        // Update hasil status menjadi 'Tidak Hadir'
        $item->hasil_status = 'Tidak Hadir';
        $item->flag_schedule = 'Rescheduled';
        $item->save();
    
        $kandidatId = $item->kandidat_id;
        $tanggalBulan = $tanggalbaru->month;
        $tanggalTahun = $tanggalbaru->year;

        $statuslama = $item->status_tahapan;
    
        $kandidat = Kandidat::find($kandidatId);
        if ($kandidat) {

            LogTahapan::create([
                'kandidat_id' => $kandidat->id,
                'status_tahapan' => $statuslama,
                'tanggal' => $tanggalbaru,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $kandidat->posisi_id,
                'wilayah_id' => $kandidat->wilayah_id,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Simpan Kandidat',
            ]);
    
            return response()->json(['success' => true, 'message' => 'Penjadwalan ulang berhasil dibuat.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan.']);
    }
    public function updateStatustandem(Request $request)
    {
        $item = LogTahapan::find($request->id);
        $statustahapan = $item->status_tahapan;
        $kandidatid = $item->kandidat_id;
        $kandidat = Kandidat::find($kandidatid);
        
        if ($item) {

            $item->hasil_status = $request->status;
            $item->save();

            $status = $request->status;

            if($status === 'Lolos'){
                $kandidat->status_hire = $statustahapan;
                $kandidat->save();
            }

            if($status === 'Tidak Lolos'){
                $kandidat->status_hire = $status;
                $kandidat -> save();
            }

            if($status === 'Simpan Kandidat') {
                $kandidat->status_hire = $status;
                $kandidat->save();
            }

            if($status === 'Stop Proses') {
                $kandidat->status_hire = $status;
                $kandidat->save();
            }
    
            return response()->json(['success' => true, 'message' => 'Status berhasil diubah.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan.']);
    }
    
    public function updateStatustraining(Request $request)
    {
        $item = LogTahapan::find($request->id);
        $statustahapan = $item->status_tahapan;
        $kandidatid = $item->kandidat_id;
        $kandidat = Kandidat::find($kandidatid);
        
        if ($item) {

            $item->hasil_status = $request->status;
            $item->save();

            $status = $request->status;

            if($status === 'Lolos'){
                $kandidat->status_hire = $statustahapan;
                $kandidat->save();
            }

            if($status === 'Tidak Lolos'){
                $kandidat->status_hire = $status;
                $kandidat -> save();
            }

            if($status === 'Simpan Kandidat') {
                $kandidat->status_hire = $status;
                $kandidat->save();
            }

            if($status === 'Stop Proses') {
                $kandidat->status_hire = $status;
                $kandidat->save();
            }
    
            return response()->json(['success' => true, 'message' => 'Status berhasil diubah.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan.']);
    }
     public function tandemindex(Request $request){

        $roleid = auth()->user()->role_id;

        if($roleid == 2 || $roleid == 3){

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

            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Tandem')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
            return view('superadmin.tandem.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
           ]);


        }else {

            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = Kandidat::query();
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Tandem')->get();
            return view('superadmin.tandem.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
              
               ]);
        }

     }



     public function jadwalindextandem(Request $request)
    {
        $ids = explode(',', $request->query('ids'));
        $candidates = Kandidat::whereIn('id', $ids)->get();
    
        return view('superadmin.tandem.penjadwalan',[
            'candidates' => $candidates
        ]);
    }


    public function jadwalstoretandem(Request $request){
        
        
        $request->validate([
            'status' => 'required',
            'date' => 'required|date',
        ]);
    
        // Ambil input status dan tanggal yang akan diterapkan pada semua kandidat
        $statusTahapan = $request->status;
        $tanggal = $request->date;
        $tanggalHari = date('d', strtotime($tanggal));
        $tanggalBulan = date('m', strtotime($tanggal));
        $tanggalTahun = date('Y', strtotime($tanggal));
    
        // Proses setiap kandidat menggunakan ID yang dikirim
        foreach ($request->candidates as $kandidatId) {
            $datakandidat = Kandidat::find($kandidatId);
            $posisiid = $datakandidat->posisi_id;
            $wilayahid = $datakandidat->wilayah_id;
            $namakandidat = $datakandidat->nama_kandidat;
    
            // Cek apakah sudah ada jadwal untuk kandidat ini
           
    
            // Update status hire kandidat
            $datakandidat->status_hire = "$statusTahapan Sudah Dijadwalkan";
            $datakandidat->save();
    
            // Simpan log tahapan baru
            LogTahapan::create([
                'kandidat_id' => $kandidatId,
                'status_tahapan' => $statusTahapan,
                'tanggal' => $tanggal,
                'bulan' => $tanggalBulan,
                'tahun' => $tanggalTahun,
                'posisi_id' => $posisiid,
                'wilayah_id' => $wilayahid,
                'hasil_status' => 'Dijadwalkan',
                'flag_tahapan' => 'Tandem',
                'requester' => Auth::user()->nama,
                'waktu' => now(),
            ]);
        }
    
        $request->session()->flash('success', 'Penjadwalan kandidat berhasil dibuat.');
        return redirect(route('superadmin.tandem.index'));
    }



    public function tandemafterindex(Request $request) {
        $roleid = auth()->user()->role_id;
    
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
    
            // Initialize the query for LogTahapan
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Tandem')
            ->where(function ($query) {
                // Kondisi untuk hasil status Dijadwalkan dan Tidak Hadir
                $query->where('hasil_status', 'Dijadwalkan')
                    ;
            })
            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Tandem Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Tandem Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
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
    
            // Get all log tahapan that match the criteria
            $logTahapan = $query->orderBy('created_at', 'desc')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
    
            return view('superadmin.tandem.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
    
        } else {
            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Tandem')
            ->where(function ($query) {
                // Kondisi untuk hasil status Dijadwalkan dan Tidak Hadir
                $query->where('hasil_status', 'Dijadwalkan')
                    ;
            })
            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Tandem Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Tandem Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $logTahapan = $query->orderBy('created_at', 'desc')->get();

            
            return view('superadmin.tandem.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
        }
    }



     public function tandemprocess(Request $request)
     {

         $data = $request->all();

         $checkedIds = explode(',', $data['checked_ids']);
         
         $status = $data['status'];
         
        
         foreach ($checkedIds as $id) {
            
             $kandidat = Kandidat::find($id);
             $posisiid = $kandidat->posisi_id;
             $wilayahid = $kandidat ->wilayah_id;

             
             if ($kandidat) {
                $now = now();  // Mengambil waktu saat ini
                $bulan = $now->format('m'); // Mendapatkan bulan
                $tahun = $now->format('Y'); // Mendapatkan tahun
                 $kandidat->status_hire = $status;
                 $kandidat->save();

                 LogTahapan::create([
                    'kandidat_id' => $id,
                    'status_tahapan' => $status, 
                    'tanggal' => $now,  // Menyimpan waktu lengkap
                    'bulan' => $bulan,  // Menyimpan bulan
                    'tahun' => $tahun,  // Menyimpan tahun
                    'posisi_id' => $posisiid,
                    'wilayah_id' => $wilayahid
                    
                ]);
             }
         }
         
         $request->session()->flash('success', 'Status kandidat berhasil diubah.');

         return redirect(route('superadmin.tandem.index'));
     }


     public function saveindex(Request $request){

        $roleid = auth()->user()->role_id;
        

        if($roleid == 2){

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

            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Simpan Kandidat')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
            return view('superadmin.save.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
           ]);


        }else {

            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = Kandidat::query();
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Simpan Kandidat')->get();
            return view('superadmin.save.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
              
               ]);
        }

     }
     
     public function mundurkanStatus(Request $request)
     {  
        
        $kandidatid = $request -> kandidat_id;
        $statusmundur = $request->status;

        $datalogtahapan = LogTahapan::where('status_tahapan','Psikotes')
        ->where('kandidat_id', $kandidatid)
        ->first();

        $datalogtahapan->delete();

        $datakandidat = Kandidat::find($kandidatid);
        $datakandidat -> status_hire = $statusmundur;
        $datakandidat->save();

        $request->session()->flash('success', 'Status kandidat berhasil dimundurkan.');

        return redirect(route('superadmin.psikotes.index'));
     }


     public function itvhrmundurkanStatus(Request $request){

        $kandidatid = $request -> kandidat_id;
        $statusmundur = $request->status;

        $datalogtahapan = LogTahapan::where('status_tahapan','Interview HR')
        ->where('kandidat_id', $kandidatid)
        ->first();

        $datalogtahapan->delete();

        $datakandidat = Kandidat::find($kandidatid);
        $datakandidat -> status_hire = $statusmundur;
        $datakandidat->save();

        $request->session()->flash('success', 'Status kandidat berhasil dimundurkan.');

        return redirect(route('superadmin.itvhr.index'));
        
     }

     public function itvusermundurkanStatus(Request $request) {

        $kandidatid = $request->kandidat_id;
        $statusmundur = $request->status;

        $datalogtahapan = LogTahapan::where('status_tahapan', 'Interview User' )
        ->where('kandidat_id', $kandidatid)
        ->first();

        $datalogtahapan -> delete();

        $datakandidat = Kandidat::find($kandidatid);
        $datakandidat -> status_hire = $statusmundur;
        $datakandidat->save();

        $request->session()->flash('success', 'Status kandidat berhasil dimundurkan.');

        return redirect(route('superadmin.itvuser.index'));
     }

     public function trainingmundurkanStatus(Request $request){

        $kandidatid = $request->kandidat_id;
        $statusmundur = $request -> status;
        
        $datalogtahapan = LogTahapan::where('status_tahapan', 'Training')
        ->where('kandidat_id', $kandidatid)
        ->first();

        $datalogtahapan ->delete();

        $datakandidat = Kandidat::find($kandidatid);
        $datakandidat -> status_hire = $statusmundur;
        $datakandidat->save();

        $request->session()->flash('success', 'Status kandidat berhasil dimundurkan.');

        return redirect(route('superadmin.training.index'));

     }

     public function tandemmundurkanStatus(Request $request) {

        $kandidatid = $request->kandidat_id;
        $statusmundur = $request->status;

        $datalogtahapan = LogTahapan::where('status_tahapan', 'Tandem')
        ->where('kandidat_id', $kandidatid)
        ->first();

        $datalogtahapan -> delete();
        $datakandidat = Kandidat::find($kandidatid);
        $datakandidat -> status_hire = $statusmundur;
        $datakandidat->save();

        $request->session()->flash('success', 'Status kandidat berhasil dimundurkan.');

        return redirect(route('superadmin.tandem.index'));

     }



     public function lolosmundurkanStatus(Request $request){

        $kandidatid = $request->kandidat_id;
        $statusmundur = $request->status;
        
        $datalogtahapan = LogTahapan::where('status_tahapan', 'Lolos')
        ->where('kandidat_id', $kandidatid)
        ->first();

        $datalogtahapan->delete();

        $datakandidat = Kandidat::find($kandidatid);
        $datakandidat -> status_hire = $statusmundur;
        $datakandidat->save();

        $request->session()->flash('success', 'Status kandidat berhasil dimundurkan.');

        return redirect(route('superadmin.lolos.index'));

     }


     
     public function tidaklolosmundurkanStatus(Request $request){

        $kandidatid = $request->kandidat_id;
        $statusmundur = $request->status;

        $datalogtahapan = LogTahapan::where('status_tahapan', 'Tidak Lolos')
        ->where('kandidat_id', $kandidatid)
        ->first();
        
        $datalogtahapan->delete();

        $datakandidat = Kandidat::find($kandidatid);
        $datakandidat->status_hire = $statusmundur;
        $datakandidat->save();

        $request->session()->flash('success', 'Status kandidat berhasil dimundurkan.');

        return redirect(route('superadmin.tidaklolos.index'));

     }

     public function trainingindex(Request $request){

        $roleid = auth()->user()->role_id;


        if($roleid == 2){

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

            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Training')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
            return view('superadmin.training.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
           ]);


        }
        
        if($roleid == 3){
          

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

            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Training')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
            return view('superadmin.training.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
           ]);


        }
        else {
            
            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = Kandidat::query();
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Training')->get();
            return view('superadmin.training.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
              
               ]);
        }

     }

     public function trainingprocess(Request $request)
     {

         $data = $request->all();

         $checkedIds = explode(',', $data['checked_ids']);
         
         $status = $data['status'];
         
        
         foreach ($checkedIds as $id) {
            
             $kandidat = Kandidat::find($id);
             $posisiid = $kandidat->posisi_id;
             $wilayahid = $kandidat ->wilayah_id;

             
             if ($kandidat) {
                $now = now();  // Mengambil waktu saat ini
                $bulan = $now->format('m'); // Mendapatkan bulan
                $tahun = $now->format('Y'); // Mendapatkan tahun
                 $kandidat->status_hire = $status;
                 $kandidat->save();

                 LogTahapan::create([
                    'kandidat_id' => $id,
                    'status_tahapan' => $status, 
                    'tanggal' => $now,  // Menyimpan waktu lengkap
                    'bulan' => $bulan,  // Menyimpan bulan
                    'tahun' => $tahun,  // Menyimpan tahun
                    'posisi_id' => $posisiid,
                    'wilayah_id' => $wilayahid
                ]);
             }
         }
         
         $request->session()->flash('success', 'Status kandidat berhasil diubah.');

         return redirect(route('superadmin.training.index'));
     }


     public function lolosindex(Request $request){

        $roleid = auth()->user()->role_id;

    
        if($roleid == 2){

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

            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Proses PKM')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
            return view('superadmin.lolos.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
           ]);


        }else {

            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = Kandidat::query();
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Proses PKM')->get();
            return view('superadmin.lolos.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
              
               ]);
        }

     }




     public function lolosafterindex(Request $request) {
        $roleid = auth()->user()->role_id;
    
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
    
            // Initialize the query for LogTahapan
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Proses PKM')

            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Psikotes Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Proses PKM Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
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
    
            // Get all log tahapan that match the criteria
            $logTahapan = $query->orderBy('created_at', 'desc')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
    
            return view('superadmin.lolos.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
    
        } else {
            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            
            $query = LogTahapan::query()
            ->where('status_tahapan', 'Proses PKM')
            ->where(function ($query) {
                // Kondisi untuk status_hire yang harus Proses PKM Sudah Dijadwalkan
                $query->whereHas('kandidat', function ($query) {
                    $query->where('status_hire', 'Proses PKM Sudah Dijadwalkan');
                });
            })
            ->whereNot('hasil_status', 'Simpan Kandidat');
        
    
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $logTahapan = $query->orderBy('created_at', 'desc')->get();

            
            return view('superadmin.lolos.dijadwalkan', [
                'logTahapan' => $logTahapan, // Changed to logTahapan
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
            ]);
        }
    }
     public function tidaklolosindex(Request $request){

        $roleid = auth()->user()->role_id;

    
        if($roleid == 2){

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

            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Tidak Lolos')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
            return view('superadmin.tidaklolos.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
           ]);


        }else {

            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = Kandidat::query();
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Tidak Lolos')->get();
            return view('superadmin.tidaklolos.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
              
               ]);
        }

     }


     public function stopprosesindex(Request $request){

        $roleid = auth()->user()->role_id;

    
        if($roleid == 2){

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

            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Stop Proses')->get();
            $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
            return view('superadmin.stopproses.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
           ]);


        }else {

            $posisi = Posisi::all()->sortBy('nama_posisi');
            $wilayah = Wilayah::all()->sortBy('nama_wilayah');
            $query = Kandidat::query();
            if ($request->has('filter_posisi') && $request->filter_posisi != '') {
                $query->where('posisi_id', $request->filter_posisi);
            }
    
            if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
                $query->where('wilayah_id', $request->filter_wilayah);
            }
    
            $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Stop Proses')->get();
            return view('superadmin.stopproses.index',[
                'kandidat' => $kandidat,
                'posisi' => $posisi,
                'wilayah' => $wilayah,
                'selectedPosisi' => $request->filter_posisi,
                'selectedWilayah' => $request->filter_wilayah,
              
               ]);
        }

     }
     public function saveprocess(Request $request)
     {
      
         $data = $request->all();
         
         $checkedIds = explode(',', $data['checked_ids']);
         
         $status = $data['status'];

         $posisi = $request->posisi_ganti;
         $wilayah = $request->wilayah_ganti;

        
         
        
         foreach ($checkedIds as $id) {            
             $kandidat = Kandidat::find($id);
             $posisiid = $kandidat->posisi_id;

             
             if ($kandidat) {
                $now = now();  // Mengambil waktu saat ini
                $bulan = $now->format('m'); // Mendapatkan bulan
                $tahun = $now->format('Y'); // Mendapatkan tahun
                 $kandidat->status_hire = $status;
                 $kandidat->posisi_id = $posisi;
                 $kandidat->wilayah_id = $wilayah;
                 
                 $kandidat->save();

                 LogTahapan::create([
                    'kandidat_id' => $id,
                    'status_tahapan' => $status,
                    'tanggal' => $now,  // Menyimpan waktu lengkap
                    'bulan' => $bulan,  // Menyimpan bulan
                    'tahun' => $tahun,  // Menyimpan tahun
                    'posisi_id' => $posisi,
                    'wilayah_id' => $wilayah,
                ]);
             }

         }
         
        
         $request->session()->flash('success', 'Status kandidat berhasil diubah.');

         return redirect(route('superadmin.save.index'));
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


    public function updateLogTahapan(Request $request)
{
    $logTahapan = LogTahapan::find($request->id);
    if ($logTahapan) {
        $status = $request->status;

        $kandidatid = $logTahapan->kandidat_id;
        $datakandidat = Kandidat::find($kandidatid);

        $datakandidat->status_hire = "$status Sudah Dijadwalkan";
        $datakandidat->save();

        $logTahapan->status_tahapan = $request->status;
        $logTahapan->tanggal = $request->tanggal;
        $logTahapan->save();

        return response()->json(['success' => true, 'message' => 'Log Tahapan updated successfully.']);
    }
    return response()->json(['success' => false, 'message' => 'Log Tahapan not found.']);
}


public function jadwalindexsave(Request $request)
{
    $ids = explode(',', $request->query('ids'));
    $candidates = Kandidat::whereIn('id', $ids)->get();

    $roleid = auth()->user()->role_id;
        
    if($roleid == 2){

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

        $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Simpan Kandidat')->get();
        $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
        return view('superadmin.save.penjadwalan',[
            'kandidat' => $kandidat,
            'posisi' => $posisi,
            'wilayah' => $wilayah,
            'selectedPosisi' => $request->filter_posisi,
            'selectedWilayah' => $request->filter_wilayah,
            'candidates' => $candidates
       ]);


    }else {

        $posisi = Posisi::all()->sortBy('nama_posisi');
        $wilayah = Wilayah::all()->sortBy('nama_wilayah');
        $query = Kandidat::query();
        if ($request->has('filter_posisi') && $request->filter_posisi != '') {
            $query->where('posisi_id', $request->filter_posisi);
        }

        if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
            $query->where('wilayah_id', $request->filter_wilayah);
        }

        $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Simpan Kandidat')->get();
        return view('superadmin.save.penjadwalan',[
            'kandidat' => $kandidat,
            'posisi' => $posisi,
            'wilayah' => $wilayah,
            'selectedPosisi' => $request->filter_posisi,
            'selectedWilayah' => $request->filter_wilayah,
            'candidates' => $candidates
          
           ]);
    }



}

public function jadwalindextidaklolos(Request $request)
{
    $ids = explode(',', $request->query('ids'));
    $candidates = Kandidat::whereIn('id', $ids)->get();

    $roleid = auth()->user()->role_id;
        
    if($roleid == 2){

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

        $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Simpan Kandidat')->get();
        $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();
        return view('superadmin.tidaklolos.penjadwalan',[
            'kandidat' => $kandidat,
            'posisi' => $posisi,
            'wilayah' => $wilayah,
            'selectedPosisi' => $request->filter_posisi,
            'selectedWilayah' => $request->filter_wilayah,
            'candidates' => $candidates
       ]);


    }else {

        $posisi = Posisi::all()->sortBy('nama_posisi');
        $wilayah = Wilayah::all()->sortBy('nama_wilayah');
        $query = Kandidat::query();
        if ($request->has('filter_posisi') && $request->filter_posisi != '') {
            $query->where('posisi_id', $request->filter_posisi);
        }

        if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
            $query->where('wilayah_id', $request->filter_wilayah);
        }

        $kandidat = $query->orderBy('created_at', 'desc')->where('status_hire','Simpan Kandidat')->get();
        return view('superadmin.tidaklolos.penjadwalan',[
            'kandidat' => $kandidat,
            'posisi' => $posisi,
            'wilayah' => $wilayah,
            'selectedPosisi' => $request->filter_posisi,
            'selectedWilayah' => $request->filter_wilayah,
            'candidates' => $candidates
          
           ]);
    }



}

public function jadwalstoresave(Request $request){
        
    $request->validate([
        'status' => 'required',
        'date' => 'required|date',
    ]);

    // Ambil input status dan tanggal yang akan diterapkan pada semua kandidat
    $statusTahapan = $request->status;
    $tanggal = $request->date;
    $posisiidnew = $request->posisi_id;
    $wilayahidnew = $request->wilayah_id;

    
    $tanggalHari = date('d', strtotime($tanggal));
    $tanggalBulan = date('m', strtotime($tanggal));
    $tanggalTahun = date('Y', strtotime($tanggal));

    // Proses setiap kandidat menggunakan ID yang dikirim
    foreach ($request->candidates as $kandidatId) {
        $datakandidat = Kandidat::find($kandidatId);
        $posisiid = $datakandidat->posisi_id;
        $wilayahid = $datakandidat->wilayah_id;
        $namakandidat = $datakandidat->nama_kandidat;

        $datakandidat = Kandidat::find($kandidatId);
       
       
        $datakandidat->posisi_id = $posisiidnew;
        $datakandidat->wilayah_id = $wilayahidnew;
        $datakandidat->save();
 

        // Cek apakah sudah ada jadwal untuk kandidat ini
        

        // Update status hire kandidat
        $datakandidat->status_hire = "$statusTahapan Sudah Dijadwalkan";
        $datakandidat->save();

        // Simpan log tahapan baru
        LogTahapan::create([
            'kandidat_id' => $kandidatId,
            'status_tahapan' => $statusTahapan,
            'tanggal' => $tanggal,
            'bulan' => $tanggalBulan,
            'tahun' => $tanggalTahun,
            'posisi_id' => $posisiidnew,
            'wilayah_id' => $wilayahidnew,
            'hasil_status' => 'Dijadwalkan',
            'flag_tahapan' => 'Simpan Kandidat',
            'requester' => Auth::user()->nama,
            'waktu' => now(),
        ]);

    }

    $request->session()->flash('success', 'Penjadwalan kandidat berhasil dibuat.');
    return redirect(route('superadmin.save.index'));
}


public function jadwalstoretidaklolos(Request $request){
        
    $request->validate([
        'status' => 'required',
        'date' => 'required|date',
    ]);

    // Ambil input status dan tanggal yang akan diterapkan pada semua kandidat
    $statusTahapan = $request->status;
    $tanggal = $request->date;
    $posisiidnew = $request->posisi_id;
    $wilayahidnew = $request->wilayah_id;

    
    $tanggalHari = date('d', strtotime($tanggal));
    $tanggalBulan = date('m', strtotime($tanggal));
    $tanggalTahun = date('Y', strtotime($tanggal));

    // Proses setiap kandidat menggunakan ID yang dikirim
    foreach ($request->candidates as $kandidatId) {
        $datakandidat = Kandidat::find($kandidatId);
        $posisiid = $datakandidat->posisi_id;
        $wilayahid = $datakandidat->wilayah_id;
        $namakandidat = $datakandidat->nama_kandidat;

        $datakandidat = Kandidat::find($kandidatId);
       
       
        $datakandidat->posisi_id = $posisiidnew;
        $datakandidat->wilayah_id = $wilayahidnew;
        $datakandidat->save();
 

        // Cek apakah sudah ada jadwal untuk kandidat ini


        // Update status hire kandidat
        $datakandidat->status_hire = "$statusTahapan Sudah Dijadwalkan";
        $datakandidat->save();

        // Simpan log tahapan baru
        LogTahapan::create([
            'kandidat_id' => $kandidatId,
            'status_tahapan' => $statusTahapan,
            'tanggal' => $tanggal,
            'bulan' => $tanggalBulan,
            'tahun' => $tanggalTahun,
            'posisi_id' => $posisiidnew,
            'wilayah_id' => $wilayahidnew,
            'hasil_status' => 'Dijadwalkan',
            'flag_tahapan' => 'Tidak Lolos',
            'requester' => Auth::user()->nama,
            'waktu' => now(),
        ]);

    }

    $request->session()->flash('success', 'Penjadwalan kandidat berhasil dibuat.');
    return redirect(route('superadmin.tidaklolos.index'));
}


public function saveafterindex(Request $request) {
    $roleid = auth()->user()->role_id;

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

        // Initialize the query for LogTahapan
        $query = LogTahapan::query()
        ->where('flag_tahapan', 'Simpan Kandidat')
        ->whereNot('hasil_status','Simpan Kandidat')
            ->whereHas('kandidat', function ($query) {
                $query->where('status_hire', 'Simpan Kandidat Sudah Dijadwalkan');
            });

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

        // Get all log tahapan that match the criteria
        $logTahapan = $query->orderBy('created_at', 'desc')->get();
        $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();

        return view('superadmin.psikotes.dijadwalkan', [
            'logTahapan' => $logTahapan, // Changed to logTahapan
            'posisi' => $posisi,
            'wilayah' => $wilayah,
            'selectedPosisi' => $request->filter_posisi,
            'selectedWilayah' => $request->filter_wilayah,
        ]);

    } else {
        $posisi = Posisi::all()->sortBy('nama_posisi');
        $wilayah = Wilayah::all()->sortBy('nama_wilayah');
        $query = LogTahapan::query()
        ->where('flag_tahapan', 'Simpan Kandidat')
        ->whereNot('hasil_status','Simpan Kandidat')
            ->whereHas('kandidat', function ($query) {
                $query->where('status_hire', 'Simpan Kandidat Sudah Dijadwalkan');
            });

        if ($request->has('filter_posisi') && $request->filter_posisi != '') {
            $query->where('posisi_id', $request->filter_posisi);
        }

        if ($request->has('filter_wilayah') && $request->filter_wilayah != '') {
            $query->where('wilayah_id', $request->filter_wilayah);
        }

        $logTahapan = $query->orderBy('created_at', 'desc')->get();

        
        return view('superadmin.save.dijadwalkan', [
            'logTahapan' => $logTahapan, // Changed to logTahapan
            'posisi' => $posisi,
            'wilayah' => $wilayah,
            'selectedPosisi' => $request->filter_posisi,
            'selectedWilayah' => $request->filter_wilayah,
        ]);
    }
}



public function trainertrainerindex(Request $request){

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

    // Initialize the query for LogTahapan
    $query = LogTahapan::query()
    ->where('status_tahapan', 'Training')
    ->where(function ($query) {
        $query->where('hasil_status', 'Dijadwalkan')
              
              ->orWhere('hasil_status', 'Lolos')
              ->orWhere('hasil_status', 'Tidak Lolos');;
    })
    ->whereNot('hasil_status', 'Simpan Kandidat');



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

    // Get all log tahapan that match the criteria
    $logTahapan = $query->orderBy('created_at', 'desc')->get();

   
    $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();

    return view('trainer.training.index', [
        'logTahapan' => $logTahapan, // Changed to logTahapan
        'posisi' => $posisi,
        'wilayah' => $wilayah,
        'selectedPosisi' => $request->filter_posisi,
        'selectedWilayah' => $request->filter_wilayah,
    ]);

 
 }



 public function tandemtrainerindex(Request $request){

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

    // Initialize the query for LogTahapan
    $query = LogTahapan::query()
    ->where('status_tahapan', 'Tandem')
    ->where(function ($query) {
        $query->where('hasil_status', 'Dijadwalkan')
              
              ->orWhere('hasil_status', 'Lolos')
              ->orWhere('hasil_status', 'Tidak Lolos');;
    })
    ->whereNot('hasil_status', 'Simpan Kandidat');



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

    // Get all log tahapan that match the criteria
    $logTahapan = $query->orderBy('created_at', 'desc')->get();
    $posisi = Posisi::whereIn('id', $assignedPosisiIds)->get();

    return view('trainer.tandem.index', [
        'logTahapan' => $logTahapan, // Changed to logTahapan
        'posisi' => $posisi,
        'wilayah' => $wilayah,
        'selectedPosisi' => $request->filter_posisi,
        'selectedWilayah' => $request->filter_wilayah,
    ]);

 
 }
}
