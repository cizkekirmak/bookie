<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bookie - {{ __('Profile') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mystery+Quest&display=swap" rel="stylesheet">

    {{-- MODAL FONKSİYONLARI (GLOBAL SCOPE) --}}
    <script>
        window.openFriendsModal = function() {
            const modal = document.getElementById('friendsModal');
            if (modal) {
                modal.style.display = 'flex';
            }
        };

        window.closeFriendsModal = function() {
            const modal = document.getElementById('friendsModal');
            if (modal) {
                modal.style.display = 'none';
            }
        };

        window.switchProfileView = function(mode) {
            const listView = document.getElementById('profile-list-view');
            const shelfView = document.getElementById('profile-shelf-view');
            const btnList = document.getElementById('btn-list-view');
            const btnShelf = document.getElementById('btn-shelf-view');

            if (mode === 'list') {
                if (listView) listView.style.display = 'flex';
                if (shelfView) shelfView.style.display = 'none';
                if (btnList) { btnList.style.background = '#255719'; btnList.style.color = '#ffffff'; }
                if (btnShelf) { btnShelf.style.background = 'transparent'; btnShelf.style.color = '#1a3c11'; }
            } else {
                if (listView) listView.style.display = 'none';
                if (shelfView) shelfView.style.display = 'flex';
                if (btnShelf) { btnShelf.style.background = '#255719'; btnShelf.style.color = '#ffffff'; }
                if (btnList) { btnList.style.background = 'transparent'; btnList.style.color = '#1a3c11'; }
            }
        };

        window.filterStatus = function(status, clickedBtn) {
            document.querySelectorAll('.status-tab').forEach(btn => {
                btn.style.background = '#eaf3e4';
                btn.style.color = '#1a3c11';
                btn.style.border = '1px solid #737e3d';
            });
            clickedBtn.style.background = '#255719';
            clickedBtn.style.color = '#ffffff';
            clickedBtn.style.border = 'none';

            const cards = document.querySelectorAll('.book-card-item');
            cards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                if (status === 'all' || cardStatus === status) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        };
    </script>

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
        * {
            box-sizing: border-box;
        }

        body {
            background-color: #badfa0;
            background-image: url('{{ asset('images/giris.png') }}');
            background-size: cover;
            background-position: center top;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-family: 'Unkempt', cursive;
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
        }

        .site-header-inner {
            width: 100%;
            height: 100%;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
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

        .header-actions-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }

        .header-icon-box {
            width: 52px;
            height: 52px;
            max-width: 52px;
            max-height: 52px;
            object-fit: contain;
            border: 1.5px solid #4b813b;
            display: block;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .header-icon-box:hover {
            transform: scale(1.1);
        }

        /* ANA GÖVDE: Masaüstü */
        .profile-container {
            width: 100%;
            max-width: 1520px;
            margin: 0 auto;
            height: calc(100vh - 76px);
            display: flex;
            background-color: #f7faf5;
            overflow: hidden;
            font-family: 'Unkempt', cursive;
            border-left: 2px solid #4c7237;
            border-right: 2px solid #4c7237;
            border-bottom: 2px solid #4c7237;
        }

        .profile-sidebar-panel {
            width: 320px;
            background: #8ec46f;
            border-right: 2px solid #4c7237;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-sizing: border-box;
            flex-shrink: 0;
            height: 100%;
        }

        .profile-main-content {
            flex: 1;
            padding: 25px 30px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-width: 0;
            background-color: #f7faf5;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: rgba(225, 232, 213, 0.4);
            border-radius: 4px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #8ec46f;
            border-radius: 4px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #4c7237;
        }

        @keyframes popInModal {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes rainbowWave {
            0% { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(360deg); }
        }

        .admin-rainbow-badge {
            background: linear-gradient(90deg, #ff7675, #fab1a0, #ffeaa7, #55efc4, #74b9ff, #a29bfe, #fd79a8) !important;
            animation: rainbowWave 3s linear infinite !important;
            border: 1.5px solid rgba(255, 255, 255, 0.8) !important;
            color: #1a3c11 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
        }

        .mobile-profile-tab,
        .mobile-profile-overlay,
        .profile-close-btn {
            display: none !important;
        }

        /* TELEFON / MOBİL UYARLAMA */
        @media (max-width: 1024px) {
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

            .header-actions-wrap {
                gap: 8px !important;
            }

            .header-icon-box,
            .notification-icon-img,
            .header-actions-wrap img {
                width: 44px !important;
                height: 44px !important;
                max-width: 44px !important;
                max-height: 44px !important;
                border: 2px solid #4b813b !important;
                border-radius: 10px !important;
                display: block !important;
            }

            .profile-container {
                height: auto !important;
                min-height: calc(100vh - 68px) !important;
                min-height: calc(100dvh - 68px) !important;
                border: none !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                background-color: transparent !important;
                padding: 16px 12px 100px 12px !important;
                overflow-x: hidden !important;
            }

            .profile-sidebar-panel {
                position: fixed !important;
                top: 0 !important;
                right: 0 !important;
                left: auto !important;
                width: 82vw !important;
                max-width: 320px !important;
                height: 100vh !important;
                max-height: 100vh !important;
                border-radius: 16px 0 0 16px !important;
                border-left: 2px solid #2d5a27 !important;
                border-right: none !important;
                z-index: 100001 !important;
                transform: translateX(100%);
                transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: -6px 0 24px rgba(0,0,0,0.3) !important;
                padding: 20px 16px !important;
                overflow-y: auto !important;
            }

            .profile-sidebar-panel.drawer-open {
                transform: translateX(0) !important;
            }

            .profile-close-btn {
                display: block !important;
                align-self: flex-start;
                background: none;
                border: none;
                font-size: 22px;
                color: #1a3c11;
                cursor: pointer;
                padding: 0;
                margin-bottom: 6px;
                line-height: 1;
            }

            .mobile-profile-overlay {
                display: block !important;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.45);
                z-index: 100000;
                backdrop-filter: blur(2px);
            }

            .mobile-profile-overlay.hidden {
                display: none !important;
            }

            .mobile-profile-tab {
                display: flex !important;
                align-items: center;
                gap: 6px;
                position: fixed !important;
                bottom: 18px !important;
                right: 0 !important;
                left: auto !important;
                background: #fdf5a6;
                color: #2c441b;
                border: 2px solid #5a7d3b;
                border-right: none;
                border-radius: 14px 0 0 14px;
                padding: 7px 14px;
                font-family: 'Unkempt', cursive;
                font-weight: bold;
                font-size: 14px;
                box-shadow: -3px 4px 10px rgba(0,0,0,0.18);
                cursor: pointer;
                z-index: 9999;
                transform: rotate(-1.5deg);
            }

            .profile-main-content {
                width: 100% !important;
                max-width: 440px !important;
                height: calc(100vh - 165px) !important;
                height: calc(100dvh - 165px) !important;
                min-height: 480px !important;
                padding: 16px 14px !important;
                border: 2px solid #4c7237 !important;
                border-radius: 16px !important;
                background-color: #f7faf5 !important;
                box-shadow: 0 4px 16px rgba(0,0,0,0.08) !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
                flex: none !important;
            }

            .profile-main-content > div:first-child {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
                margin-bottom: 12px !important;
                flex-shrink: 0 !important;
            }

            .profile-main-content h3 {
                font-size: 19px !important;
            }
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <header class="site-header-outer">
        <div class="site-header-inner">     

            {{-- LOGO --}}
            <a href="{{ route('dashboard') }}" class="header-logo">
                Bookie
            </a>

            {{-- SAĞ İKONLAR & DİL SEÇİCİ --}}
            <div class="header-actions-wrap">
                @include('partials.lang-switch')

                <div style="display: flex; align-items: center; justify-content: center; line-height: 0;">
                    @include('partials.notifications')
                </div>

                <a href="{{ route('ayarlar') }}" style="display: inline-block; line-height: 0; text-decoration: none; flex-shrink: 0;">
                    <img src="{{ asset('images/ayarlar.jpg') }}" alt="{{ __('Settings') }}" class="header-icon-box">
                </a>

                <a href="{{ route('dashboard') }}" style="display: inline-block; line-height: 0; text-decoration: none; flex-shrink: 0;">
                    <img src="{{ asset('images/dash.jpg') }}" alt="{{ __('Dashboard') }}" class="header-icon-box">
                </a>
            </div>

        </div>
    </header>

    {{-- GÖVDE --}}
    <div class="profile-container">

        {{-- SAĞDAN AÇILAN ÇEKMECE PANEL --}}
        <aside class="profile-sidebar-panel" id="profileSidebarDrawer">
            
            <button type="button" class="profile-close-btn" id="closeProfileDrawerBtn">&times;</button>

            @php
                $defaultAvatar = asset('images/profile.jpg');
                $userAvatar = (!empty($user->avatar) && str_starts_with($user->avatar, 'http')) 
                    ? $user->avatar 
                    : $defaultAvatar;
            @endphp

            {{-- Profil Fotoğrafı --}}
            <div style="width: 120px; height: 120px; border-radius: 50%; border: 2px solid #2d5a27; background: #eaf3e4; display: flex; justify-content: center; align-items: center; margin-bottom: 0; overflow: hidden; flex-shrink: 0;">
                @if(!empty($user->avatar))
                    <img src="{{ $userAvatar }}" 
                         alt="{{ $user->username ?? $user->name }}" 
                         referrerpolicy="no-referrer" 
                         style="width: 100%; height: 100%; object-fit: cover; display: block;" 
                         onerror="this.onerror=null; this.src='{{ $defaultAvatar }}';">
                @else
                    <span style="font-size: 45px;">🌱</span>
                @endif
            </div>

            @php
                $title = $user->reading_title;
            @endphp

            {{-- 1. Kullanıcı Adı --}}
            <div style="font-family: 'Henny Penny', cursive; font-size: 28px; color: #1a3c11; margin-bottom: 2px;">
                {{ $user->username ?? $user->name }}
            </div>

            {{-- 2. Unvan Rozeti --}}
            <button type="button" onclick="openTitlesModal()" style="background: none; border: none; padding: 0; cursor: pointer;">
                <div class="{{ !empty($title['is_admin']) ? 'admin-rainbow-badge' : '' }}" style="
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    @if(empty($title['is_admin']))
                        background-color: {{ $title['bg'] }};
                        border: 1.5px solid {{ $title['border'] }};
                        color: {{ $title['color'] }};
                    @endif
                    padding: 3px 12px;
                    border-radius: 16px;
                    font-family: 'Unkempt', cursive;
                    font-size: 13px;
                    font-weight: bold;
                    margin-top: 2px;
                    margin-bottom: 8px;
                    cursor: pointer;
                ">
                    <span>{{ $title['icon'] }}</span>
                    <span>{{ __($title['name'] ?? 'certified noob') }}</span>
                    <span style="font-size: 10px; opacity: 0.6;">▼</span>
                </div>
            </button>

            {{-- 3. Bio Metni --}}
            @if(!empty($user->bio))
                <div style="font-family: 'Unkempt', cursive; font-size: 15px; color: #355e28; margin: 0; line-height: 1.2; text-align: center;">
                    "{{ $user->bio }}"
                </div>
            @endif

            @if(!$isOwnProfile)
                <div style="margin-bottom: 20px; width: 100%; display: flex; justify-content: center; margin-top: 10px;">
                    @if(!$friendship)
                        <form action="{{ route('friends.request', $user->id) }}" method="POST" style="margin: 0; width: 100%;">
                            @csrf
                            <button type="submit" style="width: 100%; background-color: #2d5a27; color: white; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 15px;">
                                {{ __('Add friend') }}
                            </button>
                        </form>
                    @elseif($friendship->status === 'pending')
                        @if($friendship->user_id === auth()->id())
                            <form action="{{ route('friends.request', $user->id) }}" method="POST" style="margin: 0; width: 100%;">
                                @csrf
                                <button type="submit" style="width: 100%; background-color: #6c757d; color: white; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 14px;">
                                    {{ __('Request Sent (Cancel)') }}
                                </button>
                            </form>
                        @else
                            <div style="display: flex; gap: 5px; width: 100%;">
                                <form action="{{ route('friends.accept', $user->id) }}" method="POST" style="flex: 1; margin: 0;">
                                    @csrf
                                    <button type="submit" style="width: 100%; background-color: #25621d; color: rgb(140, 155, 25); border: none; padding: 8px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 14px;">{{ __('Accept') }}</button>
                                </form>
                                <form action="{{ route('friends.reject', $user->id) }}" method="POST" style="flex: 1; margin: 0;">
                                    @csrf
                                    <button type="submit" style="width: 100%; background-color: #516a28; color: rgb(119, 131, 26); border: none; padding: 8px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 14px;">{{ __('Decline') }}</button>
                                </form>
                            </div>
                        @endif
                    @elseif($friendship->status === 'accepted')
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 6px; width: 100%;">
                            <form action="{{ route('friends.remove', $user->id) }}" method="POST" style="margin: 0; display: flex; justify-content: center; width: 100%;">
                                @csrf
                                <button type="submit" style="width: 150px; background-color: #d2f48a; color: #101e08; border: 1px solid #1d491b; padding: 4px 8px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 13px; text-align: center; display: block;">
                                    {{ __('Remove Friend') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Friends Butonu --}}
            <div style="width: 100%; border-top: 1.5px solid #deeaa5; padding-top: 20px; margin-top: 14px; display: flex; justify-content: center;">
                <button 
                    type="button" 
                    onclick="openFriendsModal()" 
                    style="
                        width: 100%;
                        display: flex; 
                        align-items: center; 
                        justify-content: space-between; 
                        background: #deeaa5; 
                        border: 1.5px solid #2d5a27; 
                        border-radius: 12px; 
                        padding: 8px 14px; 
                        cursor: pointer; 
                        font-family: 'Unkempt', cursive; 
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06); 
                        transition: all 0.2s ease;
                    "
                    onmouseenter="this.style.background='#c5f58b'; this.style.transform='translateY(-1px)';"
                    onmouseleave="this.style.background='#deeaa5'; this.style.transform='translateY(0)';"
                >
                    <span style="font-size: 15px; font-weight: bold; color: #1a3c11; display: flex; align-items: center; gap: 6px;">
                        🌱 {{ __('friends') }}
                    </span>

                    <span style="
                        background: #2d5a27; 
                        color: #ffffff; 
                        font-size: 12px; 
                        font-weight: bold; 
                        padding: 2px 8px; 
                        border-radius: 12px;
                    ">
                        {{ $friendsCount }}
                    </span>
                </button>
            </div>

        </aside>

        {{-- SAĞ İÇERİK ALANI --}}
        <main class="profile-main-content">

            {{-- ÜST PANEL: BAŞLIK & SWITCH --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-shrink: 0;">
                <h3 style="font-family: 'Henny Penny', cursive; font-size: 24px; color: #1a3c11; margin: 0;">
                    {{ __('Bookshelf & Reviews') }}
                </h3>

                <div style="display: flex; background: #cae28c; border: 2px solid #737e3d; border-radius: 12px; padding: 4px; gap: 6px;">
                    <button type="button" id="btn-list-view" onclick="switchProfileView('list')" style="border: none; background: #255719; color: #ffffff; padding: 6px 14px; border-radius: 8px; font-family: 'Unkempt', cursive; font-size: 14px; font-weight: bold; cursor: pointer; transition: all 0.2s ease;">
                        {{ __('Book List') }}
                    </button>
                    <button type="button" id="btn-shelf-view" onclick="switchProfileView('shelf')" style="border: none; background: transparent; color: #1a3c11; padding: 6px 14px; border-radius: 8px; font-family: 'Unkempt', cursive; font-size: 14px; font-weight: bold; cursor: pointer; transition: all 0.2s ease;">
                        {{ __('Shelf View') }}
                    </button>
                </div>
            </div>

            {{-- PARTIALS ÇAĞRILARI --}}
            @include('profile.list-view')
            @include('profile.shelf-view')

        </main>

    </div>

    {{-- SAĞDAKİ MOBİL PROFİL POST-IT BUTONU & OVERLAY --}}
    <div class="mobile-profile-tab" id="openProfileDrawerBtn">
        <span>📌 {{ __('Profile') }}</span>
    </div>
    <div class="mobile-profile-overlay hidden" id="profileDrawerOverlay"></div>

{{-- JAVASCRIPT ÇEKMECE YÖNETİMİ --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const openDrawerBtn = document.getElementById('openProfileDrawerBtn');
    const closeDrawerBtn = document.getElementById('closeProfileDrawerBtn');
    const drawer = document.getElementById('profileSidebarDrawer');
    const overlay = document.getElementById('profileDrawerOverlay');

    if (openDrawerBtn && drawer && overlay) {
        function openProfile() {
            drawer.classList.add('drawer-open');
            overlay.classList.remove('hidden');
        }

        function closeProfile() {
            drawer.classList.remove('drawer-open');
            overlay.classList.add('hidden');
        }

        openDrawerBtn.addEventListener('click', openProfile);
        overlay.addEventListener('click', closeProfile);
        if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeProfile);
    }
});
</script>

{{-- FRIENDS MODAL --}}
<div 
    id="friendsModal" 
    style="
        display: none; 
        position: fixed; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
        z-index: 999999; 
        background: rgba(0, 0, 0, 0.45); 
        justify-content: center; 
        align-items: center; 
        backdrop-filter: blur(2px);
    "
>
    <div 
        style="
            background: #f7faf5; 
            border: 2px solid #4c7237; 
            border-radius: 12px; 
            width: 380px; 
            max-width: 90%; 
            max-height: 80vh; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
            animation: popInModal 0.2s ease-out; 
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
        "
    >
        {{-- Modal Başlığı --}}
        <div 
            style="
                display: flex; 
                justify-content: space-between; 
                align-items: center; 
                padding: 14px 18px; 
                border-bottom: 2px solid #82b564; 
                background: #c5e8ad;
            "
        >
            <span 
                style="
                    font-family: 'Henny Penny', cursive; 
                    font-size: 18px; 
                    color: #1a3c11;
                "
            >
                🌱 {{ __('Friends') }} ({{ $friendsCount ?? ($user->friends ? $user->friends->count() : 0) }})
            </span>

            <button 
                type="button" 
                onclick="closeFriendsModal()" 
                style="
                    background: transparent; 
                    border: none; 
                    font-size: 20px; 
                    font-weight: bold; 
                    color: #1a3c11; 
                    cursor: pointer; 
                    line-height: 1; 
                    padding: 0 4px;
                "
            >
                ✕
            </button>
        </div>

        {{-- Liste Alanı --}}
        <div 
            class="custom-scroll" 
            style="
                padding: 14px; 
                overflow-y: auto; 
                display: flex; 
                flex-direction: column; 
                gap: 10px; 
                max-height: 380px;
            "
        >
            @php
                $friendsList = $user->friends();
            @endphp

            @forelse($friendsList as $friend)
                @php
                    $friendAvatarUrl = (!empty($friend->avatar) && str_starts_with($friend->avatar, 'http'))
                        ? $friend->avatar
                        : asset('images/profile.jpg');
                @endphp
                <div 
                    onclick="window.location.href='/profile/{{ $friend->id }}'" 
                    style="
                        display: flex; 
                        align-items: center; 
                        justify-content: space-between; 
                        background: #ffffff; 
                        border: 1.5px solid #4c7237; 
                        border-radius: 10px; 
                        padding: 10px 12px; 
                        cursor: pointer; 
                        transition: all 0.15s ease;
                    "
                    onmouseenter="this.style.background='#f1f8ed'; this.style.transform='translateY(-1px)';"
                    onmouseleave="this.style.background='#ffffff'; this.style.transform='translateY(0)';"
                >
                    <div 
                        style="
                            display: flex; 
                            align-items: center; 
                            gap: 10px;
                        "
                    >
                        <div 
                            style="
                                width: 34px; 
                                height: 34px; 
                                border-radius: 50%; 
                                background: #badfa0; 
                                border: 1px solid #4c7237; 
                                display: flex; 
                                align-items: center; 
                                justify-content: center; 
                                font-size: 15px; 
                                color: #1a3c11; 
                                font-weight: bold; 
                                font-family: 'Unkempt', cursive; 
                                overflow: hidden;
                            "
                        >
                            <img src="{{ $friendAvatarUrl }}" 
                                 alt="{{ $friend->username ?? 'Profile' }}" 
                                 referrerpolicy="no-referrer" 
                                 style="width: 100%; height: 100%; border-radius: 50%; display: block; object-fit: cover;"
                                 onerror="this.onerror=null; this.src='{{ asset('images/profile.jpg') }}';">
                        </div>

                        <div>
                            <div 
                                style="
                                    font-family: 'Unkempt', cursive; 
                                    font-size: 15px; 
                                    font-weight: bold; 
                                    color: #1f5117;
                                "
                            >
                                {{ $friend->username ?? $friend->name }}
                            </div>

                            <div 
                                style="
                                    font-family: 'Unkempt', cursive; 
                                    font-size: 11px; 
                                    color: #666;
                                "
                            >
                                {{ \App\Models\UserBook::where('user_id', $friend->id)->whereHas('book')->count() }} {{ __('books') }}
                            </div>
                        </div>
                    </div>

                    <span 
                        style="
                            font-family: 'Unkempt', cursive; 
                            font-size: 12px; 
                            color: #4c7237; 
                            font-weight: bold;
                        "
                    >
                        {{ __('wanna see their profile? →') }}
                    </span>
                </div>
            @empty
                <div 
                    style="
                        text-align: center; 
                        color: #355726; 
                        font-family: 'Unkempt', cursive; 
                        padding: 25px 10px; 
                        font-size: 14px;
                    "
                >
                    {{ __('You dont have any friends yet :((  🌱') }}
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
window.addEventListener('click', function(e) {
    const modal = document.getElementById('friendsModal');
    if (e.target === modal) {
        closeFriendsModal();
    }
});
</script>

@include('partials.chat')
</body>
</html>
@include('partials.title-modal')