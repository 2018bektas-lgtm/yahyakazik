/*
 * Yönetim paneli takip takvimi (FullCalendar 6, public/yonetim/vendor).
 * Veriler TakvimController@veriler'den gelir; sürükle-bırak TakvimController@tasi'ye yazılır.
 */
(function () {
    'use strict';

    var kutu = document.getElementById('takvim');
    if (!kutu || !window.FullCalendar) return;

    var token = document.querySelector('meta[name=csrf-token]').content;
    var secim = document.getElementById('secim');
    var mobil = window.matchMedia('(max-width: 760px)').matches;

    function iki(n) { return (n < 10 ? '0' : '') + n; }
    function yerelTarih(d) { return d.getFullYear() + '-' + iki(d.getMonth() + 1) + '-' + iki(d.getDate()); }
    function yerelSaat(d) { return iki(d.getHours()) + ':' + iki(d.getMinutes()); }

    var takvim = new FullCalendar.Calendar(kutu, {
        locale: 'tr',
        timeZone: 'local',
        initialView: mobil ? 'listWeek' : 'dayGridMonth',
        headerToolbar: mobil
            ? { left: 'prev,next', center: 'title', right: 'today' }
            : { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' },
        footerToolbar: mobil ? { center: 'dayGridMonth,timeGridWeek,listWeek' } : false,
        buttonText: { listWeek: 'Liste' },
        height: 'auto',
        firstDay: 1,
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        nowIndicator: true,
        slotMinTime: '07:00:00',
        slotMaxTime: '21:00:00',
        slotDuration: '00:30:00',
        businessHours: { daysOfWeek: [1, 2, 3, 4, 5], startTime: '09:00', endTime: '18:00' },
        dayMaxEvents: 4,
        editable: true,
        eventDurationEditable: true,
        selectable: true,
        events: kutu.dataset.veriler,

        eventDidMount: function (bilgi) {
            var p = bilgi.event.extendedProps;
            bilgi.el.title = [bilgi.event.title, p.tur, p.detay].filter(Boolean).join('\n');
        },

        select: function (bilgi) {
            var tarih = yerelTarih(bilgi.start);
            var deger = bilgi.allDay ? tarih : tarih + ' ' + yerelSaat(bilgi.start);
            var etiket = bilgi.start.toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', weekday: 'long' });
            if (!bilgi.allDay) etiket += ', ' + yerelSaat(bilgi.start);

            document.getElementById('secimBaslik').textContent = etiket;
            secim.querySelectorAll('a').forEach(function (a) {
                var ne = a.dataset.ne;
                var url = ne === 'randevu'
                    ? kutu.dataset.yeniRandevu + '?tarih=' + encodeURIComponent(deger)
                    : kutu.dataset.yeniEtkinlik + '?tur=' + ne + '&tarih=' + encodeURIComponent(deger) + (bilgi.allDay && ne !== 'durusma' ? '&tum_gun=1' : '');
                a.href = url;
            });
            secim.hidden = false;
        },

        eventDrop: tasi,
        eventResize: tasi
    });

    function tasi(bilgi) {
        var e = bilgi.event;
        fetch(kutu.dataset.tasi, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({
                id: e.id,
                start: e.start.toISOString(),
                end: e.end ? e.end.toISOString() : null,
                allDay: e.allDay
            })
        }).then(function (c) {
            if (!c.ok) throw new Error();
        }).catch(function () {
            bilgi.revert();
            alert('Kayıt taşınamadı. Sayfayı yenileyip tekrar deneyin.');
        });
    }

    document.getElementById('secimKapat').addEventListener('click', function () {
        secim.hidden = true;
        takvim.unselect();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { secim.hidden = true; takvim.unselect(); }
    });

    takvim.render();
})();
