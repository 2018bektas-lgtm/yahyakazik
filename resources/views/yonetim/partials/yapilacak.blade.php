{{-- Yapılacaklar listesinde tek satır. $kalem: YapilacaklarController'daki dizi. --}}
@php $altlar = $kalem['altlar'] ?? collect(); @endphp
<div class="yapilacak {{ $kalem['tur'] }} {{ $kalem['tamam'] ? 'tamam' : '' }}">
    <form method="post" action="{{ $kalem['tamamla'] }}">
        @csrf @method('PATCH')
        <button type="submit" class="onay-kutusu" role="checkbox" aria-checked="{{ $kalem['tamam'] ? 'true' : 'false' }}"
                aria-label="{{ $kalem['baslik'] }}: {{ $kalem['tamam'] ? 'tamamlanmadı olarak işaretle' : 'tamamlandı olarak işaretle' }}">
            <i class="fa-solid fa-check"></i>
        </button>
    </form>
    <div class="yapilacak-govde">
        <div class="yapilacak-ust">
            @if ($kalem['saat'])
                <time>{{ $kalem['saat'] }}@if ($kalem['bitis'])–{{ $kalem['bitis'] }}@endif</time>
            @endif
            @if ($kalem['link'])
                <a href="{{ $kalem['link'] }}" class="yapilacak-baslik">{{ $kalem['baslik'] }}</a>
            @else
                <span class="yapilacak-baslik">{{ $kalem['baslik'] }}</span>
            @endif
        </div>
        @if ($kalem['alt'])<small>{{ $kalem['alt'] }}</small>@endif
    </div>
    @if ($altlar->isNotEmpty())
        <span class="alt-sayac" title="Tamamlanan alt başlıklar">{{ $altlar->where('tamam', true)->count() }}/{{ $altlar->count() }}</span>
    @endif
    <span class="etiket yapilacak-etiket">{{ $kalem['etiket'] }}</span>
    @if ($kalem['sil'])
        <form method="post" action="{{ $kalem['sil'] }}" onsubmit="return confirm('Görev silinsin mi?')">
            @csrf @method('DELETE')
            <button type="submit" class="ikon-dugme sil" aria-label="{{ $kalem['baslik'] }} görevini sil"><i class="fa-regular fa-trash-can"></i></button>
        </form>
    @endif

    @if (! empty($kalem['altEkle']))
        <div class="alt-basliklar">
            @foreach ($altlar as $alt)
                <div class="alt-baslik {{ $alt['tamam'] ? 'tamam' : '' }}">
                    <form method="post" action="{{ $alt['tamamla'] }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="onay-kutusu kucuk-kutu" role="checkbox" aria-checked="{{ $alt['tamam'] ? 'true' : 'false' }}"
                                aria-label="{{ $alt['baslik'] }}: {{ $alt['tamam'] ? 'tamamlanmadı olarak işaretle' : 'tamamlandı olarak işaretle' }}">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </form>
                    <span class="alt-baslik-metin">{{ $alt['baslik'] }}</span>
                    <form method="post" action="{{ $alt['sil'] }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="ikon-dugme sil" aria-label="{{ $alt['baslik'] }} alt başlığını sil"><i class="fa-regular fa-xmark"></i></button>
                    </form>
                </div>
            @endforeach

            <details class="alt-ekle">
                <summary><i class="fa-regular fa-plus"></i> Alt başlık ekle</summary>
                <form method="post" action="{{ $kalem['altEkle'] }}">
                    @csrf
                    <input type="text" name="baslik" required maxlength="255" placeholder="Örn. Ekleri tarat" aria-label="Alt başlık">
                    <button type="submit" class="dugme">Ekle</button>
                </form>
            </details>
        </div>
    @endif
</div>
