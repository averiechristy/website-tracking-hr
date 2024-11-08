  <!-- ======= Sidebar ======= -->
@php
  // Convert the comma-separated permissions string to an array
  $permissions = explode(',', auth()->user()->role->permision ?? '');
@endphp

  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
@php
    $roleid = auth()->user()->role_id;
@endphp


@if ($roleid == 3)
  <li class="nav-item">
    <a class="nav-link collapsed" href="{{route('trainingtrainer')}}">
  
      <span>Training</span>
    </a>
  </li><!-- End Dashboard Nav -->

  <li class="nav-item">
  <a class="nav-link collapsed" href="{{route('tandemtrainer')}}">

      <span>Tandem</span>
    </a>
  </li><!-- End Dashboard Nav -->

@endif



@if ($roleid != 3)
  <li class="nav-item">
    <a class="nav-link collapsed" href="{{ route('superadmindashboard') }}">
      <i class="bi bi-grid"></i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->
@endif


      @if (in_array('MasterData', $permissions))

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Master Data</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>

      <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{route('superadmin.posisi.index')}}">
              <i class="bi bi-circle"></i><span>Posisi</span>
          </a>
        </li>
          
        <li>
          <a href="{{route('superadmin.wilayah.index')}}">
              <i class="bi bi-circle"></i><span>Wilayah</span>
          </a>
        </li>

        <li>
          <a href="{{route('superadmin.sumber.index')}}">
              <i class="bi bi-circle"></i><span>Sumber</span>
          </a>
        </li>

        <li>
            <a href="{{route('superadmin.akunuser.index')}}">
              <i class="bi bi-circle"></i><span>Akun User</span>
            </a>
        </li>

        </ul>
      </li><!-- End Components Nav -->

@endif

@if (in_array('ManajemenPosisi', $permissions))
      <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="">
        <i class="bi-person-lines-fill"></i>
          <span>Manajemen Posisi</span>
        </a>
      </li> -->
@endif
      
@if (in_array('Kandidat', $permissions))
      <li class="nav-item">
        <a class="nav-link collapsed" href="{{route('superadmin.kandidat.index')}}">
        <i class="bi-people-fill"></i>
          <span>Kandidat</span>
        </a>
      </li>
@endif

@php
    
$roleid = auth()->user()->role_id;

