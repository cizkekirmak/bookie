<style>
/* MASAÜSTÜ: 1. Görseldeki Orijinal Boyutlar */
.cat-rec-main-card {
    background: #cae28c;
    border: 2px solid #5a8c69; 
    border-radius: 16px;
    padding: 14px 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 220px;
    width: 620px;
    box-sizing: border-box;
    font-family: 'Unkempt', cursive;
}

.css-bubble-right {
    position: relative;
    background: #ffffff;
    border: 2px solid #4c7237;
    border-radius: 14px;
    padding: 10px 18px;
    font-size: 16px;
    color: #1a3c11;
    line-height: 1.35;
    box-shadow: 0 3px 6px rgba(0,0,0,0.06);
}

.css-bubble-right::after, .css-bubble-right::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 100%;
    transform: translateY(-50%);
    border: solid transparent;
}

.css-bubble-right::after {
    border-left-color: #ffffff;
    border-width: 8px;
}

.css-bubble-right::before {
    border-left-color: #4c7237;
    border-width: 11px;
}

.genre-chip-btn {
    background: #f1f8ed;
    border: 1px solid #4c7237;
    border-radius: 14px;
    padding: 4px 10px;
    font-family: 'Unkempt', cursive;
    font-size: 12px;
    color: #1a3c11;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.genre-chip-btn:hover {
    background: #255719;
    color: #ffffff;
    transform: translateY(-1px);
}

@keyframes pulseCat {
    0% { transform: scale(1); }
    100% { transform: scale(1.06); }
}

/* MOBİL UYARLAMA */
@media (max-width: 768px) {
    .cat-rec-main-card {
        width: 100% !important;
        height: auto !important;
        min-height: 175px !important;
        padding: 12px 10px !important;
        border-radius: 14px !important;
    }

    .cat-img-responsive {
        width: 100px !important;
        height: 100px !important;
    }

    .cat-mobile-col {
        align-items: center !important;
    }

    .cat-mobile-chips {
        justify-content: center !important;
        max-width: 100% !important;
        gap: 4px !important;
    }

    .cat-mobile-chips .genre-chip-btn {
        padding: 3px 8px !important;
        font-size: 11px !important;
    }

    .css-bubble-right {
        font-size: 13px !important;
        padding: 6px 12px !important;
    }

    .css-bubble-right::after, .css-bubble-right::before {
        display: none !important;
    }

    #cat-step-3 {
        gap: 8px !important;
    }

    #cat-step-3 #cat-rec-book-link {
        width: 125px !important;
        padding: 6px 8px !important;
    }

    #cat-step-3 #cat-rec-cover {
        width: 46px !important;
        height: 64px !important;
    }
}
</style>

