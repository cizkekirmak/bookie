@php
    $userBook = auth()->check()
        ? \App\Models\UserBook::with('book')
            ->where('user_id', auth()->id())
            ->where('status', 'reading')
            ->latest('updated_at')
            ->first()
        : null;

    $currentBook = $userBook ? $userBook->book : null;

    $total = $currentBook ? ($currentBook->page_count ?? 0) : 0;
    $current = $userBook ? ($userBook->current_page ?? 0) : 0;
    $hasPercentage = ($total > 0 && $current > 0);
    $pct = $hasPercentage ? min(100, round(($current / $total) * 100)) : null;
@endphp

<style>
    .continue-reading-card {
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

    .continue-card-title {
        font-family: 'Henny Penny', cursive;
        font-size: 16.5px;
        color: #1a3c11;
        margin: 0 0 10px 0;
        display: block;
        font-weight: normal;
        flex-shrink: 0;
    }

    .continue-reading-body {
        display: flex;
        gap: 12px;
        align-items: center;
        flex: 1;
        min-height: 0;
    }

    .continue-book-cover {
        width: 90px;
        height: 140px;
        object-fit: cover;
        border-radius: 8px;
        border: 1.5px solid #2d5a27;
        background: #eaf3e4;
        display: block;
        flex-shrink: 0;
    }

    .continue-book-info {
        overflow: hidden;
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 140px;
    }

    .continue-btn {
        align-self: flex-start;
        background: #2d5a27;
        color: white;
        text-decoration: none;
        padding: 5px 12px;
        border-radius: 12px;
        font-size: 14px;
        font-family: 'Unkempt', cursive;
        font-weight: bold;
        transition: transform 0.15s ease;
        white-space: nowrap;
    }

    .continue-btn:hover {
        transform: scale(1.05);
    }

    .btn-text-mobile { display: none; }
    .btn-text-desktop { display: inline; }

    @media (max-width: 1024px) {
        .continue-reading-card {
            width: 100% !important;
            height: 152px !important;
            padding: 8px 8px !important;
            border-radius: 14px !important;
        }

        .continue-card-title {
            font-size: 14px !important;
            margin: 0 0 4px 0 !important;
            height: 16px !important;
            line-height: 16px !important;
        }

        .continue-reading-body {
            gap: 8px !important;
            align-items: flex-start !important;
            height: 114px !important;
        }

        .continue-book-cover {
            width: 58px !important;
            height: 88px !important;
            border-radius: 6px !important;
        }

        .continue-book-info {
            height: 114px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
        }

        .continue-reading-card h4 {
            font-size: 13.5px !important;
            margin: 0 0 2px 0 !important;
            line-height: 1.15 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .continue-reading-card p {
            font-size: 11.5px !important;
            margin: 0 0 4px 0 !important;
            line-height: 1.15 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .btn-text-mobile { display: inline !important; }
        .btn-text-desktop { display: none !important; }

        .continue-btn {
            padding: 3px 8px !important;
            font-size: 11.5px !important;
            border-radius: 6px !important;
            margin-top: 4px !important;
        }
    }
</style>

<div class="continue-reading-card">
    <span class="continue-card-title">{{ __('continue?') }}</span>

    <div class="continue-reading-body">
        @if($currentBook)
            <a href="{{ route('show', $currentBook->google_book_id ?? $currentBook->open_library_key) }}" style="flex-shrink: 0; line-height: 0;">
                @if(!empty($currentBook->cover_image))
                    <img src="{{ $currentBook->cover_image }}" 
                         alt="{{ $currentBook->title }}" 
                         loading="eager"
                         decoding="sync"
                         referrerpolicy="no-referrer" 
                         class="continue-book-cover">
                @else
                    <div class="continue-book-cover" style="display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        📖
                    </div>
                @endif
            </a>

            <div class="continue-book-info">
                <div>
                    <h4 style="color: #1a3c11; font-size: 16px; margin: 0 0 2px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: bold; font-family: 'Unkempt', cursive;">
                        {{ $currentBook->title }}
                    </h4>
                    <p style="color: #3b612d; font-size: 13.5px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'Unkempt', cursive;">
                        {{ $currentBook->author }}
                    </p>

                    @if($hasPercentage)
                        <div style="margin-top: 4px;">
                            <div style="display: flex; justify-content: space-between; font-size: 10.5px; color: #1a3c11; font-weight: bold; margin-bottom: 2px;">
                                <span>%{{ $pct }}</span>
                                <span style="font-weight: normal; color: #3b612d;">{{ $current }}/{{ $total }} {{ __('p.') }}</span>
                            </div>
                            <div style="width: 100%; height: 4px; background-color: #eaf3e4; border: 1px solid #737e3d; border-radius: 6px; overflow: hidden;">
                                <div style="width: {{ $pct }}%; height: 100%; background: #2d5a27; border-radius: 6px;"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <a href="{{ route('show', $currentBook->google_book_id ?? $currentBook->open_library_key) }}" class="continue-btn">
                    <span class="btn-text-desktop">{{ __('view book →') }}</span>
                    <span class="btn-text-mobile">{{ __('view →') }}</span>
                </a>
            </div>
        @else
            <div style="text-align: center; width: 100%; color: #3b612d; font-size: 13px; font-family: 'Unkempt', cursive; margin: auto 0;">
                <p style="margin: 0 0 3px 0;">{{ __('No books currently being read.') }}</p>
                <span style="font-size: 18px;">📚</span>
            </div>
        @endif
    </div>
</div>