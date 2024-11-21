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
    <a class="nav-link collapsed {{ request()->routeIs('trainingtrainer') ? 'active' : '' }}" href="{{ route('trainingtrainer') }}">
     
      <span>Training</span>
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link collapsed {{ request()->routeIs('tandemtrainer') ? 'active' : '' }}" href="{{ route('tandemtrainer') }}">
     
      <span>Tandem</span>
    </a>
  </li>

@endif


@if ($roleid != 3)
  <li class="nav-item">
    <a class="nav-link collapsed {{ request()->routeIs('superadmindashboard') ? 'active' : '' }}" href="{{ route('superadmindashboard') }}">
      <i class="bi bi-grid"></i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->
@endif



@if (in_array('MasterData', $permissions))
  <li class="nav-item">
    <a class="nav-link collapsed {{ request()->routeIs('superadmin.posisi.index') || request()->routeIs('superadmin.wilayah.index') || request()->routeIs('superadmin.sumber.index') || request()->routeIs('superadmin.akunuser.index') ? 'active' : '' }}" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Master Data</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>

    <ul id="components-nav" class="nav-content collapse {{ request()->routeIs('superadmin.posisi.index') || request()->routeIs('superadmin.wilayah.index') || request()->routeIs('superadmin.sumber.index') || request()->routeIs('superadmin.akunuser.index') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
    
    <li>
        <a href="{{route('superadmin.akunuser.index')}}" class="{{ request()->routeIs('superadmin.akunuser.index') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Akun User</span>
        </a>
      </li>

    <li>
        <a href="{{route('superadmin.posisi.index')}}" class="{{ request()->routeIs('superadmin.posisi.index') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Posisi</span>
        </a>
      </li>

      <li>
        <a href="{{route('superadmin.wilayah.index')}}" class="{{ request()->routeIs('superadmin.wilayah.index') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Wilayah</span>
        </a>
      </li>

      <li>
        <a href="{{route('superadmin.sumber.index')}}" class="{{ request()->routeIs('superadmin.sumber.index') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Sumber</span>
        </a>
      </li>

      <li>
        <a href="{{route('superadmin.abm.index')}}" class="{{ request()->routeIs('superadmin.abm.index') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>ABM</span>
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
  <a class="nav-link collapsed {{ request()->routeIs('superadmin.kandidat.index') || request()->routeIs('superadmin.kandidat.create') || request()->is('superadminshowkandidat/*') ? 'active' : '' }}" href="{{ route('superadmin.kandidat.index') }}">
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

            $new = \App\Models\LogTahapan::query();
       
        
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

            $new->where(function ($query) use ($assignedPosisiIds, $assignedWilayahIds) {
                foreach ($assignedPosisiIds as $posisiId) {
                    foreach ($assignedWilayahIds as $wilayahId) {
                        $query->orWhere(function ($query) use ($posisiId, $wilayahId) {
                            $query->where('posisi_id', $posisiId)
                                  ->where('wilayah_id', $wilayahId);
                        });
                    }
                }
            });

            $logtahapan = $new->get();
            $kandidat = $query->get();
        

    $jumlahBelumProses = $kandidat->where('status_hire', 'Belum Diproses')->count();
    $jumlahBelumProsesafter = $kandidat->where('status_hire', 'Belum Diproses Sudah Dijadwalkan')->count();
    $jumlahPsikotes = $kandidat->where('status_hire', 'Psikotes')->count();
    $jumlahPsikotesafter = $logtahapan->where('status_tahapan', 'Psikotes')->where('hasil_status','Dijadwalkan')->count();
    $jumlahInterviewHR = $kandidat->where('status_hire', 'Interview HR')->count();
    $jumlahInterviewHRafter = $logtahapan->where('status_tahapan', 'Interview HR')->where('hasil_status','Dijadwalkan')->count();
    $jumlahInterviewUser = $kandidat->where('status_hire', 'Interview User')->count();
    $jumlahInterviewUserafter =  $logtahapan->where('status_tahapan', 'Interview User')->where('hasil_status','Dijadwalkan')->count();
    $jumlahInterviewUserdua = $kandidat->where('status_hire', 'Interview User 2')->count();
    $jumlahInterviewUserafterdua =  $logtahapan->where('status_tahapan', 'Interview User 2')->where('hasil_status','Dijadwalkan')->count();
    $jumlahInterviewUsertiga = $kandidat->where('status_hire', 'Interview User 3')->count();
    $jumlahInterviewUseraftertiga = $logtahapan->where('status_tahapan', 'Interview User 3')->where('hasil_status','Dijadwalkan')->count();
    $jumlahTraining = $kandidat->where('status_hire', 'Training')->count();
    $jumlahTrainingafter = $logtahapan->where('status_tahapan', 'Training')->where('hasil_status','Dijadwalkan')->count();
    $jumlahTandem = $kandidat->where('status_hire', 'Tandem')->count();
    $jumlahTandemafter = $logtahapan->where('status_tahapan', 'Tandem')->where('hasil_status','Dijadwalkan')->count();
    $jumlahLolos = $kandidat->where('status_hire', 'Proses PKM')->count();
    $jumlahLolosafter = $logtahapan->where('status_tahapan', 'Proses PKM')->where('hasil_status','Dijadwalkan')->count();
    $jumlahTidakLolos = $kandidat->where('status_hire', 'Tidak Lolos')->count();
    $jumlahSimpan = $kandidat->where('status_hire', 'Simpan Kandidat')->count();
    $jumlahSimpanafter = $kandidat->where('status_hire', 'Simpan Kandidat Sudah Dijadwalkan')->count();

    $jumlahStopProses = $kandidat->where('status_hire', 'Stop Proses')->count();

         } else {

    $jumlahBelumProses = \App\Models\Kandidat::where('status_hire', 'Belum Diproses')->count();
    $jumlahBelumProsesafter = \App\Models\Kandidat::where('status_hire', 'Belum Diproses Sudah Dijadwalkan')->count();
    $jumlahPsikotes = \App\Models\Kandidat::where('status_hire', 'Psikotes')->count();
    $jumlahPsikotesafter = \App\Models\LogTahapan::where('status_tahapan', 'Psikotes')->where('hasil_status','Dijadwalkan')->count();
    $jumlahInterviewHR = \App\Models\Kandidat::where('status_hire', 'Interview HR')->count();
    $jumlahInterviewHRafter = \App\Models\LogTahapan::where('status_tahapan', 'Interview HR')->where('hasil_status','Dijadwalkan')->count();
    $jumlahInterviewUser = \App\Models\Kandidat::where('status_hire', 'Interview User')->count();
    $jumlahInterviewUserafter = \App\Models\LogTahapan::where('status_tahapan', 'Interview User')->where('hasil_status','Dijadwalkan')->count();
    $jumlahInterviewUserdua = \App\Models\Kandidat::where('status_hire', 'Interview User 2')->count();
    $jumlahInterviewUserafterdua = \App\Models\LogTahapan::where('status_tahapan', 'Interview User 2')->where('hasil_status','Dijadwalkan')->count();
    $jumlahInterviewUsertiga = \App\Models\Kandidat::where('status_hire', 'Interview User 3')->count();
    $jumlahInterviewUseraftertiga = \App\Models\LogTahapan::where('status_tahapan', 'Interview User 3')->where('hasil_status','Dijadwalkan')->count();
    $jumlahTraining = \App\Models\Kandidat::where('status_hire', 'Training')->count();
    $jumlahTrainingafter = \App\Models\LogTahapan::where('status_tahapan', 'Training')->where('hasil_status','Dijadwalkan')->count();
    $jumlahTandem = \App\Models\Kandidat::where('status_hire', 'Tandem')->count();
    $jumlahTandemafter = \App\Models\LogTahapan::where('status_tahapan', 'Tandem')->where('hasil_status','Dijadwalkan')->count();
    $jumlahLolos = \App\Models\Kandidat::where('status_hire', 'Proses PKM')->count();
    $jumlahLolosafter = \App\Models\LogTahapan::where('status_tahapan', 'Proses PKM')->where('hasil_status','Dijadwalkan')->count();
    $jumlahTidakLolos = \App\Models\Kandidat::where('status_hire', 'Tidak Lolos')->count();
    $jumlahSimpan = \App\Models\Kandidat::where('status_hire', 'Simpan Kandidat')->count();
    $jumlahSimpanafter = \App\Models\Kandidat::where('status_hire', 'Simpan Kandidat Sudah Dijadwalkan')->count();
    $jumlahStopProses = \App\Models\Kandidat::where('status_hire', 'Stop Proses')->count();
  }

