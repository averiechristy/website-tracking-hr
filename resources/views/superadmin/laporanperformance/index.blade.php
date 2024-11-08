@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">
<!-- End Page Title -->
<!-- End Page Title -->
<section class="section dashboard">

    <div class="row">
    
    <!-- FILTER -->
    <div class="mb-3">
        <h10>Filter</h10>
    <div class="row g-3">
        <!-- Select Posisi -->
        <div class="col-md-4">
            <select name="posisi_id" id="selectPosisi" class="form-select select2" style="color:black;">
                <option value="" selected disabled>Pilih Posisi</option>
                @foreach ($posisi as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_posisi }}</option>
                @endforeach
            </select>
        </div>

        <!-- Input Month -->
        <div class="col-md-4">
            <select name="month" id="selectBulan" class="form-control" style="color:black; height:90%; font-size: 10pt; "  >
                <option value="" selected>Pilih Bulan</option>
                <option value="all">Semua Bulan</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                @endfor
            </select>
        </div>

        <!-- Input Year -->
        <div class="col-md-4">
            <select name="year" id="selectTahun" class="form-control"style="color:black; height:90%;font-size: 10pt;" >
                <option value=""selected>Pilih Tahun</option>

                @for($y = date('Y'); $y >= date('Y') - 1; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>
</div>

<hr style="width:98%;" class="mt-2">

<div class="mb-3 text-right">
    <button id="exportPdfBtn" class="btn btn-sm btn-primary" style="width: 150px;">Export Laporan PDF</button>
</div>

<div id="performanceReport">
<div class="pagetitle">
    <h1 id="laporanTitle">Laporan Performance</h1>
</div>
<div class="row">
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
                  <h5 class="card-title">% Pencapaian</h5>
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


    <!-- CHART -->

    <div>
  <canvas id="myChart"></canvas>
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

        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart;

        $('#selectPosisi, #selectBulan, #selectTahun').change(function() {
            var posisi_id = $('#selectPosisi').val();
            var bulan = $('#selectBulan').val();
            var tahun = $('#selectTahun').val();

            if (posisi_id && bulan && tahun) {
                $.ajax({
                    url: '{{ route('superadmin.laporanperformance.index') }}',
                    type: 'GET',
                    data: {
                        posisi_id: posisi_id,
                        bulan: bulan,
                        tahun: tahun
                    },
                    success: function(response) {
                        console.log(response); // Untuk melihat respons yang diterima
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

                        if (myChart) {
                            myChart.destroy();
                        }
                        
                        var labels = [];
                        var lolossortirData = [];
                        var konfirmasihadirData = [];
                        var lolosData = [];
                        var trainingData = [];
                        var tandemData = [];
                        var pkmbaruData = [];

if (bulan === 'all') {
    labels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    // Ambil data untuk setiap bulan
    for (var i = 1; i <= 12; i++) {
        lolossortirData.push(response.lolossortir_bulan['lolossortir_bulan_' + i] || 0);
        konfirmasihadirData.push(response.konfirmasihadir_bulan['konfirmasihadir_bulan_' + i] || 0);
        lolosData.push(response.lolos_bulan['lolos_bulan_' + i] || 0);
        trainingData.push(response.training_bulan['training_bulan_' + i] || 0);
        tandemData.push(response.tandem_bulan['tandem_bulan_' + i] || 0);
        pkmbaruData.push(response.pkmbaru_bulan['pkmbaru_bulan_' + i] || 0);
    }

    var colors = [];
var hueSteps = 60; // Set a minimum step between hues to ensure distinct colors

for (var i = 0; i < 6; i++) {
    var hue = (i * hueSteps) % 360; // Ensure hue is spaced evenly
    var color = `hsl(${hue}, 80%, 60%)`; // Use fixed saturation and lightness for variety
    colors.push(color);
}

    myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Lolos Sortir',
                data: lolossortirData,
                backgroundColor: colors[0],
                borderWidth: 1
            }, {
                label: 'Konfirmasi Hadir',
                data: konfirmasihadirData,
                backgroundColor: colors[1],
                borderWidth: 1
            }, {
                label: 'Lolos',
                data: lolosData,
                backgroundColor: colors[2],
                borderWidth: 1
            }, {
                label: 'Training',
                data: trainingData,
                backgroundColor: colors[3],
                borderWidth: 1
            }, {
                label: 'Tandem',
                data: tandemData,
                backgroundColor: colors[4],
                borderWidth: 1
            }, {
                label: 'PKM Baru',
                data: pkmbaruData,
                backgroundColor: colors[5],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
} else {
    labels = ['Lamaran Masuk', 'Konfirmasi Hadir', 'Interview (Lolos)', 'Training', 'Tandem', 'PKM Baru'];
    lolossortirData = [response.lolossortir];
    konfirmasihadirData = [response.konfirmasihadir];
    lolosData = [response.lolos];
    trainingData = [response.training];
    tandemData = [response.tandem];
    pkmbaruData = [response.pkmbaru];

    var colors = [];
    var hueSteps = 60; // Set a minimum step between hues to ensure distinct colors

    for (var i = 0; i < 6; i++) {
        var hue = (i * hueSteps) % 360; // Ensure hue is spaced evenly
        var color = `hsl(${hue}, 80%, 60%)`; // Use fixed saturation and lightness for variety
        colors.push(color);
    }

    myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Status Data',  // Add this line to define the label
                data: lolossortirData.concat(konfirmasihadirData, lolosData, trainingData, tandemData, pkmbaruData),
                backgroundColor: colors,
                borderWidth: 1,
                barThickness: 30,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

}

// Function to generate a random color

function getRandomColorHSL() {
    var hue = Math.floor(Math.random() * 360); // Generate a random hue value (0-360)
    var saturation = 70 + Math.random() * 30; // Set saturation between 70% and 100%
    var lightness = 50 + Math.random() * 30;  // Set lightness between 50% and 80%
    return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
}

                    }
                });
            }
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
document.getElementById('exportPdfBtn').addEventListener('click', function() {
    // Pilih elemen laporan (kartu dan chart) yang akan di-export
    var elementToCapture = document.getElementById('performanceReport'); // Pastikan ini ID dari container yang berisi laporan
   
    html2canvas(elementToCapture, {scale: 2}).then(canvas => {
        // Buat objek PDF baru dengan ukuran A3
        const pdf = new jspdf.jsPDF('p', 'mm', 'a3');
        
        // Konversi canvas ke gambar
        const imgData = canvas.toDataURL('image/png');
        
        // Tentukan ukuran PDF untuk kertas A3
        const imgWidth = 297; // Lebar A3 dalam mm
        const pageHeight = 420; // Tinggi A3 dalam mm
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        let heightLeft = imgHeight;
        let position = 0;
        
        // Tambahkan gambar pertama ke PDF
        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        // Jika konten melebihi satu halaman, tambahkan halaman baru
        while (heightLeft > 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        // Ambil teks dari filter
        var posisi = $('#selectPosisi option:selected').text();
        var bulan = $('#selectBulan option:selected').text();
        var tahun = $('#selectTahun option:selected').text();

        // Unduh PDF dengan judul yang sesuai
        pdf.save(`Laporan Performance ${posisi} ${bulan} ${tahun}.pdf`);
    });
});
</script>

<script>
    $('#selectPosisi, #selectBulan, #selectTahun').change(function() {
    var posisi = $('#selectPosisi option:selected').text();
    var bulan = $('#selectBulan option:selected').text();
    var tahun = $('#selectTahun option:selected').text();

    if (posisi && bulan && tahun) {
        $('#laporanTitle').text(`Laporan Performance ${posisi} ${bulan} ${tahun}`);
    }
});

</script>
@endsection
