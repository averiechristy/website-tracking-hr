@extends('layouts.superadmin.app')
@section('content')

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Tambah Kandidat</h1>      
    </div><!-- End Page Title -->
    <section class="section dashboard">
        <div class="card">
            <div class="card-body">
                <!-- Form input start -->
                <form action="{{ route('superadmin.kandidat.store') }}" class="mt-3" id="saveform" onsubmit="return validateForm()" method="post">
                    @csrf

                    <!-- Input Tanggal -->
                    <div class="mb-3">
                        <label for="inputTanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control">
                    </div>
                    <script>
document.addEventListener('DOMContentLoaded', function() {
    var dateInput = document.getElementById('tanggal');
    
    // Set maximum date to today's date
    var today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('max', today);
    
    dateInput.addEventListener('click', function() {
        this.showPicker();
    });
});
</script>

<div class="mb-3">
                            <label for="selectSumber" class="form-label">Sumber</label>
                            <select name="sumber_id" id="selectSumber" class="form-select select2" style="color:black;">
                                <option value="" selected disabled>Pilih Sumber</option>
                                @foreach ($sumber as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_sumber }}</option>
                                @endforeach
                            </select>
                        </div>


                    <!-- Nama Kandidat -->
                    <div class="mb-3">
                        <label for="inputName" class="form-label">Nama Kandidat</label>
                        <input type="text" name="nama_kandidat" id="nama_kandidat" class="form-control" id="inputName">
                    </div>

                    <script>
document.getElementById('nama_kandidat').addEventListener('input', function (event) {
    var input = event.target;
    var value = input.value;
    
    // Hapus karakter yang bukan huruf, angka, atau spasi
    input.value = value.replace(/[^A-Za-z\s]/g, '');

    // Jika perlu, tambahkan logika untuk menampilkan pesan error
    // document.getElementById('err_alamat').textContent = 'Hanya huruf, angka, dan spasi yang diperbolehkan';
});
</script>

                    @if (auth()->user()->role_id == 2)
                     

                        <!-- Pilihan Posisi -->
                        <div class="mb-3">
                            <label for="selectPosisi" class="form-label">Posisi</label>
                            <select name="posisi_id" id="selectPosisilain" class="form-select select2" style="color:black;">
                                <option value="" selected disabled>Pilih Posisi</option>
                                @foreach ($filteredPosisi as $posisi)
                                    <option value="{{ $posisi->id }}">{{ $posisi->nama_posisi }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Wilayah Berdasarkan Posisi -->
                        <div class="mb-3">
                            <label for="selectWilayah" class="form-label">Wilayah</label>
                            <select name="wilayah_id" id="selectWilayahlain" class="form-select select2" style="color:black;">
                                <option value="" selected disabled>Pilih Wilayah</option>
                                <!-- Wilayah akan dimuat berdasarkan posisi yang dipilih -->
                            </select>
                        </div>
                    @else
                        <!-- Posisi untuk pengguna lain -->
                        <div class="mb-3">
                            <label for="selectPosisi" class="form-label">Posisi</label>
                            <select name="posisi_id" id="selectPosisi" class="form-select select2" style="color:black;">
                                <option value="" selected disabled>Pilih Posisi</option>
                                @foreach ($posisi as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_posisi }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Wilayah untuk pengguna lain -->
                        <div class="mb-3">
                            <label for="selectWilayah" class="form-label">Wilayah</label>
                            <select name="wilayah_id" id="selectWilayah" class="form-select select2" style="color:black;">
                                <option value="" selected disabled>Pilih Wilayah</option>
                                @foreach ($wilayah as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_wilayah }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

             

<div class="mb-3">
                    <label for="inputNo" class="form-label">No Hp</label>
                    <input type="number" name="no_hp" class="form-control" id="inputNo" oninput="validasiNumber(this)">
                  </div>

                  <script>
function validasiNumber(input) {
    // Hapus karakter titik (.) dari nilai input
    input.value = input.value.replace(/\./g, '');

    // Pastikan hanya karakter angka yang diterima
    input.value = input.value.replace(/\D/g, '');
}
</script>


<div class="mb-3">
                    <label for="inputEmail" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" id="inputEmail">
                  </div>


                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
                <!-- Form input end -->
            </div>
        </div>
    </section>
</main>

<!-- JavaScript untuk memuat Wilayah Berdasarkan Posisi -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#selectWilayah').select2({
            placeholder: "Pilih Wilayah",
            allowClear: true
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#selectPosisi').select2({
            placeholder: "Pilih Posisi",
            allowClear: true
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#selectSumber').select2({
            placeholder: "Pilih Sumber",
            allowClear: true
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#selectWilayahlain').select2({
            
            allowClear: true
        });
    });
