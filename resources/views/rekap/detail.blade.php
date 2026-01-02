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
                    <h3>Detail Absensi</h3>
                    <p class="text-subtitle text-muted">{{ $karyawan->nama }} - {{ $karyawan->jabatan }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('rekap.index') }}">Rekap</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">
                        Detail Absensi Bulan {{ $bulanList[$bulan] }} {{ $tahun }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                    <th>Status</th>
                                    <th>Lokasi</th>
                                    <th>Foto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($absensis as $absensi)
                                <tr class="{{ $absensi->status == 'telat' ? 'table-warning' : '' }}">
                                    <td>{{ $absensi->tanggal->format('d/m/Y') }}</td>
                                    <td>{{ $absensi->tanggal->locale('id')->translatedFormat('l') }}</td>
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
                                            <span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i> Telat (Tidak dapat bonus)</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Hadir</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($absensi->latitude_masuk && $absensi->longitude_masuk)
                                            <a href="https://www.google.com/maps?q={{ $absensi->latitude_masuk }},{{ $absensi->longitude_masuk }}" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-geo-alt"></i> Lihat Map
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($absensi->foto_masuk)
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalFoto{{ $absensi->id }}">
                                                <i class="bi bi-image"></i> Lihat
                                            </button>

                                            <!-- Modal Foto -->
                                            <div class="modal fade" id="modalFoto{{ $absensi->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Foto Absensi - {{ $absensi->tanggal->format('d/m/Y') }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                @if($absensi->foto_masuk)
                                                                <div class="col-md-{{ $absensi->foto_keluar ? '6' : '12' }}">
                                                                    <h6>Foto Masuk</h6>
                                                                    <img src="{{ asset('storage/' . $absensi->foto_masuk) }}" class="img-fluid rounded" alt="Foto Masuk">
                                                                    <p class="small mt-2">
                                                                        <i class="bi bi-clock"></i> {{ date('H:i:s', strtotime($absensi->jam_masuk)) }}<br>
                                                                        <i class="bi bi-geo-alt"></i> Lat: {{ $absensi->latitude_masuk }}, Long: {{ $absensi->longitude_masuk }}
                                                                    </p>
                                                                </div>
                                                                @endif
                                                                @if($absensi->foto_keluar)
                                                                <div class="col-md-6">
                                                                    <h6>Foto Keluar</h6>
                                                                    <img src="{{ asset('storage/' . $absensi->foto_keluar) }}" class="img-fluid rounded" alt="Foto Keluar">
                                                                    <p class="small mt-2">
                                                                        <i class="bi bi-clock"></i> {{ date('H:i:s', strtotime($absensi->jam_keluar)) }}<br>
                                                                        <i class="bi bi-geo-alt"></i> Lat: {{ $absensi->latitude_keluar }}, Long: {{ $absensi->longitude_keluar }}
                                                                    </p>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data absensi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#table1').DataTable({
            order: [[0, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endsection
