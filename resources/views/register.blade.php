<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('join us!') }} - Bookie</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Henny+Penny&family=Mystery+Quest&display=swap" rel="stylesheet">
    <style>
        @font-face {
    font-family: 'Unkempt';
    src: url('{{ asset('fonts/Unkempt-Regular.ttf') }}') format('truetype');
    font-weight: 400;
    font-style: normal;
    font-display: swap;
}
        body { 
            font-family: "Mystery Quest" , system-ui;
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

        .dis-kapsayici {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0px;
        }
        label {
            display: block;
            margin-bottom: -2px;
            color: #333;
            font-size: 17px;
        }
        
        .site-basligi {
            font-family: "Henny Penny", cursive;
            font-size: 100px;
            color: #1a562b;
            margin: 0 0 -20px 0;
            letter-spacing: 2px;
        }

        .kutucuk { 
            background: #ebf8e2; 
            padding: 20px; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px rgba(44, 159, 76, 0.64); 
            width: 280px;
            font-family: "Henny Penny", cursive; 
        }

        .alt-linkler {
            margin-top: 15px;
            text-align: center;
            font-size: 15px;
        }

        .alt-linkler p {
            margin: 6px 0;
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

        input {     
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #5f9852;
            border-radius: 5px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        button { 
            width: 250px; 
            padding: 10px; 
            display: block;
            margin: 0 auto;
            background-color: #2e6f40; 
            color: #D1ffbd; 
            border: none; 
            font-size: 16px;
            border-radius: 5px; 
            cursor: pointer;
            font-weight: bold;
            font-family: "Henny Penny", cursive;
        }

        button:hover { background-color: #45a049; }

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
    
        <div class="kutucuk">

            <form method="POST" action="/register" novalidate>
                @csrf

                <label for="username">{{ __('username:') }}</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}">
                @error('username')
                    <small style="color: #2e6433; font-size: 15px; font-family: 'Unkempt', cursive; display: block; margin-top: -5px; margin-bottom: 10px;">
                        {{ $message }}
                    </small>
                @enderror

                <label for="email">{{ __('email:') }}</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}">
                @error('email')
                    <small style="color: #2e6433; font-size: 15px; font-family: 'Unkempt', cursive; display: block; margin-top: -5px; margin-bottom: 10px;">
                        {{ $message }}
                    </small>
                @enderror   

                <label for="password">{{ __('password:') }}</label>
                <input type="password" id="password" name="password">
                @error('password')
                    <small style="color: #2e6433; font-size: 15px; font-family: 'Unkempt', cursive; display: block; margin-top: -5px; margin-bottom: 10px;">
                        {{ $message }}
                    </small>
                @enderror

                <button type="submit">{{ __('register') }}</button>

                <div class="alt-linkler">
                    <p><a href="/login">{{ __('already got an account?') }}</a></p>
                    <p><a href="/forgotpassword">{{ __('forgot your password?') }}</a></p>
                </div>
            </form>
        </div>
    </div>

    {{-- SAĞ ALT KÖŞE DİL BUTONU --}}
    <div class="floating-lang-switch">
        @include('partials.lang-switch')
    </div>

</body>
</html>