if ($roleid == 2 || $roleid == 3) {
            $userId = auth()->id();
            $detailPosisi = \App\Models\DetailPosisi::where('user_id', $userId)->get();
        
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
            $query = \App\Models\Kandidat::query();

       
        
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
            $kandidat = $query->get();
        

            $jumlahBelumProses = $kandidat->where('status_hire', 'Belum Diproses')->count();
            $jumlahBelumProsesafter = $kandidat->where('status_hire', 'Belum Diproses Sudah Dijadwalkan')->count();
    $jumlahPsikotes = $kandidat->where('status_hire', 'Psikotes')->count();
    $jumlahPsikotesafter = $kandidat->where('status_hire', 'Psikotes Sudah Dijadwalkan')->count();
    $jumlahInterviewHR = $kandidat->where('status_hire', 'Interview HR')->count();
    $jumlahInterviewHRafter = $kandidat->where('status_hire', 'Interview HR Sudah Dijadwalkan')->count();
    $jumlahInterviewUser = $kandidat->where('status_hire', 'Interview User')->count();
    $jumlahInterviewUserafter = $kandidat->where('status_hire', 'Interview User Sudah Dijadwalkan')->count();
    $jumlahInterviewUserdua = $kandidat->where('status_hire', 'Interview User 2')->count();
    $jumlahInterviewUserafterdua = $kandidat->where('status_hire', 'Interview User 2 Sudah Dijadwalkan')->count();
    $jumlahInterviewUsertiga = $kandidat->where('status_hire', 'Interview User 3')->count();
    $jumlahInterviewUseraftertiga = $kandidat->where('status_hire', 'Interview User 3 Sudah Dijadwalkan')->count();
    $jumlahTraining = $kandidat->where('status_hire', 'Training')->count();
    $jumlahTrainingafter = $kandidat->where('status_hire', 'Training Sudah Dijadwalkan')->count();
    $jumlahTandem = $kandidat->where('status_hire', 'Tandem')->count();
    $jumlahTandemafter = $kandidat->where('status_hire', 'Tandem Sudah Dijadwalkan')->count();
    $jumlahLolos = $kandidat->where('status_hire', 'Join')->count();
    $jumlahTidakLolos = $kandidat->where('status_hire', 'Tidak Lolos')->count();
    $jumlahSimpan = $kandidat->where('status_hire', 'Simpan Kandidat')->count();
    $jumlahSimpanafter = $kandidat->where('status_hire', 'Simpan Kandidat Sudah Dijadwalkan')->count();

    $jumlahStopProses = $kandidat->where('status_hire', 'Stop Proses')->count();

         } else {

    $jumlahBelumProses = \App\Models\Kandidat::where('status_hire', 'Belum Diproses')->count();
    $jumlahBelumProsesafter = \App\Models\Kandidat::where('status_hire', 'Belum Diproses Sudah Dijadwalkan')->count();
    $jumlahPsikotes = \App\Models\Kandidat::where('status_hire', 'Psikotes')->count();
    $jumlahPsikotesafter = \App\Models\Kandidat::where('status_hire', 'Psikotes Sudah Dijadwalkan')->count();
    $jumlahInterviewHR = \App\Models\Kandidat::where('status_hire', 'Interview HR')->count();
    $jumlahInterviewHRafter = \App\Models\Kandidat::where('status_hire', 'Interview HR Sudah Dijadwalkan')->count();
    $jumlahInterviewUser = \App\Models\Kandidat::where('status_hire', 'Interview User')->count();
    $jumlahInterviewUserafter = \App\Models\Kandidat::where('status_hire', 'Interview User Sudah Dijadwalkan')->count();
    $jumlahInterviewUserdua = \App\Models\Kandidat::where('status_hire', 'Interview User 2')->count();
    $jumlahInterviewUserafterdua = \App\Models\Kandidat::where('status_hire', 'Interview User 2 Sudah Dijadwalkan')->count();
    $jumlahInterviewUsertiga = \App\Models\Kandidat::where('status_hire', 'Interview User 3')->count();
    $jumlahInterviewUseraftertiga = \App\Models\Kandidat::where('status_hire', 'Interview User 3 Sudah Dijadwalkan')->count();
    $jumlahTraining = \App\Models\Kandidat::where('status_hire', 'Training')->count();
    $jumlahTrainingafter = \App\Models\Kandidat::where('status_hire', 'Training Sudah Dijadwalkan')->count();
    $jumlahTandem = \App\Models\Kandidat::where('status_hire', 'Tandem')->count();
    $jumlahTandemafter = \App\Models\Kandidat::where('status_hire', 'Tandem Sudah Dijadwalkan')->count();
    $jumlahLolos = \App\Models\Kandidat::where('status_hire', 'Join')->count();
    $jumlahTidakLolos = \App\Models\Kandidat::where('status_hire', 'Tidak Lolos')->count();
    $jumlahSimpan = \App\Models\Kandidat::where('status_hire', 'Simpan Kandidat')->count();
    $jumlahSimpanafter = \App\Models\Kandidat::where('status_hire', 'Simpan Kandidat Sudah Dijadwalkan')->count();
    $jumlahStopProses = \App\Models\Kandidat::where('status_hire', 'Stop Proses')->count();
  }

@endphp
      
@if (in_array('ProsesRekrutmen', $permissions))
      
