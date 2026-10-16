/*
 * Başvuru ve randevu sihirbazı.
 * Adımları tek tek gösterir, her adımda basit kontrol yapar ve seçilen
 * güne göre boş saatleri sunucudan çeker. Asıl doğrulama sunucudadır.
 */
(function () {
    'use strict';

    var form = document.getElementById('sihirbaz');
    if (!form) return;

    form.classList.add('js');

    var tumAdimlar = Array.prototype.slice.call(form.querySelectorAll('.adim'));
    var ileri = document.getElementById('ileri');
    var geri = document.getElementById('geri');
    var gonder = document.getElementById('gonder');
    var ilerleme = form.querySelector('.ilerleme span');
    var saatKutusu = document.getElementById('saatler');
    var ozet = document.getElementById('ozet');
    var simdiki = 0;

    function deger(ad) {
        var alan = form.querySelector('[name="' + ad + '"]:checked') || form.querySelector('[name="' + ad + '"]:not([type=radio]):not([type=checkbox])');
        return alan ? alan.value.trim() : '';
    }

    function yol() { return deger('tip') || 'basvuru'; }

    function adimlar() {
        return tumAdimlar.filter(function (a) {
            return !a.dataset.yol || a.dataset.yol === yol();
        });
    }

    function yolMetinleri() {
        form.querySelectorAll('[data-yol-metin]').forEach(function (el) {
            el.hidden = el.dataset.yolMetin !== yol();
        });
        form.querySelectorAll('button[data-yol]').forEach(function (el) {
            el.hidden = el.dataset.yol !== yol();
        });
    }

    function hata(adim, mesaj) {
        var kutu = adim.querySelector('.adim-hata');
        if (!kutu) {
            kutu = document.createElement('p');
            kutu.className = 'adim-hata';
            kutu.setAttribute('role', 'alert');
            adim.appendChild(kutu);
        }
        kutu.textContent = mesaj || '';
        kutu.hidden = !mesaj;
        return !mesaj;
    }

    function kontrol(adim) {
        switch (adim.dataset.adim) {
            case 'tip':
                return hata(adim, deger('tip') ? '' : 'Lütfen bir seçenek işaretleyin.');
            case 'konu':
                return hata(adim, yol() === 'basvuru' && !deger('konu') ? 'Lütfen bir konu seçin.' : '');
            case 'aciklama':
                return hata(adim, deger('aciklama').length < 20 ? 'Durumunuzu en az birkaç cümleyle anlatır mısınız?' : '');
            case 'zaman':
                if (yol() === 'randevu' && !deger('tarih')) return hata(adim, 'Lütfen bir gün seçin.');
                if (deger('tarih') && !deger('saat')) return hata(adim, 'Lütfen bir saat seçin.');
                return hata(adim, '');
            case 'iletisim':
                if (!deger('ad_soyad')) return hata(adim, 'Lütfen adınızı ve soyadınızı yazın.');
                if (deger('telefon').replace(/\D/g, '').length < 10) return hata(adim, 'Lütfen geçerli bir telefon numarası yazın.');
                if (!form.querySelector('[name=kvkk]').checked) return hata(adim, 'Devam etmek için onay kutusunu işaretleyin.');
                return hata(adim, '');
        }
        return true;
    }

    function ozetiDoldur() {
        var gun = form.querySelector('[name=tarih]:checked');
        var satirlar = [
            ['Talep', yol() === 'randevu' ? 'Randevu' : 'Başvuru'],
            ['Konu', deger('konu')],
            ['Kurum / kişi', yol() === 'basvuru' ? deger('kurum') : ''],
            ['Randevu', gun && deger('saat') ? gun.parentNode.textContent.replace(/\s+/g, ' ').trim() + ', ' + deger('saat') : '']
        ].filter(function (s) { return s[1]; });

        ozet.innerHTML = '';
        satirlar.forEach(function (s) {
            var p = document.createElement('p');
            var b = document.createElement('b');
            b.textContent = s[0] + ': ';
            p.appendChild(b);
            p.appendChild(document.createTextNode(s[1]));
            ozet.appendChild(p);
        });
        ozet.hidden = satirlar.length === 0;
    }

    function goster(sira) {
        var liste = adimlar();
        simdiki = Math.max(0, Math.min(sira, liste.length - 1));

        tumAdimlar.forEach(function (a) { a.classList.remove('aktif'); });
        liste.forEach(function (a, i) { a.querySelector('.adim-no').textContent = i + 1; });
        liste[simdiki].classList.add('aktif');

        var son = simdiki === liste.length - 1;
        geri.hidden = simdiki === 0;
        ileri.hidden = son;
        gonder.hidden = !son;
        ilerleme.style.width = ((simdiki + 1) / liste.length * 100) + '%';
        yolMetinleri();
        if (son) ozetiDoldur();
    }

    function sonraki() {
        var liste = adimlar();
        if (kontrol(liste[simdiki])) {
            goster(simdiki + 1);
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    ileri.addEventListener('click', sonraki);
    geri.addEventListener('click', function () { goster(simdiki - 1); });

    // Tek seçimli adımlarda seçim yapılınca kendiliğinden ilerle (zaten seçili olana tıklanınca da)
    form.querySelectorAll('[name=tip], [name=konu]').forEach(function (r) {
        r.addEventListener('click', function () { setTimeout(sonraki, 180); });
    });

    // Son adım dışında Enter formu göndermesin, ileri götürsün
    form.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && !gonder.offsetParent) {
            e.preventDefault();
            sonraki();
        }
    });

    form.addEventListener('submit', function (e) {
        var gecersiz = adimlar().filter(function (a) { return !kontrol(a); })[0];
        if (gecersiz) {
            e.preventDefault();
            goster(adimlar().indexOf(gecersiz));
            return;
        }
        gonder.disabled = true;
    });

    // Karakter sayacı
    var aciklama = document.getElementById('aciklama');
    var sayac = document.getElementById('aciklamaSayac');
    function say() { sayac.textContent = aciklama.value.length; }
    aciklama.addEventListener('input', say);
    say();

    // Gün seçilince boş saatleri getir
    function saatleriGetir(tarih, secili) {
        saatKutusu.innerHTML = '<p class="ipucu">Boş saatler yükleniyor…</p>';
        fetch(form.dataset.saatler + '?tarih=' + encodeURIComponent(tarih), { headers: { Accept: 'application/json' } })
            .then(function (c) { return c.json(); })
            .then(function (veri) {
                saatKutusu.innerHTML = '';
                if (!veri.saatler || !veri.saatler.length) {
                    saatKutusu.innerHTML = '<p class="ipucu">Bu günde boş saat kalmadı. Lütfen başka bir gün seçin.</p>';
                    return;
                }
                veri.saatler.forEach(function (saat) {
                    var etiket = document.createElement('label');
                    etiket.className = 'saat';
                    etiket.innerHTML = '<input type="radio" name="saat"><span></span>';
                    etiket.querySelector('input').value = saat;
                    etiket.querySelector('input').checked = saat === secili;
                    etiket.querySelector('span').textContent = saat;
                    saatKutusu.appendChild(etiket);
                });
            })
            .catch(function () {
                saatKutusu.innerHTML = '<p class="ipucu">Saatler yüklenemedi. Lütfen sayfayı yenileyin ya da bizi arayın.</p>';
            });
    }

    form.querySelectorAll('[name=tarih]').forEach(function (r) {
        r.addEventListener('change', function () { saatleriGetir(r.value, ''); });
    });

    var temizle = form.querySelector('[data-temizle-zaman]');
    temizle.addEventListener('click', function () {
        form.querySelectorAll('[name=tarih]').forEach(function (r) { r.checked = false; });
        saatKutusu.innerHTML = '<p class="ipucu">Önce bir gün seçin.</p>';
        sonraki();
    });

    form.querySelectorAll('[name=tip]').forEach(function (r) {
        r.addEventListener('change', yolMetinleri);
    });

    // Sunucu doğrulamasından dönüldüyse seçimleri ve ilgili adımı geri getir
    var seciliGun = form.querySelector('[name=tarih]:checked');
    if (seciliGun) saatleriGetir(seciliGun.value, saatKutusu.dataset.eski);

    if (form.querySelector('.uyari')) {
        var ilkHatali = adimlar().filter(function (a) { return !kontrol(a); })[0];
        goster(ilkHatali ? adimlar().indexOf(ilkHatali) : adimlar().length - 1);
    } else {
        goster(0);
    }
})();
