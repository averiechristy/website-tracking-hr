@extends('layouts.superadmin.app')
@section('content')

<main id="main" class="main">
    <div class="pagetitle">
      <h1>Tambah Daftar Training ABM</h1>      
    </div><!-- End Page Title -->
    <section class="section dashboard">
        <div class="card">
          <div class="card-body">
            <!-- Form input start -->
            <form action="{{route('superadmin.trainingabm.store')}}" class="mt-3" id="saveform" onsubmit="return validateForm()" method="post">
                @csrf
                <div class="mb-3">
                        <label for="inputTanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control">
                    </div>
                    <script>
document.addEventListener('DOMContentLoaded', function() {
    var dateInput = document.getElementById('tanggal');
    
    // Set maximum date to today's date
    var today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);
    
    dateInput.addEventListener('click', function() {
        this.showPicker();
    });
});
</script>

  <div class="mb-3">
                            <label for="selectabm" class="form-label">ABM</label>
                            <select name="abm_id" id="selectabm" class="form-select select2" style="color:black;">
                                <option value="" selected disabled>Pilih ABM</option>
                                @foreach ($abm as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_ABM }}</option>
                                @endforeach
                            </select>
                        </div>

                        <script>
    $(document).ready(function() {
        $('#selectabm').select2({
            placeholder: "Pilih ABM",
            allowClear: true
        });
    });
</script>

                <div id="dynamicForm">
                    <div class="form-group row mb-3 candidate-entry">
                        <div class="col-md-5">
                            <label for="selectKandidat" class="form-label">Kandidat</label>
                            <select name="kandidat_id[]" class="form-select select2" style="color:black;">
                                <option value="" selected disabled>Pilih Kandidat</option>
                                @foreach ($kandidat as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_kandidat }}</option>
                                @endforeach
                            </select>
                        </div>

                       

                        <div class="mt-2">
                            <button type="button" class="btn btn-danger btn-sm remove-entry" onclick="removeEntry(this)">Hapus</button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success mt-3" onclick="addEntry()">Tambah Kandidat</button>
                <button type="submit" class="btn btn-primary mt-3">Simpan</button>
            </form>
            <!-- Form input end -->
          </div>
        </div>
    </section>
</main><!-- End #main -->

<!-- Add these inside the <head> tag -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

<!-- Add these before closing the </body> tag -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
    // Initialize Select2 for all select elements when the document is ready
    $(document).ready(function() {
        $('.select2').select2();
    });
    function addEntry() {
    const dynamicForm = document.getElementById('dynamicForm');
    
    // Clone the candidate entry
    const originalEntry = document.querySelector('.candidate-entry');
    
    // Destroy Select2 instance before cloning
    $(originalEntry).find('select').select2('destroy');
    
    const newEntry = originalEntry.cloneNode(true); // Clone the entry
    
    // Reset values in the cloned entry
    newEntry.querySelector('select').value = '';
   

    // Append the new entry
    dynamicForm.appendChild(newEntry);

    // Re-initialize Select2 for both original and cloned entries
    $(originalEntry).find('select').select2({ width: '100%' });
    $(newEntry).find('select').select2({ width: '100%' });
}


    function removeEntry(button) {
        const dynamicForm = document.getElementById('dynamicForm');
        if (dynamicForm.childElementCount > 1) {
            button.closest('.candidate-entry').remove();
        } else {
            alert('Minimal satu kandidat harus diisi.');
        }
    }

    function validateForm() {
    let isValid = true;
    const selects = document.querySelectorAll('select[name="kandidat_id[]"]');
    let abm =document.forms["saveform"]["abm_id"].value.trim();
    let tanggal =document.forms["saveform"]["tanggal"].value.trim();
    const selectedKandidat = [];


    if(tanggal === ""){
        alert("Tanggal harus diisi.");
        return false;
    }
    if(abm === ""){
        alert("ABM harus diisi.");
        return false;
    }


    // Check for empty fields and duplicate kandidat
    selects.forEach(select => {
        const selectedValue = select.value;
        
        // Check if the select value is empty
        if (selectedValue === "") {
            isValid = false;
            alert("Kandidat harus diisi.");
        }

        // Check if the selected value has already been chosen
        if (selectedKandidat.includes(selectedValue)) {
            isValid = false;
            alert("Kandidat yang sama tidak boleh dipilih lebih dari sekali.");
        } else {
            selectedKandidat.push(selectedValue);
        }
    });

   

    return isValid;
}

</script>

<style>
    .form-group.row.mb-3.candidate-entry {
        margin-bottom: 20px;
       
    }
    /* Styling to maintain label alignment */
    .form-group label {
        display: block;
        margin-bottom: 5px;
    }


</style>

@endsection
