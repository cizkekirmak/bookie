<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bookie - {{ __('Settings') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Henny+Penny&family=Mystery+Quest&family=Unkempt:wght@400;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100vh;
            background-color: #badfa0;
            background-image: url('{{ asset('images/giris.png') }}');
            background-size: cover;
            background-position: center top;
            background-attachment: fixed;
            background-repeat: no-repeat;
            font-family: 'Unkempt', cursive;
            display: flex;
            flex-direction: column;
        }

        /* HEADER: Masaüstü (76px) */
        .site-header-outer {
            width: 100%;
            height: 76px;
            background-color: #477c35;
            background-image: 
                url('{{ asset('images/profil-header.png') }}'),
                url('{{ asset('images/bosluk.png') }}');
            background-size: 
                auto 100%, 
                auto 100%;
            background-position: 
                center bottom, 
                0 bottom;
            background-repeat: 
                no-repeat, 
                repeat-x;
            border-bottom: 2px solid #2d5a27;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            flex-shrink: 0;
        }

        .site-header-inner {
            width: 100%;
            height: 100%;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-logo {
            font-family: 'Henny Penny', cursive;
            font-size: 48px;
            color: #1f5117;
            flex-shrink: 0;
            margin-top: 10px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            user-select: none;
            text-decoration: none;
        }

        .header-icon-box {
            width: 52px;
            height: 52px;
            object-fit: contain;
            border: 1.5px solid #4b813b;
            display: block;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .header-icon-box:hover {
            transform: scale(1.1);
        }

        /* ORTA GÖVDE */
        .settings-main-area {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px 85px 16px;
            width: 100%;
        }

        .settings-card {
            background: #cae28c;
            border: 2px solid #5a8c69;
            border-radius: 18px;
            padding: 28px 32px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .settings-title {
            font-family: 'Henny Penny', cursive;
            font-size: 26px;
            color: #1a3c11;
            margin: 0 0 18px 0;
            text-align: center;
        }

        .avatar-preview-wrap {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 2px solid #2d5a27;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }

        .avatar-preview-img {
            width: 100%;
            height: 100%;
            max-width: 110px;
            max-height: 110px;
            object-fit: cover;
            display: block;
        }

        .choose-pic-btn {
            background: #ffffff;
            border: 1.5px solid #4c7237;
            border-radius: 16px;
            padding: 5px 14px;
            font-family: 'Unkempt', cursive;
            font-size: 13px;
            color: #1a3c11;
            cursor: pointer;
            margin-bottom: 18px;
            transition: all 0.15s ease;
        }

        .choose-pic-btn:hover {
            background: #255719;
            color: #ffffff;
        }

        .form-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 6px;
            font-size: 14px;
            color: #1a3c11;
            font-weight: bold;
        }

        .bio-char-counter {
            font-size: 12px;
            color: #555;
            font-weight: normal;
        }

        .bio-textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1.5px solid #4c7237;
            background: #ffffff;
            font-family: 'Unkempt', cursive;
            font-size: 14px;
            color: #1a3c11;
            resize: none;
            outline: none;
            margin-bottom: 18px;
            box-sizing: border-box;
            line-height: 1.4;
        }

        .save-btn {
            width: 100%;
            background: #255719;
            color: #ffffff;
            border: none;
            padding: 11px;
            border-radius: 10px;
            font-family: 'Unkempt', cursive;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .save-btn:hover {
            background: #1a3c11;
            transform: scale(1.02);
        }

        .alert-box {
            width: 100%;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 14px;
            text-align: center;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* MOBİL UYARLAMA */
        @media (max-width: 768px) {
            .site-header-outer {
                height: 68px !important;
            }

            .site-header-inner {
                padding: 0 14px !important;
            }

            .header-logo {
                font-size: 30px !important;
                margin-top: 0 !important;
            }

            .header-icon-box {
                width: 44px !important;
                height: 44px !important;
                border: 2px solid #4b813b !important;
                border-radius: 10px !important;
            }

            .settings-main-area {
                padding: 20px 14px 90px 14px !important;
                min-height: calc(100vh - 68px) !important;
                min-height: calc(100dvh - 68px) !important;
            }

            .settings-card {
                padding: 24px 18px !important;
                border-radius: 16px !important;
                max-width: 360px !important;
                margin: auto 0 !important;
            }

            .settings-title {
                font-size: 24px !important;
                margin-bottom: 14px !important;
            }

            .avatar-preview-wrap {
                width: 95px !important;
                height: 95px !important;
            }

            .avatar-preview-img {
                max-width: 95px !important;
                max-height: 95px !important;
            }

            .choose-pic-btn {
                padding: 5px 12px !important;
                font-size: 12px !important;
                margin-bottom: 14px !important;
            }

            .bio-textarea {
                font-size: 13px !important;
                margin-bottom: 16px !important;
            }

            .save-btn {
                padding: 10px !important;
                font-size: 15px !important;
            }
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <header class="site-header-outer">
        <div class="site-header-inner">
            
            <a href="{{ route('dashboard') }}" class="header-logo">
                Bookie
            </a>

            {{-- SAĞ İKONLAR & DİL SEÇİCİ --}}
            <div style="display: flex; align-items: center; gap: 14px; flex-shrink: 0;">
                @include('partials.lang-switch')

                <a href="{{ route('profile') }}" style="display: inline-block; line-height: 0; text-decoration: none; flex-shrink: 0;">
                    <img src="{{ asset('images/profile.jpg') }}" alt="{{ __('Profile') }}" class="header-icon-box">
                </a>
                
                <a href="{{ route('dashboard') }}" style="display: inline-block; line-height: 0; text-decoration: none; flex-shrink: 0;">
                    <img src="{{ asset('images/dash.jpg') }}" alt="{{ __('Dashboard') }}" class="header-icon-box">
                </a>
            </div>

        </div>
    </header>

    {{-- GÖVDE --}}
    <div class="settings-main-area">
        <div class="settings-card">
            
            <h2 class="settings-title">{{ __('profile settings') }}</h2>

            @if(session('success'))
                <div class="alert-box alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert-box alert-error">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" style="width: 100%; display: flex; flex-direction: column; align-items: center;">
                @csrf

                {{-- Avatar Önizleme --}}
                @php
                    $currentAvatar = auth()->user()->avatar;
                    $defaultAvatar = asset('images/profile.jpg');
                    $avatarSrc = (!empty($currentAvatar) && str_starts_with($currentAvatar, 'http'))
                        ? $currentAvatar
                        : $defaultAvatar;
                @endphp

                <div class="avatar-preview-wrap">
                    <img id="avatar-preview" 
                         src="{{ $avatarSrc }}" 
                         alt="{{ __('Profile') }}" 
                         class="avatar-preview-img"
                         referrerpolicy="no-referrer"
                         onerror="this.onerror=null; this.src='{{ $defaultAvatar }}';">
                </div>

                {{-- Avatar Yükleme Butonu --}}
                <input type="file" name="avatar" id="avatar-input" accept="image/*" style="display: none;" onchange="previewImage(event)">
                <button type="button" class="choose-pic-btn" onclick="document.getElementById('avatar-input').click()">
                    {{ __('choose your best pic !') }}
                </button>

                {{-- Bio Alanı --}}
                <div class="form-label-row">
                    <span>{{ __('About you (bio)') }}</span>
                    <span id="char-count" class="bio-char-counter">{{ strlen(auth()->user()->bio ?? '') }}/160</span>
                </div>

                <textarea name="bio" id="bio-textarea" rows="3" maxlength="160" placeholder="{{ __('tell us about yourself...') }}" class="bio-textarea" oninput="updateCharCount(this)">{{ auth()->user()->bio ?? '' }}</textarea>

                {{-- Kaydet Butonu --}}
                <button type="submit" class="save-btn">
                    {{ __('save') }}
                </button>
            </form>

        </div>
    </div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('avatar-preview');
        if (preview) {
            preview.src = reader.result;
        }
    }
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

function updateCharCount(textarea) {
    const counter = document.getElementById('char-count');
    if (counter) {
        counter.innerText = `${textarea.value.length}/160`;
    }
}
</script>

@include('partials.chat')
</body>
</html>