</script>
<script>
$(document).ready(function() {
    $('#selectPosisilain').select2({
        placeholder: "Pilih Posisi",
        allowClear: true
    }).on('change', function() {
        var posisiId = $(this).val();
        var userId = {{ auth()->user()->id }}; // Get the authenticated user's ID

        if (posisiId) {
            // Show "Loading..." option
            var wilayahSelect = $('#selectWilayahlain');
            wilayahSelect.empty();
            wilayahSelect.append('<option value="" disabled selected>Loading...</option>');
            
            $.ajax({
                url: '{{ route("superadmin.kandidat.getWilayahByPosisi") }}', // Route for fetching regions based on position
                type: 'GET',
                data: { posisi_id: posisiId, user_id: userId }, // Pass both posisi_id and user_id
                success: function(response) {
                    console.log(response);
                    
                    // Extract wilayah_id from the response
                    var wilayahIds = response.map(function(item) {
                        return item.wilayah_id;
                    }).join(',');

                    if (wilayahIds) {
                        // Make another AJAX call to fetch wilayah details based on wilayah_id
                        $.ajax({
                            url: '{{ route("superadmin.kandidat.getWilayahById") }}', // Route to fetch wilayah details
                            type: 'GET',
                            data: { wilayah_ids: wilayahIds },
                            success: function(wilayahResponse) {
                                wilayahSelect.empty();
                                wilayahSelect.append('<option value="" selected disabled>Pilih Wilayah</option>');
                                
                                // Populate the select dropdown with the wilayah details
                                $.each(wilayahResponse, function(index, item) {
                                    wilayahSelect.append('<option value="' + item.id + '">' + item.nama_wilayah + '</option>');
                                });
                            }
                        });
                    } else {
                        // If no wilayah data, clear the dropdown
                        wilayahSelect.empty();
                        wilayahSelect.append('<option value="" selected disabled>Tidak ada wilayah tersedia</option>');
                    }
                },
                error: function() {
                    // Handle errors here
                    wilayahSelect.empty();
                    wilayahSelect.append('<option value="" selected disabled>Terjadi kesalahan</option>');
                }
            });
        } else {
            // If no posisiId is selected, clear the wilayah dropdown
            $('#selectWilayahlain').empty();
            $('#selectWilayahlain').append('<option value="" selected disabled>Pilih Wilayah</option>');
        }
    });
});
</script>
<script>
function validateForm() {
    // Get form input values
    let tanggal = document.forms["saveform"]["tanggal"].value.trim();
    let sumber =document.forms["saveform"]["sumber_id"].value.trim();
    let nama = document.forms["saveform"]["nama_kandidat"].value.trim();
    let posisi =document.forms["saveform"]["posisi_id"].value.trim();
    let wilayah = document.forms["saveform"]["wilayah_id"].value.trim();
    let nohp =document.forms["saveform"]["no_hp"].value.trim();
    let email =document.forms["saveform"]["email"].value.trim();

    // Validate the name field
    if (tanggal === "") {
        alert("Tanggal harus diisi.");
        return false;
    }

    if (sumber === "") {
        alert("Sumber harus diisi.");
        return false;
    }

    if(nama === ""){
        alert("Nama kandidat harus diisi.");
        return false;
    }

    if(posisi === ""){
        alert("Posisi harus diisi.");
        return false;
    }

    if(wilayah === ""){
        alert("Wilayah harus diisi.");
        return false;
    }

    if (nohp === "") {
        alert("No Hp harus diisi.");
        return false;
    } else if (nohp.length < 10 ) {
        alert("No hp minimal 10 digit.");
        return false;
    }
    else if (nohp.length > 13 ) {
        alert("No hp maksimal 13 digit.");
        return false;
    }

    if(email === ""){
        alert("Email harus diisi.");
        return false;
    }


    // Additional validations can be added here
    return true; // If all validations pass
}
</script>

@endsection
