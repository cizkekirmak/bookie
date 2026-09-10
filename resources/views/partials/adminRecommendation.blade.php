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
        height: 227px;
        width: 320px;
        box-sizing: border-box;
        position: relative;
    }

    .admin-card-title {
        font-family: 'Henny Penny', cursive;
        font-size: 16.5px;
        color: #1a3c11;
        margin: 0 0 10px 0;
        display: block;
        font-weight: normal;
        flex-shrink: 0;
    }

    .admin-recommendation-body {
        display: flex;
        gap: 12px;
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
            height: 154px !important;
            padding: 9px 8px !important;
            border-radius: 14px !important;
            box-sizing: border-box !important;
        }

        .admin-card-title {
            font-size: 14px !important;
            margin: 0 0 6px 0 !important;
            height: 16px !important;
            line-height: 16px !important;
        }

        .admin-recommendation-body {
            gap: 8px !important;
            align-items: flex-start !important;
            height: 110px !important;
        }

        .admin-book-cover-link {
            width: 58px !important;
            height: 88px !important;
            border-radius: 6px !important;
            flex-shrink: 0 !important;
        }

        .admin-book-info {
            height: 88px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
            position: relative !important;
        }

        .adminRecommendation-card h4 {
            font-size: 13px !important;
            margin: 0 0 1px 0 !important;
            line-height: 1.15 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .adminRecommendation-card p.admin-author-text {
            font-size: 11px !important;
            margin: 0 0 2px 0 !important;
            line-height: 1.15 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .admin-note-text {
            font-size: 10px !important;
            line-height: 1.15 !important;
            margin: 0 !important;
            color: #27491d !important;
            font-style: italic !important;
            font-family: 'Unkempt', cursive !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
            display: block !important;
            max-height: 28px !important;
            overflow: hidden !important;
        }

        .admin-btn-text-mobile { display: inline !important; }
        .admin-btn-text-desktop { display: none !important; }

        /* Buton tam continue-reading ile aynı seviyeye (bottom: 9px) kilitlendi */
        .admin-btn {
            position: absolute !important;
            bottom: 9px !important;
            left: 74px !important;
            padding: 3px 8px !important;
            font-size: 11.5px !important;
            border-radius: 6px !important;
            margin: 0 !important;
        }
    }
</style>

<div class="adminRecommendation-card">
    <span class="admin-card-title">{{ __('admin recommends!') }}</span>

    <div class="admin-recommendation-body">
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

        <div class="admin-book-info">
            <h4 style="color: #1a3c11; font-size: 16px; margin: 0 0 2px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: bold; font-family: 'Unkempt', cursive;">
                {{ $adminRecommendation->title }}
            </h4>
            <p class="admin-author-text" style="color: #3b612d; font-size: 13.5px; margin: 0 0 4px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'Unkempt', cursive;">
                {{ $adminRecommendation->authors ?? $adminRecommendation->author }}
            </p>

            @if(!empty($adminRecommendation->admin_note))
                <div class="admin-note-text">
                    "{{ $adminRecommendation->admin_note }}"
                </div>
            @endif

            <a href="{{ route('show', $adminBookKey) }}" class="admin-btn">
                <span class="admin-btn-text-desktop">{{ __('view book →') }}</span>
                <span class="admin-btn-text-mobile">{{ __('view →') }}</span>
            </a>
        </div>
    </div>
</div>
@endif