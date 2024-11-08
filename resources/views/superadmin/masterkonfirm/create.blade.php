@extends('layouts.superadmin.app')
@section('content')

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Tambah Data</h1>      
    </div><!-- End Page Title -->
    <section class="section dashboard">
        <div class="card">
            <div class="card-body">
                <!-- Form input start -->
                <form action="{{route('superadmin.masterkonfirm.store')}}" class="mt-3" id="saveform" onsubmit="return validateForm()" method="post">
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
                    @if (auth()->user()->role_id == 2)
                     
                <div class="mb-3">
                    <input type="text" name="sourcing_id" class="form-control" id="sourcingid" value="{{$loggedid}}" hidden>
                </div>    

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
                    <div class="mb-3">
                        <label for="selectUser" class="form-label">PIC</label>
                            <select name="sourcing_id" id="sourcingid" class="form-select select2" style="color:black;">
                                <option value="" selected disabled>Pilih PIC</option>
                                @foreach ($user as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>

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
    <label for="undangOtomatis" class="form-label">Jumlah Undang</label>
    <input type="number" name="jumlah_undang_otomatis" class="form-control" id="jumlah_undang_otomatis" readonly>
</div>


<div class="mb-3">
    <label for="undangOtomatis" class="form-label">Jumlah Konfirm</label>
    <input type="number" name="jumlah_konfirm_manual" class="form-control" id="jumlah_konfirm_manual" oninput="validasiNumber(this)">
</div>

<div class="mb-3">
    <label for="keterangan" class="form-label">Keterangan</label>
    <input type="text" name="keterangan" class="form-control">
</div>


<script>
function validasiNumber(input) {
    // Hapus karakter titik (.) dari nilai input
    input.value = input.value.replace(/\./g, '');

    // Pastikan hanya karakter angka yang diterima
    input.value = input.value.replace(/\D/g, '');
}
</script>


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
        $('#selectUser').select2({
            placeholder: "Pilih PIC",
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
    $(document).ready(function() {
    // Trigger this function when any of the required fields change
    function fetchJumlahUndang() {
        var tanggal = $('#tanggal').val();
        
        @if (auth()->user()->role_id == 2)   
        var posisiId = $('#selectPosisilain').val();
        var wilayahId = $('#selectWilayahlain').val();
        @else
        var posisiId = $('#selectPosisi').val();
        var wilayahId = $('#selectWilayah').val();
        @endif
    
        var sourcingId = $('#sourcingid').val(); // Assuming sourcing id is already set

        console.log(tanggal,posisiId,wilayahId,sourcingId);
        
        if (tanggal && posisiId && wilayahId && sourcingId) {
            $.ajax({
                url: '{{ route("getJumlahUndang") }}',
                type: 'GET',
                data: {
                    tanggal: tanggal,
                    posisi_id: posisiId,
                    wilayah_id: wilayahId,
                    sourcing_id: sourcingId
                },
                success: function(response) {
                    $('#jumlah_undang_otomatis').val(response.count);
                },
                error: function() {
                    $('#jumlah_undang_otomatis').val(0); // Default to 0 on error
                }
            });
        }
    }

    // Attach the function to change events
   // Attach the function to change events
$('#tanggal, #selectPosisi, #selectWilayah, #selectPosisilain, #selectWilayahlain').change(fetchJumlahUndang);

});

</script>


<script>
function validateForm() {
    // Get form input values
    let tanggal = document.forms["saveform"]["tanggal"].value.trim();
    let sourcing = document.forms["saveform"]["sourcing_id"].value.trim();
    let posisi = document.forms["saveform"]["posisi_id"].value.trim();
    let wilayah =document.forms["saveform"]["wilayah_id"].value.trim();
    let undangotomatis = document.forms["saveform"]["jumlah_undang_otomatis"].value.trim();
    let konfirmmanual =document.forms["saveform"]["jumlah_konfirm_manual"].value.trim();

    // Validate the name field
    if (tanggal === "") {
        alert("Tanggal harus diisi.");
        return false;
    }

    if(sourcing ==="") {
        alert("PIC harus diisi.");
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

    if(undangotomatis === ""){
        alert("Jumlah undang harus terisi.");
        return false;
    }

    if(konfirmmanual === ""){
        alert("Jumlah konfirm harus diisi.");
        return false;
    }
  
    if (konfirmmanual > undangotomatis){
        alert("Jumlah konfirm tidak boleh kurang dari jumlah undang");
        return false;
    }

    // Additional validations can be added here
    return true; // If all validations pass
}
</script>


@endsection