@endphp
      
      @if (in_array('ProsesRekrutmen', $permissions))
  <li class="nav-item">
    <a class="nav-link collapsed {{ request()->routeIs('superadmin.belumproses.index') || 
                                   request()->routeIs('superadmin.psikotesesafter.index') || 
                                   request()->routeIs('superadmin.psikotes.index') || 
                                   request()->routeIs('superadmin.itvhrafter.index') || 
                                   request()->routeIs('superadmin.itvhr.index') || 
                                   request()->routeIs('superadmin.itvuserafter.index') || 
                                   request()->routeIs('superadmin.itvuser.index') || 
                                   request()->routeIs('superadmin.itvuserduaafter.index') || 
                                   request()->routeIs('superadmin.itvuserdua.index') || 
                                   request()->routeIs('superadmin.itvusertigaafter.index') || 
                                   request()->routeIs('superadmin.itvusertiga.index') || 
                                   request()->routeIs('superadmin.trainingafter.index') || 
                                   request()->routeIs('superadmin.training.index') || 
                                   request()->routeIs('superadmin.tandemafter.index') || 
                                   request()->routeIs('superadmin.tandem.index') || 
                                   request()->routeIs('superadmin.lolosafter.index') || 
                                   request()->routeIs('superadmin.lolos.index') || 
                                   request()->routeIs('superadmin.stopproses.index') || 
                                   request()->routeIs('superadmin.tidaklolos.index') || 
                                   request()->routeIs('superadmin.save.index') ? 'active' : '' }}" 
       data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-bar-chart"></i>
      <span>Proses Rekrutmen</span>
      <i class="bi bi-chevron-down ms-auto"></i>
    </a>

    <ul id="forms-nav" class="nav-content collapse {{ request()->routeIs('superadmin.belumproses.index') || 
                                                     request()->routeIs('superadmin.psikotesesafter.index') || 
                                                     request()->routeIs('superadmin.psikotes.index') || 
                                                     request()->routeIs('superadmin.itvhrafter.index') || 
                                                     request()->routeIs('superadmin.itvhr.index') || 
                                                     request()->routeIs('superadmin.itvuserafter.index') || 
                                                     request()->routeIs('superadmin.itvuser.index') || 
                                                     request()->routeIs('superadmin.itvuserduaafter.index') || 
                                                     request()->routeIs('superadmin.itvuserdua.index') || 
                                                     request()->routeIs('superadmin.itvusertigaafter.index') || 
                                                     request()->routeIs('superadmin.itvusertiga.index') || 
                                                     request()->routeIs('superadmin.trainingafter.index') || 
                                                     request()->routeIs('superadmin.training.index') || 
                                                     request()->routeIs('superadmin.tandemafter.index') || 
                                                     request()->routeIs('superadmin.tandem.index') || 
                                                     request()->routeIs('superadmin.lolosafter.index') || 
                                                     request()->routeIs('superadmin.lolos.index') || 
                                                     request()->routeIs('superadmin.stopproses.index') || 
                                                     request()->routeIs('superadmin.tidaklolos.index') || 
                                                     request()->routeIs('superadmin.save.index') ? 'show' : '' }}" 
        data-bs-parent="#sidebar-nav">
      
      @if (in_array('BelumProses', $permissions))
        <li>
          <a href="{{route('superadmin.belumproses.index')}}" class="{{ request()->routeIs('superadmin.belumproses.index') || request()->routeIs('superadmin.penjadwalan')  ? 'active' : '' }}">
            <span>Kandidat Baru</span>
            @if ($jumlahBelumProses > 0)
              <span class="badge bg-danger">{{$jumlahBelumProses}}</span>
            @endif
          </a>
        </li>
      @endif

      @if (in_array('Psikotes', $permissions))
        <li>
          <a data-bs-toggle="collapse" href="#psikotesSubmenu" aria-expanded="false" aria-controls="psikotesSubmenu" class="{{ request()->routeIs('superadmin.psikotesesafter.index') || request()->routeIs('superadmin.psikotes.index') ? 'active' : '' }}">
            <span>Psikotes</span>
            <i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="psikotesSubmenu" class="nav-content collapse {{ request()->routeIs('superadmin.psikotesesafter.index') || request()->routeIs('superadmin.psikotes.index') ? 'show' : '' }}">
            <li>
              <a href="{{route('superadmin.psikotesesafter.index')}}" class="{{ request()->routeIs('superadmin.psikotesesafter.index') ? 'active' : '' }}">
                <i class="bi bi-circle"></i>
                <span>Sedang Dijadwalkan</span>
                @if ($jumlahPsikotesafter > 0)
                  <span class="badge bg-danger">{{$jumlahPsikotesafter}}</span>
                @endif
              </a>
            </li>
            <li>
              <a href="{{route('superadmin.psikotes.index')}}" class="{{ request()->routeIs('superadmin.psikotes.index') ? 'active' : '' }}">
                <i class="bi bi-circle"></i>
                <span>Lolos</span>
                @if ($jumlahPsikotes > 0)
                  <span class="badge bg-danger">{{$jumlahPsikotes}}</span>
                @endif
              </a>
            </li>
          </ul>
        </li>
      @endif

      @if (in_array('InterviewHR', $permissions))
        <li>
          <a data-bs-toggle="collapse" href="#itvhrSubmenu" aria-expanded="false" aria-controls="itvhrSubmenu" class="{{ request()->routeIs('superadmin.itvhrafter.index') || request()->routeIs('superadmin.itvhr.index') ? 'active' : '' }}">
            <span>Interview HR</span>
            <i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="itvhrSubmenu" class="nav-content collapse {{ request()->routeIs('superadmin.itvhrafter.index') || request()->routeIs('superadmin.itvhr.index') ? 'show' : '' }}">
            <li>
              <a href="{{route('superadmin.itvhrafter.index')}}" class="{{ request()->routeIs('superadmin.itvhrafter.index') ? 'active' : '' }}">
                <i class="bi bi-circle"></i>
                <span>Sedang Dijadwalkan</span>
                @if ($jumlahInterviewHRafter > 0)
                  <span class="badge bg-danger">{{$jumlahInterviewHRafter}}</span>
                @endif
              </a>
            </li>
            <li>
              <a href="{{route('superadmin.itvhr.index')}}" class="{{ request()->routeIs('superadmin.itvhr.index') ? 'active' : '' }}">
                <i class="bi bi-circle"></i>
                <span>Lolos</span>
                @if ($jumlahInterviewHR > 0)
                  <span class="badge bg-danger">{{$jumlahInterviewHR}}</span>
                @endif
              </a>
            </li>
          </ul>
        </li>
      @endif

      @if (in_array('InterviewUser', $permissions))
        <li>
          <a data-bs-toggle="collapse" href="#itvuserSubmenu" aria-expanded="false" aria-controls="itvuserSubmenu" class="{{ request()->routeIs('superadmin.itvuserafter.index') || request()->routeIs('superadmin.itvuser.index') ? 'active' : '' }}">
            <span>Interview User</span>
            <i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="itvuserSubmenu" class="nav-content collapse {{ request()->routeIs('superadmin.itvuserafter.index') || request()->routeIs('superadmin.itvuser.index') ? 'show' : '' }}">
            <li>
              <a href="{{route('superadmin.itvuserafter.index')}}" class="{{ request()->routeIs('superadmin.itvuserafter.index') ? 'active' : '' }}">
                <i class="bi bi-circle"></i>
                <span>Sedang Dijadwalkan</span>
                @if ($jumlahInterviewUserafter > 0)
                  <span class="badge bg-danger">{{$jumlahInterviewUserafter}}</span>
                @endif
              </a>
            </li>
            <li>
              <a href="{{route('superadmin.itvuser.index')}}" class="{{ request()->routeIs('superadmin.itvuser.index') ? 'active' : '' }}">
                <i class="bi bi-circle"></i>
                <span>Lolos</span>
                @if ($jumlahInterviewUser > 0)
                  <span class="badge bg-danger">{{$jumlahInterviewUser}}</span>
                @endif
              </a>
            </li>
          </ul>
        </li>
      @endif


      @if (in_array('InterviewUserdua', $permissions))
  <li>
    <a data-bs-toggle="collapse" href="#itvuserduaSubmenu" aria-expanded="false" aria-controls="itvuserduaSubmenu" class="{{ request()->routeIs('superadmin.itvuserduaafter.index') || request()->routeIs('superadmin.itvuserdua.index') ? 'active' : '' }}">
      <span>Interview User Dua</span>
      <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="itvuserduaSubmenu" class="nav-content collapse {{ request()->routeIs('superadmin.itvuserduaafter.index') || request()->routeIs('superadmin.itvuserdua.index') ? 'show' : '' }}">
      <li>
        <a href="{{route('superadmin.itvuserduaafter.index')}}" class="{{ request()->routeIs('superadmin.itvuserduaafter.index') ? 'active' : '' }}">
        <i class="bi bi-circle"></i>
                            <span>Sedang Dijadwalkan</span>
                            @if ($jumlahInterviewUserafterdua > 0)
                            <span class="badge bg-danger">{{ $jumlahInterviewUserafterdua }}</span>
                            @endif

        </a>
      </li>
      <li>
        <a href="{{route('superadmin.itvuserdua.index')}}" class="{{ request()->routeIs('superadmin.itvuserdua.index') ? 'active' : '' }}">
        <i class="bi bi-circle"></i>
                            <span>Lolos</span>
                            @if ($jumlahInterviewUserdua > 0)
                            <span class="badge bg-danger">{{ $jumlahInterviewUserdua }}</span>
                            @endif

        </a>
      </li>
    </ul>
  </li>
