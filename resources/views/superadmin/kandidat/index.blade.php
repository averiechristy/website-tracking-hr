@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">
<div class="pagetitle">
  <h1>Kandidat</h1>
</div>
<!-- End Page Title -->
<section class="section dashboard">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

            
          
            <div class="importdata ml-3 mr-3 mt-4 mb-4">
                
            <p style="color:black;">Unggah file untuk menambahkan kandidat baru, atau tambahkan secara manual dengan menekan tombol Tambah Kandidat. <br> Anda dapat mengunggah file Excel</p>
            <a href="{{ route('superadmin.kandidat.create') }}" class="btn btn-sm btn-primary mb-4">Tambah Kandidat</a>
            <a href="{{ route('download.template') }}" class="btn btn-info btn-sm mb-4" download>Unduh Template</a>
    
            <form id="upload-form" action="{{ route('importkandidat') }}" method="post" enctype="multipart/form-data" style="display: flex; align-items: center;">
        @csrf
       
    
                        <input type="date" name="tanggal" id="tanggal" class="form-control mr-3" required>
          
                        <select name="sumber" id="sumber" class="form-select mr-3" required>
            <option value="" disabled selected>Pilih Sumber</option>
            @foreach ($sumber as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_sumber }}</option>
                                @endforeach
        </select>

        <input type="file" name="file" accept=".xlsx, .xls" style="margin-right: 10px;" class="form-control" required>

        <button class="btn btn-warning" type="submit">Unggah File</button>
    </form>
</div>


                <div class="card-body">
              
