@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">
<div class="pagetitle">
  <h1>Jadwal Tandem</h1>
</div>
@include('components.alert')    



<section class="section dashboard">
<div class="row">
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
                  
<form method="GET" action="{{ route('superadmin.belumprosesafter.index') }}" class="mb-3 mt-4">
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
                                    <th>Nama</th>      
                                    <th>Posisi</th>
                                    <th>Wilayah</th> 
                                    <th>Jadwal</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                   
                                    <th>Action</th>
                                </tr>
                            </thead>
<tbody>
    @foreach ($logTahapan as $item)
        <tr data-id="{{ $item->id }}">
            <td>{{ $item->kandidat->nama_kandidat }}</td>
            <td>{{ $item->posisi->nama_posisi }}</td>
            <td>{{ $item->wilayah->nama_wilayah }}</td>
            <td>{{ $item->status_tahapan }}</td>
            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
            <td class="hasilStatus">{{ $item->hasil_status }}</td>

            <td>
                <button type="button" class="btn btn-success btn-sm mr-2 mt-2" onclick="handleAction('lolos', {{ $item->id }})"{{ $item->hasil_status !== 'Dijadwalkan' ? 'disabled' : '' }}>Lolos</button>
                <button type="button" class="btn btn-danger btn-sm mr-2 mt-2" onclick="handleAction('tidak lolos', {{ $item->id }})"{{ $item->hasil_status !== 'Dijadwalkan' ? 'disabled' : '' }}>Tidak Lolos</button>

                <button type="button" class="btn btn-secondary btn-sm mr-2 mt-2" onclick="handleAction('tidak hadir', {{ $item->id }})"{{ $item->hasil_status !== 'Dijadwalkan' ? 'disabled' : '' }}>Tidak Hadir</button>
        

             
            </td>
        </tr>
    @endforeach
</tbody>

                        </table>
                    </div>
<!-- Modal Ubah Jadwal -->
<div class="modal fade" id="ubahJadwalModal" tabindex="-1" aria-labelledby="ubahJadwalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ubahJadwalLabel">Ubah Jadwal Kandidat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="ubahJadwalForm">
          <div class="mb-3">
            <label for="ubahStatus" class="form-label">Status</label>
            <select id="ubahStatus" class="form-select" required>
              <option value="">-- Pilih Status --</option>
              <option value="Psikotes">Psikotes</option>
              <option value="Interview HR">Interview HR</option>
              <option value="Interview User">Interview User</option>
              <option value="Training">Training</option>
              <option value="Tandem">Tandem</option>
              <option value="Join">Join</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="ubahTanggal" class="form-label">Tanggal</label>
            <input type="date" id="ubahTanggal" class="form-control" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" onclick="simpanPerubahanJadwal()">Simpan</button>
      </div>
    </div>
  </div>
</div>


                    <!-- Modal Konfirmasi Umum -->
<div class="modal fade" id="generalConfirmModal" tabindex="-1" aria-labelledby="generalConfirmLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="generalConfirmLabel">Konfirmasi Tindakan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin mengubah status penjadwalan?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" onclick="proceedWithAction()">Ya</button>
      </div>
    </div>
  </div>
</div>


            <!-- Modal Konfirmasi Penjadwalan Ulang -->
<div class="modal fade" id="rescheduleConfirmModal" tabindex="-1" aria-labelledby="rescheduleConfirmLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rescheduleConfirmLabel">Konfirmasi Penjadwalan Ulang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah akan melakukan penjadwalan ulang?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="updateStatus()">Tidak</button>
        <button type="button" class="btn btn-primary" onclick="showRescheduleModal()">Iya</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Input Tanggal untuk Penjadwalan Ulang -->
<div class="modal fade" id="rescheduleDateModal" tabindex="-1" aria-labelledby="rescheduleDateLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rescheduleDateLabel">Penjadwalan Ulang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label for="newDate" class="form-label">Pilih Tanggal</label>
        <input type="date" id="newDate" class="form-control" required>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
    let selectedKandidatId = null;

    function bukaModalUbahJadwal(id, currentStatus, currentTanggal, namaKandidat) {
    selectedKandidatId = id;
    
    // Atur status yang terpilih dari data
    document.getElementById('ubahStatus').value = currentStatus;
    document.getElementById('ubahTanggal').value = currentTanggal;

    // Ganti judul modal dengan nama kandidat
    document.getElementById('ubahJadwalLabel').textContent = `Ubah Jadwal Kandidat ${namaKandidat}`;

    // Tampilkan modal
    new bootstrap.Modal(document.getElementById('ubahJadwalModal')).show();
}

function simpanPerubahanJadwal() {
    const status = document.getElementById('ubahStatus').value;
    const tanggal = document.getElementById('ubahTanggal').value;

    if (!status || !tanggal) {
        alert("Silakan isi status dan tanggal.");
        return;
    }

    fetch("{{ route('update-jadwal') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id: selectedKandidatId, status: status, tanggal: tanggal })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Jadwal berhasil diperbarui.");
            location.reload();
        } else {
            alert("Gagal memperbarui jadwal: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
}

</script>
<script>
  let selectedId = null;

  let selectedAction = null; // Variabel untuk menyimpan aksi yang dipilih

function handleAction(action, id) {
    selectedId = id;
    selectedAction = action;

        // Tampilkan modal konfirmasi umum sebelum update status
        new bootstrap.Modal(document.getElementById('generalConfirmModal')).show();
 
}

function proceedWithAction() {
    // Tutup modal konfirmasi umum
    let generalConfirmModal = bootstrap.Modal.getInstance(document.getElementById('generalConfirmModal'));
    generalConfirmModal.hide();

    // Lanjutkan update status dengan aksi yang telah dipilih
    updateStatus(selectedAction);
}


function showRescheduleModal() {
    // Tutup modal konfirmasi dan buka modal input tanggal
    let rescheduleConfirmModal = bootstrap.Modal.getInstance(document.getElementById('rescheduleConfirmModal'));
    rescheduleConfirmModal.hide();

    new bootstrap.Modal(document.getElementById('rescheduleDateModal')).show();
}

function updateStatus(action = 'tidak hadir') {
    let status;

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
        case 'tidak hadir':
            status = 'Tidak Hadir';
            break;
        case 'simpan kandidat':
            status = 'Simpan Kandidat';
            break;
        default:
            status = '';
    }

    fetch("{{ route('update-status') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id: selectedId, status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert("Error updating status: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
}

function saveReschedule() {
    const newDate = document.getElementById('newDate').value;
    if (!newDate) {
        alert("Pilih tanggal terlebih dahulu!");
        return;
    }

    fetch("{{ route('create-log-tahapan') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            id: selectedId,
            tanggal: newDate
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Penjadwalan ulang berhasil disimpan!");
            location.reload();
        } else {
            alert("Gagal menyimpan penjadwalan ulang: " + data.message);
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