@endif


@if (in_array('InterviewUsertiga', $permissions))
  <li>
    <a data-bs-toggle="collapse" href="#itvusertigaSubmenu" aria-expanded="false" aria-controls="itvusertigaSubmenu" class="{{ request()->routeIs('superadmin.itvusertigaafter.index') || request()->routeIs('superadmin.itvusertiga.index') ? 'active' : '' }}">
      <span>Interview User Tiga</span>
      <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="itvusertigaSubmenu" class="nav-content collapse {{ request()->routeIs('superadmin.itvusertigaafter.index') || request()->routeIs('superadmin.itvusertiga.index') ? 'show' : '' }}">
      <li>
        <a href="{{route('superadmin.itvusertigaafter.index')}}" class="{{ request()->routeIs('superadmin.itvusertigaafter.index') ? 'active' : '' }}">
        <i class="bi bi-circle"></i>
                            <span>Sedang Dijadwalkan</span>
                            @if ($jumlahInterviewUseraftertiga > 0)
                            <span class="badge bg-danger">{{ $jumlahInterviewUseraftertiga }}</span>
                            @endif

        </a>
      </li>
      <li>
        <a href="{{route('superadmin.itvusertiga.index')}}" class="{{ request()->routeIs('superadmin.itvusertiga.index') ? 'active' : '' }}">
        <i class="bi bi-circle"></i>
                            <span>Lolos</span>
                            @if ($jumlahInterviewUsertiga > 0)
                            <span class="badge bg-danger">{{ $jumlahInterviewUsertiga }}</span>
                            @endif

        </a>
      </li>
    </ul>
  </li>
