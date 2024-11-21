<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPerformanceExport;
use App\Models\Kandidat;
use App\Models\LaporanPerformance;
use App\Models\LogTahapan;
use App\Models\MasterAktif;
use App\Models\MasterKonfirm;
use App\Models\MasterTidakAktif;
use App\Models\MasterTrainingTandem;
use App\Models\Posisi;
use App\Models\TargetJumlah;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPerformaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function superadminindex(Request $request)
     {
         $posisi = Posisi::all();
         $targetMPP = null;
     
         // Cek jika request berasal dari AJAX
         if ($request->ajax()) {
             if ($request->has('posisi_id') && $request->has('bulan') && $request->has('tahun')) {
                 $posisi_id = $request->input('posisi_id');
                 $bulan = $request->input('bulan');
                 $tahun = $request->input('tahun');
     
                 // Jika bulan adalah 'all', set range bulan dari 1 sampai 12
                 if ($bulan === 'all') {
                     $monthRange = range(1, 12); // bulan 1 hingga 12
                 } else {
                     $month = $bulan;
                     $monthRange = [$month]; // hanya 1 bulan yang dipilih
                 }
     
                 // Ambil target jumlah berdasarkan posisi dan range bulan
                 $targetMPP = TargetJumlah::where('posisi_id', $posisi_id)
                     ->whereIn('bulan', $monthRange)
                     ->where('tahun', $tahun)
                     ->sum('target_mpp') ?? 0;
     
                 $mitraexisting = TargetJumlah::where('posisi_id', $posisi_id)
                     ->whereIn('bulan', $monthRange)
                     ->where('tahun', $tahun)
                     ->sum('jumlah_mitra') ?? 0;
     
                 // Set persenmpp menjadi 0 jika bulan adalah 'all'
                 if ($bulan === 'all') {
                    $persenmpp = $targetMPP > 0 ? round(($mitraexisting / $targetMPP) * 100) : 0;
                 } else {
                     $persenmpp = $targetMPP > 0 ? round(($mitraexisting / $targetMPP) * 100) : 0;
                 }
     
                 $lolossortir = Kandidat::where('posisi_id', $posisi_id)
                     ->whereIn('bulan', $monthRange)
                     ->where('tahun', $tahun)
                     ->count() ?? 0;
     
                 $konfirmasihadir = MasterKonfirm::where('posisi_id', $posisi_id)
                     ->whereIn('month', $monthRange)
                     ->where('year', $tahun)
                     ->sum('jumlah_konfirm_manual') ?? 0;
     
                 $lolos = LogTahapan::where('posisi_id', $posisi_id)
                     ->whereIn('bulan', $monthRange)
                     ->where('tahun', $tahun)
                     ->where('status_tahapan', 'Interview HR')
                     ->where('hasil_status', 'Lolos')
                     ->count() ?? 0;
     
                //  $training = MasterTrainingTandem::where('posisi_id', $posisi_id)
                //      ->whereIn('bulan', $monthRange)
                //      ->where('tahun', $tahun)
                //      ->whereRaw('LOWER(kelas_training) = ?', ['hadir'])
                //      ->count() ?? 0;


                
                $training = LogTahapan::where('posisi_id', $posisi_id)
                ->whereIn('bulan', $monthRange)
                ->where('tahun', $tahun)
                ->where(function($query) {
                    $query->where('status_tahapan', 'Training')
                          ->where(function($subQuery) {
                              $subQuery->where('hasil_status', 'Lolos')
                                       ->orWhere('hasil_status', 'Tidak Lolos')
                                       ->orWhere('hasil_status', 'Simpan Kandidat');
                          })
                          ->orWhere('status_tahapan', 'Training ABM');
                })
                ->count() ?? 0;


                Log::info('Training : ' . $training);

                
                $tandem = LogTahapan::where('posisi_id', $posisi_id)
                ->whereIn('bulan', $monthRange)
                ->where('tahun', $tahun)
                ->where('status_tahapan', 'Tandem')
                ->where(function($query) {
                    $query->where('hasil_status', 'Lolos')
                          ->orWhere('hasil_status', 'Tidak Lolos')
                          ->orWhere('hasil_status', 'Simpan Kandidat');
                })
                ->count() ?? 0;

     
                //  $tandem = MasterTrainingTandem::where('posisi_id', $posisi_id)
                //      ->whereIn('bulan', $monthRange)
                //      ->where('tahun', $tahun)
                //      ->where(function ($query) {
                //          $query->where(DB::raw('LOWER(status)'), 'tandem')
                //              ->orWhere(DB::raw('LOWER(status)'), 'proses tandem')
                //              ->orWhere(DB::raw('LOWER(status)'), 'pkm')
                //              ->orWhere(DB::raw('LOWER(status)'), 'akan pkm')
                //              ->orWhere(DB::raw('LOWER(status)'), 'hold pkm');
                //      })
                //      ->count() ?? 0;
     
                 $pkmbaru = MasterAktif::where('posisi_id', $posisi_id)
                     ->whereIn('bulan', $monthRange)
                     ->where('tahun', $tahun)
                     ->count() ?? 0;
     
                 $pkmbataljoin = MasterTidakAktif::where('posisi_id', $posisi_id)
                     ->whereIn('bulan', $monthRange)
                     ->where('tahun', $tahun)
                     ->where('flag_ket', 'Batal Join')
                     ->count() ?? 0;
     
                 $resign = MasterTidakAktif::where('posisi_id', $posisi_id)
                     ->whereIn('bulan', $monthRange)
                     ->where('tahun', $tahun)
                     ->where('flag_ket', 'Resign')
                     ->count() ?? 0;
     
                 $tambahkurangmitra = $pkmbaru - $resign;
     
                 $targetjoin = TargetJumlah::where('posisi_id', $posisi_id)
                     ->whereIn('bulan', $monthRange)
                     -> where('tahun', $tahun)
                     ->sum('target_join') ?? 0;
     

                if ($bulan === 'all') {
                        $persenpencapaian = $targetjoin > 0 ? round(($pkmbaru / $targetjoin) * 100) : 0;
                } else {
                        $persenpencapaian = $targetjoin > 0 ? round(($pkmbaru / $targetjoin) * 100) : 0;
                }
                     
                 // Ambil data untuk setiap bulan
                 $lolossortirBulan = [];
                 $konfirmasihadirBulan = [];
                 $lolosBulan = [];
                 $trainingBulan = [];
                 $tandemBulan = [];
                 $pkmbaruBulan = [];
     
                 foreach ($monthRange as $i) {
                     $lolossortirBulan['lolossortir_bulan_' . $i] = Kandidat::where('posisi_id', $posisi_id)
                         ->where('bulan', $i)
                         ->where('tahun', $tahun)
                         ->count() ?? 0;
     
                     $konfirmasihadirBulan['konfirmasihadir_bulan_' . $i] = MasterKonfirm::where('posisi_id', $posisi_id)
                         ->where('month', $i)
                         ->where('year', $tahun)
                         ->sum('jumlah_konfirm_manual') ?? 0;
     
                     $lolosBulan['lolos_bulan_' . $i] = LogTahapan::where('posisi_id', $posisi_id)
                     ->where('bulan', $i)
                     ->where('tahun', $tahun)
                     ->where('status_tahapan', 'Interview HR')
                     ->where('hasil_status', 'Lolos')
                     ->count() ?? 0;
     
                     $trainingBulan['training_bulan_' . $i] = LogTahapan::where('posisi_id', $posisi_id)
                     ->where('bulan', $i)
                     ->where('tahun', $tahun)
                     ->where(function($query) {
                         $query->where('status_tahapan', 'Training')
                               ->where(function($subQuery) {
                                   $subQuery->where('hasil_status', 'Lolos')
                                            ->orWhere('hasil_status', 'Tidak Lolos')
                                            ->orWhere('hasil_status', 'Simpan Kandidat');
                               })
                               ->orWhere('status_tahapan', 'Training ABM');
                     })
                     ->count() ?? 0;
     
                     $tandemBulan['tandem_bulan_' . $i] = LogTahapan::where('posisi_id', $posisi_id)
                     ->where('bulan', $i)
                     ->where('tahun', $tahun)
                     ->where('status_tahapan', 'Tandem')
                     ->where(function($query) {
                         $query->where('hasil_status', 'Lolos')
                               ->orWhere('hasil_status', 'Tidak Lolos')
                               ->orWhere('hasil_status', 'Simpan Kandidat');
                     })
                     ->count() ?? 0;
     
                     $pkmbaruBulan['pkmbaru_bulan_' . $i] = MasterAktif::where('posisi_id', $posisi_id)
                         ->where('bulan', $i)
                         ->where('tahun', $tahun)
                         ->count() ?? 0;
                 }
     
                 $response = [
                     'targetMPP' => $targetMPP,
                     'mitraexisting' => $mitraexisting,
                     'persenmpp' => $persenmpp,
                     'lolossortir' => $lolossortir,
                     'konfirmasihadir' => $konfirmasihadir,
                     'lolos' => $lolos,
                     'training' => $training,
                     'tandem' => $tandem,
                     'pkmbaru' => $pkmbaru,
                     'pkmbataljoin' => $pkmbataljoin,
                     'resign' => $resign,
                     'tambahkurangmitra' => $tambahkurangmitra,
                     'targetjoin' => $targetjoin,
                     'persenpencapaian' => $persenpencapaian,
                     'lolossortir_bulan' => $lolossortirBulan,
                     'konfirmasihadir_bulan' => $konfirmasihadirBulan,
                     'lolos_bulan' => $lolosBulan,
                     'training_bulan' => $trainingBulan,
                     'tandem_bulan' => $tandemBulan,
                     'pkmbaru_bulan' => $pkmbaruBulan,
                 ];
     
                 return response()->json($response);
             }
         }
     
         return view('superadmin.laporanperformance.index', [
            'posisi' => $posisi,
            'targetMPP' => $targetMPP,
        ]);
     }
     
