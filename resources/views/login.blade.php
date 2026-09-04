<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2e6f40">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Login') }} - Bookie</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mystery+Quest&display=swap" rel="stylesheet">
    
    <style>
        @font-face {
            font-family: 'Unkempt';
            src: url('{{ asset('fonts/Unkempt-Regular.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Henny Penny';
            src: url('{{ asset('fonts/HennyPenny-Regular.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        body { 
            font-family: "Mystery Quest", system-ui;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #D1FFBD;
            margin: 0; 
            background-image: url("{{ asset('images/arkaplan.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }

        * {
            -webkit-tap-highlight-color: transparent !important;
        }

        button, a, label, span, img {
            user-select: none !important;
            -webkit-user-select: none !important;
            -webkit-touch-callout: none !important;
        }

        .dis-kapsayici {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0px;
        }
        
        .site-basligi {
            font-family: "Henny Penny", cursive;
            font-size: 100px;
            color: #1a562b;
            margin: 0 0 -20px 0;
            letter-spacing: 2px;
            user-select: none;
        }

        .kutucuk-sarmalayici {
            position: relative;
        }

        .kutucuk { 
            position: relative;
            z-index: 2;
            background: #ebf8e2;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(44, 159, 76, 0.64);
            font-family: "Henny Penny", cursive;
            width: 280px;
            box-sizing: border-box;
        }

        .kutucuk label {
            display: block;
            margin-bottom: 2px;
            color: #333;
            font-size: 17px;
            cursor: pointer;
        }

        .kutucuk input {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #5f9852;
            border-radius: 5px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            outline: none;
        }

        .kutucuk button {
            width: 100%;
            padding: 10px;
            margin: 0px auto;
            background-color: #2e6f40;
            color: #d1ffbd;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
            font-family: "Henny Penny", cursive;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .kutucuk button:active {
            transform: scale(0.98);
        }

        .kutucuk button:hover {
            background-color: #235631;
        }

        .alt-linkler {
            margin-top: 18px;
            text-align: center;
            font-size: 14px;
        }

        .alt-linkler p {
            margin: 8px 0;
            color: #0f511e;
        }

        .alt-linkler a {
            color: #2e6f40;
            text-decoration: none;
            font-family: "Henny Penny", cursive;
            font-weight: normal;
        }

        .alt-linkler a:hover {
            text-decoration: underline;
        }

        /* ---- MASAÜSTÜ ÇEKMECESİ ---- */
        .cekmece {
            position: absolute;
            top: 50%;
            left: 100%;
            width: 190px;
            box-sizing: border-box;
            background: #ebf8e2;
            border-radius: 0 12px 12px 0;
            box-shadow: 4px 4px 8px rgba(44, 159, 76, 0.5);
            padding: 22px 20px;
            z-index: 1;
            text-align: center;
            transform: translateY(-50%) translateX(-100%);
            transition: transform 0.4s ease;
        }

        .kutucuk-sarmalayici.acik .cekmece {
            transform: translateY(-50%) translateX(0);
        }

        .cekmece-tab {
            position: absolute;
            top: 50%;
            right: -34px;
            transform: translateY(-50%);
            width: 34px;
            height: 64px;
            background: #2e6f40;
            border-radius: 0 10px 10px 0;
            box-shadow: 3px 2px 6px rgba(44, 159, 76, 0.55);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            padding: 0;
            z-index: 3;
            transition: background-color 0.2s ease;
        }

        .cekmece-tab:hover {
            background-color: #235631;
        }

        .cekmece-tab img {
            width: 30px;
            height: 30px;
            pointer-events: none;
            transition: transform 0.35s ease;
        }

        .kutucuk-sarmalayici.acik .cekmece-tab img {
            transform: rotate(180deg);
        }

        .cekmece-icerik {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease 0.1s;
        }

        .kutucuk-sarmalayici.acik .cekmece-icerik {
            opacity: 1;
            pointer-events: auto;
        }

        .cekmece-icerik p {
            font-family: "Henny Penny", cursive;
            color: #1a562b;
            font-size: 18px;
            margin: 0 0 14px 0;
        }

        .cekmece-icerik button {
            width: 100%;
            padding: 10px;
            background-color: #2e6f40;
            color: #d1ffbd;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
            font-weight: bold;
            font-family: "Henny Penny", cursive;
            transition: background-color 0.2s ease;
        }

        .cekmece-icerik button:hover {
            background-color: #235631;
        }

        /* Mobildeki buton varsayılanda gizli */
        .mobil-indir-alani {
            display: none;
        }

        /* ---- MOBİL UYUMLULUK (@media) ---- */
        @media (max-width: 640px) {
            .site-basligi {
                font-size: 80px;
                margin: 0 0 -10px 0;
            }

            .kutucuk {
                width: 260px;
                padding: 18px;
            }

            /* Mobilde masaüstü çekmecesini kapat */
            .cekmece {
                display: none !important;
            }

            /* Kutunun altındaki mobil butonunu aktif et */
            .mobil-indir-alani {
                display: block;
                margin-top: 14px;
                text-align: center;
            }

            .mobil-indir-btn {
                background: #d1ffbd;
                color: #1a562b;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 14px;
                font-family: "Henny Penny", cursive;
                cursor: pointer;
                box-shadow: 0 3px 6px rgba(44, 159, 76, 0.35);
                display: inline-flex;
                align-items: center;
                gap: 6px;
                transition: transform 0.1s ease;
            }

            .mobil-indir-btn:active {
                transform: scale(0.96);
            }

            .mobil-indir-btn img {
                width: 25px;
                height: 25px;
            }
        }

        .floating-lang-switch {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }
    </style>
</head>
<body>
    <div class="dis-kapsayici">
        <h1 class="site-basligi">Bookie</h1>

        <div class="kutucuk-sarmalayici" id="kutucukSarmalayici">
            <div class="kutucuk">

                <form method="POST" action="/login">
                    @csrf
                    
                    @if (session('status'))
                        <p style="color: #2e6f40; font-size: 15px; font-family: 'Unkempt', cursive; text-align: center; margin-bottom: 15px;">
                            {{ session('status') }}
                        </p>
                    @endif

                    <label for="loginname">{{ __('username or email:') }}</label>
                    <input type="text" id="loginname" name="loginname" value="{{ old('loginname') }}" required autocomplete="username">
                    @error('loginname')
                        <small style="color: #2e6433; font-size: 14px; font-family: 'Unkempt', cursive; display: block; margin-top: -12px; margin-bottom: 14px; text-align: center;">
                            {{ $message }}
                        </small>
                    @enderror

                    <label for="password">{{ __('password:') }}</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                    @error('password')
                        <small style="color: #2e6433; font-size: 14px; font-family: 'Unkempt', cursive; display: block; margin-top: -12px; margin-bottom: 14px; text-align: center;">
                            {{ $message }}
                        </small>
                    @enderror

                    <button type="submit">{{ __('log in') }}</button>

                    <div class="alt-linkler">
                         <a href="/register"><p>{{ __("don't have an account?") }}</p></a>
                         <a href="/forgotpassword"><p>{{ __('forgot your password?') }}</p></a>
                    </div>
                </form>
            </div>

            {{-- MASAÜSTÜ ÇEKMECESİ --}}
            <div class="cekmece" id="masaustuCekmece">
                <button type="button" class="cekmece-tab" id="cekmeceTab" aria-label="{{ __('Download Bookie') }}">
                    <img src="{{ asset('images/indir-ikon.png') }}" alt="{{ __('download') }}">
                </button>

                <div class="cekmece-icerik">
                    <p>{{ __('download bookie') }}</p>
                    <button type="button" id="indirButonu">{{ __('download') }}</button>
                </div>
            </div>
        </div>

        {{-- MOBİL BUTONU --}}
        <div class="mobil-indir-alani" id="mobilIndirAlani">
            <button type="button" class="mobil-indir-btn" id="mobilIndirBtn">
                <img src="{{ asset('images/indir-ikon.png') }}" alt="">
                {{ __('download bookie') }}
            </button>
        </div>
    </div>

    {{-- DİL BUTONU --}}
    <div class="floating-lang-switch">
        @include('partials.lang-switch')
    </div>

    <script>
        // Çeviriler
        const pwaLang = {
            generalHint: @json(__('To install, you can use your browser\'s menu or address bar! ✨')),
            iosHint: @json(__('To install on iPhone/iPad: tap the Share button below and select \'Add to Home Screen\'! ✨'))
        };

        // 1. SADECE UYGULAMA İÇİNDEYKEN İNDİRME ALANLARINI KALDIR
        function kontrolEtVeGizle() {
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
            if (isStandalone) {
                const cekmece = document.getElementById('masaustuCekmece');
                const mobilAlan = document.getElementById('mobilIndirAlani');
                if (cekmece) cekmece.style.display = 'none';
                if (mobilAlan) mobilAlan.style.display = 'none';
            }
        }
        kontrolEtVeGizle();

        // 2. Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }

        // 3. Masaüstü Çekmece Aç/Kapat
        const tabBtn = document.getElementById('cekmeceTab');
        const sarmalayici = document.getElementById('kutucukSarmalayici');
        if (tabBtn && sarmalayici) {
            tabBtn.addEventListener('click', function () {
                sarmalayici.classList.toggle('acik');
            });
        }

        // 4. PWA Kurulum Olayı
        let deferredPrompt = null;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
        });

        // Kurulum tamamlandığı an kapat
        window.addEventListener('appinstalled', () => {
            if (sarmalayici) sarmalayici.classList.remove('acik');
            deferredPrompt = null;
        });

        // 5. İndirme Tetikleyicisi (Hem Masaüstü Hem Mobil İçin Ortak)
        async function indir() {
            const isIos = /iphone|ipad|ipod/.test(window.navigator.userAgent.toLowerCase());

            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted' && sarmalayici) {
                    sarmalayici.classList.remove('acik');
                }
                deferredPrompt = null;
            } else if (isIos) {
                alert(pwaLang.iosHint);
            } else {
                alert(pwaLang.generalHint);
            }
        }

        const indirButonu = document.getElementById('indirButonu');
        if (indirButonu) indirButonu.addEventListener('click', indir);

        const mobilIndirBtn = document.getElementById('mobilIndirBtn');
        if (mobilIndirBtn) mobilIndirBtn.addEventListener('click', indir);

        // 6. Giriş Butonu Tıklandığında Anında Geri Bildirim
        const loginForm = document.querySelector('form[action="/login"]');
        if (loginForm) {
            loginForm.addEventListener('submit', function () {
                const submitBtn = loginForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.75';
                    submitBtn.style.cursor = 'wait';
                    submitBtn.textContent = @json(__('logging in...'));
                }
            });
        }
    </script>
</body>
</html>