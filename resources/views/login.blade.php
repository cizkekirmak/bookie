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
            height: 100vh;
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

            button,
            a,
            label,
            span,
            img {
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
            transition: background-color 0.2s ease;
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

        /* ---- İNDİRME ÇEKMECESİ ---- */
        /* Sekme (.cekmece-tab), çekmecenin (.cekmece) bir PARÇASI:
           kapalıyken kutunun arkasına gizlenir, sadece sekme kenardan
           taşar; çekildiğinde ikisi birlikte hareket eder. */

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
            width: 20px;
            height: 20px;
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

        /* ---- MOBİLDE: SAĞDAN DEĞİL, ALTTAN AÇILSIN ---- */
        @media (max-width: 640px) {
            .cekmece {
                top: 100%;
                left: 50%;
                width: min(240px, 82vw);
                border-radius: 0 0 12px 12px;
                transform: translateX(-50%) translateY(-100%);
            }

            .kutucuk-sarmalayici.acik .cekmece {
                transform: translateX(-50%) translateY(0);
            }

            .cekmece-tab {
                top: auto;
                right: auto;
                bottom: -34px;
                left: 50%;
                transform: translateX(-50%);
                width: 64px;
                height: 34px;
                border-radius: 0 0 10px 10px;
            }

            .cekmece-tab img {
                transform: rotate(-90deg);
            }

            .kutucuk-sarmalayici.acik .cekmece-tab img {
                transform: rotate(90deg);
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

            {{-- ÇEKMECE: sekme (tab) ve içerik AYNI parçanın içinde, birlikte hareket ediyor --}}
            <div class="cekmece">
                <button type="button" class="cekmece-tab" id="cekmeceTab" aria-label="{{ __('Download Bookie') }}">
                    <img src="{{ asset('images/indir-ikon.png') }}" alt="{{ __('download') }}">
                </button>

                <div class="cekmece-icerik">
                    <p>{{ __('download bookie') }}</p>
                    <button type="button" id="indirButonu">{{ __('download') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- SAĞ ALT KÖŞE DİL BUTONU --}}
    <div class="floating-lang-switch">
        @include('partials.lang-switch')
    </div>

   <script>
    // Uygulama zaten kuruluysa çekmeceyi sayfada hiç gösterme
    window.addEventListener('DOMContentLoaded', () => {
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
        
        // Zaten masaüstü uygulaması içindeyse çekmeceyi gizle
        if (isStandalone) {
            const cekmece = document.querySelector('.cekmece');
            if (cekmece) cekmece.style.display = 'none';
        }
    });

    document.getElementById('cekmeceTab').addEventListener('click', function () {
        document.getElementById('kutucukSarmalayici').classList.toggle('acik');
    });

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js');
    }

    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
    });

    // Kullanıcı uygulamayı kurduğu an çekmeceyi kapat
    window.addEventListener('appinstalled', () => {
        document.getElementById('kutucukSarmalayici').classList.remove('acik');
        deferredPrompt = null;
    });

    document.getElementById('indirButonu').addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                document.getElementById('kutucukSarmalayici').classList.remove('acik');
            }
            deferredPrompt = null;
        } else {
            // deferredPrompt yoksa ya zaten kuruludur ya da desteklenmiyordur
            alert("Bookie zaten bilgisayarında yüklü! Adres çubuğundaki 'Uygulamada aç' butonundan veya masaüstünden açabilirsin. ✨");
        }
    });

    const loginForm = document.querySelector('form[action="/login"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function () {
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.75';
                submitBtn.style.cursor = 'wait';
                // Laravel dil çevirisinden anlık seçili dili alır
                submitBtn.textContent = @json(__('logging in...'));
            }
        });
    }
</script>
</body>
</html>