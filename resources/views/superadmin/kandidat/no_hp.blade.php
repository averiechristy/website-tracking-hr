@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">

<div class="pagetitle">
  <h1>Proses Nomor HP</h1>
</div>
<!-- End Page Title -->

<section class="section dashboard">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('superadmin.kandidat.index') }}" class="btn btn-sm btn-primary mb-3 mt-3">Kembali</a>

                    <!-- Display No Hp Horizontally -->
                    <div class="no-hp-list">
                        @if(!empty($noHpList))
                         
                                {{ $noHpList }}
                         
                        @else
                            <p>Tidak ada nomor HP yang dipilih.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</main><!-- End #main -->

<style>
    .no-hp-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .no-hp-list .badge {
        font-size: 14px;
        padding: 10px 15px;
    }
</style>
@endsection
