@extends('layouts.superadmin.app')
@section('content')

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Ubah Akun User</h1>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('superadminupdateuser', $user->id) }}" id="saveform" class="mt-3" onsubmit="return validateForm()" method="post">
                    @csrf

                    <div class="mb-3">
                        <label for="selectRole" class="form-label">Role</label>
                        <select name="role_id" id="selectRole" class="form-select select2" style="color:black;" aria-label=".form-select-lg example">
                            <option value="" disabled>Pilih Role</option>
                            @foreach ($role as $item)
                                <option value="{{ $item->id }}" {{ old('role_id', $user->role_id) == $item->id ? 'selected' : '' }}>{{ $item->nama_role }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="inputName" class="form-label">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" id="inputName" value="{{ old('nama', $user->nama) }}">
                    </div>
                    <script>
document.getElementById('nama_posisi').addEventListener('input', function (event) {
    var input = event.target;
    var value = input.value;
    
    // Hapus karakter yang bukan huruf, angka, atau spasi
    input.value = value.replace(/[^A-Za-z\s]/g, '');

    // Jika perlu, tambahkan logika untuk menampilkan pesan error
    // document.getElementById('err_alamat').textContent = 'Hanya huruf, angka, dan spasi yang diperbolehkan';
});
</script>
                    <div class="mb-3">
                        <label for="inputEmail" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="inputEmail" value="{{ old('email', $user->email) }}">
                    </div>

                    <!-- Dynamic Posisi and Wilayah Fields -->
                    <div id="dynamicPosisiContainer">
                        @foreach ($nama as $index => $detail)
                            <div class="posisi-group mb-3" id="posisiGroup{{ $index + 1 }}">
                                <label for="selectPosisi{{ $index + 1 }}" class="form-label">Posisi</label>
                                <select name="posisi[{{ $index + 1 }}][posisi_id]" id="selectPosisi{{ $index + 1 }}" class="form-select select2" style="color:black;">
                                    @foreach ($posisi as $item)
                                        <option value="{{ $item->id }}" {{ $detail->posisi_id == $item->id ? 'selected' : '' }}>{{ $item->nama_posisi }}</option>
                                    @endforeach
                                </select>

                                <label for="selectWilayah{{ $index + 1 }}" class="form-label mt-2">Wilayah</label>
                                <select name="posisi[{{ $index + 1 }}][wilayah][]" id="selectWilayah{{ $index + 1 }}" class="form-select select2" multiple="multiple" style="color:black; width:100%;">
                                    @foreach ($wilayah as $item)
                                        <option value="{{ $item->id }}" {{ in_array($item->nama_wilayah, explode(',', $detail->wilayah)) ? 'selected' : '' }}>{{ $item->nama_wilayah }}</option>
                                    @endforeach
                                </select>

                                <button type="button" class="btn btn-danger btn-sm mt-2 removePosisiButton">Hapus Posisi</button>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" id="addPosisiButton" class="btn btn-secondary btn-sm mb-3">Tambah Posisi</button>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main><!-- End #main -->

<!-- Include jQuery and Select2 JS/CSS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for the main role selector
    $('#selectRole').select2({
        placeholder: "Pilih...",
        allowClear: true
    });

    // Initialize Select2 for existing dynamic fields
    $('.select2').select2({
        placeholder: "Pilih...",
        allowClear: true
    });

    // Dynamically add/remove Posisi and Wilayah fields
    $('#addPosisiButton').click(function() {
        addPosisiField();
    });

    function addPosisiField() {
        var count = $('.posisi-group').length + 1; // Count the number of existing Posisi groups
        var html = `
            <div class="posisi-group mb-3" id="posisiGroup${count}">
                <label for="selectPosisi${count}" class="form-label">Posisi</label>
                <select name="posisi[${count}][posisi_id]" id="selectPosisi${count}" class="form-select select2" style="color:black;">
                    @foreach ($posisi as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_posisi }}</option>
                    @endforeach
                </select>

                <label for="selectWilayah${count}" class="form-label mt-2">Wilayah</label>
                <select name="posisi[${count}][wilayah][]" id="selectWilayah${count}" class="form-select select2" multiple="multiple" style="color:black; width:100%;">
                    @foreach ($wilayah as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_wilayah }}</option>
                    @endforeach
                </select>

                <button type="button" class="btn btn-danger btn-sm mt-2 removePosisiButton">Hapus Posisi</button>
            </div>
        `;
        $('#dynamicPosisiContainer').append(html);
        

        // Initialize Select2 for dynamically added fields
       // Initialize Select2 for dynamically added fields
       $('#selectPosisi' + count).select2({
            placeholder: "Pilih Posisi",
            allowClear: true
        }).on('change', function() {
            // Reset Wilayah when Posisi changes
            $('#selectWilayah' + count).val(null).trigger('change');
        });

        $('#selectWilayah' + count).select2({
            placeholder: "Pilih Wilayah",
            allowClear: true
        });
    }

    // Remove Posisi field group
    $(document).on('click', '.removePosisiButton', function() {
        $(this).closest('.posisi-group').remove();
    });

    // Show/hide Posisi and Wilayah fields based on role selection
    $('#selectRole').change(function() {
        if ($(this).val() == 2) { // Role ID 2 is "Rekrutmen"
            $('#addPosisiButton').show();
            $('#dynamicPosisiContainer').show();
        }
       else if ($(this).val() == 3) { // Role ID 2 is "Rekrutmen"
            $('#addPosisiButton').show();
            $('#dynamicPosisiContainer').show();
        } else {
            $('#addPosisiButton').hide();
            $('#dynamicPosisiContainer').hide(); // Clear all dynamic fields if role is not Rekrutmen
        }
    });

    // Initially hide Add Posisi button if not Rekrutmen
    if ($('#selectRole').val() != 2) {
        $('#addPosisiButton').hide();
    }
});

function validateForm() {
    let role = document.forms["saveform"]["role_id"].value.trim();
    let name = document.forms["saveform"]["nama"].value.trim();
    let email = document.forms["saveform"]["email"].value.trim();

    if (role === "" || role === "Pilih Role") {
        alert("Role harus dipilih.");
        return false;
    }
    if (name === "") {
        alert("Nama harus diisi.");
        return false;
    }
    if (email === "") {
        alert("Email harus diisi.");
        return false;
    }

    if (role == 2 || role == 3) { // Role ID 2 adalah "Rekrutmen"

        if ($('.posisi-group').length === 0) {
            alert("Minimal satu posisi harus diisi.");
            return false;
        }
        
        
        let allValid = true;
        let posisiSelected = new Set();
        
        $('.posisi-group').each(function() {
            let posisiSelect = $(this).find('select[name^="posisi"][name$="][posisi_id]"]');
            let wilayahSelect = $(this).find('select[name^="posisi"][name$="][wilayah][]"]');
            
            // Check if Posisi is selected
            let posisiValue = posisiSelect.val();
            if (!posisiValue) {
                alert("Posisi harus diisi.");
                allValid = false;
                return false; // Exit loop
            }
            
            // Check if Wilayah is selected if Posisi is selected
            if (wilayahSelect.val().length === 0) {
                alert("Wilayah harus diisi.");
                allValid = false;
                return false; // Exit loop
            }
            
            // Check for duplicate Posisi
            if (posisiSelected.has(posisiValue)) {
                alert("Posisi yang sama tidak boleh dipilih lebih dari sekali.");
                allValid = false;
                return false; // Exit loop
            }
            posisiSelected.add(posisiValue);
        });

        if (!allValid) {
            return false;
        }
    }

    return true;
}


</script>

@endsection