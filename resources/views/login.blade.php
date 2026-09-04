<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
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
            font-display: swap;
        }
        @font-face {
            font-family: 'Henny Penny';
            src: url('{{ asset('fonts/HennyPenny-Regular.ttf') }}') format('truetype');
            font-weight: 400;
            font-display: swap;
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent !important;
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
            overflow-x: hidden;
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

        /* KART ALANI */
        .login-card-wrapper {
            position: relative;
            width: 280px;
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
            font-family: Arial, sans-serif;
            outline: none;
        }

        .kutucuk button {
            width: 100%;
            padding: 10px;
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
        .alt-linkler p { margin: 8px 0; color: #0f511e; }
        .alt-linkler a { color: #2e6f40; text-decoration: none; font-family: "Henny Penny", cursive; }

        /* --- DEFTER AYRACI --- */
        .postit-tab-container {
            position: absolute;
            top: 55px;
            left: 0;
            z-index: 1; /* Login kutusunun arkasında */
            display: flex;
            align-items: center;
            /* 180px gövde + 38px kulakçık = 218px. 280 - 218 = 62px. */
            transform: translateX(62px); 
            transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Tıklanınca sağa fırlar */
        .postit-tab-container.open {
            transform: translateX(280px);
        }

        /* Sarı Not Kağıdı (Solda) */
        .postit-content {
            width: 180px;
            background: #fdf5a6;
            border: 2px solid #5a7d3b;
            border-radius: 0 12px 12px 0;
            padding: 12px 10px;
            text-align: center;
            font-family: 'Unkempt', cursive;
            box-shadow: 3px 4px 10px rgba(0,0,0,0.15);
            flex-shrink: 0;
        }

        /* Kulakçık (Sağ Uçta) */
        .postit-handle {
            width: 38px;
            height: 48px;
            background: #fdf5a6;
            border: 2px solid #5a7d3b;
            border-left: none;
            border-radius: 0 10px 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 3px 2px 5px rgba(0,0,0,0.12);
            flex-shrink: 0;
            margin-left: -2px;
        }

        .btn-app-install {
            background: #2e6f40;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: bold;
            font-family: 'Unkempt', cursive;
            cursor: pointer;
            margin-top: 8px;
            display: inline-block;
        }

        .btn-app-install:hover {
            background: #235631;
        }

        /* Uygulama yüklüyse ayracı gizle */
        @media all and (display-mode: standalone) {
            .postit-tab-container {
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

            {{-- ARKADAKİ DEFTER AYRACI --}}
            <div class="postit-tab-container" id="postitTab">
                <div class="postit-content">
                    <div style="font-size: 13px; font-weight: bold; color: #1a562b; line-height: 1.2;">
                        download the bookieapp !!
                    </div>
                    <button type="button" class="btn-app-install" onclick="installAppAction()">
                        {{ __('install now') }} 📲
                    </button>
                </div>
                <div class="postit-handle" onclick="togglePostit(event)">
                    📲
                </div>
            </div>

        </div>
    </div>

    {{-- SAĞ ALT DİL BUTONU --}}
    <div class="floating-lang-switch">
        @include('partials.lang-switch')
    </div>

    <script>
        // Yüklü uygulama kontrolü
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
            const tab = document.getElementById('postitTab');
            if (tab) tab.style.display = 'none';
        }

        function togglePostit(e) {
            e.stopPropagation();
            document.getElementById('postitTab').classList.toggle('open');
        }

        document.addEventListener('click', function(e) {
            const tab = document.getElementById('postitTab');
            if (tab && !tab.contains(e.target)) {
                tab.classList.remove('open');
            }
        });

        function installAppAction() {
            alert("Bookie uygulamasını telefonuna eklemek için Safari/Chrome menüsünden 'Ana Ekrana Ekle' seçeneğini seçebilirsin! ✨");
        }
    </script>
</body>
</html>