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
                    <h3>Rekap Absensi & Bonus Gaji</h3>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filter Periode</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rekap.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Bulan</label>
                                    <select name="bulan" class="form-select">
                                        @foreach($bulanList as $key => $nama)
                                            <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                                                {{ $nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tahun</label>
                                    <select name="tahun" class="form-select">
                                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i> Tampilkan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">
                        Rekap Bulan {{ $bulanList[$bulan] }} {{ $tahun }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Bonus per Kehadiran:</strong> Rp {{ number_format($bonusPerKehadiran, 0, ',', '.') }}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Karyawan</th>
                                    <th>Jabatan</th>
                                    <th>Hadir</th>
                                    <th>Telat</th>
                                    <th>Total Kehadiran</th>
                                    <th>Bonus Gaji</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalBonus = 0; @endphp
                                @foreach ($rekap as $index => $item)
                                    @php $totalBonus += $item['bonus']; @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item['foto_terakhir'])
                                                    <img src="{{ asset('storage/' . $item['foto_terakhir']) }}" 
                                                         alt="Foto" 
                                                         class="rounded me-2" 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="rounded me-2 bg-secondary d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="bi bi-person text-white"></i>
                                                    </div>
                                                @endif
                                                <span>{{ $item['karyawan']->nama }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $item['karyawan']->jabatan }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ $item['hadir'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $item['telat'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $item['total_kehadiran'] }} hari</span>
                                        </td>
                                        <td>
                                            <strong class="text-success">Rp {{ number_format($item['bonus'], 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            <a href="{{ route('rekap.detail', ['karyawan' => $item['karyawan']->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="6" class="text-end">TOTAL BONUS:</th>
                                    <th colspan="2">
                                        <strong class="text-success">Rp {{ number_format($totalBonus, 0, ',', '.') }}</strong>
                                    </th>
                                </tr>
                            </tfoot>
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
            order: [[1, 'asc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel"></i> Export Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Rekap Absensi ' + '{{ $bulanList[$bulan] }} {{ $tahun }}'
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-earmark-pdf"></i> Export PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Rekap Absensi ' + '{{ $bulanList[$bulan] }} {{ $tahun }}'
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Print',
                    className: 'btn btn-info btn-sm',
                    title: 'Rekap Absensi ' + '{{ $bulanList[$bulan] }} {{ $tahun }}'
                }
            ]
        });
    });
</script>
@endsection
