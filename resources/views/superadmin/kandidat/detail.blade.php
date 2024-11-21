@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">


<section class="section dashboard">
<div class="card ">
                  <div class="card-body py-3">
                      <h4 class="card-title"><a href="{{ route('superadmin.kandidat.index') }}">Kandidat</a> / Detail Tahapan {{$data->nama_kandidat}}</h4>       
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered" id="example">
                        <thead>
                        <tr>
                        <th>Posisi</th>
                        <th>Wilayah</th>
                        <th>Tahapan</th>
                        <th>Hasil</th>
                        <th>Tanggal</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($detail as $item)
                        <tr>
                          <td>{{$item->posisi->nama_posisi}}</td>
                          <td>{{$item->wilayah->nama_wilayah}}</td>
                          <td>{{$item->status_tahapan}}</td>
                          <td>{{$item->hasil_status}}</td>
                          <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
      
                        </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
      </section>
  </main>
@endsection