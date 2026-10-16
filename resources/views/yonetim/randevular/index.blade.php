@extends('yonetim.layout')

@section('baslik', 'Randevular ve Başvurular')

@section('ust_dugmeler')
    <a href="{{ route('yonetim.randevular.create') }}" class="dugme dugme-birincil"><i class="fa-solid fa-plus"></i> Yeni randevu</a>
@endsection

@section('icerik')

    <form method="get" class="suzgec cam">
        <div class="hap-grubu" role="group" aria-label="Duruma göre süz">
            <a href="{{ request()->fullUrlWithQuery(['durum' => null, 'page' => null]) }}" class="hap {{ request('durum') ? '' : 'aktif' }}">
                Tümü <em>{{ $sayilar->sum() }}</em>
            </a>
            @foreach (\App\Models\Randevu::DURUMLAR as $deger => $etiket)
                <a href="{{ request()->fullUrlWithQuery(['durum' => $deger, 'page' => null]) }}" class="hap {{ request('durum') === $deger ? 'aktif' : '' }}">
                    {{ $etiket }} <em>{{ $sayilar[$deger] ?? 0 }}</em>
                </a>
            @endforeach
        </div>
        <div class="suzgec-sag">
            @if (request('durum'))<input type="hidden" name="durum" value="{{ request('durum') }}">@endif
            <select name="tip" onchange="this.form.submit()" aria-label="Tür">
                <option value="">Tüm türler</option>
                @foreach (\App\Models\Randevu::TIPLER as $deger => $etiket)
                    <option value="{{ $deger }}" @selected(request('tip') === $deger)>{{ $etiket }}</option>
                @endforeach
            </select>
            <input type="search" name="ara" value="{{ request('ara') }}" placeholder="İsim, telefon, konu ara…" aria-label="Ara">
        </div>
    </form>

    <section class="kart cam tablo-kart">
        @if ($randevular->isEmpty())
            <p class="bos"><i class="fa-regular fa-folder-open"></i> Bu süzgece uyan kayıt yok.</p>
        @else
            <table class="tablo">
                <thead>
                    <tr><th>Kişi</th><th>Tür / Konu</th><th>Zaman</th><th>Durum</th><th class="sag">İşlem</th></tr>
                </thead>
                <tbody>
                    @foreach ($randevular as $randevu)
                        <tr>
                            <td data-baslik="Kişi">
                                <a href="{{ route('yonetim.randevular.edit', $randevu) }}" class="kalin">{{ $randevu->ad_soyad }}</a>
                                <small class="soluk blok">{{ collect([$randevu->telefon, $randevu->eposta])->filter()->join(' · ') }}</small>
                            </td>
                            <td data-baslik="Tür / Konu">
                                <span class="etiket tip-{{ $randevu->tip }}">{{ $randevu->tipEtiketi() }}</span>
                                <small class="soluk blok">{{ $randevu->konu ?? '—' }}@if ($randevu->kurum) · {{ $randevu->kurum }}@endif</small>
                            </td>
                            <td data-baslik="Zaman">
                                @if ($randevu->baslangic)
                                    {{ $randevu->baslangic->translatedFormat('j M Y') }}
                                    <small class="soluk blok">{{ $randevu->baslangic->format('H:i') }} · {{ $randevu->gorusmeSekliEtiketi() }}</small>
                                @else
                                    <span class="soluk">Zaman yok</span>
                                    <small class="soluk blok">Geldi: {{ $randevu->created_at->diffForHumans() }}</small>
                                @endif
                            </td>
                            <td data-baslik="Durum"><span class="etiket durum-{{ $randevu->durum }}">{{ $randevu->durumEtiketi() }}</span></td>
                            <td class="sag">@include('yonetim.partials.durum-dugmeleri')</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($randevular->hasPages())
                <nav class="sayfalama">
                    @if ($randevular->previousPageUrl())<a href="{{ $randevular->previousPageUrl() }}" class="dugme">&larr; Önceki</a>@endif
                    <span class="soluk">{{ $randevular->currentPage() }} / {{ $randevular->lastPage() }}</span>
                    @if ($randevular->nextPageUrl())<a href="{{ $randevular->nextPageUrl() }}" class="dugme">Sonraki &rarr;</a>@endif
                </nav>
            @endif
        @endif
    </section>

@endsection
