@extends('layouts.superadmin.app')
@section('content')

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Tambah Akun User</h1>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('superadmin.akunuser.store') }}" id="saveform" class="mt-3" onsubmit="return validateForm()" method="post">
                    @csrf

                    <div class="mb-3">
                        <label for="selectRole" class="form-label">Role</label>
                        <select name="role_id" id="selectRole" class="form-select select2" style="color:black;" aria-label=".form-select-lg example">
                            <option value="" selected disabled>Pilih Role</option>
                            @foreach ($role as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_role }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="inputName" class="form-label">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" id="inputName">
                    </div>
                    
                    <script>
                    document.getElementById('nama').addEventListener('input', function (event) {
                        var input = event.target;
                        var value = input.value;
                        
                        // Hapus karakter yang bukan huruf, angka, atau spasi
                        input.value = value.replace(/[^A-Za-z\s]/g, '');
                    });
                    </script>

                    <div class="mb-3">
                        <label for="inputEmail" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="inputEmail">
                    </div>

                    <!-- Dynamic Posisi and Wilayah Fields -->
                    <div id="dynamicPosisiContainer"></div>

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

    // Fungsi untuk menambahkan form posisi-group
    function addPosisiField() {
        var count = $('.posisi-group').length + 1; // Hitung jumlah Posisi group yang ada
        var html = `
            <div class="posisi-group mb-3" id="posisiGroup${count}">
                <label for="selectPosisi${count}" class="form-label">Posisi</label>
               <select name="posisi[${count}][posisi_id]" id="selectPosisi${count}" class="form-select select2" style="color:black;">
    <option value="" selected disabled>Pilih Posisi</option>
    @foreach ($posisi as $item)
        <option value="{{ $item->id }}">{{ $item->nama_posisi }}</option>
    @endforeach
</select>

<label for="selectWilayah${count}" class="form-label mt-2">Wilayah</label>
<select name="posisi[${count}][wilayah][]" id="selectWilayah${count}" class="form-select select2" multiple="multiple" style="color:black; width:100%;">
    <option value="all">Semua Wilayah</option>
    @foreach ($wilayah as $item)
        <option value="{{ $item->id }}">{{ $item->nama_wilayah }}</option>
    @endforeach
</select>

                <button type="button" class="btn btn-danger btn-sm mt-2 removePosisiButton">Hapus Posisi</button>
            </div>
        `;
        $('#dynamicPosisiContainer').append(html);

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

// Logika untuk "Semua Wilayah"
$('#selectWilayah' + count).on('select2:select', function (e) {
    const selectedValue = e.params.data.id;

    if (selectedValue === 'all') {
        // Jika "Semua Wilayah" dipilih, pilih semua opsi lainnya
        const allOptions = $(this).find('option').map(function () {
            return $(this).val();
        }).get().filter(value => value !== 'all'); // Kecuali "Semua Wilayah"
        
        $(this).val(allOptions).trigger('change');
    }
});

$('#selectWilayah' + count).on('select2:unselect', function (e) {
    const unselectedValue = e.params.data.id;

    if (unselectedValue === 'all') {
        // Jika "Semua Wilayah" di-unselect, hapus semua pilihan
        $(this).val(null).trigger('change');
    }
});
    }

    // Event change untuk menampilkan otomatis form posisi-group saat memilih role tertentu
    $('#selectRole').change(function() {
        if ($(this).val() == 2 || $(this).val() == 3) { // Role ID 2 atau 3 adalah "Trainer" atau "Rekrutmen"
            $('#addPosisiButton').show(); // Tampilkan tombol Tambah Posisi
            $('#dynamicPosisiContainer').empty(); // Kosongkan container sebelum menambahkan baru
            addPosisiField(); // Tambahkan form posisi-group pertama
        } else {
            $('#addPosisiButton').hide();
            $('#dynamicPosisiContainer').empty(); // Kosongkan semua form jika bukan Trainer atau Rekrutmen
        }
    });

    // Event click untuk menambahkan form posisi-group baru saat tombol "Tambah Posisi" diklik
    $('#addPosisiButton').click(function() {
        addPosisiField();
    });

    // Hapus form posisi-group tertentu
    $(document).on('click', '.removePosisiButton', function() {
        $(this).closest('.posisi-group').remove();
    });

    // Sembunyikan tombol Tambah Posisi secara default
    $('#addPosisiButton').hide();
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

    if (role == 2 || role == 3) {
        if ($('.posisi-group').length === 0) {
            alert("Minimal satu posisi harus diisi.");
            return false;
        }

        let allValid = true;
        let posisiSelected = new Set();
        
        $('.posisi-group').each(function() {
            let posisiSelect = $(this).find('select[name^="posisi"][name$="][posisi_id]"]');
            let wilayahSelect = $(this).find('select[name^="posisi"][name$="][wilayah][]"]');
            
            if (!posisiSelect.val()) {
                alert("Posisi harus diisi.");
                allValid = false;
                return false;
            }
            
            if (wilayahSelect.val().length === 0) {
                alert("Wilayah harus diisi.");
                allValid = false;
                return false;
            }
            
            if (posisiSelected.has(posisiSelect.val())) {
                alert("Posisi yang sama tidak boleh dipilih lebih dari sekali.");
                allValid = false;
                return false;
            }
            posisiSelected.add(posisiSelect.val());
        });

        if (!allValid) {
            return false;
        }
    }

    return true;
}
</script>

@endsection
