@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">
<div class="pagetitle">
  <h1>Jadwal Kandidat {{$nama}}</h1>
</div>
@include('components.alert')    

<section class="section dashboard">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive mt-4">
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
                                    <th>Posisi</th>
                                    <th>Wilayah</th> 
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
<tbody>
    @foreach ($data as $item)
        <tr data-id="{{ $item->id }}">
            <td>{{ $item->posisi->nama_posisi }}</td>
            <td>{{ $item->wilayah->nama_wilayah }}</td>
            <td>{{ $item->status_tahapan }}</td>
            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
            <td class="hasilStatus">{{ $item->hasil_status }}</td>
            <td>
                <button type="button" class="btn btn-success btn-sm" onclick="handleAction('lolos', {{ $item->id }})"{{ $item->hasil_status !== 'Dijadwalkan' ? 'disabled' : '' }}>Lolos</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="handleAction('tidak lolos', {{ $item->id }})"{{ $item->hasil_status !== 'Dijadwalkan' ? 'disabled' : '' }}>Tidak Lolos</button>
                <button type="button" class="btn btn-warning btn-sm" onclick="handleAction('stop proses', {{ $item->id }})"{{ $item->hasil_status !== 'Dijadwalkan' ? 'disabled' : '' }}>Stop Proses</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="handleAction('tidak hadir', {{ $item->id }})"{{ $item->hasil_status !== 'Dijadwalkan' ? 'disabled' : '' }}>Tidak Hadir</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="handleAction('simpan kandidat', {{ $item->id }})"{{ $item->hasil_status !== 'Dijadwalkan' ? 'disabled' : '' }}>Simpan Kandidat</button>
            </td>
        </tr>
    @endforeach
</tbody>

                        </table>
                    </div>

          <!-- Modal Konfirmasi Penjadwalan Ulang -->
<div class="modal fade" id="confirmRescheduleModal" tabindex="-1" aria-labelledby="confirmRescheduleLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmRescheduleLabel">Penjadwalan Ulang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda ingin melakukan penjadwalan ulang?</p>
                <button type="button" class="btn btn-primary" onclick="showDateInputModal()">Ya</button>
                <button type="button" class="btn btn-secondary" onclick="skipReschedule()">Tidak</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Input Tanggal untuk Penjadwalan Ulang -->
<div class="modal fade" id="dateInputModal" tabindex="-1" aria-labelledby="dateInputLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dateInputLabel">Masukkan Tanggal Penjadwalan Ulang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="date" id="rescheduleDate" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveReschedule()">Simpan</button>
            </div>
        </div>
    </div>
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
function handleAction(action, id) {
    let status;

    if (action === 'tidak hadir') {
        // Simpan kandidat ID untuk penjadwalan ulang jika dipilih
        window.currentKandidatId = id;

        // Tampilkan modal konfirmasi penjadwalan ulang
        let confirmModal = new bootstrap.Modal(document.getElementById('confirmRescheduleModal'));
        confirmModal.show();
        return;
    }

    // Proses aksi lainnya seperti biasa
    switch (action) {
        case 'lolos':
            status = 'Lolos';
            break;
        case 'tidak lolos':
            status = 'Tidak Lolos';
            break;
        case 'stop proses':
            status = 'Stop Proses';
            break;
        case 'simpan kandidat':
            status = 'Simpan Kandidat';
            break;
        default:
            status = '';
    }

    // Kirim permintaan AJAX untuk memperbarui status
    updateStatus(id, status);
}

// Menampilkan modal input tanggal jika pengguna memilih penjadwalan ulang
function showDateInputModal() {
    let dateModal = new bootstrap.Modal(document.getElementById('dateInputModal'));
    dateModal.show();
}

// Jika pengguna memilih untuk tidak melakukan penjadwalan ulang
function skipReschedule() {
    // Tutup modal konfirmasi
    bootstrap.Modal.getInstance(document.getElementById('confirmRescheduleModal')).hide();

    // Update status menjadi "Tidak Hadir"
    updateStatus(window.currentKandidatId, 'Tidak Hadir');
}

// Fungsi untuk menyimpan penjadwalan ulang
function saveReschedule() {
    let rescheduleDate = document.getElementById('rescheduleDate').value;

    // Pastikan tanggal terisi
    if (!rescheduleDate) {
        alert("Harap isi tanggal penjadwalan ulang.");
        return;
    }

    // Kirim data ke server untuk menyimpan log tahapan dengan penjadwalan ulang
    fetch("{{ route('reschedule-kandidat') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id: window.currentKandidatId, tanggal: rescheduleDate })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);

            // Perbarui tampilan status kandidat
            document.querySelector(`tr[data-id='${window.currentKandidatId}'] .hasilStatus`).textContent = 'Dijadwalkan';

            // Tutup modal
            bootstrap.Modal.getInstance(document.getElementById('dateInputModal')).hide();
        } else {
            alert("Error updating status: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
}

// Fungsi untuk memperbarui status
function updateStatus(id, status) {
    fetch("{{ route('update-status') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id: id, status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            document.querySelector(`tr[data-id='${id}'] .hasilStatus`).textContent = status;
        } else {
            alert("Error updating status: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
}



</script>

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
