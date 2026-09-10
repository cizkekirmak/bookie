<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 | Bookie</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            background-color: #fdfdfd; /* Bookie pastel yeşili */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .error-link {
            display: block;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.2s ease, filter 0.2s ease;
        }
        .error-link:hover {
            transform: scale(1.02);
        }
        .error-img {
            width: 100%;
            max-width: 520px; /* Yatay dikdörtgeni masaüstünde tatlı bir boyutta tutar */
            height: auto;
            display: block;
            border-radius: 16px;
        }
    </style>
</head>
<body>
    @php
        $locale = app()->getLocale();
        $imgName = ($locale === 'tr') ? '404-tr.png' : '404-en.png';
    @endphp

    <a href="{{ route('dashboard') }}" class="error-link" title="{{ __('Back') }}">
        <img src="{{ asset('images/' . $imgName) }}" alt="404 Not Found" class="error-img">
    </a>
</body>
</html>