@endif
@if (in_array('Training', $permissions))
  <li>
    <a data-bs-toggle="collapse" href="#trainingSubmenu" aria-expanded="false" aria-controls="trainingSubmenu" class="{{ request()->routeIs('superadmin.trainingafter.index') || request()->routeIs('superadmin.training.index') ? 'active' : '' }}">
      <span>Training</span>
      <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="trainingSubmenu" class="nav-content collapse {{ request()->routeIs('superadmin.trainingafter.index') || request()->routeIs('superadmin.training.index') ? 'show' : '' }}">
      <li>
        <a href="{{route('superadmin.trainingafter.index')}}" class="{{ request()->routeIs('superadmin.trainingafter.index') ? 'active' : '' }}">
        <i class="bi bi-circle"></i>
                            <span>Sedang Dijadwalkan</span>
                            @if ($jumlahTrainingafter > 0)
                            <span class="badge bg-danger">{{ $jumlahTrainingafter }}</span>
                            @endif

        </a>
      </li>
      <li>
        <a href="{{route('superadmin.training.index')}}" class="{{ request()->routeIs('superadmin.training.index') ? 'active' : '' }}">
        <i class="bi bi-circle"></i>
                            <span>Lolos</span>
                            @if ($jumlahTraining > 0)
                            <span class="badge bg-danger">{{ $jumlahTraining }}</span>
                            @endif

        </a>
      </li>
    </ul>
  </li>
