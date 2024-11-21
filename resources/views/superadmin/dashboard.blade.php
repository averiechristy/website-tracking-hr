@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">
    <!-- End Page Title -->
    <section class="section dashboard">
        <div class="pagetitle">
            <h1>Dashboard</h1>
        </div>
        <hr style="width:98%;" class="mt-2">

        <div class="row">
        <div class="col-lg-4 col-md-6">
                <div class="card info-card border-primary">
                    <div class="card-body">
                    <h3 class="card-title fw-bold">Kandidat Baru</h3>
                    <h3 class="display-6 text-dark fw-bold">{{$belumproses}}</h3> <!-- Total psikotes -->
                       
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card info-card border-primary">
                    <div class="card-body">
                    <h3 class="card-title fw-bold">Proses PKM</h3>
                    <h3 class="display-6 text-dark fw-bold">{{$lolos}}</h3> <!-- Total psikotes -->
                       
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card info-card border-primary">
                    <div class="card-body">
                    <h3 class="card-title fw-bold">Simpan Kandidat</h3>
                    <h3 class="display-6 text-dark fw-bold">{{$simpankandidat}}</h3> <!-- Total psikotes -->
                       
                    </div>
                </div>
            </div>
            
        </div>

        <div class="row">
        <div class="col">
                <div class="card info-card border-warning">
                    <div class="card-body">
                    <h3 class="card-title fw-bold">Stop Proses</h3>
                    <h3 class="display-6 text-dark fw-bold">{{$stopproses}}</h3> <!-- Total psikotes -->
                       
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card info-card border-warning">
                    <div class="card-body">
                    <h3 class="card-title fw-bold">Tidak Lolos</h3>
                    <h3 class="display-6 text-dark fw-bold">{{$tidaklolos}}</h3> <!-- Total psikotes -->
                       
                    </div>
                </div>
            </div>
        </div>


        <div class="row">


        <div class="col-lg-3 col-md-5">
        <div class="card info-card border-info shadow-lg hover-shadow-lg">
            <div class="card-body text-center">
                <h3 class="card-title fw-bold">Psikotes</h3>
                <h3 class="display-6 text-dark fw-bold">{{$psikotessum}}</h3> <!-- Total psikotes -->
                <div class="my-3">
                    <hr class="border-primary">
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-calendar-check fs-3 text-warning"></i> <!-- Icon kalender -->
                            <h4 class="ms-2 text-muted mb-0">{{$psikotesjadwal}}</h4> <!-- Jumlah sedang dijadwalkan -->
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-check-circle fs-3 text-success"></i> <!-- Icon centang -->
                            <h4 class="ms-2 text-muted mb-0">{{$psikotes}}</h4> <!-- Jumlah lolos -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-5">
        <div class="card info-card border-info shadow-lg hover-shadow-lg">
            <div class="card-body text-center">
                <h3 class="card-title fw-bold">Interview HR</h3>
                <h3 class="display-6 text-dark fw-bold">{{$itvhrsum}}</h3> <!-- Total psikotes -->
                <div class="my-3">
                    <hr class="border-primary">
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-calendar-check fs-3 text-warning"></i> <!-- Icon kalender -->
                            <h4 class="ms-2 text-muted mb-0">{{$itvhrjadwal}}</h4> <!-- Jumlah sedang dijadwalkan -->
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-check-circle fs-3 text-success"></i> <!-- Icon centang -->
                            <h4 class="ms-2 text-muted mb-0">{{$itvhr}}</h4> <!-- Jumlah lolos -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-5">
        <div class="card info-card border-info shadow-lg hover-shadow-lg">
            <div class="card-body text-center">
                <h3 class="card-title fw-bold">Interview User</h3>
                <h3 class="display-6 text-dark fw-bold">{{$itvusersum}}</h3> <!-- Total psikotes -->
                <div class="my-3">
                    <hr class="border-primary">
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-calendar-check fs-3 text-warning"></i> <!-- Icon kalender -->
                            <h4 class="ms-2 text-muted mb-0">{{$itvuserjadwal}}</h4> <!-- Jumlah sedang dijadwalkan -->
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-check-circle fs-3 text-success"></i> <!-- Icon centang -->
                            <h4 class="ms-2 text-muted mb-0">{{$itvuser}}</h4> <!-- Jumlah lolos -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-5">
        <div class="card info-card border-info shadow-lg hover-shadow-lg">
            <div class="card-body text-center">
                <h3 class="card-title fw-bold">Interview User 2</h3>
                <h3 class="display-6 text-dark fw-bold">{{$itvuserduasum}}</h3> <!-- Total psikotes -->
                <div class="my-3">
                    <hr class="border-primary">
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-calendar-check fs-3 text-warning"></i> <!-- Icon kalender -->
                            <h4 class="ms-2 text-muted mb-0">{{$itvuserduajadwal}}</h4> <!-- Jumlah sedang dijadwalkan -->
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-check-circle fs-3 text-success"></i> <!-- Icon centang -->
                            <h4 class="ms-2 text-muted mb-0">{{$itvuserdua}}</h4> <!-- Jumlah lolos -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>

