@extends('layouts.superadmin.app')
@section('content')


<main id="main" class="main">
    <div class="pagetitle">
      <h1>Tambah ABM</h1>      
    </div><!-- End Page Title -->
    <section class="section dashboard">
        <div class="card">
          <div class="card-body">
            <!-- Form input start -->
            <form action="{{route('superadmin.abm.store')}}"  class="mt-3" id="saveform" class="mt-3" onsubmit="return validateForm()" method="post">
                    @csrf
                  <div class="mb-3">
                    <label for="inputName" class="form-label">Nama ABM</label>
                    <input type="text" name="nama_abm" id="nama_abm" class="form-control" id="inputName">
                  </div>

                  <script>
document.getElementById('nama_abm').addEventListener('input', function (event) {
    var input = event.target;
    var value = input.value;
    
    // Hapus angka dan izinkan semua karakter selain angka
    input.value = value.replace(/[0-9]/g, '');

    // Jika perlu, tambahkan logika untuk menampilkan pesan error
    // document.getElementById('err_alamat').textContent = 'Hanya huruf dan karakter khusus yang diperbolehkan';
});
</script>


              <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
            <!-- Form input end -->
          </div>
        </div>
      </section>
  </main><!-- End #main -->
  <script>
function validateForm() {
    // Get form input values
    let namaabm = document.forms["saveform"]["nama_abm"].value.trim();

    // Validate the name field
    if (namaabm === "") {
        alert("Nama ABM harus diisi.");
        return false;
    }

    // Additional validations can be added here
    return true; // If all validations pass
}
</script>

@endsection