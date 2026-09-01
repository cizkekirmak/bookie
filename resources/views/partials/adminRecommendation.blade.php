@if($adminRecommendation)
@php
    $adminBookKey = $adminRecommendation->open_library_key 
        ?? $adminRecommendation->google_book_id 
        ?? $adminRecommendation->book_key 
        ?? $adminRecommendation->book_id 
        ?? $adminRecommendation->id;

    $coverSrc = $adminRecommendation->cover_image ?? $adminRecommendation->cover_url ?? null;
    if ($coverSrc && !str_starts_with($coverSrc, 'http')) {
        $coverSrc = asset($coverSrc);
    }
@endphp

<style>
    /* MASAÜSTÜ: Birebir Orijinal Hali */
    .adminRecommendation-card {
        background: #cae28c;
        border: 2px solid #5a8c69; 
        border-radius: 16px;
        padding: 14px;
        display: flex;
        flex-direction: column;
        height: 220px;
        width: 320px;
        box-sizing: border-box;
    }

    .admin-book-cover-link {
        flex-shrink: 0;
        width: 90px;
        height: 140px;
        display: block;
        overflow: hidden;
        border-radius: 8px;
        border: 1.5px solid #2d5a27;
        background: #eaf3e4;
    }

    .admin-book-info {
        overflow: hidden;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 140px;
    }

    .admin-btn {
        align-self: flex-start;
        background: #255719;
        color: #fff;
        text-decoration: none;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 13px;
        font-family: 'Unkempt', cursive;
        font-weight: bold;
        transition: transform 0.15s ease;
    }

    .admin-btn:hover {
        transform: scale(1.05);
    }

    /* MOBİL UYARLAMA */
    @media (max-width: 1024px) {
        .adminRecommendation-card {
            width: 100% !important;
            height: 175px !important;
            padding: 10px 8px !important;
            border-radius: 14px !important;
        }

        .admin-book-cover-link {
            width: 52px !important;
            height: 80px !important;
        }

        .admin-book-info {
            height: 80px !important;
            justify-content: space-between !important;
        }

        .adminRecommendation-card h4 {
            font-size: 12px !important;
            margin-bottom: 2px !important;
        }

        .adminRecommendation-card p {
            font-size: 10px !important;
            margin-bottom: 3px !important;
        }

        .admin-note-text {
            -webkit-line-clamp: 1 !important;
            font-size: 9px !important;
        }

        .admin-btn {
            padding: 3px 8px !important;
            font-size: 11px !important;
            border-radius: 8px !important;
        }
    }
</style>

<div class="adminRecommendation-card">

    <span style="
        font-family: 'Henny Penny', cursive;
        font-size: 15px;
        color: #1a3c11;
        margin-bottom: 6px;
        display: block;
        font-weight: normal;">
        {{ __('admin recommends!') }}
    </span>

    <div style="display: flex; gap: 10px; align-items: center; flex: 1;">
        <a href="{{ route('show', $adminBookKey) }}" class="admin-book-cover-link">
            @if(!empty($coverSrc))
                <img src="{{ $coverSrc }}" 
                     alt="{{ $adminRecommendation->title ?? __('Book Cover') }}"
                     style="width: 100%; height: 100%; object-fit: cover; display: block;"
                     onerror="this.onerror=null; this.src='https://covers.openlibrary.org/b/id/10849922-M.jpg';">
            @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    📖
                </div>
            @endif
        </a>

        {{-- Kitap Bilgileri --}}
        <div class="admin-book-info">
            <div>
                <h4 style="color: #1a3c11; font-size: 15px; margin: 0 0 2px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: bold; font-family: 'Unkempt', cursive;">
                    {{ $adminRecommendation->title }}
                </h4>
                <p style="color: #3b612d; font-size: 12px; margin: 0 0 4px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'Unkempt', cursive;">
                    {{ $adminRecommendation->authors ?? $adminRecommendation->author }}
                </p>
            </div>

            @if(!empty($adminRecommendation->admin_note))
                <p class="admin-note-text" style="font-size: 11px; color: #1a3c11; font-style: italic; margin: 0; line-height: 1.2; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; font-family: 'Unkempt', cursive;">
                    "{{ $adminRecommendation->admin_note }}"
                </p>
            @endif

            <a href="{{ route('show', $adminBookKey) }}" class="admin-btn">
                {{ __('Go to Book →') }}
            </a>
        </div>
    </div>
</div>
@endif