@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
      <h1>Laporan Performance</h1>
    </div><!-- End Page Title -->
<section class="section dashboard">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
            <form name="saveform" action="{{route('laporanperformance.process')}}" method="post" onsubmit="return validateForm()">
            @csrf
    <div class="row ml-1">
        <div class="col-md-3 mt-3">
            <p>Bulan</p>
            <select name="bulan" id="filterMonth" class="form-control" style="color:black;" required>
                @php
                    $months = [
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                    ];
                    $currentMonth = date(format: 'n');
                @endphp
                <option value="" disabled selected>Pilih Bulan</option>
                @foreach ($months as $key => $month)
                    <option value="{{ $key }}">{{ $month }}</option>
                @endforeach
            </select>
        </div>  
        <div class="col-md-3 mt-3">
            <p>Tahun</p>
            <select name="tahun" id="filterYear" class="form-control" style="color:black;" required>
                @php
                    $currentYear = date('Y');
                    $previousYear = $currentYear - 1;
                @endphp
                <option value="" disabled selected>Pilih Tahun</option>
                <option value="{{ $previousYear }}">{{ $previousYear }}</option>
                <option value="{{ $currentYear }}">{{ $currentYear }}</option>
            </select>
        </div>
        <div class="col-md-3 mt-3">
        <p>Posisi</p>
            <select name="posisi" class="form-select" required >
                <option value="">-- Pilih Posisi --</option>
                @foreach($posisi as $p)
                    <option value="{{ $p->id }}">
                        {{ $p->nama_posisi }}
                    </option>
                @endforeach
            </select>
        </div>        

        <div class="col-md-3 mt-5">
            <button type="submit" class="btn btn-primary mt-2">Proses</button>
        </div>

        <div class="col-md-3" style="margin-top:26px;">
</div>
    </div>
            </form>         
            <form name="downloadform" action="{{route('laporanperformance.download')}}" method="post">        
                @csrf
    <div class="download-laporan ml-3">
    <div class="row">
        <div class="col-md-3 mt-3">
            <p>Tahun</p>
            <select name="tahun_download" id="filterYearDownload" class="form-control" style="color:black;" required>
                @php
                    $currentYear = date('Y');
                    $previousYear = $currentYear - 1;
                @endphp
                <option value="" disabled selected>Pilih Tahun</option>
                <option value="{{ $previousYear }}">{{ $previousYear }}</option>
                <option value="{{ $currentYear }}">{{ $currentYear }}</option>
            </select>
        </div>

        <div class="col-md-3 mt-3">
            <p>Posisi</p>
            <select name="posisi_download" class="form-select" required>
                <option value="">-- Pilih Posisi --</option>
                <option value="All">Semua Posisi</option>
                @foreach($posisi as $p)
                    <option value="{{ $p->id }}">
                        {{ $p->nama_posisi }}
                    </option>
                @endforeach
            </select>
        </div>        

        <div class="col-md-3 mt-5">
        <button type="submit" class="btn btn-primary mt-2">Download Laporan</button>
        </div>
    </div>
    </div>
    </form>

              <div class="card-body">
                @include('components.alert')               
                <div class="table-responsive mt-5">
                <div class="dataTables_length mb-3" id="myDataTable_length">
                    <label for="entries">Show
                        <select id="entries" name="myDataTable_length" aria-controls="myDataTable" onchange="changeEntries()" class>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>                         
                        </select> entries
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
                       <th>Bulan</th>
                       <th>Tahun</th>
                       <th>Lolos Sortir</th>
                       <th>Konfirmasi Hadir</th>
                       <th>Lolos</th>
                       <th>Training</th>
                       <th>Tandem</th>
                       <th>PKM Baru</th>
                       <th>PKM Batal Join</th>
                       <th>Mitra Keluar / Resign</th>
                       <th>Action</th>
                    </tr>
                  </thead>

                  <tbody>
                  @foreach ($laporanperformance as $item )
                  <tr>
                    <td>{{ \Carbon\Carbon::createFromDate(null, $item->bulan, 1)->format('F') }}</td>
                    <td>{{$item->tahun}}</td>
                    <td>{{$item->lolos_sortir}}</td>
                    <td>{{$item->konfirmasi_hadir}}</td>
                    <td>{{$item->lolos}}</td>
                    <td>{{$item->training}}</td>
                    <td>{{$item->tandem}}</td>
                    <td>{{$item->PKM_baru}}</td>
                    <td>{{$item->PKM_batal_join}}</td>
                    <td>{{$item->resign}}</td>
                <td>
                      <!-- Delete Button Form -->                     
    <form method="POST" action="{{ route('delete.laporanperformance', $item->id) }}" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger show_confirm" data-toggle="tooltip" title="Hapus">
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
                Show <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalEntries">0</span> entries
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

  </main>

  <!-- End #main -->
  
  <script>
    var itemsPerPage = 10; // Ubah nilai ini sesuai dengan jumlah item per halaman
    var currentPage = 1;
    var filteredData = [];
    
function initializeData() {
    var tableRows = document.querySelectorAll("table tbody tr");
    filteredData = Array.from(tableRows); // Konversi NodeList ke array
    updatePagination();
}



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

    var tableRows = document.querySelectorAll("table tbody tr");
    tableRows.forEach(function (row) {
        row.style.display = 'none';
    });

    
    for (var i = startIndex; i < endIndex && i < filteredData.length; i++) {
        filteredData[i].style.display = 'table-row';
    }

  
    var totalPages = Math.ceil(filteredData.length / itemsPerPage);
    var pageNumbers = document.getElementById('pageNumbers');
    pageNumbers.innerHTML = '';

    var totalEntries = filteredData.length;

    document.getElementById('showingStart').textContent = startIndex + 1;
    document.getElementById('showingEnd').textContent = Math.min(endIndex, totalEntries);
    document.getElementById('totalEntries').textContent = totalEntries;

    var pageRange = 3; 
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

    // Set current Page kembali ke 1
    currentPage = 1;

    updatePagination();
}

updatePagination();

    // Menangani perubahan pada input pencarian
    
    document.getElementById('search').addEventListener('input', applySearchFilter);
    
             
</script>
@endsection