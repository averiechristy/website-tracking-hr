@extends('layouts.superadmin.app')
@section('content')


<main id="main" class="main">
    <div class="pagetitle">
      <h1>Tambah Sumber</h1>      
    </div><!-- End Page Title -->
    <section class="section dashboard">
        <div class="card">
          <div class="card-body">
            <!-- Form input start -->
            <form action="{{route('superadmin.sumber.store')}}"  class="mt-3" id="saveform" class="mt-3" onsubmit="return validateForm()" method="post">
                    @csrf
                  <div class="mb-3">
                    <label for="inputName" class="form-label">Sumber</label>
                    <input type="text" name="nama_sumber" id="nama_sumber" class="form-control" id="inputName">
                  </div>

                  <script>
document.getElementById('nama_sumber').addEventListener('input', function (event) {
    var input = event.target;
    var value = input.value;
    
    // Hapus karakter yang bukan huruf, angka, atau spasi
    input.value = value.replace(/[^A-Za-z\s]/g, '');

    // Jika perlu, tambahkan logika untuk menampilkan pesan error
    // document.getElementById('err_alamat').textContent = 'Hanya huruf, angka, dan spasi yang diperbolehkan';
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
    let sumber = document.forms["saveform"]["nama_sumber"].value.trim();

    // Validate the name field
    if (sumber === "") {
        alert("Sumber harus diisi.");
        return false;
    }

    // Additional validations can be added here
    return true; // If all validations pass
}
</script>

@endsection