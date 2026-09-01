<!DOCTYPE html>
<html lang="tr">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Book Details' }} - Bookie</title>
    
    <!-- Fontlar -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Henny+Penny&family=Unkempt:wght@400;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
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
            display: inline-block;
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

        /* ORTA GÖVDE VE İÇERİK */
        .show-main-area {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px 85px 16px;
            width: 100%;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: rgba(254, 255, 252, 0.95);
            border: 2px solid #2d5a27;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2d5a27;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .book-main-flex {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .book-cover-wrap {
            flex-shrink: 0;
            width: 190px;
        }

        .book-cover-img {
            width: 100%;
            height: auto;
            max-height: 280px;
            object-fit: contain;
            border-radius: 8px;
            border: 1.5px solid #2d5a27;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            display: block;
            background: #fff;
        }

        .book-info-col {
            flex: 1;
            min-width: 280px;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f1f8ed;
            border: 1.5px solid #4c7237;
            border-radius: 6px;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 15px;
            color: #1f5117;
        }

        .radio-label input {
            cursor: pointer;
        }

        .page-box {
            background: #f1f8ed;
            border: 1.5px dashed #4c7237;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .page-input {
            width: 80px;
            padding: 5px 8px;
            border-radius: 6px;
            border: 1.5px solid #2d5a27;
            font-family: 'Unkempt', cursive;
            font-size: 15px;
            text-align: center;
            outline: none;
            color: #1f5117;
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

            .header-actions-wrap {
                gap: 8px !important;
            }

            .header-icon-box,
            .header-actions-wrap img {
                width: 44px !important;
                height: 44px !important;
                max-width: 44px !important;
                max-height: 44px !important;
                border: 2px solid #4b813b !important;
                border-radius: 10px !important;
                display: block !important;
            }

            .show-main-area {
                padding: 16px 10px 90px 10px !important;
                min-height: calc(100vh - 68px) !important;
                min-height: calc(100dvh - 68px) !important;
            }

            .container {
                width: 100% !important;
                max-width: 100% !important;
                margin: auto 0 !important;
                padding: 14px 12px !important;
                border-radius: 12px !important;
            }

            .back-link {
                margin-bottom: 10px;
                font-size: 13px;
            }

            .book-main-flex {
                gap: 10px;
                flex-wrap: nowrap !important;
            }

            .book-cover-wrap {
                width: 85px !important;
            }

            .book-cover-img {
                max-height: 130px !important;
                border-radius: 6px;
            }

            .book-info-col {
                min-width: 0 !important;
                flex: 1 !important;
            }

            .book-title-heading {
                font-size: 16px !important;
                line-height: 1.2;
            }

            .book-author-text {
                font-size: 12px !important;
                margin-bottom: 4px !important;
            }

            .book-desc-text {
                font-size: 12px !important;
                max-height: 80px !important;
                margin-bottom: 12px !important;
                padding-bottom: 8px !important;
            }

            .radio-group-wrap {
                gap: 6px !important;
                margin-bottom: 8px !important;
            }

            .radio-label {
                padding: 4px 8px !important;
                font-size: 11px !important;
                border-radius: 5px !important;
            }

            .page-box {
                padding: 6px 8px !important;
                margin-bottom: 10px !important;
                gap: 6px !important;
            }

            .page-input {
                width: 55px !important;
                padding: 3px 5px !important;
                font-size: 12px !important;
            }

            .star-btn {
                font-size: 24px !important;
            }

            .review-submit-btn {
                width: 100% !important;
                padding: 8px 14px !important;
                font-size: 14px !important;
            }

            #chat-draggable-btn,
            .chat-bubble-btn,
            .chat-toggle-btn {
                position: fixed !important;
                left: 14px !important;
                bottom: 16px !important;
                top: auto !important;
                right: auto !important;
                z-index: 99999 !important;
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

            {{-- SAĞ İKONLAR --}}
            <div class="header-actions-wrap">
                <div style="display: flex; align-items: center; justify-content: center; line-height: 0;">
                    @include('partials.notifications')
                </div>

                <a href="{{ route('profile') }}" style="display: inline-block; line-height: 0; text-decoration: none; flex-shrink: 0;">
                    <img src="{{ asset('images/profile.jpg') }}" alt="profile" class="header-icon-box">
                </a>

                <a href="{{ route('ayarlar') }}" style="display: inline-block; line-height: 0; text-decoration: none; flex-shrink: 0;">
                    <img src="{{ asset('images/ayarlar.jpg') }}" alt="ayarlar" class="header-icon-box">
                </a>

                <a href="{{ route('dashboard') }}" style="display: inline-block; line-height: 0; text-decoration: none; flex-shrink: 0;">
                    <img src="{{ asset('images/dash.jpg') }}" alt="dashboard" class="header-icon-box">
                </a>
            </div>

        </div>
    </header>

    {{-- ANA İÇERİK ALANI --}}
    <div class="show-main-area">
        <div class="container">
            <a href="{{ route('dashboard') }}" class="back-link">← wanna go back?</a>

            @if(session('success'))
                <div style="background-color: #d4edda; color: #155724; border: 1.5px solid #c3e6cb; padding: 10px 15px; border-radius: 8px; margin-bottom: 20px; font-family: 'Henny Penny', cursive; font-size: 14px;">
                    ✨ {{ session('success') }}
                </div>
            @endif

            <div class="book-main-flex">
                
                {{-- SOL: Kapak --}}
                <div class="book-cover-wrap">
                    @if($coverUrl)
                        <img src="{{ $coverUrl }}" 
                             alt="Cover" 
                             referrerpolicy="no-referrer" 
                             class="book-cover-img"
                             onerror="this.onerror=null; this.src='{{ asset('images/default-book.png') }}';">
                    @else
                        <div style="width: 100%; height: 130px; background: #eaf3e4; border: 1.5px solid #2d5a27; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 28px;">
                            📖
                        </div>
                    @endif
                </div>

                {{-- SAĞ: Detaylar ve Formlar --}}
                <div class="book-info-col">
                    
                    {{-- Başlık ve Sağdaki İndir/Oku Butonu --}}
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                        <h1 class="book-title-heading" style="font-family: 'Henny Penny', cursive; color: #1f5117; margin: 0; font-size: 26px;">
                            {{ $title ?? 'Unknown book' }}
                        </h1>

                        @if(!empty($downloadUrl) || (!empty($book) && !empty($book->download_url)))
                            <a href="{{ !empty($book) && !empty($book->download_url) ? $book->download_url : $downloadUrl }}" 
                               target="_blank" 
                               style="
                                   display: inline-flex; 
                                   align-items: center; 
                                   gap: 4px; 
                                   background-color: #d2f48a; 
                                   color: #101e08; 
                                   border: 1.5px solid #1d491b; 
                                   padding: 4px 10px; 
                                   border-radius: 6px; 
                                   text-decoration: none; 
                                   font-family: 'Unkempt', cursive; 
                                   font-size: 12px;
                                   white-space: nowrap;
                                   flex-shrink: 0;
                                   cursor: pointer;
                               ">
                                📖 <span>Kitabı Oku / İndir</span>
                            </a>
                        @endif
                    </div>

                    <h4 class="book-author-text" style="color: #4a5d44; margin: 0 0 8px 0; font-size: 15px; font-weight: normal;">
                        by {{ $authors ?? 'Unknown author' }}
                    </h4>
                    @php
                        $averageRating = $averageRating ?? 0;
                        $totalReviews = $totalReviews ?? 0;
                        $colorMap = [
                            1 => '#d43b82',
                            2 => '#e67e22',
                            3 => '#fee16c',
                            4 => '#8dd04e',
                            5 => '#3a91bc'
                        ];
                        $avgRounded = round($averageRating);
                        $starColor = $avgRounded > 0 ? $colorMap[$avgRounded] : '#ccc';
                    @endphp

                    <div style="display: flex; align-items: center; gap: 6px; margin: 4px 0 8px 0;">
                        <div style="color: {{ $starColor }}; font-size: 16px; display: flex; gap: 2px;">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($averageRating >= $i || $averageRating >= ($i - 0.5))
                                    ★ 
                                @else
                                    <span style="color: #ccc;">★</span>
                                @endif
                            @endfor
                        </div>

                        <span style="font-family: 'Unkempt', cursive; font-weight: bold; color: #1a3c11; font-size: 13px;">
                            {{ $averageRating > 0 ? number_format($averageRating, 1) : 'no ratings yet' }}
                        </span>

                        @if($totalReviews > 0)
                            <span style="color: #666; font-size: 11px; font-family: 'Unkempt', cursive;">
                                ({{ $totalReviews }} inceleme)
                            </span>
                        @endif
                    </div>

                    <p class="book-desc-text" style="color: #4a5d44; font-size: 14px; line-height: 1.4; max-height: 110px; overflow-y: auto; margin-bottom: 14px; padding-right: 5px; border-bottom: 1px solid #e0ebd9; padding-bottom: 8px;">
                        {{ Str::limit($description ?? '', 350) }}
                    </p>

                    {{-- 1. KULLANICI KİTAP FORMU --}}
                    <form action="{{ route('books.save', request()->route('key')) }}" method="POST">
                        @csrf
                        <input type="hidden" name="title" value="{{ $title ?? 'Unknown Title' }}">
                        <input type="hidden" name="cover_image" value="{{ $coverUrl }}">
                        <input type="hidden" name="author" value="{{ $authors ?? 'Unknown author' }}">
                        <input type="hidden" name="download_url" value="{{ $downloadUrl ?? ($book->download_url ?? '') }}">

                        {{-- Kitap Anahtarı (Key) Tanımlaması --}}
                        @php
                            $routeKey = (string) request()->route('key');
                            $isOl = str_starts_with($routeKey, 'OL') || str_contains($routeKey, '/works/');
                            $isGut = str_starts_with($routeKey, 'GUT_');
                            $pageCount = $pageCount ?? ($book->page_count ?? null);
                            $currentPage = $userBook->current_page ?? 0;
                        @endphp

                        {{-- Toplam Sayfa Sayısı (Hidden) --}}
                        <input type="hidden" name="page_count" value="{{ $pageCount ?? '' }}">

                        @if($isOl)
                            <input type="hidden" name="open_library_key" value="{{ $routeKey }}">
                        @elseif($isGut)
                            <input type="hidden" name="gutenberg_id" value="{{ $routeKey }}">
                        @else
                            <input type="hidden" name="google_book_id" value="{{ $routeKey }}">
                        @endif

                        {{-- Okuma Durumu --}}
                        <label style="display: block; font-weight: bold; color: #1f5117; margin-bottom: 4px; font-size: 13px;">reading progress</label>
                        <div class="radio-group-wrap" style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 8px;">
                            <label class="radio-label">
                                <input type="radio" name="status" value="reading" class="status-radio" {{ ($userBook && $userBook->status == 'reading') ? 'checked' : '' }} required>
                                currently reading 
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="status" value="read" class="status-radio" {{ ($userBook && $userBook->status == 'read') ? 'checked' : '' }}>
                                read
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="status" value="want_to_read" class="status-radio" {{ (!$userBook || $userBook->status == 'want_to_read' || $userBook->status == 'toRead') ? 'checked' : '' }}>
                                want to read
                            </label>
                        </div>

                        {{-- Sayfa İlerleme Kutusu --}}
                        <div id="page-box" class="page-box" style="display: {{ ($userBook && $userBook->status == 'reading') ? 'flex' : 'none' }};">
                            <span style="color: #1f5117; font-size: 12px; font-weight: bold;">on page:</span>
                            <input type="number" name="current_page" id="current_page" class="page-input" min="0" max="{{ $pageCount ?? 9999 }}" value="{{ $currentPage }}" placeholder="0">
                            
                            @if(!empty($pageCount) && $pageCount > 0)
                                <span style="color: #4a5d44; font-size: 12px;">/ {{ $pageCount }} pages</span>
                            @endif
                        </div>

                        {{-- 5 Renkli Puan Verme --}}
                        <div style="margin-bottom: 12px;">
                            <label style="font-family: 'Henny Penny', cursive; color: #1f5117; display: block; margin-bottom: 4px; font-size: 13px;">rate this book:</label>
                            <input type="hidden" name="rating" id="selected-rating" value="{{ $userBook->rating ?? '' }}">

                            <div class="star-rating-multi" style="display: inline-flex; gap: 4px; cursor: pointer; user-select: none;">
                                @php
                                    $ratingColors = [
                                        1 => '#d43b82',
                                        2 => '#e67e22',
                                        3 => '#fee16c',
                                        4 => '#8dd04e',
                                        5 => '#3a91bc',
                                    ];
                                    $currentRating = $userBook->rating ?? 0;
                                @endphp

                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star-btn" 
                                          data-value="{{ $i }}" 
                                          data-color="{{ $ratingColors[$i] }}"
                                          style="font-size: 28px; transition: transform 0.15s ease, color 0.15s ease; color: {{ ($currentRating >= $i) ? $ratingColors[$currentRating] : '#ccc' }};">
                                        ★
                                    </span>
                                @endfor
                            </div>
                            
                            <span id="rating-text" style="font-family: 'Henny Penny', cursive; color: #3a7d2c; font-size: 12px; margin-left: 6px; vertical-align: middle;">
                                {{ isset($userBook->rating) ? "({$userBook->rating}/5)" : '(no rating)' }}
                            </span>
                        </div>

                        {{-- Değerlendirme / Yorum --}}
                        <label style="display: block; font-weight: bold; color: #1f5117; margin-bottom: 4px; font-size: 13px;">your thoughts:</label>
                        <textarea name="review" rows="3" placeholder="what did u think about this book?" style="width: 100%; padding: 8px 10px; border-radius: 6px; border: 1.5px solid #2d5a27; font-family: 'Unkempt', cursive; font-size: 13px; color: #1b3711; resize: vertical; box-sizing: border-box; margin-bottom: 12px; outline: none;">{{ $userBook->review ?? '' }}</textarea>

                        <button type="submit" class="review-submit-btn" style="background: #2d5a27; color: white; border: none; padding: 9px 20px; border-radius: 6px; font-family: 'Unkempt', cursive; font-size: 14px; cursor: pointer;">
                            save it to my library!
                        </button>
                    </form>

                    {{-- 2. ADMİN TAVSİYE FORMU --}}
                    @if(auth()->check() && auth()->user()->email === "bookieapp.info@gmail.com")
                        <div style="margin-top: 16px; padding: 10px; background: #eaf3e4; border: 1.5px dashed #2d5a27; border-radius: 8px;">
                            <h4 style="margin: 0 0 6px 0; font-size: 12px; color: #1a3c11; font-weight: bold; font-family: 'Henny Penny', cursive;">
                                ⭐ Admin Tavsiyesi Olarak Belirle
                            </h4>
                            <form action="{{ route('adminRecommendation.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 6px;">
                                @csrf
                                <input type="hidden" name="book_key" value="{{ request()->route('key') }}">
                                <input type="hidden" name="title" value="{{ $title ?? '' }}">
                                <input type="hidden" name="authors" value="{{ $authors ?? '' }}">
                                <input type="hidden" name="cover_url" value="{{ $coverUrl ?? '' }}">
                                
                                <textarea name="admin_note" rows="2" placeholder="Admin tavsiye notunu buraya yaz..." style="width: 100%; box-sizing: border-box; padding: 6px 8px; border-radius: 6px; border: 1px solid #737e3d; font-size: 12px; resize: none; font-family: 'Unkempt', cursive;"></textarea>

                                <button type="submit" style="align-self: flex-start; background: #2d5a27; color: #fff; border: none; padding: 5px 10px; border-radius: 6px; font-size: 11px; cursor: pointer; font-weight: bold; font-family: 'Unkempt', cursive;">
                                    tavsiyeni kaydet 📌
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- 3. KULLANICI YORUMLARI --}}
                    <div style="margin-top: 20px;">
                        <h3 style="font-family: 'Henny Penny', cursive; color: #1f5117; margin-bottom: 8px; font-size: 16px;">reviews ({{ $allReviews->count() }})</h3>

                        @forelse($allReviews as $item)
                            @php
                                $itemRatingColor = $ratingColors[$item->rating] ?? '#4a5d44';
                            @endphp
                            <div id="review-{{ $item->id }}" style="background: #f1f8ed; border: 1.5px solid #4c7237; border-radius: 8px; padding: 8px 10px; margin-bottom: 8px; transition: transform 0.2s ease;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                    <strong style="font-weight: bold; color: #1f5117; font-size: 12px;">{{ $item->user->username ?? ($item->user->name ?? 'Anonim') }}</strong>
                                    <span style="color: {{ $itemRatingColor }}; font-size: 13px;">
                                        {{ $item->rating > 0 ? str_repeat('★', $item->rating) : 'no rating' }}
                                    </span> 
                                </div>
                                
                                @if(!empty($item->review))
                                    <p style="color: #4a5d44; font-size: 12px; line-height: 1.3; margin: 0 0 6px 0;">{{ $item->review }}</p>
                                @endif

                                <!-- Beğeni Butonu & Tarih -->
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px; padding-top: 4px; border-top: 1px dashed #d7e8cf;">
                                    @include('partials.review-like-btn', ['review' => $item])

                                    <span style="font-size: 10px; color: #666;">
                                        {{ $item->created_at ? $item->created_at->diffForHumans() : '' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p style="color: #4a5d44; font-size: 12px;">there are no ratings for this book yet, wanna be the first one :>?</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const starButtons = document.querySelectorAll('.star-rating-multi .star-btn');
    const ratingInput = document.getElementById('selected-rating');
    const ratingText = document.getElementById('rating-text');

    const colorMap = {
        1: '#d43b82',
        2: '#e67e22',
        3: '#fee16c',
        4: '#8dd04e',
        5: '#3a91bc'
    };

    function paintStars(rating) {
        starButtons.forEach(btn => {
            const val = parseInt(btn.getAttribute('data-value'));
            if (rating > 0 && val <= rating) {
                btn.style.color = colorMap[rating];
            } else {
                btn.style.color = '#ccc';
            }
        });
    }

    starButtons.forEach(btn => {
        btn.addEventListener('mouseenter', function () {
            const hoverVal = parseInt(this.getAttribute('data-value'));
            paintStars(hoverVal);
            this.style.transform = 'scale(1.25)';
        });

        btn.addEventListener('mouseleave', function () {
            const selectedVal = parseInt(ratingInput.value) || 0;
            paintStars(selectedVal);
            this.style.transform = 'scale(1)';
        });

        btn.addEventListener('click', function () {
            const clickVal = parseInt(this.getAttribute('data-value'));
            
            if (parseInt(ratingInput.value) === clickVal) {
                ratingInput.value = '';
                ratingText.innerText = '(no rating)';
                paintStars(0);
            } else {
                ratingInput.value = clickVal;
                ratingText.innerText = `(${clickVal}/5)`;
                paintStars(clickVal);
            }
        });
    });

    const statusRadios = document.querySelectorAll('.status-radio');
    const pageBox = document.getElementById('page-box');

    statusRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.value === 'reading') {
                pageBox.style.display = 'flex';
            } else {
                pageBox.style.display = 'none';
            }
        });
    });
});

function toggleReviewLike(reviewId, buttonElement) {
    const csrfToken = document.querySelector("meta[name='csrf-token']")?.getAttribute('content') 
                      || '{{ csrf_token() }}';

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
                
                if (heartImg) {
                    heartImg.src = data.liked ? fullHeartSrc : emptyHeartSrc;
                }
                if (count) {
                    count.innerText = data.likes_count;
                }
            });
        }
    })
    .catch(err => console.error('Beğeni hatası:', err));
}
</script>
@include('partials.chat')
</body>
</html>