<div class="row">
     <div class="col-lg-3 col-md-5">
        <div class="card info-card border-info shadow-lg hover-shadow-lg">
            <div class="card-body text-center">
                <h3 class="card-title fw-bold">Interview User 3</h3>
                <h3 class="display-6 text-dark fw-bold">{{$itvusertigasum}}</h3> <!-- Total psikotes -->
                <div class="my-3">
                    <hr class="border-primary">
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-calendar-check fs-3 text-warning"></i> <!-- Icon kalender -->
                            <h4 class="ms-2 text-muted mb-0">{{$itvusertigajadwal}}</h4> <!-- Jumlah sedang dijadwalkan -->
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-check-circle fs-3 text-success"></i> <!-- Icon centang -->
                            <h4 class="ms-2 text-muted mb-0">{{$itvusertiga}}</h4> <!-- Jumlah lolos -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

         <div class="col-lg-3 col-md-5">
        <div class="card info-card border-info shadow-lg hover-shadow-lg">
            <div class="card-body text-center">
                <h3 class="card-title fw-bold">Training</h3>
                <h3 class="display-6 text-dark fw-bold">{{$trainingsum}}</h3> <!-- Total psikotes -->
                <div class="my-3">
                    <hr class="border-primary">
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-calendar-check fs-3 text-warning"></i> <!-- Icon kalender -->
                            <h4 class="ms-2 text-muted mb-0">{{$trainingjadwal}}</h4> <!-- Jumlah sedang dijadwalkan -->
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-check-circle fs-3 text-success"></i> <!-- Icon centang -->
                            <h4 class="ms-2 text-muted mb-0">{{$training}}</h4> <!-- Jumlah lolos -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

         <div class="col-lg-3 col-md-5">
        <div class="card info-card border-info shadow-lg hover-shadow-lg">
            <div class="card-body text-center">
                <h3 class="card-title fw-bold">Tandem</h3>
                <h3 class="display-6 text-dark fw-bold">{{$tandemsum}}</h3> <!-- Total psikotes -->
                <div class="my-3">
                    <hr class="border-primary">
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-calendar-check fs-3 text-warning"></i> <!-- Icon kalender -->
                            <h4 class="ms-2 text-muted mb-0">{{$tandemjadwal}}</h4> <!-- Jumlah sedang dijadwalkan -->
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-check-circle fs-3 text-success"></i> <!-- Icon centang -->
                            <h4 class="ms-2 text-muted mb-0">{{$tandem}}</h4> <!-- Jumlah lolos -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-5">
        <div class="card info-card border-info shadow-lg hover-shadow-lg">
            <div class="card-body text-center">
                <h3 class="card-title fw-bold">PKM</h3>
                <h3 class="display-6 text-dark fw-bold">{{$lolossum}}</h3> <!-- Total psikotes -->
                <div class="my-3">
                    <hr class="border-primary">
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-calendar-check fs-3 text-warning"></i> <!-- Icon kalender -->
                            <h4 class="ms-2 text-muted mb-0">{{$lolosjadwal}}</h4> <!-- Jumlah sedang dijadwalkan -->
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-check-circle fs-3 text-success"></i> <!-- Icon centang -->
                            <h4 class="ms-2 text-muted mb-0">{{$lolos}}</h4> <!-- Jumlah lolos -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


    </section>
</main><!-- End #main -->

@endsection
