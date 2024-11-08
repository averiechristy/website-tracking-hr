@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">

<!-- End Page Title -->
<section class="section dashboard">
    <div class="row">
    
        <div class="mb-3" style="display: flex; gap: 10px;">
            <!-- Select Posisi -->
            <select name="posisi_id" id="selectPosisi" class="form-select select2" style="color:black; width:50%">
                <option value="" selected disabled>Pilih Posisi</option>
                @foreach ($posisi as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_posisi }}</option>
                @endforeach
            </select>

            <!-- Input Month -->
            <input type="month" name="bulan" id="selectBulan" class="form-control" style="width: 50%; color:black; height:80%;" placeholder="Pilih Bulan">    
        
            <script>
document.addEventListener('DOMContentLoaded', function() {
    var dateInput = document.getElementById('selectBulan');
    
    // Set maximum date to today's date
    var today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('max', today);
    
    dateInput.addEventListener('click', function() {
        this.showPicker();
    });
    
});
</script>
        </div>

        <div class="col-xxl-4 col-md-6 mb-2">
            <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Target MPP</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <div class="ps-3">
    <h6 class="isidata" id="isidata">
        {{ $targetMPP ?? '0' }}
    </h6>
</div>
                  </div>
                </div>
            </div>
        </div>

<div class="col-xxl-4 col-md-6">
            <div class="card info-card revenue-card">
                <div class="card-body">
                  <h5 class="card-title">Jumlah Mitra Existing</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-fill-check"></i>
                    </div>
                    <div class="ps-3">
    <h6 class="mitraexist" id="mitraexist">
        {{ $mitraexisting ?? '0' }}
    </h6>
</div>

</div>
                </div>
            </div>
        </div>


<div class="col-xxl-4 col-md-6">
            <div class="card info-card customers-card">
                <div class="card-body">
                  <h5 class="card-title">% Pemenuhan MPP</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-percent"></i>
                    </div>
<div class="ps-3">
    <h6 class="persenmpp" id="persenmpp">
        {{ $persenmpp ?? '0' }} %
    </h6>
</div>
</div>
</div>
</div>
</div>

        <div class="col-xxl-4 col-md-6">
            <div class="card info-card lolossortir-card">
                <div class="card-body">
                  <h5 class="card-title">Jumlah Lamaran Masuk</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                   
                    <i class="bi bi-file-earmark-arrow-up"></i>
                    </div>
                    <div class="ps-3">
    <h6 class="lolossortir" id="lolossortir">
        {{ $lolossortir ?? '0' }}
    </h6>
</div>
</div>
                </div>
            </div>
        </div>

    <div class="col-xxl-4 col-md-6">
        <div class="card info-card konfirmasihadir-card">
            <div class="card-body">
                  <h5 class="card-title">Konfirmasi Hadir</h5>
            <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="ps-3">
    <h6 class="konfirmasihadir" id="konfirmasihadir">
        {{ $konfirmasihadir ?? '0' }}
    </h6>
</div>
</div>
                </div>
            </div>
        </div>

    <div class="col-xxl-4 col-md-6">
            <div class="card info-card lolos-card">
                <div class="card-body">
                  <h5 class="card-title">Interview (Lolos)</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
    <h6 class="lolos" id="lolos">
        {{ $lolos ?? '0' }}
    </h6>
</div>
</div>
                </div>
            </div>
        </div>
     
        <div class="col-xxl-4 col-md-6">
            <div class="card info-card training-card">
                <div class="card-body">
                  <h5 class="card-title">Training</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-book"></i>
                    </div>
                    <div class="ps-3">
    <h6 class="training" id="training">
        {{ $training ?? '0' }}
    </h6>
</div>
</div>
                </div>
            </div>
        </div>
          

        <div class="col-xxl-4 col-md-6">
            <div class="card info-card tandem-card">
                <div class="card-body">
                  <h5 class="card-title">Tandem</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="ps-3">
    <h6 class="tandem" id="tandem">
        {{ $tandem ?? '0' }}
    </h6>
</div>
</div>
                </div>
            </div>
        </div>


        <div class="col-xxl-4 col-md-6">
            <div class="card info-card pkmbaru-card">
                <div class="card-body">
                  <h5 class="card-title">PKM Baru</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-file-earmark-check"></i>
                    </div>
                    <div class="ps-3">
    <h6 class="pkmbaru" id="pkmbaru">
        {{ $pkmbaru ?? '0' }}
    </h6>