public function backup(Request $request)
{
    $posisi = Posisi::all();
    $targetMPP = null;

    // Cek jika request berasal dari AJAX
    if ($request->ajax()) {
        if ($request->has('posisi_id') && $request->has('bulan') && $request->has('tahun')) {
            $posisi_id = $request->input('posisi_id');
            $bulan = $request->input('bulan');
            $tahun = $request->input('tahun');

            // Jika bulan adalah 'all', set range bulan dari 1 sampai 12
            if ($bulan === 'all') {
                $monthRange = range(1, 12); // bulan 1 hingga 12
            } else {
                $month = $bulan;
                $monthRange = [$month]; // hanya 1 bulan yang dipilih
            }

            // Ambil target jumlah berdasarkan posisi dan range bulan
            $targetMPP = TargetJumlah::where('posisi_id', $posisi_id)
                ->whereIn('bulan', $monthRange)
                ->where('tahun', $tahun)
                ->sum('target_mpp') ?? 0;

            $mitraexisting = TargetJumlah::where('posisi_id', $posisi_id)
                ->whereIn('bulan', $monthRange)
                ->where('tahun', $tahun)
                ->sum('jumlah_mitra') ?? 0;

            // Set persenmpp menjadi 0 jika bulan adalah 'all'
            if ($bulan === 'all') {
                $persenmpp = 0;
            } else {
                $persenmpp = $targetMPP > 0 ? round(($mitraexisting / $targetMPP) * 100) : 0;
            }

            $lolossortir = Kandidat::where('posisi_id', $posisi_id)
                ->whereIn('bulan', $monthRange)
                ->where('tahun', $tahun)
                ->count() ?? 0;

            $konfirmasihadir = MasterKonfirm::where('posisi_id', $posisi_id)
                ->whereIn('month', $monthRange)
                ->where('year', $tahun)
                ->sum('jumlah_konfirm_manual') ?? 0;

            $lolos = LogTahapan::where('posisi_id', $posisi_id)
                ->whereIn('bulan', $monthRange)
                ->where('tahun', $tahun)
                ->where('status_tahapan', 'Interview HR')
                ->where('flag_lolos', 'Yes')
                ->count() ?? 0;

            $training = MasterTrainingTandem::where('posisi_id', $posisi_id)
                ->whereIn('bulan', $monthRange)
                ->where('tahun', $tahun)
                ->whereRaw('LOWER(kelas_training) = ?', ['hadir'])
                ->count() ?? 0;

            $tandem = MasterTrainingTandem::where('posisi_id', $posisi_id)
                ->whereIn('bulan', $monthRange)
                ->where('tahun', $tahun)
                ->where(function ($query) {
                    $query->where(DB::raw('LOWER(status)'), 'tandem')
                        ->orWhere(DB::raw('LOWER(status)'), 'proses tandem')
                        ->orWhere(DB::raw('LOWER(status)'), 'pkm')
                        ->orWhere(DB::raw('LOWER(status)'), 'akan pkm')
                        ->orWhere(DB::raw('LOWER(status)'), 'hold pkm');
                })
                ->count() ?? 0;

            $pkmbaru = MasterAktif::where('posisi_id', $posisi_id)
                ->whereIn('bulan', $monthRange)
                ->where('tahun', $tahun)
                ->count() ?? 0;

            $pkmbataljoin = MasterTidakAktif::where('posisi_id', $posisi_id)
                ->whereIn('bulan', $monthRange)
                ->where('tahun', $tahun)
                ->where('flag_ket', 'Batal Join')
                ->count() ?? 0;

            $resign = MasterTidakAktif::where('posisi_id', $posisi_id)
                ->whereIn('bulan', $monthRange)
                ->where('tahun', $tahun)
                ->where('flag_ket', 'Resign')
                ->count() ?? 0;

            $tambahkurangmitra = $pkmbaru - $resign;

            $targetjoin = TargetJumlah::where('posisi_id', $posisi_id)
                ->whereIn('bulan', $monthRange)
                ->where('tahun', $tahun)
                ->sum('target_join') ?? 0;

            // Set persenpencapaian menjadi 0 jika bulan adalah 'all'
            if ($bulan === 'all') {
                $persenpencapaian = 0;
            } else {
                $persenpencapaian = $targetjoin > 0 ? round(($pkmbaru / $targetjoin) * 100) : 0;
            }

            return response()->json([
                'targetMPP' => $targetMPP,
                'mitraexisting' => $mitraexisting,
                'persenmpp' => $persenmpp,
                'lolossortir' => $lolossortir,
                'konfirmasihadir' => $konfirmasihadir,
                'lolos' => $lolos,
                'training' => $training,
                'tandem' => $tandem,
                'pkmbaru' => $pkmbaru,
                'pkmbataljoin' => $pkmbataljoin,
                'resign' => $resign,
                'tambahkurangmitra' => $tambahkurangmitra,
                'targetjoin' => $targetjoin,
                'persenpencapaian' => $persenpencapaian,
            ]);
        }
    }

    // Jika bukan AJAX, kembalikan view
    return view('superadmin.laporanperformance.index', [
        'posisi' => $posisi,
        'targetMPP' => $targetMPP,
    ]);
}

    public function download(Request $request)
    {
        $tahun = $request->input('tahun_download');
        $posisi = $request->input('posisi_download');
        
        return Excel::download(new LaporanPerformanceExport($tahun, $posisi), 'Laporan_Performance.xlsx');
    }

    public function store(Request $request){
        
            $bulan = $request->bulan;
            $tahun = $request->tahun;

            $monthName = Carbon::createFromDate($tahun, $bulan, 1)->format('F');

            $posisi = $request->posisi;
            
            $lolossortir = Kandidat::where('posisi_id', $posisi)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->count();

            $konfirmasihadir = MasterKonfirm::where('posisi_id', $posisi)
            ->where('month', $bulan)
            ->where('year', $tahun)->sum('jumlah_konfirm_manual');


            $training = MasterTrainingTandem::where('posisi_id', $posisi)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereRaw('LOWER(kelas_training) = ?', ['hadir'])
            ->count();

            $lolos = LogTahapan::where('posisi_id', $posisi)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status_tahapan', 'Interview HR')
            ->where('flag_lolos', 'Yes')
            ->count();


            $tandemCount = MasterTrainingTandem::where('posisi_id', $posisi)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where(function($query) {
                $query->where(DB::raw('LOWER(status)'), 'tandem')
                      ->orWhere(DB::raw('LOWER(status)'), 'proses tandem')
                      ->orWhere(DB::raw('LOWER(status)'), 'pkm')
                      ->orWhere(DB::raw('LOWER(status)'), 'akan pkm')
                      ->orWhere(DB::raw('LOWER(status)'), 'hold pkm');
            })
            ->count();

            $pkmbaru = MasterAktif::where('posisi_id', $posisi)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->count();
            
            $pkmbataljoin = MasterTidakAktif::where('posisi_id', $posisi)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('flag_ket', 'Batal Join')
            ->count();

            $resign = MasterTidakAktif::where('posisi_id', $posisi)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('flag_ket', 'Resign')
            ->count();
            
            $tambahkurangmitra = $pkmbaru - $resign;

        

            LaporanPerformance::create([
                'bulan' => $bulan,
                'tahun' => $tahun,
                'lolos_sortir' => $lolossortir,
                'konfirmasi_hadir' => $konfirmasihadir,
                'lolos' => $lolos,
                'training' => $training,
                'tandem' => $tandemCount,
                'PKM_baru' => $pkmbaru,
                'PKM_batal_join' => $pkmbataljoin,
                'resign' => $resign,
                'posisi_id' => $posisi
            ]);

            $request->session()->flash('success', "Laporan Performance $monthName $tahun berhasil dibuat.");
 
            return redirect()->route('superadmin.laporanperformance.index');

    }


    public function superadmindestroy(Request $request, $id){

        

        $datalaporan = LaporanPerformance::find($id);

        $bulan = $datalaporan->bulan;
        $tahun = $datalaporan->tahun;

        $monthName = Carbon::createFromDate($tahun, $bulan, 1)->format('F');
        $datalaporan->delete();


   $request->session()->flash('success', "Laporan Performance $monthName $tahun berhasil dihapus.");
 
            return redirect()->route('superadmin.laporanperformance.index');

    }

    public function index(Request $request)
    {
        $posisi = Posisi::all();
         
        $targetMPP = null;
    
        // Cek jika request berasal dari AJAX
        if ($request->ajax()) {
            if ($request->has('posisi_id') && $request->has('bulan')) {
                $posisi_id = $request->input('posisi_id');
                $bulan = $request->input('bulan');
                
                // Pecah bulan dan tahun
                list($year, $month) = explode('-', $bulan);
                $month = ltrim($month, '0');
                
                // Ambil target jumlah berdasarkan posisi dan bulan
               $targetMPP = TargetJumlah::where('posisi_id', $posisi_id)
                    ->where('bulan', $month)
                    ->where('tahun', $year)
                    ->value('target_mpp') ?? 0;

               $mitraexisting = TargetJumlah::where('posisi_id', $posisi_id)
               ->where('bulan', $month)
               ->where('tahun', $year)
               ->value('jumlah_mitra') ?? 0;

               $persenmpp = $targetMPP > 0 ? round(($mitraexisting / $targetMPP) * 100) : 0;

               $lolossortir = Kandidat::where('posisi_id', $posisi_id)
               ->where('bulan', $month)
               ->where('tahun', $year)
               ->count() ?? 0;

               $konfirmasihadir = MasterKonfirm::where('posisi_id', $posisi_id)
               ->where('month', $month)
               ->where('year', $year)
               ->sum('jumlah_konfirm_manual') ?? 0;


               $lolos = LogTahapan::where('posisi_id', $posisi_id)
               ->where('bulan', $month)
               ->where('tahun', $year)
               ->where('status_tahapan', 'Interview HR')
               ->where('flag_lolos', 'Yes')
               ->count() ?? 0;
   
               
               $training = MasterTrainingTandem::where('posisi_id', $posisi_id)
               ->where('bulan', $month)
               ->where('tahun', $year)
               ->whereRaw('LOWER(kelas_training) = ?', ['hadir'])
               ->count() ?? 0;


               $tandem = MasterTrainingTandem::where('posisi_id', $posisi_id)
               ->where('bulan', $month)
               ->where('tahun', $year)
               ->where(function($query) {
                   $query->where(DB::raw('LOWER(status)'), 'tandem')
                         ->orWhere(DB::raw('LOWER(status)'), 'proses tandem')
                         ->orWhere(DB::raw('LOWER(status)'), 'pkm')
                         ->orWhere(DB::raw('LOWER(status)'), 'akan pkm')
                         ->orWhere(DB::raw('LOWER(status)'), 'hold pkm');
               })
               ->count() ?? 0;

               
               $pkmbaru = MasterAktif::where('posisi_id', $posisi_id)
               ->where('bulan', $month)
               ->where('tahun', $year)
               ->count() ?? 0;

               $pkmbataljoin = MasterTidakAktif::where('posisi_id', $posisi_id)
               ->where('bulan', $month)
               ->where('tahun', $year)
               ->where('flag_ket', 'Batal Join')
               ->count() ?? 0;
              
               $resign = MasterTidakAktif::where('posisi_id', $posisi_id)
               ->where('bulan', $month)
               ->where('tahun', $year)
               ->where('flag_ket', 'Resign')
               ->count() ?? 0; 

               $tambahkurangmitra = $pkmbaru - $resign;

               $targetjoin =  TargetJumlah::where('posisi_id', $posisi_id)
               ->where('bulan', $month)
               ->where('tahun', $year)
               ->value('target_join') ?? 0;

               $persenpencapaian = $targetjoin > 0 ? round(($pkmbaru / $targetjoin) * 100) : 0;

                return response()->json([
                   'targetMPP' => $targetMPP,
                   'mitraexisting' => $mitraexisting,
                   'persenmpp' => $persenmpp,
                   'lolossortir' => $lolossortir,
                   'konfirmasihadir' => $konfirmasihadir,
                   'lolos' =>$lolos,
                   'training' => $training,
                   'tandem' => $tandem,
                   'pkmbaru' => $pkmbaru,
                   'pkmbataljoin' => $pkmbataljoin,
                   'resign' => $resign,
                   'tambahkurangmitra' => $tambahkurangmitra,
                   'targetjoin' => $targetjoin,
                   'persenpencapaian' => $persenpencapaian,
                  
                ]);
            }
        }
    
        // Jika bukan AJAX, kembalikan view
              

        return view('superadmin.laporanperformance.index', [
            'posisi' => $posisi,
            'targetMPP' => $targetMPP,
        ]);
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
