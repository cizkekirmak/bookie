<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Login') }} - Bookie</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mystery+Quest&display=swap" rel="stylesheet">
    
    {{-- PWA Ayarları --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#477c35">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192.png') }}">

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
            overflow-x: hidden;
        }

        * {
            -webkit-tap-highlight-color: transparent !important;
            box-sizing: border-box;
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
            position: relative;
        }
        
        .site-basligi {
            font-family: "Henny Penny", cursive;
            font-size: 100px;
            color: #1a562b;
            margin: 0 0 -20px 0;
            letter-spacing: 2px;
            user-select: none;
            z-index: 10;
        }

        /* Login Kartı + Ayraç Kapsayıcısı */
        .login-card-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .kutucuk { 
            background: #ebf8e2;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(44, 159, 76, 0.64);
            font-family: "Henny Penny", cursive;
            width: 280px;
            position: relative;
            z-index: 5;
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

        /* --- SAKLANAN DEFTER AYRACI / DOWNLOAD TAB --- */
        .download-drawer-tab {
            position: absolute;
            z-index: 2;
            background: #fef8b8;
            border: 2px solid #5a7d3b;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            font-family: 'Unkempt', cursive;
            transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
        }

        /* MASAÜSTÜ: Sağdan açılma */
        @media (min-width: 769px) {
            .download-drawer-tab {
                top: 40px;
                right: 0;
                width: 220px;
                height: 180px;
                border-radius: 0 16px 16px 0;
                transform: translateX(38px); /* Sadece kulakçık dışarı taşar */
                padding: 16px 14px;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
            }

            .download-drawer-tab.is-open {
                transform: translateX(216px); /* Sağa doğru açılır */
            }

            .drawer-handle-tab {
                position: absolute;
                top: 20px;
                left: -38px;
                width: 38px;
                height: 52px;
                background: #fef8b8;
                border: 2px solid #5a7d3b;
                border-right: none;
                border-radius: 12px 0 0 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 20px;
            }
        }

        /* MOBİL: Aşağı doğru açılma */
        @media (max-width: 768px) {
            .download-drawer-tab {
                bottom: 0;
                left: 50%;
                width: 240px;
                height: 170px;
                border-radius: 0 0 16px 16px;
                transform: translate(-50%, 36px); /* Sadece kulakçık alttan sarkar */
                padding: 18px 14px 14px 14px;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
            }

            .download-drawer-tab.is-open {
                transform: translate(-50%, 166px); /* Aşağıya doğru kayar */
            }

            .drawer-handle-tab {
                position: absolute;
                top: -36px;
                left: 50%;
                transform: translateX(-50%);
                width: 60px;
                height: 36px;
                background: #fef8b8;
                border: 2px solid #5a7d3b;
                border-bottom: none;
                border-radius: 12px 12px 0 0;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 18px;
            }
        }

        .btn-download-action {
            background: #2e6f40;
            color: #ffffff;
            border: 1.5px solid #1a562b;
            border-radius: 14px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: bold;
            font-family: 'Unkempt', cursive;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.12);
            transition: transform 0.15s ease, background 0.15s;
            margin-top: 10px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-download-action:hover {
            background: #235631;
            transform: scale(1.04);
        }

        /* Uygulama olarak yüklüyse bu kısmı tamamen yok et */
        @media all and (display-mode: standalone) {
            .download-drawer-tab {
                display: none !important;
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
        
        <div class="login-card-wrapper">
            
            {{-- GİRİŞ KUTUSU --}}
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

            {{-- ARKADAN AÇILAN AYRAÇ / POST-IT TAB --}}
            <div class="download-drawer-tab" id="downloadDrawerTab">
                <div class="drawer-handle-tab" id="drawerHandleBtn" title="{{ __('download the bookieapp !!') }}">
                    📲
                </div>

                <div style="color: #1a562b; font-size: 16px; font-weight: bold; line-height: 1.2;">
                    download the bookieapp !!
                </div>
                
                <p style="font-size: 11px; color: #436d39; margin: 6px 0 0 0; line-height: 1.2;">
                    {{ __('get the cozy app experience on your phone ✨') }}
                </p>

                <button type="button" class="btn-download-action" id="triggerPwaInstallBtn">
                    {{ __('install now') }} 📲
                </button>
            </div>

        </div>
    </div>

    {{-- SAĞ ALT KÖŞE DİL BUTONU --}}
    <div class="floating-lang-switch">
        @include('partials.lang-switch')
    </div>

    <script>
        // Service Worker Kaydı
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }

        // Yüklü uygulama kontrolü (Standalone)
        const isAppMode = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
        const drawerTab = document.getElementById('downloadDrawerTab');
        if (isAppMode && drawerTab) {
            drawerTab.style.display = 'none';
        }

        // Kulakçık Aç/Kapa Yönetimi
        const handleBtn = document.getElementById('drawerHandleBtn');
        if (handleBtn && drawerTab) {
            handleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                drawerTab.classList.toggle('is-open');
            });

            // Boşluğa basınca ayracı geri kapat
            document.addEventListener('click', (e) => {
                if (!drawerTab.contains(e.target)) {
                    drawerTab.classList.remove('is-open');
                }
            });
        }

        // PWA Kurulum Tetikleyicisi
        let deferredPrompt;
        const triggerBtn = document.getElementById('triggerPwaInstallBtn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
        });

        if (triggerBtn) {
            triggerBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted' && drawerTab) {
                        drawerTab.style.display = 'none';
                    }
                    deferredPrompt = null;
                } else {
                    // iOS Safari yönlendirmesi
                    alert("iPhone için: Safari'de alttaki 'Paylaş' simgesine basıp 'Ana Ekrana Ekle' seçeneğini seçebilirsin! 📲");
                }
            });
        }
    </script>
</body>
</html>