</div>
</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-md-6">
            <div class="card info-card pkmbataljoin-card">
                <div class="card-body">
                  <h5 class="card-title">PKM Batal Join</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-x"></i>
                    </div>
                    <div class="ps-3">
    <h6 class="pkmbataljoin" id="pkmbataljoin">
        {{ $pkmbataljoin ?? '0' }}
    </h6>
</div>
</div>
                </div>
            </div>
        </div>


        
        <div class="col-xxl-4 col-md-6">
            <div class="card info-card resign-card">
                <div class="card-body">
                  <h5 class="card-title">Mitra Keluar / Resign</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-slash"></i>
                    </div>
                    <div class="ps-3">
    <h6 class="resign" id="resign">
        {{ $resign ?? '0' }}
    </h6>
</div>
</div>
                </div>
            </div>
        </div>

    <div class="col-xxl-4 col-md-6">
        <div class="card info-card tambahkurangmitra-card">
            <div class="card-body">
                  <h5 class="card-title">Penambahan/Pengurangan Mitra</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-plus-slash-minus"></i>
                    </div>
                    <div class="ps-3">
    <h6 class="tambahkurangmitra" id="tambahkurangmitra">
        {{ $tambahkurangmitra ?? '0' }}
    </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-md-6">
        <div class="card info-card targetjoin-card">
            <div class="card-body">
                  <h5 class="card-title">Target Join</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-clipboard2-data"></i>
                    </div>
                    <div class="ps-3">
    <h6 class="targetjoin" id="targetjoin">
        {{ $targetjoin ?? '0' }}
    </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-md-6">
        <div class="card info-card persenpencapaian-card">
            <div class="card-body">
                  <h5 class="card-title">% Pencampaian</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-percent"></i>
                    </div>
                    <div class="ps-3">
    <h6 class="persenpencapaian" id="persenpencapaian">
        {{ $persenpencapaian ?? '0' }}
    </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>



    </div>
</section>

</main><!-- End #main -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#selectPosisi').select2({
            placeholder: "Pilih Posisi",
            allowClear: true
        });
    });
</script>



<script>
    $(document).ready(function() {
        $('#selectPosisi, #selectBulan').change(function() {
            var posisi_id = $('#selectPosisi').val();
            var bulan = $('#selectBulan').val();
            
            if (posisi_id && bulan) {
                $.ajax({
    url: '{{ route('superadmin.laporanperformance.index') }}',
    type: 'GET',
    data: {
        posisi_id: posisi_id,
        bulan: bulan
    },
    success: function(response) {
    console.log(response);  // Untuk melihat respons yang diterima
    $('#isidata').text(response.targetMPP || '0');
    $('#mitraexist').text(response.mitraexisting || '0');
    $('#persenmpp').text((response.persenmpp || '0') + '%');
    $('#lolossortir').text(response.lolossortir || '0');
    $('#konfirmasihadir').text(response.konfirmasihadir || '0');
    $('#lolos').text(response.lolos || '0');
    $('#training').text(response.training || '0');
    $('#tandem').text(response.tandem || '0');
    $('#pkmbaru').text(response.pkmbaru || '0');
    $('#pkmbataljoin').text(response.pkmbataljoin || '0');
    $('#resign').text(response.resign || '0');
    $('#tambahkurangmitra').text(response.tambahkurangmitra || '0');
    $('#targetjoin').text(response.targetjoin || '0');
    $('#persenpencapaian').text((response.persenpencapaian || '0') + '%');
}
,
    error: function(xhr, status, error) {
        console.log(xhr.responseText); // Log error response
        $('#isidata').text('Error mengambil data');
        $('#persenmpp').text('Error mengambil data');
        $('#lolossortir').text('Error mengambil data');
        $('#konfirmasihadir').text('Error mengambil data');
        $('#lolos').text('Error mengambil data');
        $('#training').text('Error mengambil data');
        $('#tandem').text('Error mengambil data');
        $('#pkmbaru').text('Error mengambil data');
        $('#pkmbataljoin').text('Error mengambil data');
        $('#resign').text('Error mengambil data');
        $('#tambahkurangmitra').text('Error mengambil data');
        $('#targerjoin').text('Error mengambil data');
        $('#persenpencapaian').text('Error mengambil data');
    }
    
    
    

    
});

            }
        });
    });
</script>




@endsection
