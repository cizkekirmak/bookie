<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bookie - {{ __('Dashboard') }}</title>

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
        }

        /* HEADER: Masaüstü */
        .site-header-outer {
            width: 100%;
            height: 76px;
            background-color: #477c35;
            background-image: 
                url('{{ asset('images/header.png') }}'),
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
            z-index: 10000;
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
            z-index: 10001;
            text-decoration: none;
        }

        .header-search-wrap {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 500px;
            z-index: 10005;
            pointer-events: auto !important;
        }

        .header-search-bar-box {
            display: flex;
            align-items: center;
            gap: 12px;
            height: 50px;
            background-color: #fafcf7;
            background-image: url('{{ asset('images/arama.jpg') }}');
            background-repeat: repeat;
            border: 2px solid #6b9c56;
            border-radius: 28px;
            padding: 0 18px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.06);
            width: 100%;
            cursor: text;
        }

        #bookSearchInput {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 22px;
            font-family: 'Unkempt', cursive;
            color: #1b3711;
            width: 100%;
            cursor: text;
            pointer-events: auto !important;
        }

        .header-desktop-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-shrink: 0;
            z-index: 10001;
        }

        .mobile-menu-trigger,
        .mobile-dropdown-menu,
        .mobile-friends-tab,
        .mobile-drawer-overlay {
            display: none !important;
        }

        /* ANA GÖVDE */
        .app-container {
            width: 100%;
            max-width: 1520px;
            margin: 0 auto;
            min-height: calc(100vh - 76px);
            display: flex;
            align-items: flex-start;
            padding: 24px 20px 24px 20px;
            gap: 0px;
        }

        .left-content-area {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 32px;
            padding: 10px 0px 0px 0px;
        }

        .dashboard-row-equal {
            display: flex;
            align-items: flex-start;
            width: 100%;
        }

        .equal-spacer {
            flex: 1;
        }

        .right-sidebar-panel {
            width: 380px;
            height: calc(100vh - 124px);
            max-height: calc(100vh - 124px);
            background: #c6e085;
            border: 2px solid #6b9c56;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(45, 90, 39, 0.12);
            padding: 18px 16px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            flex-shrink: 0;
            position: sticky;
            top: 100px;
        }

        .friend-reviews-scroll::-webkit-scrollbar { width: 8px; }
        .friend-reviews-scroll::-webkit-scrollbar-track { background: transparent; }
        .friend-reviews-scroll::-webkit-scrollbar-thumb { background-color: #82b564; border-radius: 10px; border: 2px solid #77995f; }
        .friend-reviews-scroll::-webkit-scrollbar-thumb:hover { background-color: #5d8e40; }
        .friend-reviews-scroll { scrollbar-width: thin; scrollbar-color: #82b564 transparent; }

        .mob-grid-item-popular img,
        .popular-book-card img {
            max-height: 125px !important;
            object-fit: cover !important;
        }

        /* MOBİL UYARLAMA */
        @media (max-width: 768px) {
            body {
                overflow-x: hidden !important;
                width: 100vw !important;
            }

            .site-header-outer {
                width: 100% !important;
                left: 0 !important;
                right: 0 !important;
                height: 68px !important;
                background-image: 
                    url('{{ asset('images/profil-header.png') }}'),
                    url('{{ asset('images/bosluk.png') }}') !important;
            }

            .site-header-inner {
                width: 100% !important;
                padding: 0 8px !important;
                gap: 6px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                position: relative !important;
            }

            .header-logo {
                font-size: 26px !important;
                margin-top: 0 !important;
                flex-shrink: 0 !important;
            }

            .header-desktop-actions {
                display: none !important;
            }

            .header-search-wrap {
                position: relative !important;
                left: auto !important;
                transform: none !important;
                flex: 1 !important;
                max-width: none !important;
                margin: 0 4px !important;
                display: block !important;
                z-index: 10005 !important;
            }

            .header-search-bar-box {
                height: 42px !important;
                padding: 0 10px !important;
                gap: 6px !important;
                border-radius: 24px !important;
            }

            .header-search-bar-box img {
                width: 22px !important;
                height: 22px !important;
            }

            #bookSearchInput {
                font-size: 14px !important;
                display: block !important;
                width: 100% !important;
            }

            #searchResultsDropdown {
                position: fixed !important;
                top: 70px !important;
                left: 10px !important;
                right: 10px !important;
                width: calc(100vw - 20px) !important;
                max-height: 60vh !important;
                z-index: 1000005 !important;
            }

            .mobile-menu-trigger {
                display: flex !important;
                align-items: center;
                justify-content: center;
                width: 42px !important;
                height: 42px !important;
                background: #f4fbf0;
                border: 2px solid #4b813b;
                border-radius: 10px !important;
                cursor: pointer;
                flex-shrink: 0;
            }

            .mobile-menu-trigger img {
                width: 34px !important;
                height: 34px !important;
                border-radius: 6px !important;
            }

            .mobile-dropdown-menu {
                display: flex !important;
                position: absolute;
                top: 70px !important;
                right: 8px !important;
                width: 68px !important;
                background: #c6e085;
                border: 2px solid #4b813b;
                border-radius: 14px !important;
                padding: 8px !important;
                flex-direction: column;
                gap: 8px !important;
                box-shadow: 0 8px 24px rgba(0,0,0,0.3);
                z-index: 100000;
                align-items: center;
            }

            .mobile-dropdown-menu.hidden {
                display: none !important;
            }

            .mobile-icon-box {
                width: 48px !important;
                height: 48px !important;
                background: #f4fbf0;
                border: 1.5px solid #6b9c56;
                border-radius: 10px !important;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                text-decoration: none;
                flex-shrink: 0;
            }

            .mobile-icon-box img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .equal-spacer {
                display: none !important;
            }

            .app-container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 12px 10px 85px 10px !important;
                margin: 0 !important;
                min-height: calc(100vh - 68px) !important;
                min-height: calc(100dvh - 68px) !important;
                display: block !important;
            }

            .left-content-area {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 10px !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 auto !important;
                padding: 0 !important;
            }

            .dashboard-row-equal {
                display: contents !important;
            }

            .item-continue {
                grid-column: 1 !important;
                grid-row: 1 !important;
                width: 100% !important;
                min-width: 0 !important;
            }

            .item-admin {
                grid-column: 2 !important;
                grid-row: 1 !important;
                width: 100% !important;
                min-width: 0 !important;
            }

            .item-popular {
                grid-column: 1 / span 2 !important;
                grid-row: 2 !important;
                width: 100% !important;
                min-width: 0 !important;
            }

            .item-cat {
                grid-column: 1 / span 2 !important;
                grid-row: 3 !important;
                width: 100% !important;
                min-width: 0 !important;
            }

            .right-sidebar-panel {
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                right: 0 !important;
                width: 86vw !important;
                max-width: 340px !important;
                height: 100vh !important;
                height: 100dvh !important;
                max-height: 100% !important;
                border-radius: 16px 0 0 16px !important;
                z-index: 100001 !important;
                transform: translateX(100%);
                transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: -6px 0 24px rgba(0,0,0,0.3) !important;
            }

            .right-sidebar-panel.drawer-open {
                transform: translateX(0) !important;
            }

            .mobile-drawer-overlay {
                display: block !important;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.45);
                z-index: 100000;
                backdrop-filter: blur(2px);
            }

            .mobile-drawer-overlay.hidden {
                display: none !important;
            }

            .mobile-friends-tab {
                display: flex !important;
                align-items: center;
                gap: 6px;
                position: fixed !important;
                bottom: 18px !important;
                right: 0 !important;
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

            #chat-draggable-btn,
            .chat-bubble-btn,
            .chat-toggle-btn {
                position: fixed !important;
                left: 14px !important;
                bottom: 18px !important;
                top: auto !important;
                right: auto !important;
                z-index: 9999 !important;
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

            <div class="header-search-wrap">
                <div class="header-search-bar-box" id="searchBarBox">
                    <img src="{{ asset('images/yıldız.png') }}" id="searchStarBtn" alt="{{ __('Search') }}" style="width: 30px; height: 30px; object-fit: contain; flex-shrink: 0; cursor: pointer;">
                    <input type="text" id="bookSearchInput" placeholder="{{ __('what are you looking for?') }}" autocomplete="off" enterkeyhint="search">
                </div>
                <div id="searchResultsDropdown" style="display: none; position: absolute; top: 54px; left: 0; width: 100%; max-height: 320px; overflow-y: auto; background: #ffffff; border: 1.5px solid #2d5a27; border-radius: 12px; box-shadow: 0 8px 16px rgba(0,0,0,0.25); z-index: 100010;"></div>
            </div>
            
            <div class="header-desktop-actions">
                {{-- DİL SEÇİCİ PARTIAL --}}
                @include('partials.lang-switch')

                <div style="display: flex; align-items: center; justify-content: center; line-height: 0;">
                    @include('partials.notifications')
                </div>

                <a href="{{ route('ayarlar') }}" style="display: inline-block; line-height: 0; text-decoration: none; flex-shrink: 0; transition: transform 0.2s ease;">
                    <img src="{{ asset('images/ayarlar.jpg') }}" alt="{{ __('Settings') }}" style="width: 52px; height: 52px; object-fit: contain; border: 1.5px solid #4b813b; display: block; cursor: pointer;" onmouseenter="this.style.transform='scale(1.1)';" onmouseleave="this.style.transform='scale(1)';">
                </a>
                
                <a href="{{ route('profile') }}" style="display: inline-block; line-height: 0; text-decoration: none; flex-shrink: 0; transition: transform 0.2s ease;">
                    <img src="{{ asset('images/profile.jpg') }}" alt="{{ __('Profile') }}" style="width: 52px; height: 52px; object-fit: contain; border: 1.5px solid #4b813b; display: block; cursor: pointer;" onmouseenter="this.style.transform='scale(1.1)';" onmouseleave="this.style.transform='scale(1)';">
                </a>
            </div>

            <div class="mobile-menu-trigger" id="mobileMenuBtn">
                <img src="{{ asset('images/profile.jpg') }}" alt="{{ __('Profile') }}">
            </div>

            <div class="mobile-dropdown-menu hidden" id="mobileDropdownMenu">
                {{-- Mobil Dil Seçici --}}
                <div style="width: 100%; display: flex; justify-content: center; padding: 2px 0;">
                    @include('partials.lang-switch')
                </div>
                <div class="mobile-icon-box">
                    @include('partials.notifications')
                </div>
                <a href="{{ route('ayarlar') }}" class="mobile-icon-box">
                    <img src="{{ asset('images/ayarlar.jpg') }}" alt="{{ __('Settings') }}">
                </a>
                <a href="{{ route('profile') }}" class="mobile-icon-box">
                    <img src="{{ asset('images/profile.jpg') }}" alt="{{ __('Profile') }}">
                </a>
            </div>

        </div>
    </header>

    @if(session('success'))
        <div id="bildiri-message" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; background-color: #d4edda; color: #155724; border: 1.5px solid #c3e6cb; padding: 10px 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-family: 'Unkempt', cursive;">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => {
                const bildiri = document.getElementById("bildiri-message");
                if (bildiri) {
                    bildiri.style.opacity = "0";
                    setTimeout(() => bildiri.remove(), 500);
                }
            }, 2500);
        </script>
    @endif

    {{-- KAPSAYICI --}}
    <div class="app-container">
        
        {{-- SOL PANEL --}}
        <div class="left-content-area">
            
            {{-- 1. ÜST SATIR --}}
            <div class="dashboard-row-equal">
                <div class="item-continue" style="flex-shrink: 0;">
                    @include('partials.continue-reading')
                </div>
                
                <div class="equal-spacer"></div>

                <div class="item-popular" style="flex-shrink: 0;">
                    @include('partials.popular-books')
                </div>

                <div class="equal-spacer"></div>
            </div>

            {{-- 2. ALT SATIR --}}
            <div class="dashboard-row-equal">
                <div class="item-admin" style="flex-shrink: 0;">
                    @include('partials.adminRecommendation')
                </div>
                
                <div class="equal-spacer"></div>

                <div class="item-cat" style="flex-shrink: 0;">
                    @include('partials.cat-recommendation')
                </div>

                <div class="equal-spacer"></div>
            </div>

        </div>

        {{-- SAĞ PANEL --}}
        <div class="right-sidebar-panel" id="mobileDrawer">
            
            <div style="position: relative; width: 100%;">
                <div style="background: #f4fbf0; border: 1.5px solid #515f30; border-radius: 22px; padding: 8px 16px; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 16px;">👤</span>
                    <input type="text" id="userSearchInput" placeholder="{{ __('find other users') }}" autocomplete="off" style="border: none; background: transparent; outline: none; font-family: 'Unkempt', cursive; font-size: 16px; color: #1b3711; width: 100%;">
                </div>
                <div id="userSearchResults" style="display: none; position: absolute; top: 45px; left: 0; width: 100%; background: #ffffff; border: 1.5px solid #4c7237; border-radius: 12px; box-shadow: 0 8px 16px rgba(0,0,0,0.2); max-height: 220px; overflow-y: auto; z-index: 999999;"></div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-family: 'Henny Penny', cursive; font-size: 18px; color: #1a3c11;">
                    {{ __('Friend reviews') }}
                </span>
                <button type="button" id="closeDrawerBtn" style="display: none; background: none; border: none; font-size: 20px; cursor: pointer; color: #1a3c11;">✕</button>
            </div>

            <div class="friend-reviews-scroll" style="display: flex; flex-direction: column; padding-right: 4px; gap: 12px; overflow-y: auto; flex: 1;">
                @php
                    $friendIds = auth()->check() ? auth()->user()->friends()->pluck('id') : collect([]);
                    $friendReviews = \App\Models\UserBook::whereIn('user_id', $friendIds)
                        ->whereNotNull('review')
                        ->with(['user', 'book', 'likes'])
                        ->latest()
                        ->take(10)
                        ->get();

                    $ratingColors = [
                        1 => '#d9534f', 2 => '#f0ad4e', 3 => '#ffd700', 4 => '#5cb85c', 5 => '#2e7d32',
                    ];
                @endphp

                @forelse($friendReviews as $item)
                    @php 
                        $itemRatingColor = $ratingColors[$item->rating] ?? '#4a5d44';
                        $bookKey = $item->book->open_library_key ?? $item->book->google_book_id ?? $item->book->key ?? $item->book_id;
                        $bookTitle = $item->book->title ?? __('Book Details');
                    @endphp

                    <div id="review-{{ $item->id }}" style="background: #f1f8ed; border: 1.5px solid #4c7237; border-radius: 10px; padding: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            @if($item->user)
                            <a href="{{ route('profile', $item->user->id) }}" style="color: #1f5117; text-decoration: none; font-weight: bold; font-family: 'Unkempt', cursive;">
                                {{ $item->user->username ?? ($item->user->name ?? __('Anonymous')) }}
                            </a>
                            @else
                            <strong style="font-weight: bold; color: #1f5117; font-family: 'Unkempt', cursive;">{{ __('Anonymous') }}</strong>
                            @endif

                            <span style="color: {{ $itemRatingColor }}; font-size: 14px;">
                                {{ $item->rating > 0 ? str_repeat('★', $item->rating) : __('no rating') }}
                            </span> 
                        </div>

                        <div style="margin-bottom: 6px;">
                            <span style="font-size: 12px; color: #666; font-family: 'Unkempt', cursive;">{{ __('book:') }} </span>
                            <a href="{{ route('show', $bookKey) }}#review-{{ $item->id }}" style="font-size: 13px; font-weight: bold; color: #1a3c11; text-decoration: underline; font-family: 'Unkempt', cursive;">
                                {{ $bookTitle }}
                            </a>
                        </div>

                        @if(!empty($item->review))
                            <p style="color: #4a5d44; font-size: 13px; line-height: 1.4; margin: 0 0 8px 0; font-family: 'Unkempt', cursive;">
                                {{ $item->review }}
                            </p>
                        @endif

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px; padding-top: 6px; border-top: 1px dashed #d7e8cf;">
                            @include('partials.review-like-btn', ['review' => $item])

                            <span style="font-size: 11px; color: #777; font-family: 'Unkempt', cursive;">
                               {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->timezone('Europe/Istanbul')->diffForHumans() : '' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p style="color: #4a5d44; font-size: 13px; margin: 0; font-family: 'Unkempt', cursive;">
                        {{ __('there are no reviews yet, sorry!') }}
                    </p>
                @endforelse
            </div>

        </div>
    </div>

    {{-- MOBİL POST-IT BUTONU & OVERLAY --}}
    <div class="mobile-friends-tab" id="openFriendsDrawerBtn">
        <span>📌 {{ __('Friends') }}</span>
    </div>
    <div class="mobile-drawer-overlay hidden" id="drawerOverlay"></div>

    {{-- JS Fonksiyonları --}}
    <script>
    function toggleReviewLike(reviewId, buttonElement) {
        const csrfToken = document.querySelector("meta[name='csrf-token']")?.getAttribute('content') || '{{ csrf_token() }}';
        const emptyHeartSrc = "{{ asset('images/boskalp.png') }}";
        const fullHeartSrc = "{{ asset('images/dolukalp.png') }}";

        fetch(`/reviews/${reviewId}/toggle-like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const buttons = document.querySelectorAll(`button[data-review-id="${reviewId}"]`);
                buttons.forEach(btn => {
                    const heartImg = btn.querySelector('.like-heart-img');
                    const count = btn.parentElement.querySelector('.like-count-display');
                    if (heartImg) heartImg.src = data.liked ? fullHeartSrc : emptyHeartSrc;
                    if (count) count.innerText = data.likes_count;
                });
            }
        })
        .catch(err => console.error('Beğeni hatası:', err));
    }

    document.addEventListener('DOMContentLoaded', function () {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileDropdown = document.getElementById('mobileDropdownMenu');
        const openFriendsBtn = document.getElementById('openFriendsDrawerBtn');
        const drawer = document.getElementById('mobileDrawer');
        const drawerOverlay = document.getElementById('drawerOverlay');
        const closeDrawerBtn = document.getElementById('closeDrawerBtn');

        if (mobileMenuBtn && mobileDropdown) {
            mobileMenuBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                mobileDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function (e) {
                if (!mobileDropdown.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                    mobileDropdown.classList.add('hidden');
                }
            });
        }

        if (openFriendsBtn && drawer && drawerOverlay) {
            function openDrawer() {
                drawer.classList.add('drawer-open');
                drawerOverlay.classList.remove('hidden');
                if (closeDrawerBtn) closeDrawerBtn.style.display = 'block';
            }

            function closeDrawer() {
                drawer.classList.remove('drawer-open');
                drawerOverlay.classList.add('hidden');
                if (closeDrawerBtn) closeDrawerBtn.style.display = 'none';
            }

            openFriendsBtn.addEventListener('click', openDrawer);
            drawerOverlay.addEventListener('click', closeDrawer);
            if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);
        }

        // KİTAP ARAMA SİSTEMİ
        const input = document.getElementById('bookSearchInput');
        const searchStarBtn = document.getElementById('searchStarBtn');
        const dropdown = document.getElementById('searchResultsDropdown');

        if (input && dropdown) {
            let allSearchResults = [];
            let displayedCount = 0;
            const PAGE_SIZE = 6;
            let searchDebounceTimer;

            const textSearching = @json(__('Searching...^^'));
            const textNotFound = @json(__('No results found.'));
            const textSearchError = @json(__('An error occurred during search.'));
            const textLoadMore = @json(__('load more'));
            const textUnknownAuthor = @json(__('Unknown Author'));

            function renderNextBooks() {
                const oldBtn = document.getElementById('searchLoadMoreContainer');
                if (oldBtn) oldBtn.remove();

                const nextBatch = allSearchResults.slice(displayedCount, displayedCount + PAGE_SIZE);
                const html = nextBatch.map(book => `
                    <div onclick="window.location.href='/books/${book.id}'" style="display: flex; align-items: center; gap: 12px; padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #e2ebd8; transition: background-color 0.15s;" onmouseenter="this.style.backgroundColor='#f4f8e8'" onmouseleave="this.style.backgroundColor='transparent'">
                        <img src="${book.cover || '{{ asset('images/default-book.png') }}'}" loading="lazy" referrerpolicy="no-referrer" style="width: 38px; height: 52px; object-fit: cover; border-radius: 4px; flex-shrink: 0; background-color: #e8f0dc;" onerror="this.onerror=null; this.src='{{ asset('images/default-book.png') }}';">
                        <div style="overflow: hidden; text-align: left;">
                            <div style="font-family: 'Unkempt', cursive; font-size: 15px; font-weight: bold; color: #1f5117; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${book.title}</div>
                            <div style="font-family: 'Unkempt', cursive; font-size: 12px; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${book.authors || textUnknownAuthor}</div>
                        </div>
                    </div>
                `).join('');

                dropdown.insertAdjacentHTML('beforeend', html);
                displayedCount += nextBatch.length;

                if (displayedCount < allSearchResults.length) {
                    const remaining = allSearchResults.length - displayedCount;
                    const loadMoreHtml = `
                        <div id="searchLoadMoreContainer" style="padding: 8px 12px; text-align: center; background: #fafdf7;">
                            <button type="button" id="searchLoadMoreBtn" style="background: #eef6ea; border: 1.5px solid #4c7237; color: #1f5117; padding: 5px 16px; border-radius: 16px; font-family: 'Unkempt', cursive; font-size: 13px; cursor: pointer;">${textLoadMore} (+${Math.min(PAGE_SIZE, remaining)})</button>
                        </div>
                    `;
                    dropdown.insertAdjacentHTML('beforeend', loadMoreHtml);
                    document.getElementById('searchLoadMoreBtn').addEventListener('click', function (e) {
                        e.stopPropagation();
                        renderNextBooks();
                    });
                }
            }

            async function performBookSearch() {
                const q = input.value.trim();
                if (q.length < 2) {
                    dropdown.style.display = 'none';
                    dropdown.innerHTML = '';
                    return;
                }

                dropdown.innerHTML = `<div style="padding: 12px; font-family: 'Unkempt', cursive; color: #666; text-align: center;">${textSearching}</div>`;
                dropdown.style.display = 'block';

                try {
                    const res = await fetch(`/api/search-books?q=${encodeURIComponent(q)}`);
                    const data = await res.json();
                    const books = Array.isArray(data) ? data : (data.items || []);

                    if (!books || books.length === 0) {
                        dropdown.innerHTML = `<div style="padding: 12px; font-family: 'Unkempt', cursive; color: #666; text-align: center;">${textNotFound}</div>`;
                        return;
                    }
                    dropdown.innerHTML = '';
                    allSearchResults = books;
                    displayedCount = 0;
                    renderNextBooks();
                } catch (err) {
                    dropdown.innerHTML = `<div style="padding: 12px; font-family: 'Unkempt', cursive; color: red; text-align: center;">${textSearchError}</div>`;
                }
            }

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchDebounceTimer);
                    performBookSearch();
                    input.blur();
                }
            });

            input.addEventListener('input', function () {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(performBookSearch, 450);
            });

            if (searchStarBtn) {
                searchStarBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    performBookSearch();
                });
            }

            document.addEventListener('click', function (e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
        }

        // KULLANICI ARAMA
        const userSearchInput = document.getElementById('userSearchInput');
        const userSearchResults = document.getElementById('userSearchResults');

        if (userSearchInput && userSearchResults) {
            let debounceTimer;
            const defaultAvatar = "{{ asset('images/profile.jpg') }}";

            const textUserNotFound = @json(__('no user was found, are u sure u spelt that correctly?'));
            const textUserSearchError = @json(__('An error occurred.'));
            const textFriendsStatus = @json(__('✓ friends'));
            const textPendingStatus = @json(__('pending'));
            const textRequestedStatus = @json(__('requested'));

            userSearchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                const query = this.value.trim();

                if (query.length < 2) {
                    userSearchResults.style.display = 'none';
                    userSearchResults.innerHTML = '';
                    return;
                }

                debounceTimer = setTimeout(async () => {
                    try {
                        const res = await fetch(`/api/search-users?q=${encodeURIComponent(query)}`);
                        const users = await res.json();

                        if (users.length === 0) {
                            userSearchResults.innerHTML = `<div style="padding: 10px; font-size: 13px; color: #777; text-align: center; font-family: 'Unkempt', cursive;">${textUserNotFound}</div>`;
                            userSearchResults.style.display = 'block';
                            return;
                        }

                        const csrfToken = '{{ csrf_token() }}';
                        userSearchResults.innerHTML = users.map(user => {
                            let actionHtml = '';
                            if (user.status === 'accepted') {
                                actionHtml = `<span style="font-size: 12px; color: #1a3c11; font-weight: bold; font-family: 'Unkempt', cursive;">${textFriendsStatus}</span>`;
                            } else if (user.status === 'pending') {
                                actionHtml = user.is_sender 
                                    ? `<span style="font-size: 12px; color: #666; font-family: 'Unkempt', cursive;">${textPendingStatus}</span>`
                                    : `<span style="font-size: 12px; color: #c62828; font-family: 'Unkempt', cursive;">${textRequestedStatus}</span>`;
                            } else {
                                actionHtml = `
                                    <form action="/friends/${user.id}/request" method="POST" style="margin: 0;" onclick="event.stopPropagation();">
                                        <input type="hidden" name="_token" value="${csrfToken}">
                                        <button type="submit" style="background: #2d5a27; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 15px; font-family: 'Unkempt', cursive;" title="{{ __('Add Friend') }}">+</button>
                                    </form>
                                `;
                            }

                            let userAvatarSrc = defaultAvatar;
                            if (user.avatar && user.avatar.startsWith('http')) {
                                userAvatarSrc = user.avatar;
                            }

                            return `
                                <div onclick="window.location.href='/profile/${user.id}'" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eef4e8; transition: background 0.15s ease;" onmouseenter="this.style.background='#f1f8ed'" onmouseleave="this.style.background='transparent'">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 32px; height: 32px; border-radius: 50%; border: 1.5px solid #4c7237; overflow: hidden; flex-shrink: 0; background: #badfa0; display: flex; align-items: center; justify-content: center;">
                                            <img src="${userAvatarSrc}" alt="${user.username}" referrerpolicy="no-referrer" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block;" onerror="this.onerror=null; this.src='${defaultAvatar}';">
                                        </div>
                                        <div style="font-size: 14px; font-weight: bold; color: #1a3c11; font-family: 'Unkempt', cursive;">
                                            @${user.username}
                                        </div>
                                    </div>
                                    <div>${actionHtml}</div>
                                </div>
                            `;
                        }).join('');

                        userSearchResults.style.display = 'block';
                    } catch (err) {
                        userSearchResults.innerHTML = `<div style="padding: 10px; font-size: 13px; color: red; text-align: center; font-family: 'Unkempt', cursive;">${textUserSearchError}</div>`;
                    }
                }, 300);
            });

            document.addEventListener('click', function (e) {
                if (!userSearchInput.contains(e.target) && !userSearchResults.contains(e.target)) {
                    userSearchResults.style.display = 'none';
                }
            });
        }
    });
    </script>
    @include('partials.chat')
</body>
</html>