<?php

namespace App\Http\Controllers\Yonetim;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Yönetim paneli girişi. Hesaplar `php artisan yonetici:olustur` ile açılır;
 * sitede kayıt olma sayfası yoktur.
 */
class GirisController extends Controller
{
    public function goster(): View
    {
        return view('yonetim.giris');
    }

    public function giris(Request $istek): RedirectResponse
    {
        // Hatalar "giris" adlı torbaya yazılır; böylece sitedeki diğer formların
        // hata mesajlarıyla karışmaz ve üst menüdeki giriş kutusu açık gelir.
        $bilgiler = $istek->validateWithBag('giris', [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'E-posta adresinizi yazın.',
            'email.email'       => 'Geçerli bir e-posta adresi yazın.',
            'password.required' => 'Şifrenizi yazın.',
        ]);

        $anahtar = 'yonetim-giris:'.Str::lower($bilgiler['email']).'|'.$istek->ip();

        if (RateLimiter::tooManyAttempts($anahtar, 5)) {
            $saniye = RateLimiter::availableIn($anahtar);

            return back()->onlyInput('email')
                ->withErrors(['email' => "Çok fazla deneme yapıldı. {$saniye} saniye sonra tekrar deneyin."], 'giris');
        }

        if (! Auth::attempt($bilgiler, $istek->boolean('hatirla'))) {
            RateLimiter::hit($anahtar, 60);

            return back()->onlyInput('email')->withErrors(['email' => 'E-posta adresi veya şifre hatalı.'], 'giris');
        }

        RateLimiter::clear($anahtar);
        $istek->session()->regenerate();

        return redirect()->intended(route('yonetim.panel'));
    }

    public function cikis(Request $istek): RedirectResponse
    {
        Auth::logout();
        $istek->session()->invalidate();
        $istek->session()->regenerateToken();

        return redirect()->route('yonetim.giris');
    }
}
