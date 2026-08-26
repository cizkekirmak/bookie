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
        body {
            background-image: url('{{ asset('images/arkaplan.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            font-family: 'Unkempt', cursive;
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .container {
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
    </style>
</head>
<body>

<div class="container">
    <a href="{{ route('dashboard') }}" class="back-link">← wanna go back?</a>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; border: 1.5px solid #c3e6cb; padding: 10px 15px; border-radius: 8px; margin-bottom: 20px; font-family: 'Henny Penny', cursive; font-size: 14px;">
            ✨ {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
        
        {{-- SOL: Kapak --}}
        <div style="flex-shrink: 0; width: 190px;">
            @if($coverUrl)
                <img src="{{ $coverUrl }}" alt="Cover" referrerpolicy="no-referrer" style="width: 100%; height: auto; max-height: 280px; object-fit: contain; border-radius: 8px; border: 1.5px solid #2d5a27; box-shadow: 0 4px 10px rgba(0,0,0,0.15); display: block; background: #fff;">
            @else
                <div style="width: 100%; height: 260px; background: #eaf3e4; border: 1.5px solid #2d5a27; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 40px;">
                    📖
                </div>
            @endif
        </div>

        {{-- SAĞ: Detaylar ve Formlar --}}
        <div style="flex: 1; min-width: 280px;">
            <h1 style="font-family: 'Henny Penny', cursive; color: #1f5117; margin: 0 0 4px 0; font-size: 26px;">
                {{ $title ?? 'Unknown book' }}
            </h1>
            <h4 style="color: #4a5d44; margin: 0 0 12px 0; font-size: 15px; font-weight: normal;">
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

            <div style="display: flex; align-items: center; gap: 8px; margin: 8px 0;">
                <div style="color: {{ $starColor }}; font-size: 18px; display: flex; gap: 2px;">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($averageRating >= $i || $averageRating >= ($i - 0.5))
                            ★ 
                        @else
                            <span style="color: #ccc;">★</span>
                        @endif
                    @endfor
                </div>

                <span style="font-family: 'Unkempt', cursive; font-weight: bold; color: #1a3c11; font-size: 16px;">
                    {{ $averageRating > 0 ? number_format($averageRating, 1) : 'Henüz puan yok' }}
                </span>

                @if($totalReviews > 0)
                    <span style="color: #666; font-size: 12px; font-family: 'Unkempt', cursive;">
                        ({{ $totalReviews }} inceleme)
                    </span>
                @endif
            </div>

            <p style="color: #4a5d44; font-size: 14px; line-height: 1.5; max-height: 110px; overflow-y: auto; margin-bottom: 20px; padding-right: 5px; border-bottom: 1px solid #e0ebd9; padding-bottom: 15px;">
                {{ Str::limit($description ?? '', 350) }}
            </p>

            {{-- 1. KULLANICI KİTAP FORMU --}}
            <form action="{{ route('books.save', request()->route('key')) }}" method="POST">
                @csrf
                <input type="hidden" name="title" value="{{ $title ?? 'Unknown Title' }}">
                <input type="hidden" name="cover_image" value="{{ $coverUrl }}">
                <input type="hidden" name="author" value="{{ $authors ?? 'Unknown author' }}">

                {{-- Kitap Anahtarı (Key) Tanımlaması --}}
                @php
                    $routeKey = (string) request()->route('key');
                    $isOl = str_starts_with($routeKey, 'OL') || str_contains($routeKey, '/works/');
                @endphp

                @if($isOl)
                    <input type="hidden" name="open_library_key" value="{{ $routeKey }}">
                @else
                    <input type="hidden" name="google_book_id" value="{{ $routeKey }}">
                @endif

                {{-- Okuma Durumu --}}
                <label style="display: block; font-weight: bold; color: #1f5117; margin-bottom: 8px;">reading progress</label>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px;">
                    <label class="radio-label">
                        <input type="radio" name="status" value="reading" {{ ($userBook && $userBook->status == 'reading') ? 'checked' : '' }} required>
                        currently reading 
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="status" value="read" {{ ($userBook && $userBook->status == 'read') ? 'checked' : '' }}>
                        read
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="status" value="want_to_read" {{ (!$userBook || $userBook->status == 'want_to_read' || $userBook->status == 'toRead') ? 'checked' : '' }}>
                        want to read
                    </label>
                </div>

                {{-- 5 Renkli Puan Verme --}}
                <div style="margin-bottom: 18px;">
                    <label style="font-family: 'Henny Penny', cursive; color: #1f5117; display: block; margin-bottom: 6px;">rate this book:</label>
                    <input type="hidden" name="rating" id="selected-rating" value="{{ $userBook->rating ?? '' }}">

                    <div class="star-rating-multi" style="display: inline-flex; gap: 6px; cursor: pointer; user-select: none;">
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
                                  style="font-size: 32px; transition: transform 0.15s ease, color 0.15s ease; color: {{ ($currentRating >= $i) ? $ratingColors[$currentRating] : '#ccc' }};">
                                ★
                            </span>
                        @endfor
                    </div>
                    
                    <span id="rating-text" style="font-family: 'Henny Penny', cursive; color: #3a7d2c; font-size: 14px; margin-left: 10px; vertical-align: middle;">
                        {{ isset($userBook->rating) ? "({$userBook->rating}/5)" : '(no rating)' }}
                    </span>
                </div>

                {{-- Değerlendirme / Yorum --}}
                <label style="display: block; font-weight: bold; color: #1f5117; margin-bottom: 6px;">your thoughts:</label>
                <textarea name="review" rows="3" placeholder="what did u think about this book?" style="width: 100%; padding: 10px; border-radius: 6px; border: 1.5px solid #2d5a27; font-family: 'Unkempt', cursive; font-size: 15px; color: #1b3711; resize: vertical; box-sizing: border-box; margin-bottom: 18px; outline: none;">{{ $userBook->review ?? '' }}</textarea>

                <button type="submit" style="background: #2d5a27; color: white; border: none; padding: 10px 24px; border-radius: 6px; font-family: 'Unkempt', cursive; font-size: 16px; cursor: pointer;">
                    save it to my library!
                </button>
            </form>

            {{-- 2. ADMİN TAVSİYE FORMU --}}
            @if(auth()->check() && auth()->user()->email === "bookieapp.info@gmail.com")
                <div style="margin-top: 25px; padding: 14px; background: #eaf3e4; border: 1.5px dashed #2d5a27; border-radius: 10px;">
                    <h4 style="margin: 0 0 8px 0; font-size: 14px; color: #1a3c11; font-weight: bold; font-family: 'Henny Penny', cursive;">
                        ⭐ Admin Tavsiyesi Olarak Belirle
                    </h4>
                    <form action="{{ route('adminRecommendation.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 8px;">
                        @csrf
                        <input type="hidden" name="book_key" value="{{ request()->route('key') }}">
                        <input type="hidden" name="title" value="{{ $title ?? '' }}">
                        <input type="hidden" name="authors" value="{{ $authors ?? '' }}">
                        <input type="hidden" name="cover_url" value="{{ $coverUrl ?? '' }}">
                        
                        <textarea name="admin_note" rows="2" placeholder="Admin tavsiye notunu buraya yaz..." style="width: 100%; box-sizing: border-box; padding: 8px 10px; border-radius: 6px; border: 1px solid #737e3d; font-size: 13px; resize: none; font-family: 'Unkempt', cursive;"></textarea>

                        <button type="submit" style="align-self: flex-start; background: #2d5a27; color: #fff; border: none; padding: 7px 14px; border-radius: 6px; font-size: 13px; cursor: pointer; font-weight: bold; font-family: 'Unkempt', cursive;">
                            tavsiyeni kaydet 📌
                        </button>
                    </form>
                </div>
            @endif

            {{-- 3. KULLANICI YORUMLARI --}}
            <div style="margin-top: 35px;">
                <h3 style="font-family: 'Henny Penny', cursive; color: #1f5117; margin-bottom: 14px;">reviews ({{ $allReviews->count() }})</h3>

                @forelse($allReviews as $item)
                    @php
                        $itemRatingColor = $ratingColors[$item->rating] ?? '#4a5d44';
                    @endphp
                    <div id="review-{{ $item->id }}" style="background: #f1f8ed; border: 1.5px solid #4c7237; border-radius: 8px; padding: 12px; margin-bottom: 12px; transition: transform 0.2s ease;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <strong style="font-weight: bold; color: #1f5117;">{{ $item->user->username ?? ($item->user->name ?? 'Anonim') }}</strong>
                            <span style="color: {{ $itemRatingColor }}; font-size: 16px;">
                                {{ $item->rating > 0 ? str_repeat('★', $item->rating) : 'no rating' }}
                            </span> 
                        </div>
                        
                        @if(!empty($item->review))
                            <p style="color: #4a5d44; font-size: 14px; line-height: 1.4; margin: 0 0 8px 0;">{{ $item->review }}</p>
                        @endif

                        <!-- Beğeni Butonu & Tarih -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px; padding-top: 6px; border-top: 1px dashed #d7e8cf;">
                            @include('partials.review-like-btn', ['review' => $item])

                            <span style="font-size: 12px; color: #666;">
                                {{ $item->created_at ? $item->created_at->diffForHumans() : '' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p style="color: #4a5d44; font-size: 14px;">there is no ratings for this book yet, wanna be the first one :>?</p>
                @endforelse
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

</body>
</html>