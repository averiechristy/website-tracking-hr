@extends('layouts.superadmin.app')
@section('content')
<main id="main" class="main">
<section class="section dashboard">
<div class="card ">
                  <div class="card-body py-3">
                      <h4 class="card-title"><a href="{{ route('superadmin.akunuser.index') }}">Akun User</a> / Detail Posisi {{$data->nama}}</h4>       
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered" id="example">
                        <thead>
                        <tr>
                        <th>Posisi</th>
                        <th>Wilayah</th>
                          </tr>
                        </thead>
                        <tbody>
                        @foreach ($posisi as $item)
                        <tr>
                          <td>{{$item->posisi}}</td>
                          <td>{{$item->wilayah}}</td>
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