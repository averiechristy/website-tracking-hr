<?php

namespace App\Http\Controllers;

use App\Models\DetailPosisi;
use App\Models\Kandidat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */


     public function superadminindex() {
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
            
            // Initialize the query for Kandidat
            $query = Kandidat::query();
    
            // Get the current month and year
            $currentMonth = now()->month;
            $currentYear = now()->year;
    
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
    
            // Filter candidates created in the current month
            $query->where('bulan', $currentMonth)
                  ->where('tahun', $currentYear);
    
            $kandidat = $query->get();
    
            // Counting the number of kandidat for each status
            $belumproses = $kandidat->where('status_hire', 'Belum Diproses')->count();
            $psikotes = $kandidat->where('status_hire', 'Psikotes')->count();
            $psikotesjadwal = $kandidat->where('status_hire', 'Psikotes Sudah Dijadwalkan')->count();
            $itvhr = $kandidat->where('status_hire', 'Interview HR')->count();
            $itvhrjadwal = $kandidat->where('status_hire', 'Interview HR Sudah Dijadwalkan')->count();
            $itvuser = $kandidat->where('status_hire', 'Interview User')->count();
            $itvuserjadwal = $kandidat->where('status_hire', 'Interview User Sudah Dijadwalkan')->count();

            $itvuserdua = $kandidat->where('status_hire', 'Interview User 2')->count();
            $itvuserduajadwal = $kandidat->where('status_hire', 'Interview User 2 Sudah Dijadwalkan')->count();

            $itvusertiga = $kandidat->where('status_hire', 'Interview User 3')->count();
            $itvusertigajadwal = $kandidat->where('status_hire', 'Interview User 3 Sudah Dijadwalkan')->count();

            $training = $kandidat->where('status_hire', 'Training')->count();
            $trainingjadwal = $kandidat->where('status_hire', 'Training Sudah Dijadwalkan')->count();
            $tandem = $kandidat->where('status_hire', 'Tandem')->count();
            $tandemjadwal = $kandidat->where('status_hire', 'Tandem Sudah Dijadwalkan')->count();
            $lolos = $kandidat->where('status_hire', 'PKM Selesai')->count();
            $lolosjadwal = $kandidat->where('status_hire', 'Proses PKM Sudah Dijadwalkan')->count();
            $tidaklolos = $kandidat->where('status_hire', 'Tidak Lolos')->count();
            $simpankandidat = $kandidat->where('status_hire', 'Simpan Kandidat')->count();
            $stopproses = $kandidat->where('status_hire', 'Stop Proses')->count();
            $belumprosesafter = $kandidat->where('status_hire', 'Belum Diproses Sudah Dijadwalkan')->count();
            $psikotesafter = $kandidat->where('status_hire', 'Psikotes Sudah Dijadwalkan')->count();
            $itvhrafter = $kandidat->where('status_hire', 'Interview HR Sudah Dijadwalkan')->count();
            $itvuserafter = $kandidat->where('status_hire', 'Interview User Sudah Dijadwalkan')->count();
            $trainingafter = $kandidat->where('status_hire', 'Training Sudah Dijadwalkan')->count();
            $tandemafter = $kandidat->where('status_hire', 'Tandem Sudah Dijadwalkan')->count();
            $simpankandidatafter = $kandidat->where('status_hire', 'Simpan Kandidat Sudah Dijadwalkan')->count();


            $psikotessum = $psikotes + $psikotesjadwal;
            $itvhrsum = $itvhr + $itvhrjadwal;
            $itvusersum = $itvuser + $itvuserjadwal;
            $itvuserduasum = $itvuserdua + $itvuserduajadwal;
            $itvusertigasum = $itvusertiga + $itvusertigajadwal;
            $trainingsum = $training + $trainingjadwal;
            $tandemsum = $tandem + $tandemjadwal;
            $lolossum = $lolos + $lolosjadwal;

          
            return view('superadmin.dashboard', [
                'belumproses' => $belumproses,
                'psikotes' => $psikotes,
                'itvhr' => $itvhr,
                'itvuser' => $itvuser,
                'training' => $training,
                'tandem' => $tandem,
                'lolos' => $lolos,
                'tidaklolos' => $tidaklolos,
                'simpankandidat' => $simpankandidat,
                'belumprosesafter' => $belumprosesafter,
                'psikotesafter' => $psikotesafter,
                'itvhrafter' => $itvhrafter,
                'itvuserafter' => $itvuserafter,
                'trainingafter' => $trainingafter,
                'tandemafter' => $tandemafter,
                'simpankandidatafter' => $simpankandidatafter,
                'stopproses' => $stopproses,
                'itvhrjadwal' => $itvhrjadwal,
                'itvuserjadwal' => $itvuserjadwal,
                'trainingjadwal' => $trainingjadwal,
                'tandemjadwal' => $tandemjadwal,
                'itvuserdua' => $itvuserdua,
                'itvuserduajadwal' => $itvuserduajadwal,
                'itvusertiga' => $itvusertiga,
                'itvusertigajadwal' => $itvusertigajadwal,
                'psikotessum' => $psikotessum,
                'itvhrsum' => $itvhrsum,
                'itvusersum' => $itvusersum,
                'itvuserduasum' => $itvuserduasum,
                'itvusertigasum' => $itvusertigasum,
                'trainingsum' => $trainingsum,
                'tandemsum' => $tandemsum,
                'psikotesjadwal' => $psikotesjadwal,
                'lolosjadwal' => $lolosjadwal,
                'lolossum' => $lolossum,
            
            ]);
        } else  if ($roleid == 3) {
            return view('trainer.dashboard');
        }
         else {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            // Filter candidates created in the current month for non-superadmin users
            $kandidat = Kandidat::where('bulan', $currentMonth)
                                ->where('tahun', $currentYear)
                                ->get();
                                                               
            $belumproses = $kandidat->where('status_hire', 'Belum Diproses')->count();
            $psikotes = $kandidat->where('status_hire', 'Psikotes')->count();
            $psikotesjadwal = $kandidat->where('status_hire', 'Psikotes Sudah Dijadwalkan')->count();
            $itvhr = $kandidat->where('status_hire', 'Interview HR')->count();
            $itvhrjadwal = $kandidat->where('status_hire', 'Interview HR Sudah Dijadwalkan')->count();
            $itvuser = $kandidat->where('status_hire', 'Interview User')->count();
            $itvuserjadwal = $kandidat->where('status_hire', 'Interview User Sudah Dijadwalkan')->count();
            $training = $kandidat->where('status_hire', 'Training')->count();
            $trainingjadwal = $kandidat->where('status_hire', 'Training Sudah Dijadwalkan')->count();
            $tandem = $kandidat->where('status_hire', 'Tandem')->count();
            $tandemjadwal = $kandidat->where('status_hire', 'Tandem Sudah Dijadwalkan')->count();
            $lolos = $kandidat->where('status_hire', 'PKM Selesai')->count();
            $lolosjadwal = $kandidat->where('status_hire', 'Proses PKM Sudah Dijadwalkan')->count();
            $tidaklolos = $kandidat->where('status_hire', 'Tidak Lolos')->count();
            $simpankandidat = $kandidat->where('status_hire', 'Simpan Kandidat')->count();
            $belumprosesafter = $kandidat->where('status_hire', 'Belum Diproses Sudah Dijadwalkan')->count();
            $psikotesafter = $kandidat->where('status_hire', 'Psikotes Sudah Dijadwalkan')->count();
            $itvhrafter = $kandidat->where('status_hire', 'Interview HR Sudah Dijadwalkan')->count();
            $itvuserafter = $kandidat->where('status_hire', 'Interview User Sudah Dijadwalkan')->count();
            $trainingafter = $kandidat->where('status_hire', 'Training Sudah Dijadwalkan')->count();
            $tandemafter = $kandidat->where('status_hire', 'Tandem Sudah Dijadwalkan')->count();
            $simpankandidatafter = $kandidat->where('status_hire', 'Simpan Kandidat Sudah Dijadwalkan')->count();
            $stopproses = $kandidat->where('status_hire', 'Stop Proses')->count();
            $itvuserdua = $kandidat->where('status_hire', 'Interview User 2')->count();
            $itvuserduajadwal = $kandidat->where('status_hire', 'Interview User 2 Sudah Dijadwalkan')->count();

            $itvusertiga = $kandidat->where('status_hire', 'Interview User 3')->count();
            $itvusertigajadwal = $kandidat->where('status_hire', 'Interview User 3 Sudah Dijadwalkan')->count();

            $psikotessum = $psikotes + $psikotesjadwal;
            $itvhrsum = $itvhr + $itvhrjadwal;
            $itvusersum = $itvuser + $itvuserjadwal;
            $itvuserduasum = $itvuserdua + $itvuserduajadwal;
            $itvusertigasum = $itvusertiga + $itvusertigajadwal;
            $trainingsum = $training + $trainingjadwal;
            $tandemsum = $tandem + $tandemjadwal;

            $lolossum = $lolos + $lolosjadwal;

            return view('superadmin.dashboard', [
                'belumproses' => $belumproses,
                'psikotes' => $psikotes,
                'itvhr' => $itvhr,
                'itvuser' => $itvuser,
                'training' => $training,
                'tandem' => $tandem,
                'lolos' => $lolos,
                'tidaklolos' => $tidaklolos,
                'simpankandidat' => $simpankandidat,
                'belumprosesafter' => $belumprosesafter,
                'psikotesafter' => $psikotesafter,
                'itvhrafter' => $itvhrafter,
                'itvuserafter' => $itvuserafter,
                'trainingafter' => $trainingafter,
                'tandemafter' => $tandemafter,
                'simpankandidatafter' => $simpankandidatafter,
                'stopproses' => $stopproses,
                'itvhrjadwal' => $itvhrjadwal,
                'itvuserjadwal' => $itvuserjadwal,
                'trainingjadwal' => $trainingjadwal,
                'tandemjadwal' => $tandemjadwal,
                'itvuserdua' => $itvuserdua,
                'itvuserduajadwal' => $itvuserduajadwal,
                'itvusertiga' => $itvusertiga,
                'itvusertigajadwal' => $itvusertigajadwal,
                'psikotessum' => $psikotessum,
                'itvhrsum' => $itvhrsum,
                'itvusersum' => $itvusersum,
                'itvuserduasum' => $itvuserduasum,
                'itvusertigasum' => $itvusertigasum,
                'trainingsum' => $trainingsum,
                'tandemsum' => $tandemsum,
                'psikotesjadwal' => $psikotesjadwal,
                'lolossum' => $lolossum,
                'lolosjadwal' => $lolosjadwal,
            ]);
        }
    
        
    }
    


     public function rekrutmenindex(){
        return view('rekrutmen.dashboard');
     }

     public function trainerindex(){
        return view('trainer.dashboard');
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
