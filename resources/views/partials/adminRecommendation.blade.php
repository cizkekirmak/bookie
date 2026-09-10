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

    .admin-card-title {
        font-family: 'Henny Penny', cursive;
        font-size: 16.5px;
        color: #1a3c11;
        margin: 0 0 6px 0;
        display: block;
        font-weight: normal;
        flex-shrink: 0;
    }

    .admin-recommendation-body {
        display: flex;
        gap: 10px;
        align-items: center;
        flex: 1;
        min-height: 0;
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
        min-width: 0;
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
        padding: 5px 12px;
        border-radius: 12px;
        font-size: 14px;
        font-family: 'Unkempt', cursive;
        font-weight: bold;
        transition: transform 0.15s ease;
        white-space: nowrap;
    }

    .admin-btn:hover {
        transform: scale(1.05);
    }

    .admin-btn-text-mobile { display: none; }
    .admin-btn-text-desktop { display: inline; }

    @media (max-width: 1024px) {
        .adminRecommendation-card {
            width: 100% !important;
            height: 150px !important; /* Kare küçültüldü */
            padding: 8px 10px !important;
            border-radius: 14px !important;
            box-sizing: border-box !important;
            justify-content: flex-start !important;
        }

        .admin-card-title {
            font-size: 15px !important;
            margin-bottom: 2px !important;
            line-height: 1.1 !important;
            height: 16px !important;
        }

        .admin-recommendation-body {
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            flex: 1 !important;
            padding-top: 4px !important; /* Başlıktan aşağı indirildi */
            min-height: 0 !important;
        }

        .admin-row-top {
            display: flex !important;
            gap: 8px !important;
            align-items: flex-start !important;
            width: 100% !important;
        }

        .admin-book-cover-link {
            width: 50px !important;
            height: 72px !important;
            border-radius: 5px !important;
            flex-shrink: 0 !important;
        }

        .admin-book-info {
            height: auto !important;
            min-height: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
            flex: 1 !important;
        }

        .adminRecommendation-card h4 {
            font-size: 13.5px !important;
            margin: 0 0 2px 0 !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .adminRecommendation-card p {
            font-size: 11.5px !important;
            margin: 0 !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        /* Not kapağın altındaki tam satıra taşındı, ferahladı */
        .admin-note-text {
            font-size: 11px !important;
            line-height: 1.15 !important;
            margin: 2px 0 0 0 !important;
            color: #27491d !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            word-break: break-word !important;
        }

        .admin-btn-text-mobile { display: inline !important; }
        .admin-btn-text-desktop { display: none !important; }

        .admin-btn {
            padding: 3px 10px !important;
            font-size: 11.5px !important;
            border-radius: 6px !important;
            align-self: flex-start !important;
            margin-top: 6px !important; /* Buton yukarı çekildi */
        }
    }
</style>

<div class="adminRecommendation-card">
    <span class="admin-card-title">{{ __('admin recommends!') }}</span>

    <div class="admin-recommendation-body">
        <div class="admin-row-top">
            <a href="{{ route('show', $adminBookKey) }}" class="admin-book-cover-link">
                @if(!empty($coverSrc))
                    <img src="{{ $coverSrc }}" 
                         alt="{{ $adminRecommendation->title ?? __('Book Cover') }}"
                         style="width: 100%; height: 100%; object-fit: cover; display: block;"
                         onerror="this.onerror=null; this.src='https://covers.openlibrary.org/b/id/10849922-M.jpg';">
                @else
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        📖
                    </div>
                @endif
            </a>

            <div class="admin-book-info">
                <h4 style="color: #1a3c11; font-size: 16px; margin: 0 0 2px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: bold; font-family: 'Unkempt', cursive;">
                    {{ $adminRecommendation->title }}
                </h4>
                <p style="color: #3b612d; font-size: 13.5px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'Unkempt', cursive;">
                    {{ $adminRecommendation->authors ?? $adminRecommendation->author }}
                </p>

                @if(!empty($adminRecommendation->admin_note))
                    <p class="admin-note-text" style="font-size: 13px; color: #1a3c11; font-style: italic; margin: 2px 0 0 0; line-height: 1.25; font-family: 'Unkempt', cursive;">
                        "{{ $adminRecommendation->admin_note }}"
                    </p>
                @endif
            </div>
        </div>

        <a href="{{ route('show', $adminBookKey) }}" class="admin-btn">
            <span class="admin-btn-text-desktop">{{ __('view book →') }}</span>
            <span class="admin-btn-text-mobile">{{ __('view →') }}</span>
        </a>
    </div>
</div>
@endif