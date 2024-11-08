@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
      <h1>Master Konfirm</h1>
    </div><!-- End Page Title -->
    <section class="section dashboard">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <a href="{{route( 'superadmin.masterkonfirm.create')}}" class="btn btn-sm btn-primary mb-3 mt-3">Tambah Data</a>
                @include('components.alert')               
                <div class="table-responsive">
                <div class="dataTables_length mb-3" id="myDataTable_length">
                    <label for="entries">Show
                        <select id="entries" name="myDataTable_length" aria-controls="myDataTable" onchange="changeEntries()" class>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>entries
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
                       <th>Tanggal</th>
                       <th>Nama PIC</th>
                       <th>Posisi</th>
                        <th>Wilayah</th>
                        <th>Jumlah Undang</th>
                        <th>Jumlah Konfirm</th>
                       <th>Keterangan</th>
                       <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     @foreach ($data as $item)
                     <tr>
                       <td>{{$item->tanggal}}</td>
                       <td>{{$item->nama_sourcing}}</td>
                       <td>{{$item->nama_posisi}}</td>
                       <td>{{$item->nama_wilayah}}</td>
                       <td>{{$item->jumlah_undang_otomatis}}</td>
                       <td>{{$item->jumlah_konfirm_manual}}</td>
                       <td>{{$item->keterangan}}</td>
                        <td> 
                            <form method="POST" action="{{ route( 'superadmindeletemasterkonfirm', $item->id) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger show_confirm" data-bs-toggle="tooltip" title="Hapus">
                                <i class="bi bi-trash" style="color:white;"></i>
                            </button>
                            </form>
                        </td>
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
@endsection