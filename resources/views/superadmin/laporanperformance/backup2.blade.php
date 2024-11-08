@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">

<!-- End Page Title -->
<section class="section dashboard">
    <div class="row">
    
    <!-- Sales Card -->
       <div class="col-xxl-4 col-md-6">
            <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Target MPP</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <div class="ps-3">
                      <h6>145</h6>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    <!-- End Sales Card -->


 
    <!-- Sales Card -->
       <div class="col-xxl-4 col-md-6">
            <div class="card info-card revenue-card">
                <div class="card-body">
                  <h5 class="card-title">Jumlah Mitra Existing</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-fill-check"></i>
                    </div>
                    <div class="ps-3">
                      <h6>145</h6>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    <!-- End Sales Card -->

    </div>
</section>

</main><!-- End #main -->

@endsection
