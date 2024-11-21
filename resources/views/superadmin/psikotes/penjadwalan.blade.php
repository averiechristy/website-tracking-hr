@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Penjadwalan Kandidat Lolos Psikotes</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.penjadwalanpsikotes.store') }}">
                        @csrf
                        <!-- Input untuk status dan tanggal -->
                        <div class="mb-3 mt-2">
                            <label for="status" class="form-label">Pilih Jadwal</label>
                            <select name="status" class="form-select" id="status" required>
                                <option value="">-- Pilih Jadwal --</option>
                                <option value="Psikotes">Psikotes</option>
                                <option value="Interview HR">Interview HR</option>
                                <option value="Interview User">Interview User</option>
                                <option value="Interview User 2">Interview User 2</option>
                                <option value="Interview User 3">Interview User 3</option>
                                <option value="Training">Training</option>
                                <option value="Proses PKM">Proses PKM</option>
                                <!-- Tambahkan opsi status lainnya sesuai kebutuhan -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Pilih Tanggal</label>
                            <input type="date" name="date" class="form-control" id="date" required>
                        </div>

                        <!-- Tambahkan hidden input untuk menyimpan ID kandidat -->
                        @foreach ($candidates as $candidate)
                            <input type="hidden" name="candidates[]" value="{{ $candidate->id }}">
                        @endforeach

                        <button type="submit" class="btn btn-primary">Simpan Penjadwalan</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom untuk daftar kandidat -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Daftar Kandidat</h5>
                    <ul class="list-group">
                        @foreach ($candidates as $candidate)
                            <li class="list-group-item">{{ $candidate->nama_kandidat }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ambil semua elemen input tanggal dengan id yang sesuai
    var dateInputs = document.querySelectorAll('input[type="date"]');

    dateInputs.forEach(function(dateInput) {
        // Set tanggal maksimum ke tanggal hari ini
        var today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);

        // Tambahkan event listener untuk klik pada input
        dateInput.addEventListener('click', function() {
            this.showPicker();
        });
    });
});
</script>

@endsection
