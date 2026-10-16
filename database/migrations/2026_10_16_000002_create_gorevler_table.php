<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gorevler', function (Blueprint $table) {
            $table->id();
            // Doluysa bu kayıt, o görevin alt başlığıdır
            $table->foreignId('ust_gorev_id')->nullable()->constrained('gorevler')->cascadeOnDelete();
            $table->string('baslik');
            $table->date('tarih');
            $table->time('saat')->nullable();   // boşsa gün içinde saatsiz görev
            $table->text('aciklama')->nullable();
            $table->timestamp('tamamlandi_at')->nullable();
            $table->timestamps();

            $table->index(['tarih', 'saat']);
        });

        // Saati geçince kendiliğinden tamamlansın mı? Tamamlandı işareti elle
        // geri alınan randevularda kapatılır, böylece tekrar tamamlanmaz.
        Schema::table('randevular', function (Blueprint $table) {
            $table->boolean('otomatik_tamamla')->default(true)->after('durum');
        });
    }

    public function down(): void
    {
        Schema::table('randevular', fn (Blueprint $table) => $table->dropColumn('otomatik_tamamla'));
        Schema::dropIfExists('gorevler');
    }
};
