@extends('layouts.main')

@section('contents')
@include('layouts.sidebar')
<div id="main">
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Absensi Hari Ini</h3>
                    <p class="text-subtitle text-muted">{{ date('d F Y') }}</p>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Data Absensi</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Jabatan</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Status</th>
                                <th>Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($absensis as $index => $absensi)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $absensi->karyawan->nama }}</td>
                                <td>{{ $absensi->karyawan->jabatan }}</td>
                                <td>
                                    @if($absensi->jam_masuk)
                                        <span class="badge bg-success">{{ date('H:i', strtotime($absensi->jam_masuk)) }}</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($absensi->jam_keluar)
                                        <span class="badge bg-danger">{{ date('H:i', strtotime($absensi->jam_keluar)) }}</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($absensi->status == 'hadir')
                                        <span class="badge bg-success">Hadir</span>
                                    @elseif($absensi->status == 'telat')
                                        <span class="badge bg-warning">Telat</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Hadir</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($absensi->foto_masuk)
                                        <img src="{{ asset('storage/' . $absensi->foto_masuk) }}" 
                                             alt="Foto Masuk" 
                                             class="img-thumbnail" 
                                             style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#modalFoto{{ $absensi->id }}">

                                        <!-- Modal Foto -->
                                        <div class="modal fade" id="modalFoto{{ $absensi->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Foto Absensi - {{ $absensi->karyawan->nama }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            @if($absensi->foto_masuk)
                                                            <div class="col-md-{{ $absensi->foto_keluar ? '6' : '12' }}">
                                                                <h6>Foto Masuk</h6>
                                                                <img src="{{ asset('storage/' . $absensi->foto_masuk) }}" class="img-fluid rounded" alt="Foto Masuk">
                                                                <p class="small mt-2">
                                                                    <i class="bi bi-geo-alt"></i> 
                                                                    Lat: {{ $absensi->latitude_masuk }}, Long: {{ $absensi->longitude_masuk }}
                                                                </p>
                                                            </div>
                                                            @endif
                                                            @if($absensi->foto_keluar)
                                                            <div class="col-md-6">
                                                                <h6>Foto Keluar</h6>
                                                                <img src="{{ asset('storage/' . $absensi->foto_keluar) }}" class="img-fluid rounded" alt="Foto Keluar">
                                                                <p class="small mt-2">
                                                                    <i class="bi bi-geo-alt"></i> 
                                                                    Lat: {{ $absensi->latitude_keluar }}, Long: {{ $absensi->longitude_keluar }}
                                                                </p>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">Tidak ada foto</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        timer: 2000
    });
</script>
@endif

<script>
    // Initialize DataTable
    $(document).ready(function() {
        $('#table1').DataTable({
            order: [[0, 'asc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endsection
