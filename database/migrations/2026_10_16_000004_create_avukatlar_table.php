<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Büro avukatları (Hakkımızda > Ekibimiz ve her avukatın profil sayfası).
 * Kurucu avukatın kaydı, sitedeki mevcut özgeçmiş metniyle birlikte eklenir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avukatlar', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('ad');
            $table->string('unvan')->nullable();          // Örn. Kurucu Avukat & Arabulucu
            $table->string('baro')->nullable();           // Örn. Ankara Barosu
            $table->string('ozet', 400)->nullable();      // Ekip kartındaki kısa tanıtım
            $table->text('biyografi')->nullable();        // Paragraflar boş satırla ayrılır
            $table->json('uzmanliklar')->nullable();      // ["İş hukuku", ...]
            $table->json('ozgecmis')->nullable();         // [{donem, baslik, metin}, ...]
            $table->string('fotograf')->nullable();
            $table->string('eposta')->nullable();
            $table->string('linkedin')->nullable();
            $table->unsignedSmallInteger('sira')->default(0);
            $table->boolean('yayinda')->default(true);
            $table->timestamps();
        });

        DB::table('avukatlar')->insert([
            'slug'      => 'yahya-kazik',
            'ad'        => 'Av. Yahya Kazık',
            'unvan'     => 'Kurucu Avukat & Arabulucu',
            'baro'      => 'Ankara Barosu',
            'ozet'      => 'Kamu kurumlarında disiplin subaylığı ve MSB müfettişliği yaptı; 2017\'den bu yana serbest avukat ve arabulucu.',
            'biyografi' => "Otorite Hukuk, kurucusu Av. Yahya Kazık'ın kamu kurumlarında edindiği köklü deneyim ve akademik birikimle kurulmuştur. Uzun yıllar Türk Silahlı Kuvvetleri bünyesinde çeşitli idari görevlerde bulunmuş; disiplin subaylığı ve Millî Savunma Bakanlığı müfettişliği gibi sorumluluklar üstlenmiştir.\n\nİstanbul Üniversitesi Hukuk Fakültesi mezunu olan Av. Kazık, özel hukuk ve kamu yönetimi alanlarında iki ayrı yüksek lisans çalışması yapmıştır. 2017 yılından bu yana serbest avukat ve arabulucu olarak çalışmakta; her müvekkile özel, etik değerlere bağlı ve çözüm odaklı hizmet sunmayı ilke edinmektedir.",
            'uzmanliklar' => json_encode(['Disiplin hukuku', 'Asker alma hukuku', 'Memur hukuku', 'İş hukuku', 'Ceza hukuku', 'Sağlık hukuku'], JSON_UNESCAPED_UNICODE),
            'ozgecmis'  => json_encode([
                ['donem' => 'Eğitim', 'baslik' => 'İstanbul Üniversitesi Hukuk Fakültesi', 'metin' => 'Lisans eğitimini İstanbul Üniversitesi Hukuk Fakültesi\'nde tamamladı.'],
                ['donem' => 'Kamu Görevi', 'baslik' => 'Türk Silahlı Kuvvetleri', 'metin' => 'Uzun yıllar Türk Silahlı Kuvvetleri bünyesinde çeşitli idari görevlerde bulundu; disiplin subaylığı sorumluluğunu üstlendi.'],
                ['donem' => 'Kamu Görevi', 'baslik' => 'Millî Savunma Bakanlığı Müfettişliği', 'metin' => 'MSB müfettişi olarak görev yaptı. Bu dönemde disiplin hukuku, asker alma hukuku ve memur hukuku alanlarında saha deneyimi kazandı.'],
                ['donem' => 'Akademik', 'baslik' => 'İki Ayrı Yüksek Lisans', 'metin' => 'Özel hukuk ve kamu yönetimi alanlarında iki ayrı yüksek lisans çalışması yaptı.'],
                ['donem' => '2017', 'baslik' => 'Serbest Avukatlık ve Arabuluculuk', 'metin' => '2017 yılından bu yana serbest avukat ve arabulucu olarak çalışıyor; her müvekkile özel, etik değerlere bağlı ve çözüm odaklı hizmet sunmayı ilke ediniyor.'],
                ['donem' => 'Bugün', 'baslik' => 'Otorite Hukuk ve Danışmanlık Bürosu', 'metin' => 'Ankara Çankaya\'daki büroda, Ankara başta olmak üzere Türkiye genelinde avukatlık ve arabuluculuk faaliyetlerini sürdürüyor.'],
            ], JSON_UNESCAPED_UNICODE),
            'eposta'     => 'otoritehukuk@gmail.com',
            'sira'       => 0,
            'yayinda'    => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('avukatlar');
    }
};