@endif
      <!-- Add more submenus for other sections like InterviewUserdua, InterviewUsertiga, etc. in a similar way -->
      @if (in_array('Tandem', $permissions))
  <li>
    <a data-bs-toggle="collapse" href="#tandemSubmenu" aria-expanded="false" aria-controls="tandemSubmenu" class="{{ request()->routeIs('superadmin.tandemafter.index') || request()->routeIs('superadmin.tandem.index') ? 'active' : '' }}">
      <span>Tandem</span>
      <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="tandemSubmenu" class="nav-content collapse {{ request()->routeIs('superadmin.tandemafter.index') || request()->routeIs('superadmin.tandem.index') ? 'show' : '' }}">
      <li>
        <a href="{{route('superadmin.tandemafter.index')}}" class="{{ request()->routeIs('superadmin.tandemafter.index') ? 'active' : '' }}">
        <i class="bi bi-circle"></i>
                            <span>Sedang Dijadwalkan</span>
                            @if ($jumlahTandemafter > 0)
                            <span class="badge bg-danger">{{ $jumlahTandemafter }}</span>
                            @endif

        </a>
      </li>
      <li>
        <a href="{{route('superadmin.tandem.index')}}" class="{{ request()->routeIs('superadmin.tandem.index') ? 'active' : '' }}">
        <i class="bi bi-circle"></i>
                            <span>Lolos</span>
                            @if ($jumlahTandem > 0)
                            <span class="badge bg-danger">{{ $jumlahTandem }}</span>
                            @endif
        </a>
      </li>
    </ul>
  </li>
