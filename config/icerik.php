<?php

/*
|--------------------------------------------------------------------------
| Site İçeriği
|--------------------------------------------------------------------------
| Sayfalarda gösterilen tüm liste içerikleri burada toplanmıştır.
| Blade şablonları bu diziler üzerinden render edilir; şablonlara sabit
| metin gömülmez.
|
| İÇERİK KAYNAĞI
| Özgeçmiş, faaliyet alanları, ilkeler ve soru başlıkları mevcut site olan
| avyahyakazik.com.tr'den alınmıştır. Soruların cevapları ve blog yazıları
| taslaktır; yayına almadan önce Av. Kazık'ın onayından geçmelidir.
|
| GÖRSELLER
| Tema arşivinde slider görsellerinin çoğu eksikti; yerlerine mevcut
| 1920x1080 görseller kullanıldı. Büronun kendi fotoğrafları çekildiğinde
| public/frontend/img/ altına konup buradaki yollar güncellenmelidir.
*/

return [

    /*
     | Anasayfa hero'su.
     |
     | Tek ve sabit bir hero kullanılıyor; dönen slayt yok. Sebebi: tema ile
     | gelen manzara görsellerinin tamamı yabancı stok insan fotoğrafı ve bir
     | hukuk bürosunun açılış ekranında inandırıcı durmuyorlar. Kişisiz,
     | mimari bir görsel koyu bir katmanla dokuya çevrildi.
     |
     | TODO: Büronun kendi fotoğrafı (ofis cephesi, Ankara adliyesi vb.)
     | 1920x1080 çözünürlükte çekildiğinde 'gorsel' alanını değiştirin.
     */
    'hero' => [
        'ust_baslik' => 'Arabuluculuk &amp; Danışmanlık',
        'baslik'     => 'Hukukta saygın',
        'vurgu'      => 'çözüm ortağınız',
        'aciklama'   => 'Ankara ili başta olmak üzere Türkiye genelinde avukatlık,
                         arabuluculuk ve hukuki danışmanlık.',
        'gorsel'     => 'frontend/img/case/6.jpg',
        'butonlar'   => [
            ['metin' => 'Randevu oluşturun',   'rota' => 'basvuru',           'birincil' => true],
            ['metin' => 'Faaliyet alanlarımız','rota' => 'faaliyet-alanlari', 'birincil' => false],
        ],
    ],

    /*
     | İlkelerimiz — anasayfadaki dört sütunlu bölüm.
     | Metinler mevcut siteden birebir alınmıştır.
     */
    'ilkeler' => [
        [
            'ikon'   => 'flaticon-balance',
            'baslik' => 'Etik ve Tarafsızlık',
            'metin'  => 'Hukukun evrensel ilkelerine ve meslek etiğine bağlılık, tüm çalışmalarımızın temelini oluşturur. Müvekkillerimize karşı daima adil, şeffaf ve tarafsız bir yaklaşım benimseriz.',
        ],
        [
            'ikon'   => 'flaticon-clock',
            'baslik' => 'Disiplinli Yaklaşım',
            'metin'  => 'Her dosyayı titizlikle inceler, süreci planlı ve kontrollü bir şekilde yönetiriz. Zamanında ve etkili müdahale ile hukuki süreci müvekkil lehine sonuçlandırmayı hedefleriz.',
        ],
        [
            'ikon'   => 'flaticon-light-bulb',
            'baslik' => 'Çözüm Odaklılık',
            'metin'  => 'Sadece hukuki süreci değil, müvekkilin ihtiyaçlarını da anlayarak en uygun çözümü üretmeye odaklanırız. Karmaşık sorunları sadeleştirir, net ve uygulanabilir yol haritaları sunarız.',
        ],
        [
            'ikon'   => 'flaticon-support',
            'baslik' => 'Gizlilik ve Güven',
            'metin'  => 'Müvekkil bilgilerinin gizliliği bizim için mutlak bir sorumluluktur. Güvene dayalı ilişkiler kurar, bu güveni her aşamada korumak için azami özen gösteririz.',
        ],
    ],

    /*
     | Sayaç bölümü.
     |
     | 'sayac' => true olan kayıtlar görünür alana girince sıfırdan sayarak
     | artar; false olanlar (yıl, metin) olduğu gibi yazılır.
     |
     | ÖNEMLİ: Buraya yalnızca doğrulanabilir bilgi yazılmalıdır. "Kazanılmış
     | dava", "mutlu müvekkil" gibi ispatlanamayan sayılar hem yanıltıcı olur
     | hem de meslek kurallarıyla (reklam yasağı) çelişir.
     */
    'sayilar' => [
        ['deger' => 2017,      'etiket' => 'Yılından beri serbest avukat ve arabulucu', 'sayac' => false],
        ['deger' => 9,         'etiket' => 'Faaliyet alanı',                            'sayac' => true],
        ['deger' => 2,         'etiket' => 'Yüksek lisans derecesi',                    'sayac' => true],
        ['deger' => 'Türkiye', 'etiket' => 'Genelinde dosya takibi',                    'sayac' => false],
    ],

    /* Faaliyet alanları */
    'faaliyet_alanlari' => [
        ['ikon' => 'flaticon-suitcase',   'slug' => 'is-hukuku', 'dava' => 'İş davaları', 'ad' => 'İş Hukuku',          'ozet' => 'İşe iade, kıdem ve ihbar tazminatı, fazla mesai alacakları, iş kazası ve hizmet tespiti davaları.'],
        ['ikon' => 'flaticon-wounded',    'slug' => 'tazminat-hukuku', 'dava' => 'Tazminat davaları', 'ad' => 'Tazminat Hukuku',    'ozet' => 'Trafik kazası, iş kazası ve haksız fiilden doğan maddi ve manevi tazminat talepleri.'],
        ['ikon' => 'flaticon-parents',    'slug' => 'aile-hukuku', 'dava' => 'Boşanma davaları', 'ad' => 'Aile Hukuku',        'ozet' => 'Anlaşmalı ve çekişmeli boşanma, velayet, nafaka ve mal rejiminin tasfiyesi.'],
        ['ikon' => 'flaticon-thief',      'slug' => 'ceza-hukuku', 'dava' => 'Ceza davaları', 'ad' => 'Ceza Hukuku',        'ozet' => 'Soruşturma ve kovuşturma aşamalarında şüpheli, sanık ve mağdur vekilliği.'],
        ['ikon' => 'flaticon-standard',   'slug' => 'asker-alma-hukuku', 'dava' => 'Askerlik davaları', 'ad' => 'Asker Alma Hukuku',  'ozet' => 'Askerlik yoklaması, erteleme, muafiyet ve bedelli askerlik işlemlerine ilişkin uyuşmazlıklar.'],
        ['ikon' => 'flaticon-courthouse', 'slug' => 'memur-hukuku', 'dava' => 'Memur davaları', 'ad' => 'Memur Hukuku',       'ozet' => 'Disiplin soruşturmaları, atama ve görevden uzaklaştırma işlemleri, özlük hakları davaları.'],
        ['ikon' => 'flaticon-support',    'slug' => 'saglik-hukuku', 'dava' => 'Malpraktis davaları', 'ad' => 'Sağlık Hukuku',      'ozet' => 'Malpraktis iddiaları, hasta hakları ve sağlık personelinin hukuki sorumluluğu.'],
        ['ikon' => 'flaticon-coin',       'slug' => 'ticaret-hukuku', 'dava' => 'Ticari davalar', 'ad' => 'Ticaret Hukuku',     'ozet' => 'Şirket kuruluşu ve devri, ticari sözleşmeler, alacak takibi ve şirketler arası uyuşmazlıklar.'],
        ['ikon' => 'flaticon-mace',       'slug' => 'arabuluculuk', 'dava' => 'Arabuluculuk', 'ad' => 'Arabuluculuk',       'ozet' => 'Dava şartı ve ihtiyari arabuluculuk süreçlerinde tarafsız arabulucu olarak görev.'],
    ],

    /* Çalışma süreci — anasayfadaki dört adımlı bölüm */
    'surec' => [
        ['no' => '01', 'baslik' => 'Ön Görüşme',      'metin' => 'Dosyanızı dinler, hukuki durumu genel hatlarıyla değerlendirir ve hangi yolların açık olduğunu anlatırız.'],
        ['no' => '02', 'baslik' => 'Dosya İncelemesi','metin' => 'Belgeleri ve delilleri inceler, benzer dosyalardaki içtihatla birlikte gerçekçi bir başarı değerlendirmesi yaparız.'],
        ['no' => '03', 'baslik' => 'Strateji',        'metin' => 'İzlenecek yolu, tahmini süreyi ve maliyeti yazılı olarak paylaşır; vekâlet ilişkisini bu çerçevede kurarız.'],
        ['no' => '04', 'baslik' => 'Takip ve Bilgilendirme', 'metin' => 'Her önemli gelişmede sizi bilgilendirir, duruşma ve süre takibini eksiksiz yürütürüz.'],
    ],

    /*
     | Av. Yahya Kazık özgeçmişi.
     |
     | 'donem' alanına yalnızca doğrulanmış bilgi yazılmıştır. Kesin yıllar
     | öğrenildiğinde buraya eklenebilir.
     */
    'ozgecmis' => [
        [
            'donem'  => 'Eğitim',
            'baslik' => 'İstanbul Üniversitesi Hukuk Fakültesi',
            'metin'  => 'Lisans eğitimini İstanbul Üniversitesi Hukuk Fakültesi\'nde tamamladı.',
        ],
        [
            'donem'  => 'Kamu Görevi',
            'baslik' => 'Türk Silahlı Kuvvetleri',
            'metin'  => 'Uzun yıllar Türk Silahlı Kuvvetleri bünyesinde çeşitli idari görevlerde bulundu; disiplin subaylığı sorumluluğunu üstlendi.',
        ],
        [
            'donem'  => 'Kamu Görevi',
            'baslik' => 'Millî Savunma Bakanlığı Müfettişliği',
            'metin'  => 'MSB müfettişi olarak görev yaptı. Bu dönemde disiplin hukuku, asker alma hukuku ve memur hukuku alanlarında saha deneyimi kazandı.',
        ],
        [
            'donem'  => 'Akademik',
            'baslik' => 'İki Ayrı Yüksek Lisans',
            'metin'  => 'Özel hukuk ve kamu yönetimi alanlarında iki ayrı yüksek lisans çalışması yaptı.',
        ],
        [
            'donem'  => '2017',
            'baslik' => 'Serbest Avukatlık ve Arabuluculuk',
            'metin'  => '2017 yılından bu yana serbest avukat ve arabulucu olarak çalışıyor; her müvekkile özel, etik değerlere bağlı ve çözüm odaklı hizmet sunmayı ilke ediniyor.',
        ],
        [
            'donem'  => 'Bugün',
            'baslik' => 'Otorite Hukuk ve Danışmanlık Bürosu',
            'metin'  => 'Ankara Çankaya\'daki büroda, Ankara başta olmak üzere Türkiye genelinde avukatlık ve arabuluculuk faaliyetlerini sürdürüyor.',
        ],
    ],

    /*
     | Uzmanlık başlıkları — özgeçmiş sayfasındaki liste.
     */
    'uzmanliklar' => [
        'Disiplin hukuku',
        'Asker alma hukuku',
        'Memur hukuku',
        'İş hukuku',
        'Ceza hukuku',
        'Sağlık hukuku',
    ],

    /*
     | Blog yazıları.
     |
     | TASLAK: Aşağıdaki metinler örnek içeriktir ve Av. Kazık'ın uzmanlık
     | alanlarına göre yazılmıştır. Yayına almadan önce kendisi tarafından
     | gözden geçirilmelidir.
     */
    'yazilar' => [
        [
            'slug'     => 'disiplin-sorusturmasinda-memurun-haklari',
            'baslik'   => 'Disiplin soruşturmasında memurun hakları',
            'kategori' => 'Memur Hukuku',
            'tarih'    => '29 Nisan 2026',
            'ikon'     => 'flaticon-courthouse',
            'gorsel'   => null,
            'ozet'     => 'Savunma hakkı, dosya inceleme yetkisi ve zamanaşımı süreleri çoğu dosyada gözden kaçıyor.',
            'govde'    => [
                'Disiplin soruşturması, memurun en temel güvencelerinden biri olan savunma hakkı üzerine kuruludur. Savunma istenmeden verilen bir disiplin cezası, esasa girilmeden iptal edilir. Buna rağmen uygulamada savunma isteme yazısının usulüne uygun tebliğ edilmediği dosyalarla sık karşılaşıyoruz.',
                'Savunma için tanınan süre ve dosyayı inceleme imkânı ayrı ayrı önemlidir. Hakkınızdaki iddiaların dayanağını görmeden verilen bir savunma, çoğu zaman savunma sayılmaz. Bu nedenle önce dosyanın incelenmesi talep edilmelidir.',
                'Disiplin cezalarında zamanaşımı süreleri fiilin öğrenilmesinden ve işlenmesinden itibaren ayrı ayrı işler. Süresi geçmiş bir fiile ceza verilmesi hâlinde, cezanın iptali istenebilir.',
            ],
            'alinti'   => 'Savunma alınmadan verilen disiplin cezası, esasa girilmeden iptal edilir.',
        ],
        [
            'slug'     => 'askerlik-muafiyeti-ve-itiraz-yollari',
            'baslik'   => 'Askerlik muafiyeti ve itiraz yolları',
            'kategori' => 'Asker Alma Hukuku',
            'tarih'    => '27 Nisan 2026',
            'ikon'     => 'flaticon-standard',
            'gorsel'   => null,
            'ozet'     => 'Sağlık durumu nedeniyle verilen kararlara karşı hangi süre içinde, nereye başvurulur?',
            'govde'    => [
                'Askerlik yoklaması sonucunda verilen sağlık kararları, ilgili yönetmelikteki hastalık ve arıza listesi esas alınarak belirlenir. Kararın dayandığı madde ile kişinin gerçek sağlık durumu arasındaki uyumsuzluk, itirazın temelini oluşturur.',
                'İtiraz süreleri kısadır ve kararın tebliğinden itibaren işler. Süre kaçırıldığında idari yargı yolu kapanabileceğinden, kararı aldığınız anda tarihini not etmeniz önemlidir.',
                'Hastane raporları arasındaki çelişki hâlinde hakem hastane uygulaması devreye girer. Bu aşamada dosyaya sunulacak tıbbi belgelerin eksiksiz olması, sonucu doğrudan etkiler.',
            ],
            'alinti'   => 'İtiraz süresi, kararı elinize aldığınız gün işlemeye başlar.',
        ],
        [
            'slug'     => 'ise-iade-davasinda-surec-ve-sureler',
            'baslik'   => 'İşe iade davasında süreç ve süreler',
            'kategori' => 'İş Hukuku',
            'tarih'    => '25 Nisan 2026',
            'ikon'     => 'flaticon-suitcase',
            'gorsel'   => null,
            'ozet'     => 'Arabuluculuk başvurusu, dava açma süresi ve işe başlatmama tazminatı nasıl hesaplanır?',
            'govde'    => [
                'İş sözleşmesi geçerli bir neden olmaksızın feshedilen işçi, fesih bildiriminin tebliğinden itibaren işleyen süre içinde arabulucuya başvurmak zorundadır. Bu, dava şartı niteliğinde bir zorunluluktur.',
                'Arabuluculuk görüşmesinde anlaşma sağlanamazsa, son tutanağın düzenlendiği tarihten itibaren işe iade davası açılabilir. Arabuluculukta imzalanacak bir anlaşma metni ise dava yolunu kapatır; bu nedenle görüşmeye hazırlıksız gitmemek gerekir.',
                'Mahkeme feshin geçersizliğine karar verirse, işçinin başvurusu üzerine işveren ya işe başlatır ya da işe başlatmama tazminatı öder. Ayrıca boşta geçen süre ücreti gündeme gelir.',
            ],
            'alinti'   => 'Arabuluculukta atılan imza, dava yolunu kapatır.',
        ],
        [
            'slug'     => 'arabuluculuk-ne-zaman-zorunlu',
            'baslik'   => 'Arabuluculuk ne zaman zorunlu?',
            'kategori' => 'Arabuluculuk',
            'tarih'    => '24 Nisan 2026',
            'ikon'     => 'flaticon-mace',
            'gorsel'   => null,
            'ozet'     => 'Dava şartı arabuluculuk ile ihtiyari arabuluculuk arasındaki fark ve süreç işleyişi.',
            'govde'    => [
                'Bazı uyuşmazlıklarda arabulucuya başvurmak, dava açmanın ön şartıdır. İşçi-işveren alacakları, ticari davalar ve kira uyuşmazlıkları bu kapsamdadır. Başvuru yapılmadan açılan dava, usulden reddedilir.',
                'İhtiyari arabuluculukta ise taraflar, zorunluluk olmasa da anlaşmazlığı mahkemeye taşımadan çözmeyi tercih eder. Süreç daha hızlı, daha az maliyetli ve gizlidir.',
                'Arabuluculuk sonunda düzenlenen anlaşma belgesi, icra edilebilirlik şerhi alındığında mahkeme ilamı gücündedir. Bu nedenle metnin içeriği, en az bir dava dilekçesi kadar dikkatle hazırlanmalıdır.',
            ],
            'alinti'   => 'Anlaşma belgesi, şerh alındığında mahkeme ilamı gücündedir.',
        ],
        [
            'slug'     => 'malpraktis-iddialarinda-ispat',
            'baslik'   => 'Malpraktis iddialarında ispat',
            'kategori' => 'Sağlık Hukuku',
            'tarih'    => '23 Nisan 2026',
            'ikon'     => 'flaticon-support',
            'gorsel'   => null,
            'ozet'     => 'Komplikasyon ile hatalı tıbbi uygulama arasındaki sınır nerede çizilir?',
            'govde'    => [
                'Sağlık hukukunda en kritik ayrım, komplikasyon ile malpraktis arasındadır. Komplikasyon, tıp kurallarına uygun davranılmasına rağmen ortaya çıkabilen istenmeyen sonuçtur; malpraktis ise özen yükümlülüğünün ihlalidir.',
                'Bu ayrımı belirleyen temel unsur aydınlatılmış onamdır. Hastanın olası riskler hakkında usulüne uygun bilgilendirilmemiş olması, komplikasyon savunmasını büyük ölçüde zayıflatır.',
                'Dosyanın kaderini çoğunlukla bilirkişi raporu belirler. Rapora karşı süresi içinde ve teknik dayanaklı itiraz sunulması, sürecin en kritik aşamasıdır.',
            ],
            'alinti'   => 'Aydınlatılmış onam yoksa, komplikasyon savunması zayıflar.',
        ],
        [
            'slug'     => 'trafik-kazasi-tazminatinda-kusur-orani',
            'baslik'   => 'Trafik kazası tazminatında kusur oranı',
            'kategori' => 'Tazminat Hukuku',
            'tarih'    => '21 Nisan 2026',
            'ikon'     => 'flaticon-wounded',
            'gorsel'   => null,
            'ozet'     => 'Kusur raporu tazminatın miktarını nasıl belirler, rapora nasıl itiraz edilir?',
            'govde'    => [
                'Trafik kazasından doğan tazminat taleplerinde miktarı belirleyen ilk unsur kusur dağılımıdır. Kaza tespit tutanağındaki değerlendirme bağlayıcı değildir; mahkeme kendi bilirkişisinden rapor alır.',
                'Maddi tazminat kalemleri arasında tedavi giderleri, geçici ve sürekli iş göremezlik ile destekten yoksun kalma tazminatı yer alır. Her kalem ayrı ayrı hesaplanır ve ayrı ayrı talep edilmelidir.',
                'Sigorta şirketine yapılacak başvuru, dava açmadan önce çoğu dosyada zorunludur. Başvuruya eklenecek belgelerin eksik olması, sürecin en sık karşılaşılan gecikme sebebidir.',
            ],
            'alinti'   => 'Kaza tespit tutanağındaki kusur değerlendirmesi mahkemeyi bağlamaz.',
        ],
    ],

    /*
     | Sıkça sorulan sorular.
     | Soru başlıkları mevcut siteden alınmıştır. İlk sorunun cevabı da
     | oradan gelir; diğer cevaplar taslaktır ve onaydan geçmelidir.
     */
    'sss' => [
        [
            'soru'  => 'Otorite Hukuk hangi alanlarda hizmet veriyor?',
            'cevap' => 'Otorite Hukuk; iş hukuku, ceza hukuku, boşanma hukuku, memur hukuku, asker alma hukuku, sağlık hukuku ve ticaret hukuku başta olmak üzere birçok alanda avukatlık ve arabuluculuk hizmeti sunmaktadır.',
        ],
        [
            'soru'  => 'Arabuluculuk hizmeti hangi durumlarda alınabilir?',
            'cevap' => 'İşçi-işveren alacakları, ticari davalar ve kira uyuşmazlıkları gibi bazı konularda arabulucuya başvurmak dava şartıdır. Bunun dışındaki uyuşmazlıklarda da taraflar, süreci mahkemeye taşımadan arabuluculuk yoluyla çözmeyi tercih edebilir.',
        ],
        [
            'soru'  => 'Avukatlık hizmeti alırken hangi belgeler gereklidir?',
            'cevap' => 'İlk görüşmede kimliğiniz ve dosyanıza ilişkin elinizdeki belgeler yeterlidir. Vekâlet ilişkisi kurulduğunda noterden düzenlenecek vekâletname gerekir; dava türüne göre özel yetki içermesi gerekebilir. Hangi yetkilerin bulunması gerektiğini görüşme sonrasında yazılı olarak iletiyoruz.',
        ],
        [
            'soru'  => 'Boşanma davaları ne kadar sürede sonuçlanır?',
            'cevap' => 'Tarafların protokolde anlaşmış olduğu boşanmalar çoğunlukla tek celsede sonuçlanır. Çekişmeli boşanmalarda süre; delillerin toplanmasına, tanık sayısına ve mahkemenin iş yüküne bağlı olarak değişir. Görüşme sonrasında benzer dosyalardaki ortalama süreleri paylaşırız.',
        ],
        [
            'soru'  => 'Görüşme için randevu almak zorunlu mu?',
            'cevap' => 'Evet. Dosyanıza hak ettiği zamanı ayırabilmek için görüşmeler randevu ile yapılmaktadır. Telefon veya e-posta yoluyla randevu talebinde bulunabilirsiniz.',
        ],
        [
            'soru'  => 'Avukatlık ücretleri nasıl belirleniyor?',
            'cevap' => 'Ücret, her yıl Türkiye Barolar Birliği tarafından yayımlanan Avukatlık Asgari Ücret Tarifesi esas alınarak, dosyanın kapsamı ve süresi dikkate alınarak belirlenir. Tüm koşullar baştan yazılı avukatlık sözleşmesiyle netleştirilir.',
        ],
        [
            'soru'  => 'Dava açmadan önce hukuki danışmanlık alabilir miyim?',
            'cevap' => 'Elbette. Çoğu uyuşmazlıkta dava açmadan önce alınan danışmanlık, hem süreci kısaltır hem de maliyeti düşürür. Bazı dosyalarda dava açmanın gerekmediğini baştan söylediğimiz de olur.',
        ],
        [
            'soru'  => 'Ankara dışından da hizmet alabilir miyim?',
            'cevap' => 'Evet. Ankara başta olmak üzere Türkiye genelindeki dosyaları takip ediyor, görüşmeleri çevrim içi olarak da gerçekleştiriyoruz.',
        ],
    ],

    /*
     | Faaliyet alanı detay sayfaları — /faaliyet-alanlari/{slug}
     |
     | Anahtar, 'faaliyet_alanlari' içindeki 'slug' değeridir. Yalnızca
     | burada karşılığı olan alanlar için detay sayfası açılır; olmayanın
     | kartı doğrudan iletişime yönlendirir ve adresi 404 döner.
     |
     | ŞABLON: Aşağıdaki "asker-alma-hukuku" kaydı referans olarak
     | eksiksiz doldurulmuştur. Diğer alanları eklemek için bu kaydı
     | kopyalayıp içeriğini değiştirmeniz yeterlidir; sayfa, rota ve
     | tasarım kendiliğinden çalışır.
     |
     | TODO: Kalan 8 alanın metinleri Av. Kazık tarafından yazıldıkça eklenecek.
     */
    'faaliyet_detaylari' => [

        'asker-alma-hukuku' => [

            'ust_baslik' => 'Faaliyet alanı',
            'giris' => [
                'Asker alma hukuku; yoklama, sevk, erteleme, muafiyet ve bedelli askerlik işlemlerinden doğan uyuşmazlıkları kapsar. Bu işlemlerin tamamı idari işlem niteliğindedir ve idari yargı denetimine tabidir.',
                'Av. Yahya Kazık, Türk Silahlı Kuvvetleri bünyesinde disiplin subaylığı ve Millî Savunma Bakanlığı müfettişliği görevlerinde bulunmuş; asker alma mevzuatının uygulamadaki işleyişini kurum içinden görmüştür. Bu deneyim, dosyanın hangi aşamada hangi birimde tıkandığını baştan görebilmeyi sağlar.',
            ],

            'durumlar' => [
                'Yoklama sonucu verilen sağlık kararına itiraz',
                'Askerliğe elverişli değildir raporunun reddedilmesi',
                'Hakem hastane sürecinin yürütülmesi',
                'Öğrenim, çalışma veya sağlık nedeniyle sevk tehiri talepleri',
                'Bakaya ve yoklama kaçağı işlemlerinden doğan cezai süreçler',
                'Bedelli askerlik başvurularında çıkan uyuşmazlıklar',
                'Dövizle askerlik ve yurt dışı statüsüne ilişkin işlemler',
                'İdari işlemin iptali için yürütmenin durdurulması istemi',
            ],

            'surec' => [
                ['no' => '01', 'baslik' => 'Kararın ve sürenin tespiti',
                 'metin' => 'Tebliğ tarihi belirlenir. Asker alma dosyalarında süreler kısadır ve kaçırıldığında dava yolu kapanabilir; ilk yapılan iş bu tarihin netleştirilmesidir.'],
                ['no' => '02', 'baslik' => 'Dosya ve rapor incelemesi',
                 'metin' => 'Kararın dayandığı yönetmelik maddesi ile mevcut tıbbi belgeler karşılaştırılır. Çelişki varsa hangi belgenin eksik olduğu belirlenir.'],
                ['no' => '03', 'baslik' => 'İdari başvuru',
                 'metin' => 'Dava açmadan önce yapılması gereken idari başvuru hazırlanır ve süresi içinde sunulur.'],
                ['no' => '04', 'baslik' => 'İdari dava ve takip',
                 'metin' => 'Gerekirse iptal davası açılır, koşulları varsa yürütmenin durdurulması talep edilir. Süreç boyunca bilgilendirme yapılır.'],
            ],

            'sss' => [
                ['soru' => 'Yoklama kararına ne kadar süre içinde itiraz edilir?',
                 'cevap' => 'Süre, kararın size tebliğ edildiği tarihten itibaren işler ve kısadır. Bu nedenle kararı aldığınız günü mutlaka not edin; tebliğ tarihi belli değilse dosyanın tamamı üzerinden tespit edilmesi gerekir.'],
                ['soru' => 'Hastane raporları çelişirse ne olur?',
                 'cevap' => 'Raporlar arasında çelişki bulunması hâlinde hakem hastane uygulaması devreye girer. Bu aşamada dosyaya sunulan tıbbi belgelerin eksiksiz ve okunaklı olması sonucu doğrudan etkiler.'],
                ['soru' => 'Bakaya kaldım, cezai sorumluluğum var mı?',
                 'cevap' => 'Bakaya kalmanın idari ve cezai sonuçları olabilir. Ancak geçerli bir mazeretin belgelenmesi hâlinde sonuç değişebilir; durumunuzu değerlendirmeden genel bir yanıt vermek doğru olmaz.'],
                ['soru' => 'Yurt dışında yaşıyorum, süreci takip edebilir misiniz?',
                 'cevap' => 'Evet. Dövizle askerlik ve yurt dışı statüsüne ilişkin işlemleri vekâleten yürütüyor, görüşmeleri çevrim içi yapıyoruz.'],
            ],

            'ilgili_makale' => 'askerlik-muafiyeti-ve-itiraz-yollari',
        ],

    ],

];
