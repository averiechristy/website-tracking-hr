@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">
<div class="pagetitle">
  <h1>Kandidat Baru (Belum Dijadwalkan)</h1>
</div>
@include('components.alert')    

<button id="processButton" type="button" class="btn btn-warning mb-3" onclick="processStatus()">Buat Penjadwalan</button>

<section class="section dashboard">
<div class="row">
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
                  
<form method="GET" action="{{ route('superadmin.belumproses.index') }}" class="mb-3 mt-4">
    <div class="row">
    <div class="col-md-4">
        <select name="filter_posisi" class="form-select" onchange="this.form.submit()">
            <option value="">-- Semua Posisi --</option>
            @foreach($posisi as $p)
                <option value="{{ $p->id }}" {{ $p->id == $selectedPosisi ? 'selected' : '' }}>
                    {{ $p->nama_posisi }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <select name="filter_wilayah" class="form-select" onchange="this.form.submit()">
            <option value="">-- Semua Wilayah --</option>
            @foreach($wilayah as $w)
                <option value="{{ $w->id }}" {{ $w->id == $selectedWilayah ? 'selected' : '' }}>
                    {{ $w->nama_wilayah }}
                </option>
            @endforeach
        </select>
    </div> 
    </div>
</form>
                    <div class="table-responsive">
                        <div class="dataTables_length mb-3" id="myDataTable_length">
                            <label for="entries"> Show
                                <select id="entries" name="myDataTable_length" aria-controls="myDataTable" onchange="changeEntries()" class>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                    entries
                            </label>
                        </div>
                        <div id="myDataTable_filter" class="dataTables_filter">
                            <label for="search">Search
                                <input id="search" placeholder>
                            </label>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <input type="checkbox" class="contoh" id="checkAll" data-bs-toggle="tooltip" title="Select All">
                                    </th>                              
                                    <th scope="col">Nama Kandidat</th>
                                    <th scope="col">Posisi</th>
                                    <th scope="col">Wilayah</th> 
                                    <!-- <th>Action</th> -->
                                </tr>
                            </thead>
                        <tbody>
                @foreach ($kandidat as $item)
                    <tr>
                        <td>
                            <input type="checkbox" class="rowCheckbox" name="checked_ids[]" value="{{ $item->id }}">
                        </td>
                        <td>
                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $item->nama_kandidat }}">
                                {{ \Illuminate\Support\Str::limit($item->nama_kandidat, 15, '...') }}
                            </span>
                        </td>
                        <td>{{ $item->posisi->nama_posisi }}</td>
                        <td>{{ $item->wilayah->nama_wilayah }}</td>  
                        <!-- <td>
                        <a  href="{{ route('showjadwal', $item->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Lihat & Edit Jadwal">
                        <i class="bi bi-pencil"></i>
                    </a>
                        </td> -->
                    </tr>
                @endforeach
                            </tbody>
                        </table>
                    </div>

                            <div class="dataTables_info" id="dataTableInfo" role="status" aria-live="polite">
                                Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalEntries">0</span> entries
                            </div>

                            <div class="dataTables_paginate paging_simple_numbers" id="myDataTable_paginate">
                                <a href="#" class="paginate_button" id="doublePrevButton" onclick="doublePreviousPage()"><i class="bi bi-chevron-double-left" aria-hidden="true"></i></a>
                                <a href="#" class="paginate_button" id="prevButton" onclick="previousPage()"><i  class="bi bi-chevron-left" aria-hidden="true"></i></a>
                                <span>
                                    <a id="pageNumbers" aria-controls="myDataTable" role="link" aria-current="page" data-dt-idx="0" tabindex="0"></a>
                                </span>
                                <a href="#" class="paginate_button" id="nextButton" onclick="nextPage()"><i class="bi bi-chevron-right" aria-hidden="true"></i></a>
                                <a href="#" class="paginate_button" id="doubleNextButton" onclick="doubleNextPage()"><i class="bi bi-chevron-double-right" aria-hidden="true"></i></a>
                            </div>

                </div>
            </div>
        </div>
    </div>
</section>
</main><!-- End #main -->

<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi Proses</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <p>Apakah anda yakin ingin memproses kandidat berikut dengan status <strong><span id="modalStatus"></span></strong>?</p>
      
        <ul id="modalKandidatList"></ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary" onclick="submitForm()">Proses</button>
      </div>
    </div>
  </div>
</div>

<script>

function processStatus() {
    // Ambil semua checkbox yang dicentang
    var checkedIds = [];
    var checkboxes = document.querySelectorAll('.rowCheckbox:checked');

    // Tambahkan value setiap checkbox yang dicentang ke dalam array
    checkboxes.forEach(function(checkbox) {
        checkedIds.push(checkbox.value);
    });

    // Cek apakah ada kandidat yang dipilih
    if (checkedIds.length === 0) {
        alert('Kandidat harus dipilih.');
        return; // Hentikan proses jika tidak ada kandidat yang dipilih
    }

    // Redirect ke halaman penjadwalan dengan ID kandidat terpilih
    window.location.href = "{{ route('superadmin.penjadwalan') }}?ids=" + checkedIds.join(',');
}