@endif


@if (in_array('Lolos', $permissions))
  <li>
    <a href="{{ route('superadmin.lolosafter.index') }}" class="{{ request()->routeIs('superadmin.lolosafter.index') ? 'active' : '' }}">
      <span>Proses PKM</span>
      @if ($jumlahLolosafter > 0)
        <span class="badge bg-danger">{{ $jumlahLolosafter }}</span>
      @endif
    </a>
  </li>
@endif
@if (in_array('StopProses', $permissions))
  <li>
    <a href="{{ route('superadmin.stopproses.index') }}" class="{{ request()->routeIs('superadmin.stopproses.index') ? 'active' : '' }}">
      <span>Stop Proses</span>
      @if ($jumlahStopProses > 0)
        <span class="badge bg-danger">{{ $jumlahStopProses }}</span>
      @endif
    </a>
  </li>
@endif

@if (in_array('TidakLolos', $permissions))
  <li>
    <a href="{{ route('superadmin.tidaklolos.index') }}" class="{{ request()->routeIs('superadmin.tidaklolos.index') ? 'active' : '' }}">
      <span>Tidak Lolos</span>
      @if ($jumlahTidakLolos > 0)
        <span class="badge bg-danger">{{ $jumlahTidakLolos }}</span>
      @endif
    </a>
  </li>
@endif