<li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-bar-chart"></i>
        <span>Proses Rekrutmen</span>
        <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">

    @if (in_array('BelumProses', $permissions))
    <li>
        <!-- Parent Menu Item with Submenu Collapse -->
        <a data-bs-toggle="collapse" href="#belumProsesSubmenu" aria-expanded="false" aria-controls="belumProsesSubmenu">
         
            <span>Kandidat Baru</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <!-- Submenu for Belum Proses -->
        <ul id="belumProsesSubmenu" class="nav-content collapse">
            <li>
                <a href="{{route('superadmin.belumproses.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Belum Dijadwalkan</span>
                    @if ($jumlahBelumProses > 0)
                    <span class="badge bg-danger">{{$jumlahBelumProses}}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{route('superadmin.belumprosesafter.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Sudah Dijadwalkan</span>
                    @if ($jumlahBelumProsesafter > 0)
                    <span class="badge bg-danger">{{$jumlahBelumProsesafter}}</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>
    @endif

    @if (in_array('Psikotes', $permissions))
    <li>
        <!-- Parent Menu Item with Submenu Collapse -->
        <a data-bs-toggle="collapse" href="#psikotesSubmenu" aria-expanded="false" aria-controls="psikotesSubmenu">
         
            <span>Psikotes</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <!-- Submenu for Belum Proses -->
        <ul id="psikotesSubmenu" class="nav-content collapse">
            <li>
                <a href="{{route('superadmin.psikotes.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Belum Dijadwalkan</span>
                    @if ($jumlahPsikotes > 0)
                    <span class="badge bg-danger">{{$jumlahPsikotes}}</span>
                    @endif
                </a>
            </li>
            <li>
            <a href="{{route('superadmin.psikotesesafter.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Sudah Dijadwalkan</span>
                    @if ($jumlahPsikotesafter > 0)
                    <span class="badge bg-danger">{{$jumlahPsikotesafter}}</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>
    @endif

    @if (in_array('InterviewHR', $permissions))
    <li>
        <!-- Parent Menu Item with Submenu Collapse -->
        <a data-bs-toggle="collapse" href="#itvhrSubmenu" aria-expanded="false" aria-controls="itvhrSubmenu">
         
            <span>Interview HR</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <!-- Submenu for Belum Proses -->
        <ul id="itvhrSubmenu" class="nav-content collapse">
            <li>
                <a href="{{route('superadmin.itvhr.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Belum Dijadwalkan</span>
                    @if ($jumlahInterviewHR > 0)
                    <span class="badge bg-danger">{{$jumlahInterviewHR}}</span>
                    @endif
                </a>
            </li>
            <li>
            <a href="{{route('superadmin.itvhrafter.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Sudah Dijadwalkan</span>
                    @if ($jumlahInterviewHRafter > 0)
                    <span class="badge bg-danger">{{$jumlahInterviewHRafter}}</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>
    @endif

    @if (in_array('InterviewUser', $permissions))
    <li>
        <!-- Parent Menu Item with Submenu Collapse -->
        <a data-bs-toggle="collapse" href="#itvuserSubmenu" aria-expanded="false" aria-controls="itvuserSubmenu">
         
            <span>Interview User</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <!-- Submenu for Belum Proses -->
        <ul id="itvuserSubmenu" class="nav-content collapse">
            <li>
                <a href="{{route('superadmin.itvuser.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Belum Dijadwalkan</span>
                    @if ($jumlahInterviewUser > 0)
                    <span class="badge bg-danger">{{$jumlahInterviewUser}}</span>
                    @endif
                </a>
            </li>
            <li>
            <a href="{{route('superadmin.itvuserafter.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Sudah Dijadwalkan</span>
                    @if ($jumlahInterviewUserafter > 0)
                    <span class="badge bg-danger">{{$jumlahInterviewUserafter}}</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>
    @endif


    @if (in_array('InterviewUserdua', $permissions))
    <li>
        <!-- Parent Menu Item with Submenu Collapse -->
        <a data-bs-toggle="collapse" href="#itvuserduaSubmenu" aria-expanded="false" aria-controls="itvuserduaSubmenu">
         
            <span>Interview User 2</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <!-- Submenu for Belum Proses -->
        <ul id="itvuserduaSubmenu" class="nav-content collapse">
            <li>
                <a href="{{route('superadmin.itvuserdua.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Belum Dijadwalkan</span>
                    @if ($jumlahInterviewUserdua > 0)
                    <span class="badge bg-danger">{{$jumlahInterviewUserdua}}</span>
                    @endif
                </a>
            </li>
            <li>
            <a href="{{route('superadmin.itvuserduaafter.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Sudah Dijadwalkan</span>
                    @if ($jumlahInterviewUserafterdua > 0)
                    <span class="badge bg-danger">{{$jumlahInterviewUserafterdua}}</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>
    @endif

    @if (in_array('InterviewUsertiga', $permissions))
    <li>
        <!-- Parent Menu Item with Submenu Collapse -->
        <a data-bs-toggle="collapse" href="#itvusertigaSubmenu" aria-expanded="false" aria-controls="itvusertigaSubmenu">
         
            <span>Interview User 3</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <!-- Submenu for Belum Proses -->
        <ul id="itvusertigaSubmenu" class="nav-content collapse">
            <li>
                <a href="{{route('superadmin.itvusertiga.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Belum Dijadwalkan</span>
                    @if ($jumlahInterviewUsertiga > 0)
                    <span class="badge bg-danger">{{$jumlahInterviewUsertiga}}</span>
                    @endif
                </a>
            </li>
            <li>
            <a href="{{route('superadmin.itvusertigaafter.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Sudah Dijadwalkan</span>
                    @if ($jumlahInterviewUseraftertiga > 0)
                    <span class="badge bg-danger">{{$jumlahInterviewUseraftertiga}}</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>
    @endif

    @if (in_array('Training', $permissions))
    <li>
        <!-- Parent Menu Item with Submenu Collapse -->
        <a data-bs-toggle="collapse" href="#trainingSubmenu" aria-expanded="false" aria-controls="trainingSubmenu">
         
            <span>Training</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <!-- Submenu for Belum Proses -->
        <ul id="trainingSubmenu" class="nav-content collapse">
            <li>
                <a href="{{route('superadmin.training.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Belum Dijadwalkan</span>
                    @if ($jumlahTraining > 0)
                    <span class="badge bg-danger">{{$jumlahTraining}}</span>
                    @endif
                </a>
            </li>
            <li>
            <a href="{{route('superadmin.trainingafter.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Sudah Dijadwalkan</span>
                    @if ($jumlahTrainingafter > 0)
                    <span class="badge bg-danger">{{$jumlahTrainingafter}}</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>
    @endif

 
    @if (in_array('Tandem', $permissions))
    <li>
        <!-- Parent Menu Item with Submenu Collapse -->
        <a data-bs-toggle="collapse" href="#tandemSubmenu" aria-expanded="false" aria-controls="tandemSubmenu">
         
            <span>Tandem</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <!-- Submenu for Belum Proses -->
        <ul id="tandemSubmenu" class="nav-content collapse">
            <li>
                <a href="{{route('superadmin.tandem.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Belum Dijadwalkan</span>
                    @if ($jumlahTandem > 0)
                    <span class="badge bg-danger">{{$jumlahTandem}}</span>
                    @endif
                </a>
            </li>
            <li>
            <a href="{{route('superadmin.tandemafter.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Sudah Dijadwalkan</span>
                    @if ($jumlahTandemafter > 0)
                    <span class="badge bg-danger">{{$jumlahTandemafter}}</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>
    @endif
   
    @if (in_array('Lolos', $permissions))
        <li>
            <a href="{{route('superadmin.lolos.index')}}">
                <span>Join</span>
                @if ($jumlahLolos > 0)
                <span class="badge bg-danger">{{$jumlahLolos}}</span> <!-- Badge untuk Lolos -->
                @endif
            </a>
        </li>
        @endif

        @if (in_array('StopProses', $permissions))
        <li>
            <a href="{{route('superadmin.stopproses.index')}}">
                <span>Stop Proses</span>
                @if ($jumlahStopProses > 0)
                <span class="badge bg-danger">{{$jumlahStopProses}}</span> <!-- Badge untuk Tidak Lolos -->
                @endif
            </a>
        </li>
        @endif
        @if (in_array('TidakLolos', $permissions))
        <li>
            <a href="{{route('superadmin.tidaklolos.index')}}">
                <span>Tidak Lolos</span>
                @if ($jumlahTidakLolos > 0)
                <span class="badge bg-danger">{{$jumlahTidakLolos}}</span> <!-- Badge untuk Tidak Lolos -->
                @endif
            </a>
        </li>
        @endif
       
        @if (in_array('SimpanKandidat', $permissions))
        <li>
        <!-- Parent Menu Item with Submenu Collapse -->
        <a data-bs-toggle="collapse" href="#saveSubmenu" aria-expanded="false" aria-controls="saveSubmenu">
         
            <span>Kandidat Tersimpan</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <!-- Submenu for Belum Proses -->
        <ul id="saveSubmenu" class="nav-content collapse">
            <li>
                <a href="{{route('superadmin.save.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Belum Dijadwalkan</span>
                    @if ($jumlahSimpan > 0)
                    <span class="badge bg-danger">{{$jumlahSimpan}}</span>
                    @endif
                </a>
            </li>
            <li>
            <a href="{{route('superadmin.saveafter.index')}}">
                    <i class="bi bi-circle"></i>
                    <span>Sudah Dijadwalkan</span>
                    @if ($jumlahSimpanafter > 0)
                    <span class="badge bg-danger">{{$jumlahSimpanafter}}</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>
    @endif
        

    </ul>
</li>
@endif


@if (in_array('Blacklist', $permissions))

<li class="nav-item">
  <a class="nav-link collapsed" href="{{route('superadmin.blacklist.index')}}">
  <i class="bi bi-ban-fill"></i>
    <span>Blacklist</span>
  </a>
</li>
@endif


    @if (in_array('DataKonfirm', $permissions))

<li class="nav-item">
  <a class="nav-link collapsed" href="{{route('superadmin.masterkonfirm.index')}}">
    <i class="bi bi-check-circle-fill"></i>
    <span>Data Konfirm Pemanggilan</span>
  </a>
</li>
@endif


@if (in_array('LaporanPerformance', $permissions))
<li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#icons-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-card-text"></i><span>Data Input</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="icons-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">

        <li>
            <a href="{{route(name: 'superadmin.targetjumlah.index')}}">
              <i class="bi bi-circle"></i><span>Target MPP & Jumlah Mitra Existing</span>
            </a>
        </li>
          <li>
            <a href="{{route(name: 'superadmin.masteraktif.index')}}">
              <i class="bi bi-circle"></i><span>Karyawan Aktif</span>
            </a>
          </li>
          <li>
            <a href="{{route(name: 'superadmin.mastertidakaktif.index')}}">
              <i class="bi bi-circle"></i><span>Karyawan Tidak Aktif</span>
            </a>
          </li>
          <!-- <li>
            <a href="{{route(name: 'superadmin.mastertrainingtandem.index')}}">
              <i class="bi bi-circle"></i><span>Karyawan Training & Tandem</span>
            </a>
          </li> -->
       
       
        </ul>
      </li><!-- End Icons Nav -->
@endif


@if (in_array('Kandidat', $permissions))
      <li class="nav-item">
        <a class="nav-link collapsed" href="{{route(name: 'superadmin.laporanperformance.index')}}">
        <i class="bi bi bi-journal-text"></i>
          <span>Laporan Performance</span>
        </a>
      </li>
@endif


  </ul>
</aside><!-- End Sidebar-->