function submitForm() {
    document.getElementById('processForm').submit();
}


   document.getElementById('checkAll').addEventListener('click', function() {
    var checkboxes = document.querySelectorAll('.rowCheckbox');
    var isChecked = this.checked;
    
    // Only check/uncheck the visible rows
    var tableRows = document.querySelectorAll("table tbody tr");
    tableRows.forEach(function(row) {
        if (row.style.display !== 'none') {
            var checkbox = row.querySelector('.rowCheckbox');
            checkbox.checked = isChecked;
        }
    });
});


   
</script>
<script>
    var itemsPerPage = 10; // Ubah nilai ini sesuai dengan jumlah item per halaman
    var currentPage = 1;
    var filteredData = [];
    
    function initializeData() {
    var tableRows = document.querySelectorAll("table tbody tr");
    filteredData = Array.from(tableRows); // Konversi NodeList ke array
    updatePagination();
}

// Panggil fungsi initializeData() untuk menginisialisasi data saat halaman dimuat
initializeData();
    
function doublePreviousPage() {
        if (currentPage > 1) {
            currentPage = 1;
            updatePagination();
        }
    }
    
function nextPage() {
    var totalPages = Math.ceil(document.querySelectorAll("table tbody tr").length / itemsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        updatePagination();
    }
}
  
function doubleNextPage() {
    var totalPages = Math.ceil(document.querySelectorAll("table tbody tr").length / itemsPerPage);
    if (currentPage < totalPages) {
        currentPage = totalPages;
        updatePagination();
    }
}

    function previousPage() {
        if (currentPage > 1) {
            currentPage--;
            updatePagination();
        }
    }
 
    function updatePagination() {
    var startIndex = (currentPage - 1) * itemsPerPage;
    var endIndex = startIndex + itemsPerPage;

    // Sembunyikan semua baris
    var tableRows = document.querySelectorAll("table tbody tr");
    tableRows.forEach(function (row) {
        row.style.display = 'none';
    });

    // Tampilkan baris untuk halaman saat ini
    for (var i = startIndex; i < endIndex && i < filteredData.length; i++) {
        filteredData[i].style.display = 'table-row';
    }

    // Update nomor halaman
    var totalPages = Math.ceil(filteredData.length / itemsPerPage);
    var pageNumbers = document.getElementById('pageNumbers');
    pageNumbers.innerHTML = '';

    var totalEntries = filteredData.length;

    document.getElementById('showingStart').textContent = startIndex + 1;
    document.getElementById('showingEnd').textContent = Math.min(endIndex, totalEntries);
    document.getElementById('totalEntries').textContent = totalEntries;

    var pageRange = 3; // Jumlah nomor halaman yang ditampilkan
    var startPage = Math.max(1, currentPage - Math.floor(pageRange / 2));
    var endPage = Math.min(totalPages, startPage + pageRange - 1);

    for (var i = startPage; i <= endPage; i++) {
    var pageButton = document.createElement('button');
    pageButton.className = 'btn  btn-sm btn-spacing';
    pageButton.textContent = i;
    if (i === currentPage) {
        pageButton.classList.add('btn-active');
    }
    pageButton.onclick = function () {
        currentPage = parseInt(this.textContent);
        updatePagination();
    };
    pageNumbers.appendChild(pageButton);
}
}
    function changeEntries() {
        var entriesSelect = document.getElementById('entries');
        var selectedEntries = parseInt(entriesSelect.value);

        // Update the 'itemsPerPage' variable with the selected number of entries
        itemsPerPage = selectedEntries;

        // Reset the current page to 1 when changing the number of entries
        currentPage = 1;

        // Update pagination based on the new number of entries
        updatePagination();
    }

    function applySearchFilter() {
    var searchInput = document.getElementById('search');
    var filter = searchInput.value.toLowerCase();
    
    // Mencari data yang sesuai dengan filter
    filteredData = Array.from(document.querySelectorAll("table tbody tr")).filter(function (row) {
        var rowText = row.textContent.toLowerCase();
        return rowText.includes(filter);
    });

    // Set currentPage kembali ke 1
    currentPage = 1;

    updatePagination();
}

updatePagination();


    // Menangani perubahan pada input pencarian
    document.getElementById('search').addEventListener('input', applySearchFilter);
    // Panggil updatePagination untuk inisialisasi
  
             
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
    var checkboxes = document.querySelectorAll('.rowCheckbox');
    checkboxes.forEach(function (checkbox) {
        checkbox.checked = false; // Atur checkbox menjadi tidak tercentang
    });

    
});


document.addEventListener('DOMContentLoaded', function () {
    var checkboxes = document.querySelectorAll('.contoh');
    checkboxes.forEach(function (checkbox) {
        checkbox.checked = false; // Atur checkbox menjadi tidak tercentang
    });

    
});

</script>

@endsection
