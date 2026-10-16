<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('randevular', function (Blueprint $table) {
            $table->id();
            // basvuru: müvekkil durumunu anlattı | randevu: doğrudan saat aldı | mesaj: iletişim formu
            $table->string('tip', 20)->default('randevu');
            $table->string('konu')->nullable();          // dava türü / faaliyet alanı
            $table->string('kurum')->nullable();         // karşı taraf, işveren, kurum
            $table->text('aciklama')->nullable();        // müvekkilin anlattığı genel durum
            $table->string('ad_soyad');
            $table->string('telefon', 30)->nullable();
            $table->string('eposta')->nullable();
            $table->dateTime('baslangic')->nullable();   // randevu zamanı (başvurularda boş olabilir)
            $table->unsignedSmallInteger('sure')->default(30);
            $table->string('gorusme_sekli', 20)->default('yuz_yuze');
            // beklemede | onaylandi | tamamlandi | iptal
            $table->string('durum', 20)->default('beklemede');
            $table->string('kaynak', 20)->default('site'); // site | yonetim
            $table->text('notlar')->nullable();          // yalnızca büro içi notlar
            $table->timestamps();

            $table->index(['durum', 'baslangic']);
        });

        Schema::create('etkinlikler', function (Blueprint $table) {
            $table->id();
            $table->string('baslik');
            // durusma | gorusme | sure | toplanti | musait_degil | diger
            $table->string('tur', 20)->default('diger');
            $table->dateTime('baslangic');
            $table->dateTime('bitis')->nullable();
            $table->boolean('tum_gun')->default(false);
            $table->string('yer')->nullable();
            $table->string('dosya_no')->nullable();
            $table->text('aciklama')->nullable();
            $table->boolean('tamamlandi')->default(false);
            $table->timestamps();

            $table->index('baslangic');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etkinlikler');
        Schema::dropIfExists('randevular');
    }
};