@include('components.alert')
                  <!-- Filter by No Hp Status -->
                  <form method="GET" action="{{ route('superadmin.kandidat.index') }}" class="mb-3">
    <div class="row">
    <p>Filter Data</p>
        <div class="col-md-4 mt-3">

        
            <select name="filter_posisi" class="form-select" onchange="this.form.submit()">
                <option value="">-- Semua Posisi --</option>
                @foreach($posisi as $p)
                    <option value="{{ $p->id }}" {{ $p->id == $selectedPosisi ? 'selected' : '' }}>
                        {{ $p->nama_posisi }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 mt-3">
            <select name="filter_wilayah" class="form-select" onchange="this.form.submit()">
                <option value="">-- Semua Wilayah --</option>
                @foreach($wilayah as $w)
                    <option value="{{ $w->id }}" {{ $w->id == $selectedWilayah ? 'selected' : '' }}>
                        {{ $w->nama_wilayah }}
                    </option>
                @endforeach
            </select>
        </div>

      

        <!-- Status Copy filter -->
        <div class="col-md-4 mt-3">
            <select name="filter_status_copy" class="form-select" onchange="this.form.submit()">
                <option value="">-- Semua No Hp --</option>
                <option value="Copied" {{ $selectedStatusCopy == 'Copied' ? 'selected' : '' }}>
                    Copied
                </option>
                <option value="Not Copied" {{ $selectedStatusCopy == 'Not Copied' ? 'selected' : '' }}>
                    Not Copied
                </option>
            </select>
        </div>

          <!-- Tanggal Awal filter -->
          <div class="col-md-4 mt-3">
            <input type="date" name="filter_tanggal_awal" class="form-control" value="{{ request('filter_tanggal_awal') }}" onchange="this.form.submit()">
        </div>

        <!-- Tanggal Akhir filter -->
        <div class="col-md-4 mt-3">
            <input type="date" name="filter_tanggal_akhir" class="form-control" value="{{ request('filter_tanggal_akhir') }}" onchange="this.form.submit()">
        </div>
    </div>
</form>


                    <div class="mb-3">
                        <!-- <a href="#" id="processNoHpButton" class="btn btn-sm btn-warning">Proses No Hp</a> -->
                        <a href="#" id="copyNoHpButton" class="btn btn-sm btn-warning">Copy No Hp</a>
                    </div>

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
                                    <th scope="col" class="text-center">Tanggal</th>
                                    <th scope="col" class="text-center">Sumber</th>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center">Posisi</th>
                                    <th class="text-center">Wilayah</th>
                                    <th class="text-center">No Hp</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Status Copy</th>
                                    <th class="text-center">Status Saat Ini</th>
                                    <th class="text-center">Detail Tahapan</th>
                                    <th class="text-center">Created By</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>

    <tbody>
    @foreach ($kandidat as $item)
    <tr>
        <td>
            <input type="checkbox" class="rowCheckbox" name="checked_ids[]" value="{{ $item->id }}">
        </td>
        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>

        <td>{{ $item->sumber->nama_sumber }}</td>
        <td>
            <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $item->nama_kandidat }}">
                {{ \Illuminate\Support\Str::limit($item->nama_kandidat, 15, '...') }}
            </span>
        </td>
        <td>{{ $item->posisi->nama_posisi }}</td>
        <td>{{ $item->wilayah->nama_wilayah }}</td>
        <td>{{ $item->no_hp }}</td>
        <td>{{ $item->email }}</td>
        <td>{{ $item->status_copy }}</td>
        <td>{{ $item->status_hire }}</td>
        <td> <a href="{{ route('detailtahapan', $item->id) }}" class="detail-member">Lihat Detail</a></td> 
        <td>{{ $item->created_by }}</td>
        <td>
            <a href="{{ route('superadminshowkandidat', $item->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
            <!-- Delete Button Form -->
            <form method="POST" action="{{ route('superadmindeletekandidat', $item->id) }}" style="display: inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger show_confirm" data-bs-toggle="tooltip" title="Hapus" 
        @if ($item->status_hire !== 'Belum Diproses') disabled @endif>
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

    document.getElementById('copyNoHpButton').addEventListener('click', function () {
    var selectedCheckboxes = document.querySelectorAll('.rowCheckbox:checked');
    var noHpList = [];

    selectedCheckboxes.forEach(function (checkbox) {
        var row = checkbox.closest('tr');
        var noHp = row.querySelector('td:nth-child(7)').textContent.trim();
        noHp = '+62' + noHp.slice(1);
        noHpList.push(noHp);

        // Update status_copy to 'Copied'
        row.querySelector('td:nth-child(9)').textContent = 'Copied';
    });

    if (noHpList.length > 0) {
        var textToCopy = noHpList.join(', ') + ', ';
        
        if (navigator.clipboard) {
            // Clipboard API supported
            navigator.clipboard.writeText(textToCopy).then(function () {
                alert('Nomor HP berhasil disalin ke clipboard.');
            }).catch(function (err) {
                console.error('Error copying text to clipboard:', err);
                alert('Gagal menyalin nomor HP ke clipboard.');
            });
        } else {
            // Fallback to older method
            var tempInput = document.createElement('input');
            tempInput.value = textToCopy;
            document.body.appendChild(tempInput);
            tempInput.select();
            try {
                document.execCommand('copy');
                alert('Nomor HP berhasil disalin ke clipboard.');
            } catch (err) {
                console.error('Error copying text to clipboard:', err);
                alert('Gagal menyalin nomor HP ke clipboard.');
            }
            document.body.removeChild(tempInput);
        }

        // Optional: Send an AJAX request to update the status in the backend
        var ids = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
        var url = new URL('{{ route('superadmin.kandidat.updateStatus') }}', window.location.origin);
        
        console.log('Sending request to:', url.toString());

        fetch(url.toString(), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids, status: 'Copied' })
        }).then(response => {
            if (!response.ok) {
                console.error('Failed to update status:', response.statusText);
                alert('Terjadi kesalahan saat memperbarui status.');
                throw new Error('Network response was not ok');
            }
            return response.json();
        }).then(data => {
            console.log('Status updated:', data);
        }).catch(function (error) {
            console.error('Error updating status:', error);
        });
    } else {
        alert('Silakan pilih kandidat terlebih dahulu.');
    }
});





    document.getElementById('processNoHpButton').addEventListener('click', function() {
        var selectedCheckboxes = document.querySelectorAll('.rowCheckbox:checked');
        var noHpList = [];

        selectedCheckboxes.forEach(function(checkbox) {
            var row = checkbox.closest('tr');
            var noHp = row.querySelector('td:nth-child(6)').textContent.trim();
            // Ganti 0 dengan +62 dan tambahkan format yang diinginkan
            noHp = '+62' + noHp.slice(1);
            noHpList.push(noHp);
        });

        if (noHpList.length > 0) {
            // Redirect ke halaman baru dengan nomor HP
            var url = new URL('{{ route('superadmin.kandidat.processNoHp') }}', window.location.origin);
            url.searchParams.append('no_hp', noHpList.join(','));
            window.location.href = url.toString();
        } else {
            alert('Silakan pilih kandidat terlebih dahulu.');
        }
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