<div id="cat-rec-card" class="cat-rec-main-card">

    {{-- ADIM 1: Tür Seçimi & Duran Kedi --}}
    <div id="cat-step-1" style="display: flex; align-items: center; justify-content: space-between; gap: 14px; width: 100%;">
        {{-- Sol Kısım: Baloncuk ve Tür Butonları --}}
        <div class="cat-mobile-col" style="flex: 1; display: flex; flex-direction: column; gap: 10px; align-items: flex-end;">
            <div class="css-bubble-right">
                {{ __('what kind of book would u like to read ? :>') }}
            </div>

            {{-- Genişletilmiş Tür Butonları --}}
            <div class="cat-mobile-chips" style="display: flex; flex-wrap: wrap; gap: 6px; justify-content: flex-end; max-width: 380px;">
                <button type="button" onclick="getCatRecommendation('fantasy')" class="genre-chip-btn">{{ __('fantasy') }}</button>
                <button type="button" onclick="getCatRecommendation('classic')" class="genre-chip-btn">{{ __('classics') }}</button>
                <button type="button" onclick="getCatRecommendation('romance')" class="genre-chip-btn">{{ __('romance') }}</button>
                <button type="button" onclick="getCatRecommendation('science_fiction')" class="genre-chip-btn">{{ __('sci-fi') }}</button>
                <button type="button" onclick="getCatRecommendation('historical')" class="genre-chip-btn">{{ __('history') }}</button>
                <button type="button" onclick="getCatRecommendation('horror')" class="genre-chip-btn">{{ __('horror') }}</button>
                <button type="button" onclick="getCatRecommendation('philosophy')" class="genre-chip-btn">{{ __('philosophy') }}</button>
                <button type="button" onclick="getCatRecommendation('random')" class="genre-chip-btn" style="background: #255719; color: #fff;">{{ __('surprize me') }}</button>
            </div>
        </div>

        {{-- Sağ Kısım: Duran Kedi --}}
        <img src="{{ asset('images/talkcat.png') }}" alt="{{ __('Cat') }}" class="cat-img-responsive" style="width: 170px; height: 170px; object-fit: contain; flex-shrink: 0;">
    </div>

    {{-- ADIM 2: Düşünen Kedi --}}
    <div id="cat-step-2" style="display: none; align-items: center; justify-content: center; gap: 20px; width: 100%;">
        <div class="css-bubble-right" style="font-size: 20px; font-weight: bold; padding: 12px 22px;">
            <span id="cat-thinking-text">{{ __('Mmmmm...') }}</span> 💭
        </div>
        <img src="{{ asset('images/thinkcat.png') }}" alt="{{ __('Thinking Cat') }}" class="cat-img-responsive" style="width: 170px; height: 170px; object-fit: contain; flex-shrink: 0; animation: pulseCat 0.9s infinite alternate;">
    </div>

    {{-- ADIM 3: Zıplayan Kedi & Önerilen Kitap --}}
    <div id="cat-step-3" style="display: none; align-items: center; justify-content: space-between; gap: 12px; width: 100%;">
        
        {{-- Sol Kısım: Önerilen Kitap Kutusu --}}
        <a id="cat-rec-book-link" href="#" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; background: #ffffff; border: 1.5px solid #4c7237; padding: 8px 12px; border-radius: 12px; width: 140px; box-sizing: border-box; transition: transform 0.15s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.06); flex-shrink: 0;" onmouseenter="this.style.transform='scale(1.03)'" onmouseleave="this.style.transform='scale(1)'">
            <img id="cat-rec-cover" src="" alt="{{ __('Cover') }}" style="width: 55px; height: 75px; object-fit: cover; border-radius: 4px; border: 1px solid #c2d8b7; margin-bottom: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="text-align: center; width: 100%;">
                <div id="cat-rec-title" style="font-size: 13px; font-weight: bold; color: #1a3c11; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;"></div>
                <div id="cat-rec-author" style="font-size: 11px; color: #527943; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px;"></div>
                <div id="cat-rec-pages" style="font-size: 10px; color: #737e3d; font-weight: bold; margin-top: 3px;"></div>
            </div>
        </a>

        {{-- Orta Kısım: Baloncuk ve Altında Yenile Butonu --}}
        <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; flex: 1;">
            <div class="css-bubble-right" style="font-size: 13px; max-width: 140px; line-height: 1.3; text-align: center;">
                {{ __('i found a book for u! hope u like it :3') }}
            </div>
            
            <button type="button" onclick="resetCatRecommendation()" title="{{ __('Choose another genre') }}" style="background: #eef6ea; border: 1.5px solid #4c7237; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 15px; color: #2d5a27; transition: transform 0.15s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.08);" onmouseenter="this.style.transform='rotate(90deg)'" onmouseleave="this.style.transform='rotate(0deg)'">
                🔄
            </button>
        </div>

        {{-- Sağ Kısım: Zıplayan Kedi --}}
        <img src="{{ asset('images/jumpcat.png') }}" alt="{{ __('Jumping Cat') }}" class="cat-img-responsive" style="width: 170px; height: 170px; object-fit: contain; flex-shrink: 0;">

    </div>
</div>

<script>
window.catThinkInterval = window.catThinkInterval || null;

window.getCatRecommendation = function(genre) {
    const step1 = document.getElementById('cat-step-1');
    const step2 = document.getElementById('cat-step-2');
    const step3 = document.getElementById('cat-step-3');
    const thinkText = document.getElementById('cat-thinking-text');

    if (!step1 || !step2 || !step3) return;

    step1.style.display = 'none';
    step2.style.display = 'flex';

    let dots = 0;
    if (window.catThinkInterval) clearInterval(window.catThinkInterval);
    window.catThinkInterval = setInterval(() => {
        dots = (dots + 1) % 4;
        if (thinkText) thinkText.innerText = 'Mmmmm' + '.'.repeat(dots);
    }, 400);

    fetch(`/api/random-book-recommendation?genre=${encodeURIComponent(genre)}`)
        .then(res => res.json())
        .then(data => {
            clearInterval(window.catThinkInterval);
            if (data.success && data.book) {
                const coverEl = document.getElementById('cat-rec-cover');
                const titleEl = document.getElementById('cat-rec-title');
                const authorEl = document.getElementById('cat-rec-author');
                const linkEl = document.getElementById('cat-rec-book-link');
                const pagesEl = document.getElementById('cat-rec-pages');

                if (coverEl) coverEl.src = data.book.cover || 'https://covers.openlibrary.org/b/id/10849922-M.jpg';
                if (titleEl) titleEl.innerText = data.book.title || '';
                if (authorEl) authorEl.innerText = data.book.author || '';
                if (linkEl) linkEl.href = data.book.url || `/books/${data.book.id}`;
                
                const pageCount = data.book.page_count || data.book.pages || data.book.number_of_pages;
                if (pagesEl) pagesEl.innerText = pageCount ? `📖 ${pageCount} p.` : '';

                step2.style.display = 'none';
                step3.style.display = 'flex';
            } else {
                alert(@json(__('No recommendation found, wanna try again?')));
                window.resetCatRecommendation();
            }
        })
        .catch(err => {
            clearInterval(window.catThinkInterval);
            console.error(err);
            window.resetCatRecommendation();
        });
};

window.resetCatRecommendation = function() {
    if (window.catThinkInterval) clearInterval(window.catThinkInterval);
    const step1 = document.getElementById('cat-step-1');
    const step2 = document.getElementById('cat-step-2');
    const step3 = document.getElementById('cat-step-3');

    if (step3) step3.style.display = 'none';
    if (step2) step2.style.display = 'none';
    if (step1) step1.style.display = 'flex';
};
</script>