<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bookie - Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Henny+Penny&family=Mystery+Quest&family=Unkempt:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-image: url('{{ asset('images/giris.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            margin: 0;
        }

        :target {
            animation: highlightCard 2.5s ease forwards;
        }

        @keyframes highlightCard {
            0% { transform: scale(1.02); box-shadow: 0 0 12px #2d5a27; }
            100% { transform: scale(1); box-shadow: none; }
        }
    </style>
</head>
<body>

    <header style="
        max-width: 100%;
        margin: 0;
        height: 95px;
        padding: 0 30px;
        background-color: #d3ea76;
        border: 2px solid #2d5a27;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        align-items: center;
        display: flex;
        box-sizing: border-box;
        overflow: visible !important;
        position: relative;
        z-index: 1000;
    ">
        {{-- SOL: Logo --}}
        <div style="font-family: 'Henny Penny', cursive; 
                    font-size: 50px; 
                    color: #1f5117; 
                    flex-shrink: 0; 
                    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
                    display: flex;
                    align-items: center;
                    margin-top: 5px;">
            Bookie
        </div>

        {{-- ORTA: Logo ile Sağ İkonların Arasını Eşit Ortala --}}
        <div style="
            position: relative;
            flex: 1;
            max-width: 650px;
            margin: 0 auto;
            z-index: 99999;
        ">
            <div style="
                display: flex; 
                align-items: center; 
                gap: 12px;
                height: 52px;
                background-image: url('{{ asset('images/arama.jpg') }}');
                background-position: center;
                background-repeat: no-repeat;
                border: 1px solid #4c7237;
                border-radius: 30px;
                padding: 0 14px;
                box-sizing: border-box;
            ">
                <img 
                    src="{{ asset('images/yıldız.png') }}" 
                    alt="Search" 
                    style="width: 38px; height: 38px; object-fit: contain; flex-shrink: 0;"
                >
                
                <input 
                    type="text" 
                    id="bookSearchInput" 
                    placeholder="what are you looking for?" 
                    autocomplete="on" 
                    style="
                        flex: 1; 
                        border: none; 
                        outline: none; 
                        background: transparent; 
                        font-size: 22px; 
                        font-family: 'Unkempt', cursive; 
                        color: #1b3711;
                    "
                >
                <span id="searchLoader" style="display: none; font-size: 14px;">^^</span>
            </div>

            <div id="searchResultsDropdown" style="
                display: none;
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
                max-height: 360px;
                overflow-y: auto;
                background: #ffffff;
                border: 1.5px solid #2d5a27;
                border-radius: 12px;
                box-shadow: 0 8px 16px rgba(0,0,0,0.25);
                z-index: 999999;
                box-sizing: border-box;
            "></div>
        </div>
        
        {{-- SAĞ: İkon Paketi --}}
        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 18px; flex-shrink: 0;">
            
            <!-- 1. Bildirim Zarfı -->
            <div style="display: flex; align-items: center; justify-content: center; line-height: 0;">
                @include('partials.notifications')
            </div>

            <!-- 2. Ayarlar -->
            <a href="{{ route('ayarlar') }}" style="
                display: flex;
                align-items: center;
                justify-content: center;
                line-height: 0;
                text-decoration: none;
                flex-shrink: 0;
                transition: transform 0.2s ease, filter 0.2s ease;"
            >
                <img 
                    src="{{ asset('images/ayarlar.jpg') }}" 
                    alt="ayarlar" 
                    style="
                        width: 64px;
                        height: 64px;
                        object-fit: contain;
                        border: 1.5px solid #4b813b;
                        box-sizing: border-box;
                        display: block;
                        cursor: pointer;"
                    onmouseenter="this.style.transform='scale(1.1)'; this.style.filter='drop-shadow(0px 6px 8px rgba(0, 0, 0, 0.4))';"
                    onmouseleave="this.style.transform='scale(1)'; this.style.filter='none';"
                >
            </a>
            
            <!-- 3. Profil -->
            <a href="{{ route('profile') }}" style="
                display: flex;
                align-items: center;
                justify-content: center;
                line-height: 0;
                text-decoration: none;
                flex-shrink: 0;
                transition: transform 0.2s ease, filter 0.2s ease;"
            >
                <img 
                    src="{{ asset('images/profile.jpg') }}" 
                    alt="profile" 
                    style="
                        width: 64px;
                        height: 64px;
                        object-fit: contain;
                        border: 1.5px solid #4b813b;
                        box-sizing: border-box;
                        display: block;
                        cursor: pointer;"
                    onmouseenter="this.style.transform='scale(1.1)'; this.style.filter='drop-shadow(0px 6px 8px rgba(0, 0, 0, 0.4))';"
                    onmouseleave="this.style.transform='scale(1)'; this.style.filter='none';"
                >
            </a>
        </div>

    </header>

    @if(session('success'))
        <div id="bildiri-message" style="
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            background-color: #d4edda;
            color: #155724;
            border: 1.5px solid #c3e6cb;
            padding: 10px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-family: 'Unkempt', cursive;
            transition: opacity 0.5s ease;">
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

    {{-- Ana Yerleşim: Sağ Panel Ekrana Tam Yapışık --}}
    <div style="display: flex; min-height: calc(100vh - 95px); box-sizing: border-box; align-items: stretch;">
        
        {{-- SOL ALAN (Kutuların Olduğu Yer) --}}
        <div style="
            flex: 1; 
            padding: 45px 40px; 
            display: flex; 
            justify-content: center; 
            align-items: flex-start; 
            min-width: 0;
            box-sizing: border-box;
        ">
            <div style="
                width: 100%; 
                display: grid; 
                grid-template-columns: 1fr 1.3fr; 
                gap: 60px;
                box-sizing: border-box;
            ">
                
                {{-- 1. SOL ÜST: Devam Et --}}
                <div>
                    @include('partials.continue-reading')
                </div>

                {{-- 2. SAĞ ÜST: En Çok Okunan Kitaplar --}}
                <div>
                    @include('partials.popular-books')
                </div>

                {{-- 3. SOL ALT: Öneri --}}
                <div>
                    @include('partials.adminRecommendation')
                </div>

                {{-- 4. SAĞ ALT: Kedi Kitap Önerisi --}}
                <div>
                    @include('partials.cat-recommendation')
                </div>

            </div>
        </div>

        {{-- SAĞ ALAN: Arkadaş Yorumları ve Arama Paneli --}}
        <div style="
            width: 400px;
            align-self: stretch;
            margin: 0;
            background: #badfa0; 
            border-left: 2px solid #82b564;
            padding: 20px 16px; 
            display: flex; 
            flex-direction: column; 
            gap: 16px; 
            box-sizing: border-box; 
            flex-shrink: 0; 
            margin-left: auto;
        ">
            
            {{-- Üst: Kullanıcı Ara Barı --}}
            <div style="position: relative; width: 100%;">
                <div style="
                    background: #f4fbf0; 
                    border: 1.5px solid #4c7237; 
                    border-radius: 22px; 
                    padding: 6px 14px; 
                    display: flex; 
                    align-items: center; 
                    gap: 8px;
                ">
                    <span style="font-size: 16px;">👤</span>
                    <input 
                        type="text" 
                        id="userSearchInput" 
                        placeholder="find other users" 
                        autocomplete="off" 
                        style="
                            border: none; 
                            background: transparent; 
                            outline: none; 
                            font-family: 'Unkempt', cursive; 
                            font-size: 14px; 
                            color: #1b3711; 
                            width: 100%;
                        "
                    >
                </div>

                {{-- Arama Sonuçlarının Açılacağı Kutu --}}
                <div id="userSearchResults" style="
                    display: none;
                    position: absolute;
                    top: 45px;
                    left: 0;
                    width: 100%;
                    background: #ffffff;
                    border: 1.5px solid #4c7237;
                    border-radius: 12px;
                    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
                    max-height: 220px;
                    overflow-y: auto;
                    z-index: 999999;
                    box-sizing: border-box;
                "></div>
            </div>

            {{-- Başlık --}}
            <span style="font-family: 'Henny Penny', cursive; font-size: 18px; color: #1a3c11;">
                Arkadaş yorumları
            </span>

            {{-- Yorum Kartları Alanı --}}
            <div style="display: flex; flex-direction: column; gap: 12px; overflow-y: auto; flex: 1;">
                @php
                    $friendIds = auth()->check() ? auth()->user()->friends()->pluck('id') : collect([]);
                    
                    $friendReviews = \App\Models\UserBook::whereIn('user_id', $friendIds)
                        ->whereNotNull('review')
                        ->with(['user', 'book', 'likes'])
                        ->latest()
                        ->take(10)
                        ->get();

                    $ratingColors = [
                        1 => '#d9534f',
                        2 => '#f0ad4e',
                        3 => '#ffd700',
                        4 => '#5cb85c',
                        5 => '#2e7d32',
                    ];
                @endphp

                @forelse($friendReviews as $item)
                    @php 
                        $itemRatingColor = $ratingColors[$item->rating] ?? '#4a5d44';
                        
                        $bookKey = $item->book->open_library_key 
                                ?? $item->book->google_book_id 
                                ?? $item->book->key 
                                ?? $item->book_id;

                        $bookTitle = $item->book->title ?? 'Kitap Detayı';
                    @endphp

                    <div id="review-{{ $item->id }}" style="background: #f1f8ed; border: 1.5px solid #4c7237; border-radius: 8px; padding: 12px; transition: transform 0.2s ease;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            @if($item->user)
                            <a href="{{ route('profile', $item->user->id) }}" 
                               style="color: #1f5117; text-decoration: none; font-weight: bold; font-family: 'Unkempt', cursive; transition: opacity 0.2s;"
                               onmouseover="this.style.textDecoration='underline'" 
                               onmouseout="this.style.textDecoration='none'">
                                {{ $item->user->username ?? ($item->user->name ?? 'Anonim') }}
                            </a>
                            @else
                            <strong style="font-weight: bold; color: #1f5117; font-family: 'Unkempt', cursive;">'Anonim'</strong>
                            @endif

                            <span style="color: {{ $itemRatingColor }}; font-size: 14px;">
                                {{ $item->rating > 0 ? str_repeat('★', $item->rating) : 'puan yok' }}
                            </span> 
                        </div>

                        <!-- Kitap Başlığı ve Detay Linki -->
                        <div style="margin-bottom: 6px;">
                            <span style="font-size: 12px; color: #666; font-family: 'Unkempt', cursive;">kitap: </span>
                            <a href="{{ route('show', $bookKey) }}#review-{{ $item->id }}" 
                               style="font-size: 13px; font-weight: bold; color: #1a3c11; text-decoration: underline; font-family: 'Unkempt', cursive;">
                                {{ $bookTitle }}
                            </a>
                        </div>

                        @if(!empty($item->review))
                            <p style="color: #4a5d44; font-size: 13px; line-height: 1.4; margin: 0 0 8px 0; font-family: 'Unkempt', cursive;">
                                {{ $item->review }}
                            </p>
                        @endif

                        <!-- Alt Bilgi: Beğeni Butonu & Tarih -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px; padding-top: 6px; border-top: 1px dashed #d7e8cf;">
                            @include('partials.review-like-btn', ['review' => $item])

                            <span style="font-size: 11px; color: #777; font-family: 'Unkempt', cursive;">
                               {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->timezone('Europe/Istanbul')->diffForHumans() : '' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p style="color: #4a5d44; font-size: 13px; margin: 0; font-family: 'Unkempt', cursive;">
                        arkadaşlarından henüz yorum yapan olmamış!
                    </p>
                @endforelse
            </div>

        </div>
    </div>

<script>
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

document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('bookSearchInput');
    const dropdown = document.getElementById('searchResultsDropdown');

    if (!input || !dropdown) return;

    let allSearchResults = [];
    let displayedCount = 0;
    const PAGE_SIZE = 6;

    const savedQuery = sessionStorage.getItem('lastBookSearchQuery');
    const savedResults = sessionStorage.getItem('lastBookSearchResults');

    if (savedQuery && savedResults) {
        try {
            const parsed = JSON.parse(savedResults);
            if (Array.isArray(parsed) && parsed.length > 0) {
                input.value = savedQuery;
                allSearchResults = parsed;
                displayedCount = 0;
                dropdown.innerHTML = '';
                renderNextBooks();
                dropdown.style.display = 'block';
            }
        } catch (e) {
            console.error('Önceki arama sonuçları yüklenemedi:', e);
        }
    }

    input.addEventListener('input', function () {
        if (this.value.trim() === '') {
            sessionStorage.removeItem('lastBookSearchQuery');
            sessionStorage.removeItem('lastBookSearchResults');
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
        }
    });

    function renderNextBooks() {
        const oldBtn = document.getElementById('searchLoadMoreContainer');
        if (oldBtn) oldBtn.remove();

        const nextBatch = allSearchResults.slice(displayedCount, displayedCount + PAGE_SIZE);

        const html = nextBatch.map(book => `
            <div onclick="window.location.href='/books/${book.id}'" 
                 style="display: flex; align-items: center; gap: 12px; padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #e2ebd8; transition: background-color 0.15s;"
                 onmouseenter="this.style.backgroundColor='#f4f8e8'" 
                 onmouseleave="this.style.backgroundColor='transparent'">
                <img src="${book.cover}" 
                     loading="lazy" 
                     referrerpolicy="no-referrer" 
                     style="width: 38px; height: 52px; object-fit: cover; border-radius: 4px; flex-shrink: 0; background-color: #e8f0dc;">
                <div style="overflow: hidden; text-align: left;">
                    <div style="font-family: 'Unkempt', cursive; font-size: 15px; font-weight: bold; color: #1f5117; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${book.title}
                    </div>
                    <div style="font-family: 'Unkempt', cursive; font-size: 12px; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${book.authors}
                    </div>
                </div>
            </div>
        `).join('');

        dropdown.insertAdjacentHTML('beforeend', html);
        displayedCount += nextBatch.length;

        if (displayedCount < allSearchResults.length) {
            const remaining = allSearchResults.length - displayedCount;
            const loadMoreHtml = `
                <div id="searchLoadMoreContainer" style="padding: 8px 12px; text-align: center; background: #fafdf7;">
                    <button type="button" id="searchLoadMoreBtn" style="background: #eef6ea; border: 1.5px solid #4c7237; color: #1f5117; padding: 5px 16px; border-radius: 16px; font-family: 'Unkempt', cursive; font-size: 13px; cursor: pointer; transition: all 0.15s ease;">
                        ✨ load more (+${Math.min(PAGE_SIZE, remaining)})
                    </button>
                </div>
            `;
            dropdown.insertAdjacentHTML('beforeend', loadMoreHtml);

            document.getElementById('searchLoadMoreBtn').addEventListener('click', function (e) {
                e.stopPropagation();
                renderNextBooks();
            });
        }
    }

    input.addEventListener('keydown', async function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const q = input.value.trim();
            if (q.length < 2) return;

            dropdown.innerHTML = '<div style="padding: 12px; font-family: \'Unkempt\', cursive; color: #666;">Aranıyor...</div>';
            dropdown.style.display = 'block';
            try {
                const res = await fetch(`/api/search-books?q=${encodeURIComponent(q)}`);
                const data = await res.json();

                const books = Array.isArray(data) ? data : (data.items || []);

                if (!books || books.length === 0) {
                    dropdown.innerHTML = '<div style="padding: 12px; font-family: \'Unkempt\', cursive; color: #666;">Sonuç bulunamadı.</div>';
                    sessionStorage.setItem('lastBookSearchQuery');
                    sessionStorage.setItem('lastBookSearchResults');
                    return;
                }

                sessionStorage.setItem('lastBookSearchQuery', q);
                sessionStorage.setItem('lastBookSearchResults', JSON.stringify(books));

                dropdown.innerHTML = '';
                allSearchResults = books;
                displayedCount = 0;
                renderNextBooks();

            } catch (err) {
                dropdown.innerHTML = '<div style="padding: 12px; font-family: \'Unkempt\', cursive; color: red;">Arama sırasında hata oluştu.</div>';
            }
        }
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    const userSearchInput = document.getElementById('userSearchInput');
    const userSearchResults = document.getElementById('userSearchResults');

    if (userSearchInput && userSearchResults) {
        let debounceTimer;
        const defaultAvatar = "{{ asset('images/profile.jpg') }}";

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
                        userSearchResults.innerHTML = '<div style="padding: 10px; font-size: 13px; color: #777; text-align: center; font-family: \'Unkempt\', cursive;">no user was found, are u sure u spelt that correctly?</div>';
                        userSearchResults.style.display = 'block';
                        return;
                    }

                    const csrfToken = '{{ csrf_token() }}';

                    userSearchResults.innerHTML = users.map(user => {
                        let actionHtml = '';

                        if (user.status === 'accepted') {
                            actionHtml = `<span style="font-size: 12px; color: #1a3c11; font-weight: bold; font-family: 'Unkempt', cursive;">✓ friends</span>`;
                        } else if (user.status === 'pending') {
                            actionHtml = user.is_sender 
                                ? `<span style="font-size: 12px; color: #666; font-family: 'Unkempt', cursive;">pending</span>`
                                : `<span style="font-size: 12px; color: #c62828; font-family: 'Unkempt', cursive;">requested</span>`;
                        } else {
                            actionHtml = `
                                <form action="/friends/${user.id}/request" method="POST" style="margin: 0;" onclick="event.stopPropagation();">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <button type="submit" 
                                            style="background: #2d5a27; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 15px; font-family: 'Unkempt', cursive;"
                                            title="Arkadaş Ekle">+</button>
                                </form>
                            `;
                        }

                        // Profil fotoğrafı kontrolü
                        let userAvatarSrc = defaultAvatar;
                        if (user.avatar) {
                            userAvatarSrc = user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`;
                        }

                        return `
                            <div onclick="window.location.href='/profile/${user.id}'" 
                                 style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eef4e8; transition: background 0.15s ease;"
                                 onmouseenter="this.style.background='#f1f8ed'" 
                                 onmouseleave="this.style.background='transparent'">
                                
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; border: 1.5px solid #4c7237; overflow: hidden; flex-shrink: 0; background: #badfa0; display: flex; align-items: center; justify-content: center;">
                                        <img src="${userAvatarSrc}" 
                                             alt="${user.username}" 
                                             style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block;" 
                                             onerror="this.onerror=null; this.src='${defaultAvatar}';">
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
                    userSearchResults.innerHTML = '<div style="padding: 10px; font-size: 13px; color: red; text-align: center; font-family: \'Unkempt\', cursive;">Hata oluştu.</div>';
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
</body>
</html>