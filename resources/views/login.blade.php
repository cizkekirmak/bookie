<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Login') }} - Bookie</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Henny+Penny&family=Mystery+Quest&family=Unkempt:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
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

        .kutucuk { 
            background: #ebf8e2;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(44, 159, 76, 0.64);
            font-family: "Henny Penny", cursive;
            width: 280px;
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

            <form method="POST" action="/login">
                @csrf
                
                @if (session('status'))
                    <p style="color: #2e6f40; font-size: 15px; font-family: 'Unkempt', cursive; text-align: center; margin-bottom: 15px;">
                        {{ session('status') }}
                    </p>
                @endif
                
                @if ($errors->any())
                    <div style="color: #2e6433; font-size: 15px; font-family: 'Unkempt', cursive; text-align: center; margin-bottom: 15px;">
                        <ul style="list-style-type: none; padding: 0; margin: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <label for="loginname">{{ __('username or email:') }}</label>
                <input type="text" id="loginname" name="loginname" value="{{ old('loginname') }}" required autocomplete="username">
                @error('loginname')
                    <small style="color: #2e6433; font-size: 15px; font-family: 'Unkempt', cursive; display: block; margin-top: -12px; margin-bottom: 12px;">
                        {{ $message }}
                    </small>
                @enderror

                <label for="password">{{ __('password:') }}</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
                @error('password')
                    <small style="color: #2e6433; font-size: 15px; font-family: 'Unkempt', cursive; display: block; margin-top: -12px; margin-bottom: 12px;">
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
    </div>

    {{-- SAĞ ALT KÖŞE DİL BUTONU --}}
    <div class="floating-lang-switch">
        @include('partials.lang-switch')
    </div>
</body>
</html>