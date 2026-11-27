@props(['periode', 'rute', 'parameter' => []])
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 no-print">
    <div class="btn-group" role="group" aria-label="Pilih periode">
        @foreach (\App\Support\Periode::TIPE as $kunci => $nama)
            <a href="{{ route($rute, array_merge($parameter, ['periode' => $kunci, 'tanggal' => $periode['acuan']->toDateString()])) }}"
               class="btn btn-sm {{ $periode['tipe'] === $kunci ? 'btn-gold' : 'btn-outline-gold' }} px-3">{{ $nama }}</a>
        @endforeach
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="{{ route($rute, array_merge($parameter, ['periode' => $periode['tipe'], 'tanggal' => $periode['sebelumnya']])) }}"
           class="btn btn-sm btn-outline-gold px-2" title="Periode sebelumnya"><i class="fa-solid fa-chevron-left"></i></a>
        <span class="fw-bold text-warning px-2">{{ $periode['label'] }}</span>
        <a href="{{ route($rute, array_merge($parameter, ['periode' => $periode['tipe'], 'tanggal' => $periode['berikutnya']])) }}"
           class="btn btn-sm btn-outline-gold px-2" title="Periode berikutnya"><i class="fa-solid fa-chevron-right"></i></a>
        <a href="{{ route($rute, array_merge($parameter, ['periode' => $periode['tipe']])) }}" class="btn btn-sm btn-outline-gold px-3">Hari ini</a>
    </div>
</div>
