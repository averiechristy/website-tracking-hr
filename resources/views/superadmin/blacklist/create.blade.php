@extends('layouts.superadmin.app')
@section('content')

<main id="main" class="main">
    <div class="pagetitle">
      <h1>Tambah Daftar Blacklist</h1>      
    </div><!-- End Page Title -->
    <section class="section dashboard">
        <div class="card">
          <div class="card-body">
            <!-- Form input start -->
            <form action="{{route('superadmin.blacklist.store')}}" class="mt-3" id="saveform" onsubmit="return validateForm()" method="post">
                @csrf

                <div id="dynamicForm">
                    <div class="form-group row mb-3 candidate-entry">
                        <div class="col-md-5">
                            <label for="selectKandidat" class="form-label">Kandidat</label>
                            <select name="kandidat_id[]" class="form-select select2" style="color:black;" required>
                                <option value="" selected disabled>Pilih Kandidat</option>
                                @foreach ($kandidat as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_kandidat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea name="keterangan[]" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="col-md-1 d-flex align-items-center">
                            <button type="button" class="btn btn-danger remove-entry" onclick="removeEntry(this)">Hapus</button>
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
    newEntry.querySelector('textarea').value = '';

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
    const textareas = document.querySelectorAll('textarea[name="keterangan[]"]');
    const selectedKandidat = [];

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

    textareas.forEach(textarea => {
        if (textarea.value.trim() === "") {
            isValid = false;
            alert("Keterangan harus diisi.");
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
