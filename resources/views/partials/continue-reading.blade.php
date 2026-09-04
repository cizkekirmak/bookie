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

    .continue-book-cover {
        width: 90px;
        height: 140px;
        object-fit: cover;
        border-radius: 8px;
        border: 1.5px solid #2d5a27;
        background: #eaf3e4;
        display: block;
    }

    .continue-book-info {
        overflow: hidden;
        flex: 1;
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
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 13px;
        font-family: 'Unkempt', cursive;
        font-weight: bold;
        transition: transform 0.15s ease;
    }

    .continue-btn:hover {
        transform: scale(1.05);
    }

    @media (max-width: 1024px) {
        .continue-reading-card {
            width: 100% !important;
            height: 175px !important;
            padding: 10px 10px !important;
            border-radius: 14px !important;
            justify-content: space-between !important;
        }

        .continue-reading-body {
            align-items: center !important;
            justify-content: center !important;
            flex: 1 !important;
            margin-top: -4px !important;
        }

        .continue-book-cover {
            width: 54px !important;
            height: 84px !important;
        }

        .continue-book-info {
            height: auto !important;
            min-height: 84px !important;
            justify-content: center !important;
            gap: 4px !important;
        }

        .continue-reading-card h4 {
            font-size: 13px !important;
            margin-bottom: 0px !important;
            line-height: 1.2 !important;
        }

        .continue-reading-card p {
            font-size: 11px !important;
            margin-bottom: 2px !important;
            line-height: 1.2 !important;
        }

        .continue-btn {
            padding: 3px 10px !important;
            font-size: 11px !important;
            border-radius: 8px !important;
            margin-top: 2px !important;
        }
    }
</style>

<div class="continue-reading-card">
    
    <span style="
        font-family: 'Henny Penny', cursive;
        font-size: 15px;
        color: #1a3c11;
        margin-bottom: 4px;
        display: block;
        font-weight: normal;
        flex-shrink: 0;">
        {{ __('continue?') }}
    </span>

    <div class="continue-reading-body" style="display: flex; gap: 10px; align-items: center; flex: 1;">
        @if($currentBook)
            <a href="{{ route('show', $currentBook->google_book_id ?? $currentBook->open_library_key) }}" style="flex-shrink: 0; line-height: 0;">
                @if(!empty($currentBook->cover_image))
                    <img src="{{ $currentBook->cover_image }}" 
                         alt="{{ $currentBook->title }}" 
                         loading="eager"
                         decoding="sync"
                         fetchpriority="high"
                         referrerpolicy="no-referrer" 
                         class="continue-book-cover">
                @else
                    <div class="continue-book-cover" style="display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        📖
                    </div>
                @endif
            </a>

            {{-- Kitap Bilgileri --}}
            <div class="continue-book-info">
                <div>
                    <h4 style="color: #1a3c11; font-size: 15px; margin: 0 0 2px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: bold; font-family: 'Unkempt', cursive;">
                        {{ $currentBook->title }}
                    </h4>
                    <p style="color: #3b612d; font-size: 12px; margin: 0 0 4px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'Unkempt', cursive;">
                        {{ $currentBook->author }}
                    </p>

                    @if($hasPercentage)
                        <div style="margin-bottom: 2px;">
                            <div style="display: flex; justify-content: space-between; font-size: 10px; color: #1a3c11; font-weight: bold; margin-bottom: 1px;">
                                <span>%{{ $pct }}</span>
                                <span style="font-weight: normal; color: #3b612d;">{{ $current }}/{{ $total }} {{ __('p.') }}</span>
                            </div>
                            <div style="width: 100%; height: 5px; background-color: #eaf3e4; border: 1px solid #737e3d; border-radius: 6px; overflow: hidden;">
                                <div style="width: {{ $pct }}%; height: 100%; background: #2d5a27; border-radius: 6px; transition: width 0.4s ease;"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <a href="{{ route('show', $currentBook->google_book_id ?? $currentBook->open_library_key) }}" class="continue-btn">
                    {{ __('Read →') }}
                </a>
            </div>
        @else
            <div style="text-align: center; width: 100%; color: #3b612d; font-size: 12px; font-family: 'Unkempt', cursive;">
                <p style="margin: 0 0 4px 0;">{{ __('No books currently being read.') }}</p>
                <span style="font-size: 18px;">📚</span>
            </div>
        @endif
    </div>
</div>