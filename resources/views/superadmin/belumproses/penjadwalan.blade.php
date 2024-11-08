@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Penjadwalan Kandidat Baru</h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                <form method="POST" action="{{ route('superadmin.penjadwalan.store') }}">
                        @csrf
                        <div class="row">
                            @foreach ($candidates as $candidate)
                                <div class="col-md-4 mb-3 mt-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $candidate->nama_kandidat }}</h5>

                                            <div class="mb-3">
                                                <label for="status_{{ $candidate->id }}" class="form-label">Pilih Status</label>
                                                <select name="status[{{ $candidate->id }}]" class="form-select" id="status_{{ $candidate->id }}" required>
                                                <option value="">-- Pilih Status --</option>
                                                <option value="Psikotes">Psikotes</option>
                                                <option value="Interview HR">Interview HR</option>
                                                <option value="Interview User">Interview User</option>
                                                <option value="Training">Training</option>
                                                <option value="Tandem">Tandem</option>
                                                <option value="Join">Join</option>
                                    
                                                    <!-- Add other status options as needed -->
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="date_{{ $candidate->id }}" class="form-label">Pilih Tanggal</label>
                                                <input type="date" name="date[{{ $candidate->id }}]" class="form-control" id="date_{{ $candidate->id }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Penjadwalan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main><!-- End #main -->
@endsection
