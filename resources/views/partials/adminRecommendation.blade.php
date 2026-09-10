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

    @media (max-width: 1024px) {
        .adminRecommendation-card {
            width: 100% !important;
            height: 168px !important;
            padding: 10px 10px !important;
            border-radius: 14px !important;
            box-sizing: border-box !important;
        }

        .admin-card-title {
            font-size: 15px !important;
            margin-bottom: 8px !important;
            line-height: 1 !important;
            height: 16px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .admin-recommendation-body {
            display: flex !important;
            gap: 8px !important;
            align-items: stretch !important;
            height: 116px !important;
            min-height: 116px !important;
        }

        .admin-book-cover-link {
            width: 62px !important;
            height: 96px !important;
            border-radius: 6px !important;
            flex-shrink: 0 !important;
        }

        .admin-book-info {
            height: 116px !important;
            min-height: 116px !important;
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

        .admin-note-text {
            font-size: 11px !important;
            line-height: 1.2 !important;
            margin: 3px 0 0 0 !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            color: #27491d !important;
        }

        .admin-btn {
            padding: 4px 10px !important;
            font-size: 12px !important;
            border-radius: 8px !important;
            margin-top: auto !important; /* Butonu en alt tabana kilitler */
            align-self: flex-start !important;
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
            <div>
                <h4 style="color: #1a3c11; font-size: 16px; margin: 0 0 2px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: bold; font-family: 'Unkempt', cursive;">
                    {{ $adminRecommendation->title }}
                </h4>
                <p style="color: #3b612d; font-size: 13.5px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'Unkempt', cursive;">
                    {{ $adminRecommendation->authors ?? $adminRecommendation->author }}
                </p>

                @if(!empty($adminRecommendation->admin_note))
                    <p class="admin-note-text" style="font-size: 13px; color: #1a3c11; font-style: italic; margin: 3px 0 0 0; line-height: 1.25; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; font-family: 'Unkempt', cursive;">
                        "{{ $adminRecommendation->admin_note }}"
                    </p>
                @endif
            </div>

            <a href="{{ route('show', $adminBookKey) }}" class="admin-btn">
                {{ __('view book →') }}
            </a>
        </div>
    </div>
</div>
@endif