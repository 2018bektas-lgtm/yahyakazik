<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * Yönetim paneline giriş yapabilecek bir hesap açar veya mevcut hesabın
 * şifresini değiştirir.
 */
#[Signature('yonetici:olustur {email? : Yöneticinin e-posta adresi}')]
#[Description('Yönetim paneli için yönetici hesabı oluşturur veya şifresini yeniler')]
class YoneticiOlustur extends Command
{
    public function handle(): int
    {
        $email = $this->argument('email') ?? text('E-posta adresi', required: true);
        $mevcut = User::where('email', $email)->first();

        $ad = $mevcut?->name ?? text('Ad Soyad', default: config('site.ad'), required: true);
        $sifre = password('Şifre (en az 10 karakter)', required: true);

        $dogrulama = Validator::make(compact('email', 'sifre'), [
            'email' => ['email'],
            'sifre' => ['min:10'],
        ]);

        if ($dogrulama->fails()) {
            $this->error($dogrulama->errors()->first());

            return self::FAILURE;
        }

        User::updateOrCreate(['email' => $email], ['name' => $ad, 'password' => $sifre]);

        $this->info($mevcut ? 'Şifre güncellendi.' : 'Yönetici hesabı oluşturuldu. Giriş: /yonetim/giris');

        return self::SUCCESS;
    }
}
