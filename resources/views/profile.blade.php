<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="view-transition" content="same-origin">
    <title>Bookie - {{ __('Profile') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mystery+Quest&display=swap" rel="stylesheet">

    {{-- MODAL VE FİLTRELEME FONKSİYONLARI (GLOBAL SCOPE) --}}
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
            const boardView = document.getElementById('profile-board-view');
            const btnList = document.getElementById('btn-list-view');
            const btnBoard = document.getElementById('btn-board-view');
            const bookFilters = document.querySelectorAll('.hide-on-board');
            const mainContainer = document.querySelector('.profile-main-content');

            if (mode === 'list') {
                if (listView) listView.style.display = 'flex';
                if (boardView) boardView.style.display = 'none';
                bookFilters.forEach(el => el.style.display = '');
                if (btnList) { btnList.classList.add('active'); }
                if (btnBoard) { btnBoard.classList.remove('active'); }
                if (mainContainer) mainContainer.classList.remove('board-active');
            } else {
                if (listView) listView.style.display = 'none';
                if (boardView) boardView.style.display = 'flex';
                bookFilters.forEach(el => el.style.display = 'none');
                if (btnBoard) { btnBoard.classList.add('active'); }
                if (btnList) { btnList.classList.remove('active'); }
                if (mainContainer) mainContainer.classList.add('board-active');
            }
        };

        let currentProfileStatus = 'all';

        window.filterStatus = function(status, clickedBtn) {
            currentProfileStatus = status;

            const tabStyles = {
                'all':     { bg: '#dcedd2', activeBg: '#b8dfa4', color: '#27521e', border: '#9ccb86', shadow: 'rgba(39, 82, 30, 0.15)' },
                'read':    { bg: '#fee2e8', activeBg: '#fcc2ce', color: '#8e2b42', border: '#f7b1c0', shadow: 'rgba(142, 43, 66, 0.15)' },
                'reading': { bg: '#e2f0fc', activeBg: '#c2e1f9', color: '#1e5579', border: '#a8d3f5', shadow: 'rgba(30, 85, 121, 0.15)' },
                'toRead':  { bg: '#fef5d1', activeBg: '#fce9a5', color: '#7a5a0c', border: '#fae087', shadow: 'rgba(122, 90, 12, 0.15)' }
            };

            document.querySelectorAll('.status-tab').forEach(btn => {
                const type = btn.getAttribute('data-type');
                const style = tabStyles[type];
                if (style) {
                    btn.style.background = style.bg;
                    btn.style.color = style.color;
                    btn.style.border = '1.5px solid ' + style.border;
                    btn.style.boxShadow = 'none';
                }
            });

            const activeType = clickedBtn.getAttribute('data-type');
            const activeStyle = tabStyles[activeType];
            if (activeStyle) {
                clickedBtn.style.background = activeStyle.activeBg;
                clickedBtn.style.color = activeStyle.color;
                clickedBtn.style.border = '1.5px solid ' + activeStyle.border;
                clickedBtn.style.boxShadow = '0 2px 6px ' + activeStyle.shadow;
            }

            window.applyProfileSearchFilter();
        };

        window.applyProfileSearchFilter = function() {
            const query = (document.getElementById('profileBookSearchInput')?.value || '').toLowerCase().trim();
            const cards = document.querySelectorAll('.book-card-item');

            cards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                const title = (card.querySelector('h4')?.innerText || '').toLowerCase();
                const author = (card.querySelector('span')?.innerText || '').toLowerCase();

                let statusMatch = (currentProfileStatus === 'all');
                if (!statusMatch) {
                    if (currentProfileStatus === 'toRead') {
                        statusMatch = (cardStatus === 'toRead' || cardStatus === 'want_to_read');
                    } else {
                        statusMatch = (cardStatus === currentProfileStatus);
                    }
                }

                const searchMatch = !query || title.includes(query) || author.includes(query);

                if (statusMatch && searchMatch) {
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
        @view-transition {
            navigation: auto;
        }
        ::view-transition-old(root),
        ::view-transition-new(root) {
            animation-duration: 0.25s;
            animation-timing-function: ease-in-out;
        }
        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent !important;
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

        button,
        a,
        label,
        span,
        img {
            user-select: none !important;
            -webkit-user-select: none !important;
            -webkit-touch-callout: none !important;
        }

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
            z-index: 9999999 !important;
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

        .header-icon-box,
        .notification-icon-img {
            width: 65px !important;
            height: 65px !important;
            max-width: 65px !important;
            max-height: 65px !important;
            object-fit: contain;
            border: none !important;
            outline: none !important;
            display: block;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .header-icon-box:hover,
        .notification-icon-img:hover {
            transform: scale(1.08);
        }

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
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-sizing: border-box;
            flex-shrink: 0;
            height: 100%;
            overflow-y: auto;
        }

        .profile-main-content {
            flex: 1;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-width: 0;
            background-color: #f7faf5;
        }

        .profile-main-content.board-active {
            overflow-y: auto !important;
        }

        #profile-board-view {
            display: none;
            width: 100%;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            flex: 1;
            overflow-y: auto;
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

        /* Library Card Görünümü */
        .library-card-wrapper {
            width: 100%;
            background: #fffdf5;
            border: 2px solid #5a7d3b;
            border-radius: 14px;
            padding: 12px 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
            position: relative;
            transform: rotate(-0.5deg);
        }

        .library-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.5px dashed #c0d8b4;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }

        .library-card-title {
            font-family: 'Henny Penny', cursive;
            font-size: 14px;
            color: #d64b6f;
            letter-spacing: 0.5px;
        }

        /* ========================================================
           ANA TOOLBAR DÜZENİ (Masaüstünde TEK SATIR!)
           ======================================================== */
        .master-toolbar-row {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            margin-bottom: 12px;
            flex-shrink: 0;
        }

        .toolbar-pastel-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .status-tab {
            padding: 6px 14px;
            border-radius: 20px;
            font-family: 'Unkempt', cursive;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.15s ease;
        }

        .toolbar-search-box {
            flex: 1;
            min-width: 140px;
            display: flex;
            align-items: center;
        }

        .toolbar-search-box input {
            width: 100%;
            padding: 7px 14px;
            border-radius: 12px;
            border: 1.5px solid #737e3d;
            background: #ffffff;
            font-family: 'Unkempt', cursive;
            font-size: 14px;
            color: #1a3c11;
            outline: none;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.06);
            box-sizing: border-box;
        }

        .toolbar-switch-box {
            display: flex;
            background: #cae28c;
            border: 2px solid #737e3d;
            border-radius: 12px;
            padding: 3px;
            gap: 4px;
            flex-shrink: 0;
            margin-left: auto;
        }

        .view-toggle-btn {
            border: none;
            background: transparent;
            color: #1a3c11;
            padding: 5px 12px;
            border-radius: 8px;
            font-family: 'Unkempt', cursive;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .view-toggle-btn.active {
            background: #255719 !important;
            color: #ffffff !important;
        }

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
            .header-actions-wrap > a > img {
                width: 50px !important;
                height: 50px !important;
                max-width: 50px !important;
                max-height: 50px !important;
                border: none !important;
                outline: none !important;
                display: block !important;
            }

            .profile-container {
                height: calc(100vh - 68px) !important;
                height: calc(100dvh - 68px) !important;
                min-height: calc(100dvh - 68px) !important;
                border: none !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                background-color: transparent !important;
                padding: 10px 8px 14px 8px !important;
                overflow: hidden !important;
                box-sizing: border-box !important;
            }

            .profile-sidebar-panel {
                position: fixed !important;
                top: 68px !important;
                right: 0 !important;
                left: auto !important;
                width: 84vw !important;
                max-width: 330px !important;
                height: calc(100vh - 68px) !important;
                height: calc(100dvh - 68px) !important;
                max-height: calc(100vh - 68px) !important;
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
                top: 68px !important;
                left: 0;
                right: 0;
                bottom: 0;
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
                font-size: 15px;
                box-shadow: -3px 4px 10px rgba(0,0,0,0.18);
                cursor: pointer;
                z-index: 9999;
                transform: rotate(-1.5deg);
            }

            .profile-main-content {
                width: 100% !important;
                max-width: 100% !important;
                flex: 1 1 0% !important;
                height: 100% !important;
                min-height: 0 !important;
                padding: 10px 8px !important;
                border: 2px solid #4c7237 !important;
                border-radius: 16px !important;
                background-color: #f7faf5 !important;
                box-shadow: 0 4px 16px rgba(0,0,0,0.08) !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
                box-sizing: border-box !important;
            }

            #profile-list-view,
            #profile-board-view {
                flex: 1 1 0% !important;
                height: 100% !important;
                min-height: 0 !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 25px !important;
            }
        }

        /* ========================================================
           MOBİLDE MASTER TOOLBAR: 2 Sıra Buton + Pano Butonu + Altında Arama
           ======================================================== */
        @media (max-width: 768px) {
            .master-toolbar-row {
                display: flex !important;
                flex-wrap: wrap !important;
                gap: 8px !important;
                margin-bottom: 10px !important;
                align-items: stretch !important;
            }

            .toolbar-pastel-group {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 5px !important;
                flex: 1 !important;
                min-width: 0 !important;
            }

            .status-tab {
                padding: 5px 4px !important;
                font-size: 11.5px !important;
                border-radius: 10px !important;
                text-align: center !important;
            }

            .toolbar-switch-box {
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: stretch !important;
                padding: 3px !important;
                gap: 3px !important;
                width: 90px !important;
                margin-left: 0 !important;
            }

            .view-toggle-btn {
                padding: 4px 6px !important;
                font-size: 11px !important;
                text-align: center !important;
                line-height: 1.1 !important;
            }

            .toolbar-search-box {
                order: 3 !important;
                width: 100% !important;
                flex: 1 1 100% !important;
            }

            .toolbar-search-box input {
                font-size: 13px !important;
                padding: 6px 10px !important;
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
                    <img src="{{ asset('images/ayarlar-2.png') }}" alt="{{ __('Settings') }}" class="header-icon-box">
                </a>

                <a href="{{ route('dashboard') }}" style="display: inline-block; line-height: 0; text-decoration: none; flex-shrink: 0;">
                    <img src="{{ asset('images/dash.png') }}" alt="{{ __('Dashboard') }}" class="header-icon-box">
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
                $defaultAvatar = asset('images/profile.png');
                $userAvatar = (!empty($user->avatar) && str_starts_with($user->avatar, 'http')) 
                    ? $user->avatar 
                    : $defaultAvatar;
                $title = $user->reading_title;
                $joinDate = !empty($user->created_at) 
                    ? \Carbon\Carbon::parse($user->created_at)->translatedFormat('d.m.Y') 
                    : '-';
            @endphp

            {{-- GÜNCELLENMİŞ: LIBRARY CARD BİLEŞENİ --}}
            <div class="library-card-wrapper">
                {{-- Üst Damga Alanı --}}
                <div class="library-card-header">
                    <span class="library-card-title">★ {{ __('LIBRARY CARD') }}</span>
                    <span style="font-size: 12.5px; color: #5a7d3b; font-weight: bold;">{{ __('BOOKIE MEMBER') }}</span>
                </div>

                {{-- Orta Gövde --}}
                <div style="display: flex; gap: 10px; align-items: flex-start; position: relative;">
                    <div style="width: 72px; height: 72px; min-width: 72px; min-height: 72px; border-radius: 8px; border: 2px solid #5ca0b2; background: #e8f5f8; padding: 2px; flex-shrink: 0; box-shadow: inset 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; display: flex; justify-content: center; align-items: center;">
                        @if(!empty($user->avatar))
                            <img src="{{ $userAvatar }}" 
                                 alt="{{ $user->username ?? $user->name }}" 
                                 referrerpolicy="no-referrer" 
                                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px; display: block;" 
                                 onerror="this.onerror=null; this.src='{{ $defaultAvatar }}';">
                        @else
                            <span style="font-size: 30px;">🌱</span>
                        @endif
                    </div>

                    <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: flex-start;">
                        <div style="font-family: 'Unkempt', cursive !important; font-size: 20px; font-weight: bold; color: #1a3c11; line-height: 1.15; word-break: break-word;">
                            {{ $user->username ?? $user->name }}
                        </div>

                        <div style="margin-top: 4px;">
                            <button type="button" onclick="openTitlesModal()" style="background: none; border: none; padding: 0; cursor: pointer; text-align: left;">
                                <div class="{{ !empty($title['is_admin']) ? 'admin-rainbow-badge' : '' }}" style="
                                    display: inline-flex;
                                    align-items: center;
                                    gap: 5px;
                                    @if(empty($title['is_admin']))
                                        background-color: {{ $title['bg'] }};
                                        border: 1px solid {{ $title['border'] }};
                                        color: {{ $title['color'] }};
                                    @endif
                                    padding: 2.5px 8px;
                                    border-radius: 12px;
                                    font-family: 'Unkempt', cursive;
                                    font-size: 13px;
                                    font-weight: bold;
                                    line-height: 1.2;
                                ">
                                    <span>{{ $title['icon'] }}</span>
                                    <span>{{ __($title['name'] ?? 'gariban üye') }}</span>
                                    <span style="font-size: 10px; opacity: 0.6;">▼</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div style="position: absolute; right: 0; bottom: 0; font-size: 12px; color: #738b5e; font-family: 'Unkempt', cursive; line-height: 1; letter-spacing: 0.3px;">
                        {{ __('member:') }} <span style="font-weight: bold;">{{ $joinDate }}</span>
                    </div>
                </div>

                {{-- Bio Not Alanı --}}
                <div style="margin-top: 10px; background: #fdfaf0; border: 1px dashed #d5c8a8; border-radius: 6px; padding: 6px 8px; min-height: 40px; background-image: repeating-linear-gradient(transparent, transparent 17px, #faedd3 18px); line-height: 18px;">
                    <div style="font-size: 11.5px; color: #9c845b; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('bio :') }}</div>
                    <div style="font-family: 'Unkempt', cursive; font-size: 14.5px; color: #355e28; word-break: break-word; font-style: italic;">
                        {{ !empty($user->bio) ? $user->bio : __('No note yet...') }}
                    </div>
                </div>
            </div>

            {{-- Arkadaşlık İşlemleri --}}
            @if(!$isOwnProfile)
                <div style="margin-bottom: 12px; width: 100%; display: flex; justify-content: center; margin-top: 12px;">
                    @if(!$friendship)
                        <form action="{{ route('friends.request', $user->id) }}" method="POST" style="margin: 0; width: 100%;">
                            @csrf
                            <button type="submit" style="width: 100%; background-color: #2d5a27; color: white; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 15px; font-weight: bold;">
                                {{ __('Add friend') }}
                            </button>
                        </form>
                    @elseif($friendship->status === 'pending')
                        @if($friendship->user_id === auth()->id())
                            <form action="{{ route('friends.request', $user->id) }}" method="POST" style="margin: 0; width: 100%;">
                                @csrf
                                <button type="submit" style="width: 100%; background-color: #6c757d; color: white; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 14.5px;">
                                    {{ __('Request Sent (Cancel)') }}
                                </button>
                            </form>
                        @else
                            <div style="display: flex; gap: 5px; width: 100%;">
                                <form action="{{ route('friends.accept', $user->id) }}" method="POST" style="flex: 1; margin: 0;">
                                    @csrf
                                    <button type="submit" style="width: 100%; background-color: #25621d; color: rgb(140, 155, 25); border: none; padding: 8px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 14.5px; font-weight: bold;">{{ __('Accept') }}</button>
                                </form>
                                <form action="{{ route('friends.reject', $user->id) }}" method="POST" style="flex: 1; margin: 0;">
                                    @csrf
                                    <button type="submit" style="width: 100%; background-color: #516a28; color: rgb(119, 131, 26); border: none; padding: 8px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 14.5px; font-weight: bold;">{{ __('Decline') }}</button>
                                </form>
                            </div>
                        @endif
                    @elseif($friendship->status === 'accepted')
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 6px; width: 100%;">
                            <form action="{{ route('friends.remove', $user->id) }}" method="POST" style="margin: 0; display: flex; justify-content: center; width: 100%;">
                                @csrf
                                <button type="submit" style="width: 150px; background-color: #d2f48a; color: #101e08; border: 1.5px solid #1d491b; padding: 5px 8px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 14px; font-weight: bold; text-align: center; display: block;">
                                    {{ __('Remove Friend') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Friends Butonu --}}
            <div style="width: 100%; border-top: 1.5px solid #a6d88c; padding-top: 14px; margin-top: 12px; display: flex; justify-content: center;">
                <button 
                    type="button" 
                    onclick="openFriendsModal()" 
                    style="
                        width: 100%;
                        display: flex; 
                        align-items: center; 
                        justify-content: space-between; 
                        background-color: #deeaa5; 
                        background-image: url('{{ asset('images/goal-bg.jpg') }}');
                        background-size: cover;
                        background-position: center;
                        border: 1.5px solid #2d5a27; 
                        border-radius: 12px; 
                        padding: 9px 14px; 
                        cursor: pointer; 
                        font-family: 'Unkempt', cursive; 
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06); 
                        transition: all 0.2s ease;
                    "
                    onmouseenter="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.12)';"
                    onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.06)';"
                >
                    <span style="font-size: 16px; font-weight: bold; color: #1a3c11; display: flex; align-items: center; gap: 6px; text-shadow: 0 1px 2px rgba(255,255,255,0.7);">
                        🌱 {{ __('friends') }}
                    </span>

                    <span style="
                        background: #2d5a27; 
                        color: #ffffff; 
                        font-size: 13.5px; 
                        font-weight: bold; 
                        padding: 3px 9px; 
                        border-radius: 12px; 
                        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                    ">
                        {{ $friendsCount }}
                    </span>
                </button>
            </div>

            {{-- Yıllık Okuma Hedefi Kartı --}}
            <div style="
                width: 100%; 
                margin-top: 12px; 
                background-color: #deeaa5; 
                background-image: url('{{ asset('images/goal-bg.jpg') }}');
                background-size: cover;
                background-position: center;
                border: 1.5px solid #2d5a27; 
                border-radius: 12px; 
                padding: 10px 12px; 
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06); 
                display: flex; 
                flex-direction: column; 
                gap: 6px;
            ">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 15.5px; font-weight: bold; color: #1a3c11; text-shadow: 0 1px 2px rgba(255,255,255,0.7);">
                        🎯 {{ $currentYear ?? date('Y') }} {{ __('Goal') }}
                    </span>
                    @if(!empty($readingGoal))
                        <span style="
                            background: #2d5a27; 
                            color: #ffffff; 
                            font-size: 13px; 
                            font-weight: bold; 
                            padding: 2px 8px; 
                            border-radius: 12px; 
                            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                        ">
                            {{ $readThisYear ?? 0 }} / {{ $readingGoal->target_books }}
                        </span>
                    @endif
                </div>

                @if(!empty($readingGoal))
                    <div style="width: 100%; height: 10px; background: rgba(0,0,0,0.12); border-radius: 6px; overflow: hidden; margin-top: 2px;">
                        <div style="height: 100%; width: {{ $goalProgress ?? 0 }}%; background: #255719; border-radius: 6px; transition: width 0.4s ease;"></div>
                    </div>
                    <div style="text-align: right; font-size: 12px; color: #1a3c11; font-weight: bold;">
                        %{{ $goalProgress ?? 0 }} {{ __('completed') }}
                    </div>
                @elseif($isOwner ?? ($isOwnProfile ?? false))
                    <form onsubmit="saveReadingGoal(event)" style="display: flex; gap: 6px; margin-top: 4px;">
                        @csrf
                        <input 
                            type="number" 
                            id="reading_goal_input" 
                            min="1" 
                            max="500" 
                            placeholder="{{ __('Set goal...') }}" 
                            required
                            style="
                                width: 100%; 
                                font-size: 14px; 
                                padding: 5px 8px; 
                                border-radius: 6px; 
                                border: 1.5px solid #2d5a27; 
                                background: rgba(255,255,255,0.85); 
                                outline: none; 
                                font-family: 'Unkempt', cursive;
                            "
                        >
                        <button 
                            type="submit" 
                            style="
                                background: #2d5a27; 
                                color: white; 
                                border: none; 
                                padding: 5px 12px; 
                                border-radius: 6px; 
                                font-size: 14px; 
                                font-weight: bold; 
                                cursor: pointer; 
                                font-family: 'Unkempt', cursive;
                            "
                        >
                            {{ __('Save') }}
                        </button>
                    </form>
                    <span style="font-size: 11.5px; color: #355e28; font-style: italic;">
                        *{{ __('Can only be set once a year') }}
                    </span>
                @else
                    <span style="font-size: 13px; color: #355e28; font-style: italic; text-align: center; margin-top: 2px;">
                        {{ __('No goal set for this year.') }}
                    </span>
                @endif
            </div>
        </aside>

        {{-- SAĞ İÇERİK ALANI --}}
        <main class="profile-main-content">

            {{-- 1. HEPSİ TEK SATIR OLAN MASTER TOOLBAR --}}
            <div class="master-toolbar-row">
                
                {{-- Sol: Pastel 4 Buton (Panoda gizlenir) --}}
                <div class="toolbar-pastel-group hide-on-board">
                    <button type="button" onclick="filterStatus('all', this)" class="status-tab" data-type="all" style="border: 1.5px solid #9ccb86; background: #b8dfa4; color: #27521e; box-shadow: 0 2px 6px rgba(39, 82, 30, 0.15);">
                        {{ __('All') }} ({{ count($userBooks ?? []) }})
                    </button>
                    <button type="button" onclick="filterStatus('read', this)" class="status-tab" data-type="read" style="border: 1.5px solid #f7b1c0; background: #fee2e8; color: #8e2b42;">
                        {{ __('read') }} ({{ isset($userBooks) ? $userBooks->where('status', 'read')->count() : 0 }})
                    </button>
                    <button type="button" onclick="filterStatus('reading', this)" class="status-tab" data-type="reading" style="border: 1.5px solid #a8d3f5; background: #e2f0fc; color: #1e5579;">
                        {{ __('currently reading') }} ({{ isset($userBooks) ? $userBooks->where('status', 'reading')->count() : 0 }})
                    </button>
                    <button type="button" onclick="filterStatus('toRead', this)" class="status-tab" data-type="toRead" style="border: 1.5px solid #fae087; background: #fef5d1; color: #7a5a0c;">
                        {{ __('to read') }} ({{ isset($userBooks) ? $userBooks->whereIn('status', ['toRead', 'want_to_read'])->count() : 0 }})
                    </button>
                </div>

                {{-- Orta: Arama Çubuğu (Panoda gizlenir) --}}
                <div class="toolbar-search-box hide-on-board">
                    <input type="text" id="profileBookSearchInput" oninput="applyProfileSearchFilter()" placeholder="🔍 {{ __('Search books or authors...') }}" autocomplete="off">
                </div>

                {{-- Sağ: Kitap Listesi / Pano Anahtarı (HER ZAMAN GÖRÜNÜR) --}}
                <div class="toolbar-switch-box">
                    <button type="button" id="btn-list-view" onclick="switchProfileView('list')" class="view-toggle-btn active">
                        {{ __('Book List') }}
                    </button>
                    <button type="button" id="btn-board-view" onclick="switchProfileView('board')" class="view-toggle-btn">
                        {{ __('Board') }}
                    </button>
                </div>

            </div>

            {{-- 2. KİTAP LİSTESİ GÖRÜNÜMÜ --}}
            <div id="profile-list-view" style="display: flex; flex-direction: column; flex: 1 1 0%; height: 100%; min-height: 0; overflow: hidden;">
                @include('profile.list-view')
            </div>

            {{-- 3. PANO (BOARD) GÖRÜNÜMÜ --}}
            <div id="profile-board-view">
                @include('profile.shelf-view')
            </div>

        </main>

    </div>

    {{-- MOBİL ÇEKMECE POST-IT & OVERLAY --}}
    <div class="mobile-profile-tab" id="openProfileDrawerBtn">
        <span>{{ __('Profile') }}</span>
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
        openDrawerBtn.addEventListener('click', () => { drawer.classList.add('drawer-open'); overlay.classList.remove('hidden'); });
        overlay.addEventListener('click', () => { drawer.classList.remove('drawer-open'); overlay.classList.add('hidden'); });
        if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', () => { drawer.classList.remove('drawer-open'); overlay.classList.add('hidden'); });
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
                    font-size: 20px; 
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
                        : asset('images/profile.png');
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
                                width: 36px; 
                                height: 36px; 
                                border-radius: 50%; 
                                background: #badfa0; 
                                border: 1px solid #4c7237; 
                                display: flex; 
                                align-items: center; 
                                justify-content: center; 
                                font-size: 16px; 
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
                                 onerror="this.onerror=null; this.src='{{ asset('images/profile.png') }}';">
                        </div>

                        <div>
                            <div 
                                style="
                                    font-family: 'Unkempt', cursive; 
                                    font-size: 16px; 
                                    font-weight: bold; 
                                    color: #1f5117;
                                "
                            >
                                {{ $friend->username ?? $friend->name }}
                            </div>

                            <div 
                                style="
                                    font-family: 'Unkempt', cursive; 
                                    font-size: 13.5px; 
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
                            font-size: 14px; 
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
                        font-size: 15px;
                    "
                >
                    {{ __('You dont have any friends yet :((  🌱') }}
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function saveReadingGoal(e) {
        e.preventDefault();
        const input = document.getElementById('reading_goal_input');
        if (!input || !input.value) return;

        fetch("{{ route('profile.goal.set') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ target_books: input.value })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                location.reload();
            } else {
                alert(res.error || 'Bir hata oluştu.');
            }
        })
        .catch(() => alert('Hedef kaydedilemedi.'));
    }

    window.addEventListener('click', function(e) {
        const modal = document.getElementById('friendsModal');
        if (e.target === modal) {
            closeFriendsModal();
        }
    });
</script>

@include('partials.chat')
@include('partials.title-modal')
</body>
</html>