@if (in_array('SimpanKandidat', $permissions))
  <li>
    <a href="{{ route('superadmin.save.index') }}" class="{{ request()->routeIs('superadmin.save.index') ? 'active' : '' }}">
      <span>Simpan Kandidat</span>
            @if ($jumlahSimpan > 0)
                <span class="badge bg-danger">{{ $jumlahSimpan }}</span>
            @endif
    </a>
        </li>
        @endif
    </ul>
  </li>


  <li class="nav-item">
  <a class="nav-link collapsed {{ request()->routeIs('superadmin.trainingabm.index') || request()->routeIs('superadmin.trainingabm.create')  ? 'active' : '' }}" href="{{ route('superadmin.trainingabm.index') }}">
  <i class="bi bi-journal"></i>
  <span>Training ABM</span>
  </a>
  </li>
@endif

@if (in_array('Blacklist', $permissions))
  <li class="nav-item">
  <a class="nav-link collapsed {{ request()->routeIs('superadmin.blacklist.index') || request()->routeIs('superadmin.blacklist.create')  ? 'active' : '' }}" href="{{ route('superadmin.blacklist.index') }}">
  <i class="bi bi-ban-fill"></i>
  <span>Blacklist</span>
  </a>
  </li>
@endif

@if (in_array('DataKonfirm', $permissions))
  <li class="nav-item">
<a class="nav-link collapsed {{ request()->routeIs('superadmin.masterkonfirm.index') || request()->routeIs('superadmin.masterkonfirm.create')  ? 'active' : '' }}" href="{{ route('superadmin.masterkonfirm.index') }}">
  <i class="bi bi-check-circle-fill"></i>
  <span>Data Konfirm Pemanggilan</span>
</a>
  </li>
@endif

@if (in_array('LaporanPerformance', $permissions))
  <li class="nav-item">
    <a class="nav-link collapsed {{ request()->routeIs('superadmin.targetjumlah.index') || request()->routeIs('superadmin.masteraktif.index') || request()->routeIs('superadmin.mastertidakaktif.index') ? 'active' : '' }}" data-bs-target="#icons-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-card-text"></i><span>Data Input</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>

    <ul id="icons-nav" class="nav-content collapse {{ request()->routeIs('superadmin.targetjumlah.index') || request()->routeIs('superadmin.masteraktif.index') || request()->routeIs('superadmin.mastertidakaktif.index') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
      <li>
        <a href="{{route('superadmin.targetjumlah.index')}}" class="{{ request()->routeIs('superadmin.targetjumlah.index') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Target MPP & Jumlah Mitra Existing</span>
        </a>
      </li>

      <li>
        <a href="{{route('superadmin.masteraktif.index')}}" class="{{ request()->routeIs('superadmin.masteraktif.index') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Karyawan Aktif</span>
        </a>
      </li>

      <li>
        <a href="{{route('superadmin.mastertidakaktif.index')}}" class="{{ request()->routeIs('superadmin.mastertidakaktif.index') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Karyawan Tidak Aktif</span>
        </a>
      </li>

      <!-- <li>
        <a href="{{route('superadmin.mastertrainingtandem.index')}}" class="{{ request()->routeIs('superadmin.mastertrainingtandem.index') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Karyawan Training & Tandem</span>
        </a>
      </li> -->
    </ul>
  </li><!-- End Icons Nav -->
@endif


@if (in_array('LaporanPerformance', $permissions))
  <li class="nav-item">
  <a class="nav-link collapsed {{ request()->routeIs('superadmin.laporanperformance.index') || request()->routeIs('superadmin.laporanperformance.create')  ? 'active' : '' }}" href="{{ route('superadmin.laporanperformance.index') }}">
  <i class="bi bi bi-journal-text"></i>
  <span>Laporan Performance</span>
</a>

  </li>
@endif

@if (in_array('LogActivity', $permissions))

      <li class="nav-item">
  <a class="nav-link collapsed {{ request()->routeIs('superadmin.logactivity.index') || request()->routeIs('superadmin.logactivity.create')  ? 'active' : '' }}" href="{{ route('superadmin.logactivity.index') }}">
  <i class="bi-graph-up"></i>
          <span>Log Aktivitas</span>
</a>

  </li>
@endif
      

  </ul>
</aside><!-- End Sidebar-->

