/* ----------------------------------------------------------------
    Projeye ozel hareketler. Tema betiklerinden (custom.js) sonra
    yuklenir ve onlara dokunmaz.

    1. Sayac      - gorunur alana girince sifirdan artan sayilar
    2. Cizelge    - ozgecmis zaman cizelgesinin cizilerek dolmasi
    3. Kademeli   - kartlarin sirayla belirmesi (--gecikme degiskeni)

    Not: Kullanici sistem ayarlarinda hareketi azaltmayi sectiyse
    (prefers-reduced-motion) tum animasyonlar atlanir, icerik dogrudan
    son halinde gosterilir.
-----------------------------------------------------------------*/
(function () {
    'use strict';

    var hareketKapali = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    var gozlemDesteği = 'IntersectionObserver' in window;

    /* -------------------------------------------------- 1. Sayac */
    function sayaclariBaslat() {
        var sayilar = document.querySelectorAll('[data-sayac]');
        if (!sayilar.length) return;

        function say(el) {
            var hedef = parseInt(el.getAttribute('data-sayac'), 10);
            if (isNaN(hedef)) return;

            if (hareketKapali) {
                el.textContent = hedef;
                return;
            }

            var sure = 1400;
            var baslangic = null;

            function adim(zaman) {
                if (baslangic === null) baslangic = zaman;
                var oran = Math.min((zaman - baslangic) / sure, 1);
                // yumusak yavaslama
                var yumusak = 1 - Math.pow(1 - oran, 3);
                el.textContent = Math.round(hedef * yumusak);
                if (oran < 1) requestAnimationFrame(adim);
            }

            requestAnimationFrame(adim);
        }

        if (!gozlemDesteği) {
            Array.prototype.forEach.call(sayilar, say);
            return;
        }

        var gozlemci = new IntersectionObserver(function (girisler) {
            girisler.forEach(function (giris) {
                if (!giris.isIntersecting) return;
                say(giris.target);
                gozlemci.unobserve(giris.target); // bir kez calissin
            });
        }, { threshold: 0.4 });

        Array.prototype.forEach.call(sayilar, function (el) {
            gozlemci.observe(el);
        });
    }

    /* ----------------------------------------------- 2. Cizelge */
    function cizelgeyiBaslat() {
        var cizelge = document.querySelector('.zaman-cizelgesi');
        if (!cizelge) return;

        var maddeler = cizelge.querySelectorAll('.madde');

        if (hareketKapali || !gozlemDesteği) {
            cizelge.classList.add('tamam');
            Array.prototype.forEach.call(maddeler, function (m) {
                m.classList.add('gorundu');
            });
            return;
        }

        // Cizgi, bolum gorunur alana girdiginde yukaridan asagi dolar
        var cizgiGozlemci = new IntersectionObserver(function (girisler) {
            girisler.forEach(function (giris) {
                if (!giris.isIntersecting) return;
                cizelge.classList.add('tamam');
                cizgiGozlemci.unobserve(giris.target);
            });
        }, { threshold: 0.15 });

        cizgiGozlemci.observe(cizelge);

        // Maddeler tek tek belirir
        var maddeGozlemci = new IntersectionObserver(function (girisler) {
            girisler.forEach(function (giris) {
                if (!giris.isIntersecting) return;
                giris.target.classList.add('gorundu');
                maddeGozlemci.unobserve(giris.target);
            });
        }, { threshold: 0.25, rootMargin: '0px 0px -60px 0px' });

        Array.prototype.forEach.call(maddeler, function (m) {
            maddeGozlemci.observe(m);
        });
    }

    /* ---------------------------------------------- 3. Kademeli */
    /*  Tema, gorunur alana giren ogeye .animated ekliyor. Biz de
        style="--gecikme: 90ms" tanimli ogelere bu gecikmeyi
        animation-delay olarak uyguluyoruz; boylece kartlar ayni anda
        degil sirayla geliyor.                                        */
    function kademeliGirisiBaslat() {
        if (hareketKapali) return;

        var kutular = document.querySelectorAll('.animate-box[style*="--gecikme"]');
        Array.prototype.forEach.call(kutular, function (el) {
            var gecikme = getComputedStyle(el).getPropertyValue('--gecikme').trim();
            if (gecikme) el.style.animationDelay = gecikme;
        });
    }

    /* -------------------------------------------- 4. Mobil menu */
    /*  Tema, Bootstrap collapse ile 450px yuksekliginde sikisik bir kutu
        aciyordu ve "Ucretsiz on gorusme" dugmesini mobilde tamamen
        gizliyordu. Onun yerine tam ekran bir panel aciyoruz: data-bs-toggle
        kaldirildigi icin Bootstrap devrede degil, .show sinifini biz
        yonetiyoruz.                                                       */
    function mobilMenuyuBaslat() {
        var dugme = document.getElementById('menuDugmesi');
        var panel = document.getElementById('navbar');
        if (!dugme || !panel) return;

        function ac() {
            panel.classList.add('show');
            document.body.classList.add('menu-acik');
            dugme.setAttribute('aria-expanded', 'true');
            dugme.setAttribute('aria-label', 'Menüyü kapat');
        }

        function kapat() {
            panel.classList.remove('show');
            document.body.classList.remove('menu-acik');
            dugme.setAttribute('aria-expanded', 'false');
            dugme.setAttribute('aria-label', 'Menüyü aç');
        }

        dugme.addEventListener('click', function () {
            if (panel.classList.contains('show')) kapat(); else ac();
        });

        // Bir baglantiya tiklaninca kapansin (ayni sayfadaki capalar icin sart)
        panel.addEventListener('click', function (olay) {
            if (olay.target.closest('a')) kapat();
        });

        // Esc ile kapansin
        document.addEventListener('keydown', function (olay) {
            if (olay.key === 'Escape' && panel.classList.contains('show')) kapat();
        });

        // Masaustu genisligine gecilirse acik kalmasin
        window.addEventListener('resize', function () {
            if (window.innerWidth > 991 && panel.classList.contains('show')) kapat();
        });
    }

    function baslat() {
        sayaclariBaslat();
        cizelgeyiBaslat();
        kademeliGirisiBaslat();
        mobilMenuyuBaslat();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', baslat);
    } else {
        baslat();
    }
})();
