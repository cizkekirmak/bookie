<style>
    .popular-books-card {
        background: #cae28c;
        border: 2px solid #5a8c69;
        border-radius: 16px;
        padding: 14px;
        display: flex;
        flex-direction: column;
        height: 220px;
        width: 620px;
        box-sizing: border-box;
    }

    .popular-books-list {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 6px;
    }

    .popular-book-single-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 95px;
        min-width: 68px;
        text-align: center;
        cursor: pointer;
        transition: transform 0.15s ease;
    }

    .popular-book-img {
        width: 70px;
        height: 110px;
        object-fit: cover;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        background: #e8f0dc;
    }

    @media (max-width: 1024px) {
        .popular-books-card {
            width: 100% !important;
            height: auto !important;
            padding: 12px 10px !important;
            border-radius: 14px !important;
        }

        .popular-books-card h3 {
            margin: 0 0 10px 0 !important;
            font-size: 14px !important;
            text-align: left !important;
        }

        .popular-books-list {
            flex-direction: row !important;
            justify-content: flex-start !important;
            align-items: flex-start !important;
            gap: 12px !important;
            overflow-x: auto !important;
            overflow-y: hidden !important;
            width: 100% !important;
            padding-bottom: 6px !important;
            -webkit-overflow-scrolling: touch;
        }

        .popular-book-single-item {
            width: 80px !important;
            min-width: 80px !important;
            flex-shrink: 0 !important;
        }

        .popular-book-img {
            width: 74px !important;
            height: 108px !important;
        }

        .popular-book-single-item div {
            max-width: 80px !important;
        }
    }
</style>

<div class="popular-books-card">
    <h3 style="font-family: 'Henny Penny', cursive; color: #1a3c11; font-size: 15px; margin: 0 0 14px 0; font-weight: normal;">
        {{ __('Popular books this week:') }}
    </h3>

    <div class="popular-books-list">
        @forelse($popularBooks as $pBook)
            <div onclick="window.location.href='/books/{{ $pBook['id'] }}'" 
                 class="popular-book-single-item"
                 onmouseenter="this.style.transform='translateY(-3px)'"
                 onmouseleave="this.style.transform='translateY(0)'">
                
                <img src="{{ $pBook['cover'] }}" 
                     alt="{{ $pBook['title'] }}" 
                     loading="lazy"
                     referrerpolicy="no-referrer"
                     onerror="this.onerror=null; this.src='https://via.placeholder.com/120x180?text=No+Cover';"
                     class="popular-book-img">

                <div style="font-family: 'Unkempt', cursive; font-size: 11px; font-weight: bold; color: #1f5117; width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 5px;" title="{{ $pBook['title'] }}">
                    {{ $pBook['title'] }}
                </div>

                <div style="font-family: 'Unkempt', cursive; font-size: 10px; color: #666; width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $pBook['author'] }}">
                    {{ $pBook['author'] }}
                </div>
            </div>
        @empty
            <div style="font-family: 'Unkempt', cursive; font-size: 12px; color: #777; width: 100%; text-align: center;">
                {{ __("Couldn't load the popular books.") }}
            </div>
        @endforelse
    </div>
</div>