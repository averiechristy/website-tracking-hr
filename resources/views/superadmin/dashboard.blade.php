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
            <!-- Card 1: Belum Diproses -->
            <div class="col-lg-4 col-md-6">
                <div class="card info-card border-primary">
                    <div class="card-body">
                        <h5 class="card-title">Kandidat Baru</h5>
                        <div class="d-flex align-items-center">
                            <div class="ps-3">
                                <h6>{{ $belumproses }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Psikotes -->
            <div class="col-lg-4 col-md-6">
                <div class="card info-card border-success">
                    <div class="card-body">
                        <h5 class="card-title">Psikotes</h5>
                        <div class="d-flex align-items-center">
                            <div class="ps-3">
                                <h6>{{ $psikotes }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Interview HR -->
            <div class="col-lg-4 col-md-6">
                <div class="card info-card border-info">
                    <div class="card-body">
                        <h5 class="card-title">Interview HR</h5>
                        <div class="d-flex align-items-center">
                            <div class="ps-3">
                                <h6>{{ $itvhr }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Card 4: Interview User -->
            <div class="col-lg-4 col-md-6">
                <div class="card info-card border-warning">
                    <div class="card-body">
                        <h5 class="card-title">Interview User</h5>
                        <div class="d-flex align-items-center">
                            <div class="ps-3">
                                <h6>{{ $itvuser }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 5: Training -->
            <div class="col-lg-4 col-md-6">
                <div class="card info-card border-danger">
                    <div class="card-body">
                        <h5 class="card-title">Training</h5>
                        <div class="d-flex align-items-center">
                            <div class="ps-3">
                                <h6>{{ $training }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 6: Tandem -->
            <div class="col-lg-4 col-md-6">
                <div class="card info-card border-dark">
                    <div class="card-body">
                        <h5 class="card-title">Tandem</h5>
                        <div class="d-flex align-items-center">
                            <div class="ps-3">
                                <h6>{{ $tandem }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Card 7: Lolos -->
            <div class="col-lg-4 col-md-6">
                <div class="card info-card border-secondary">
                    <div class="card-body">
                        <h5 class="card-title">Lolos</h5>
                        <div class="d-flex align-items-center">
                            <div class="ps-3">
                                <h6>{{ $lolos }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 8: Tidak Lolos -->
            <div class="col-lg-4 col-md-6">
                <div class="card info-card border-info">
                    <div class="card-body">
                        <h5 class="card-title">Tidak Lolos</h5>
                        <div class="d-flex align-items-center">
                            <div class="ps-3">
                                <h6>{{ $tidaklolos }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 9: Simpan Kandidat -->
            <div class="col-lg-4 col-md-6">
                <div class="card info-card border-muted">
                    <div class="card-body">
                        <h5 class="card-title">Simpan Kandidat</h5>
                        <div class="d-flex align-items-center">
                            <div class="ps-3">
                                <h6>{{ $simpankandidat }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->
@endsection
