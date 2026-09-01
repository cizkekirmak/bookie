<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? __('Book Details') }} - Bookie</title>
    
    <!-- Fontlar -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mystery+Quest&display=swap" rel="stylesheet">

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
            -webkit-tap-highlight-color: transparent !important;
        }

        /* TIKLANABİLİR ELEMANLARDA MAVİ VURGU VE METİN SEÇİMİNİ ENGELLE */
        a,
        button,
        input,
        label,
        .radio-label,
        .back-link,
        .star-btn,
        .review-submit-btn {
            -webkit-tap-highlight-color: transparent !important;
            -webkit-touch-callout: none !important;
            outline: none !important;
        }

        button,
        .back-link,
        .radio-label,
        .star-btn,
        .star-rating-multi,
        .review-submit-btn {
            user-select: none !important;
            -webkit-user-select: none !important;
        }

        body {
            background-image: url('{{ asset('images/giris.png') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            font-family: 'Unkempt', cursive;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .container {
            width: 100%;
            max-width: 820px;
            margin: 0 auto;
            background: rgba(254, 255, 252, 0.96);
            border: 2px solid #2d5a27;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2d5a27;
            text-decoration: none;
            font-size: 17px;
            font-weight: bold;
            transition: transform 0.15s ease;
        }

        .back-link:hover {
            transform: translateX(-3px);
        }

        .book-main-flex {
            display: flex;
            gap: 32px;
            flex-wrap: wrap;
        }

        .book-cover-wrap {
            flex-shrink: 0;
            width: 200px;
        }

        .book-cover-img {
            width: 100%;
            height: auto;
            max-height: 290px;
            object-fit: contain;
            border-radius: 10px;
            border: 1.5px solid #2d5a27;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
            border-radius: 8px;
            padding: 7px 14px;
            cursor: pointer;
            font-size: 15px;
            color: #1f5117;
            transition: background 0.15s ease;
        }

        .radio-label:hover {
            background: #e5f3de;
        }

        .radio-label input {
            cursor: pointer;
            accent-color: #2d5a27;
        }

        .page-box {
            background: #f1f8ed;
            border: 1.5px dashed #4c7237;
            border-radius: 8px;
            padding: 8px 14px;
            margin-bottom: 16px;
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

        .floating-lang-switch {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }

        /* MOBİL UYARLAMA */
        @media (max-width: 768px) {
            body {
                padding: 16px 10px;
                min-height: 100dvh;
            }

            .container {
                width: 100% !important;
                max-width: 100% !important;
                margin: auto 0 !important;
                padding: 16px 14px !important;
                border-radius: 14px !important;
            }

            .back-link {
                margin-bottom: 12px;
                font-size: 14px;
            }

            .book-main-flex {
                gap: 12px;
                flex-wrap: nowrap !important;
            }

            .book-cover-wrap {
                width: 95px !important;
            }

            .book-cover-img {
                max-height: 145px !important;
                border-radius: 6px;
            }

            .book-info-col {
                min-width: 0 !important;
                flex: 1 !important;
            }

            .book-title-heading {
                font-size: 18px !important;
                line-height: 1.2;
            }

            .book-author-text {
                font-size: 13px !important;
                margin-bottom: 4px !important;
            }

            .book-desc-text {
                font-size: 13px !important;
                max-height: 85px !important;
                margin-bottom: 12px !important;
                padding-bottom: 8px !important;
            }

            .radio-group-wrap {
                gap: 6px !important;
                margin-bottom: 8px !important;
            }

            .radio-label {
                padding: 5px 8px !important;
                font-size: 12px !important;
                border-radius: 6px !important;
            }

            .page-box {
                padding: 6px 8px !important;
                margin-bottom: 10px !important;
                gap: 6px !important;
            }

            .page-input {
                width: 55px !important;
                padding: 3px 5px !important;
                font-size: 13px !important;
            }

            .star-btn {
                font-size: 26px !important;
            }

            .review-submit-btn {
                width: 100% !important;
                padding: 9px 14px !important;
                font-size: 15px !important;
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

<div class="container">
    <a href="{{ route('dashboard') }}" class="back-link">← {{ __('wanna go back?') }}</a>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; border: 1.5px solid #c3e6cb; padding: 10px 15px; border-radius: 8px; margin-bottom: 20px; font-family: 'Henny Penny', cursive; font-size: 14px;">
            ✨ {{ session('success') }}
        </div>
    @endif

    <div class="book-main-flex">
        
        {{-- SOL: Kapak --}}
        <div class="book-cover-wrap">
            @if($coverUrl)
                <img src="{{ $coverUrl }}" alt="Cover" referrerpolicy="no-referrer" class="book-cover-img">
            @else
                <div style="width: 100%; height: 140px; background: #eaf3e4; border: 1.5px solid #2d5a27; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                    📖
                </div>
            @endif
        </div>

        {{-- SAĞ: Detaylar ve Formlar --}}
        <div class="book-info-col">
            
            {{-- Başlık ve Sağdaki İndir/Oku Butonu --}}
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                <h1 class="book-title-heading" style="font-family: 'Henny Penny', cursive; color: #1f5117; margin: 0; font-size: 28px; line-height: 1.25;">
                    {{ $title ?? __('Unknown book') }}
                </h1>

                @if(!empty($downloadUrl) || (!empty($book) && !empty($book->download_url)))
                    <a href="{{ !empty($book) && !empty($book->download_url) ? $book->download_url : $downloadUrl }}" 
                       target="_blank" 
                       style="
                           display: inline-flex; 
                           align-items: center; 
                           gap: 5px; 
                           background-color: #d2f48a; 
                           color: #101e08; 
                           border: 1.5px solid #1d491b; 
                           padding: 5px 12px; 
                           border-radius: 8px; 
                           text-decoration: none; 
                           font-family: 'Unkempt', cursive; 
                           font-size: 13px; 
                           white-space: nowrap; 
                           flex-shrink: 0; 
                           cursor: pointer; 
                       ">
                        📖 <span>{{ __('Read / Download Book') }}</span>
                    </a>
                @endif
            </div>

            <h4 class="book-author-text" style="color: #435b3e; margin: 2px 0 8px 0; font-size: 16px; font-weight: normal;">
                {{ __('by') }} <strong>{{ $authors ?? __('Unknown author') }}</strong>
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

            <div style="display: flex; align-items: center; gap: 8px; margin: 4px 0 10px 0;">
                <div style="color: {{ $starColor }}; font-size: 18px; display: flex; gap: 2px;">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($averageRating >= $i || $averageRating >= ($i - 0.5))
                            ★ 
                        @else
                            <span style="color: #d3d3d3;">★</span>
                        @endif
                    @endfor
                </div>

                <span style="font-family: 'Unkempt', cursive; font-size: 14px; font-weight: bold; color: #1a3c11;">
                    {{ $averageRating > 0 ? number_format($averageRating, 1) : __('no ratings yet') }}
                </span>

                @if($totalReviews > 0)
                    <span style="color: #666; font-size: 12px; font-family: 'Unkempt', cursive;">
                        ({{ $totalReviews }} {{ __('reviews') }})
                    </span>
                @endif
            </div>

            <p class="book-desc-text" style="color: #4a5d44; font-size: 14px; line-height: 1.45; max-height: 110px; overflow-y: auto; margin-bottom: 16px; padding-right: 5px; border-bottom: 1px solid #e0ebd9; padding-bottom: 10px;">
                {{ Str::limit($description ?? '', 350) }}
            </p>

            {{-- 1. KULLANICI KİTAP FORMU --}}
            <form action="{{ route('books.save', request()->route('key')) }}" method="POST">
                @csrf
                <input type="hidden" name="title" value="{{ $title ?? 'Unknown Title' }}">
                <input type="hidden" name="cover_image" value="{{ $coverUrl }}">
                <input type="hidden" name="author" value="{{ $authors ?? 'Unknown author' }}">
                <input type="hidden" name="download_url" value="{{ $downloadUrl ?? ($book->download_url ?? '') }}">

                @php
                    $routeKey = (string) request()->route('key');
                    $isOl = str_starts_with($routeKey, 'OL') || str_contains($routeKey, '/works/');
                    $isGut = str_starts_with($routeKey, 'GUT_');
                    $pageCount = $pageCount ?? ($book->page_count ?? null);
                    $currentPage = $userBook->current_page ?? 0;
                @endphp

                <input type="hidden" name="page_count" value="{{ $pageCount ?? '' }}">

                @if($isOl)
                    <input type="hidden" name="open_library_key" value="{{ $routeKey }}">
                @elseif($isGut)
                    <input type="hidden" name="gutenberg_id" value="{{ $routeKey }}">
                @else
                    <input type="hidden" name="google_book_id" value="{{ $routeKey }}">
                @endif

                {{-- Okuma Durumu --}}
                <label style="display: block; font-weight: bold; color: #1f5117; margin-bottom: 6px; font-size: 14px;">{{ __('reading progress') }}</label>
                <div class="radio-group-wrap" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px;">
                    <label class="radio-label">
                        <input type="radio" name="status" value="reading" class="status-radio" {{ ($userBook && $userBook->status == 'reading') ? 'checked' : '' }} required>
                        {{ __('currently reading') }} 
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="status" value="read" class="status-radio" {{ ($userBook && $userBook->status == 'read') ? 'checked' : '' }}>
                        {{ __('read') }}
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="status" value="want_to_read" class="status-radio" {{ (!$userBook || $userBook->status == 'want_to_read' || $userBook->status == 'toRead') ? 'checked' : '' }}>
                        {{ __('want to read') }}
                    </label>
                </div>

                {{-- Sayfa İlerleme Kutusu --}}
                <div id="page-box" class="page-box" style="display: {{ ($userBook && $userBook->status == 'reading') ? 'flex' : 'none' }};">
                    <span style="color: #1f5117; font-size: 13px; font-weight: bold;">{{ __('on page:') }}</span>
                    <input type="number" name="current_page" id="current_page" class="page-input" min="0" max="{{ $pageCount ?? 9999 }}" value="{{ $currentPage }}" placeholder="0">
                    
                    @if(!empty($pageCount) && $pageCount > 0)
                        <span style="color: #4a5d44; font-size: 13px;">/ {{ $pageCount }} {{ __('pages') }}</span>
                    @endif
                </div>

                {{-- Puan Verme (Başlık kaldırıldı, ferah yıldızlar) --}}
                <div style="margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <input type="hidden" name="rating" id="selected-rating" value="{{ $userBook->rating ?? '' }}">

                    <div class="star-rating-multi" style="display: inline-flex; gap: 5px; cursor: pointer; user-select: none;">
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
                                  style="font-size: 30px; line-height: 1; transition: transform 0.15s ease, color 0.15s ease; color: {{ ($currentRating >= $i) ? $ratingColors[$currentRating] : '#ccc' }};">
                                ★
                            </span>
                        @endfor
                    </div>
                    
                    <span id="rating-text" style="font-family: 'Henny Penny', cursive; color: #3a7d2c; font-size: 14px; margin-left: 4px;">
                        {{ isset($userBook->rating) ? "({$userBook->rating}/5)" : __('(no rating)') }}
                    </span>
                </div>

                {{-- Değerlendirme / Yorum --}}
                <label style="display: block; font-weight: bold; color: #1f5117; margin-bottom: 6px; font-size: 14px;">{{ __('your thoughts:') }}</label>
                <textarea name="review" rows="3" placeholder="{{ __('what did u think about this book?') }}" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1.5px solid #2d5a27; font-family: 'Unkempt', cursive; font-size: 14px; color: #1b3711; resize: vertical; box-sizing: border-box; margin-bottom: 14px; outline: none; background: #fff;">{{ $userBook->review ?? '' }}</textarea>

                <button type="submit" class="review-submit-btn" style="background: #2d5a27; color: white; border: none; padding: 10px 22px; border-radius: 8px; font-family: 'Unkempt', cursive; font-size: 15px; cursor: pointer; transition: background 0.15s ease;">
                    {{ __('save it to my library!') }}
                </button>
            </form>

            {{-- 2. ADMİN TAVSİYE FORMU --}}
            @if(auth()->check() && auth()->user()->email === "bookieapp.info@gmail.com")
                <div style="margin-top: 18px; padding: 12px; background: #eaf3e4; border: 1.5px dashed #2d5a27; border-radius: 8px;">
                    <h4 style="margin: 0 0 6px 0; font-size: 13px; color: #1a3c11; font-weight: bold; font-family: 'Henny Penny', cursive;">
                        ⭐ {{ __('Set as Admin Recommendation') }}
                    </h4>
                    <form action="{{ route('adminRecommendation.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 8px;">
                        @csrf
                        <input type="hidden" name="book_key" value="{{ request()->route('key') }}">
                        <input type="hidden" name="title" value="{{ $title ?? '' }}">
                        <input type="hidden" name="authors" value="{{ $authors ?? '' }}">
                        <input type="hidden" name="cover_url" value="{{ $coverUrl ?? '' }}">
                        
                        <textarea name="admin_note" rows="2" placeholder="{{ __('Write your admin recommendation note here...') }}" style="width: 100%; box-sizing: border-box; padding: 8px; border-radius: 6px; border: 1px solid #737e3d; font-size: 13px; resize: none; font-family: 'Unkempt', cursive; background: #fff; outline: none;"></textarea>

                        <button type="submit" style="align-self: flex-start; background: #2d5a27; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; font-weight: bold; font-family: 'Unkempt', cursive;">
                            {{ __('save recommendation 📌') }}
                        </button>
                    </form>
                </div>
            @endif

            {{-- 3. KULLANICI YORUMLARI --}}
            <div style="margin-top: 24px;">
                <h3 style="font-family: 'Henny Penny', cursive; color: #1f5117; margin-bottom: 10px; font-size: 18px;">{{ __('reviews') }} ({{ $allReviews->count() }})</h3>

                @forelse($allReviews as $item)
                    @php
                        $itemRatingColor = $ratingColors[$item->rating] ?? '#4a5d44';
                    @endphp
                    <div id="review-{{ $item->id }}" style="background: #f1f8ed; border: 1.5px solid #4c7237; border-radius: 8px; padding: 10px 12px; margin-bottom: 10px; transition: transform 0.2s ease;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <strong style="font-weight: bold; color: #1f5117; font-size: 14px;">{{ $item->user->username ?? ($item->user->name ?? __('Anonymous')) }}</strong>
                            <span style="color: {{ $itemRatingColor }}; font-size: 14px;">
                                {{ $item->rating > 0 ? str_repeat('★', $item->rating) : __('no rating') }}
                            </span> 
                        </div>
                        
                        @if(!empty($item->review))
                            <p style="color: #4a5d44; font-size: 13px; line-height: 1.35; margin: 0 0 8px 0;">{{ $item->review }}</p>
                        @endif

                        <!-- Beğeni Butonu & Tarih -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px; padding-top: 6px; border-top: 1px dashed #d7e8cf;">
                            @include('partials.review-like-btn', ['review' => $item])

                            <span style="font-size: 11px; color: #666;">
                                {{ $item->created_at ? $item->created_at->diffForHumans() : '' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p style="color: #4a5d44; font-size: 13px;">{{ __('there are no ratings for this book yet, wanna be the first one :>?') }}</p>
                @endforelse
            </div>

        </div>
    </div>
</div>

{{-- SAĞ ALT KÖŞE DİL BUTONU --}}
<div class="floating-lang-switch">
    @include('partials.lang-switch')
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const starButtons = document.querySelectorAll('.star-rating-multi .star-btn');
    const ratingInput = document.getElementById('selected-rating');
    const ratingText = document.getElementById('rating-text');
    const noRatingText = @json(__(' (no rating)'));

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
                ratingText.innerText = noRatingText;
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