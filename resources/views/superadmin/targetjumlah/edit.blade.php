@extends('layouts.superadmin.app')
@section('content')

<main id="main" class="main">
    <div class="pagetitle">
      <h1>Ubah Data</h1>      
    </div><!-- End Page Title -->
    <section class="section dashboard">
        <div class="card">
          <div class="card-body">
            <!-- Form input start -->
            <form action="{{route('superadminupdatetargetjumlah', $data->id)}}"  class="mt-3" id="saveform" class="mt-3" onsubmit="return validateForm()" method="post">
                    @csrf
                    <div class="mb-3">
                    <label class="form-label">Bulan & Tahun</label>
                    <input type="month" name="month" id ="month" class="form-control" value="{{$monthValue}}" >
                  </div>
                  <script>
document.addEventListener('DOMContentLoaded', function() {
    var dateInput = document.getElementById('month');
    
    // Set maximum date to today's date
    var today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('max', today);
    
    dateInput.addEventListener('click', function() {
        this.showPicker();
    });
    
});
</script>

<div class="mb-3">
                            <label for="selectPosisi" class="form-label">Posisi</label>
                            <select name="posisi_id" id="selectPosisi" class="form-select select2" style="color:black;" aria-label=".form-select-lg example">
                                <option value="" selected disabled>Pilih Posisi</option>
                                @foreach ($posisi as $item)
                                    <option value="{{ $item->id }}" {{ old('posisi_id', $data->posisi_id) == $item->id ? 'selected' : '' }}>{{ $item->nama_posisi }}</option>
                                @endforeach
                            </select>
                        </div>

                       

                  <div class="mb-3">
                    <label class="form-label">Target MPP</label>
                    <input type="number" name="target_mpp" id ="target_mpp" class="form-control" oninput="validasiNumber(this)" value="{{$data->target_mpp}}">
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Jumlah Mitra Existing</label>
                    <input type="number" name="mitra_existing" id ="mitra_existing" class="form-control" oninput="validasiNumber(this)" value="{{$data->jumlah_mitra}}">
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Target Join</label>
                    <input type="number" name="target_join" id ="target_join" class="form-control" oninput="validasiNumber(this)" value="{{$data->target_join}}">
                  </div>
                
              <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
            <!-- Form input end -->
          </div>
        </div>
      </section>
  </main><!-- End #main -->

  
  <script>
function validasiNumber(input) {
    // Hapus karakter titik (.) dari nilai input
    input.value = input.value.replace(/\./g, '');

    // Pastikan hanya karakter angka yang diterima
    input.value = input.value.replace(/\D/g, '');
}
</script>
  <script>
function validateForm() {
    // Get form input values
    
   // Additional validations can be added here
    return true; // If all validations pass
}
</script>

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
@endsection