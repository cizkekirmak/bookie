@php
    $book = $item->book ?? null;
    if (!$book) {
        return;
    }

    $bookKey = $book->open_library_key 
        ?? $book->google_book_id 
        ?? $book->id;

    $coverSrc = $book->cover_image ?? null;
    if ($coverSrc && !str_starts_with($coverSrc, 'http')) {
        $coverSrc = asset($coverSrc);
    }

    // Sayfa ve İlerleme Hesaplaması
    $totalPages = $book->page_count ?? 0;
    $currentPage = $item->current_page ?? 0;
    $hasProgress = $item->status === 'reading' && $currentPage > 0;
    $pct = ($totalPages > 0 && $currentPage > 0) ? min(100, round(($currentPage / $totalPages) * 100)) : null;
@endphp

<div class="book-card-item" 
     data-status="{{ $item->status }}"
     style="background: #ffffff; border: 1.5px solid #d4e5cb; border-radius: 12px; padding: 12px; display: flex; gap: 12px; align-items: flex-start; box-shadow: 0 2px 5px rgba(0,0,0,0.03); position: relative; width: 100%;">
     
    @if($isOwnProfile ?? false)
        <div style="display: flex; align-items: center; justify-content: center; flex-shrink: 0; padding-top: 2px;">
            <input type="checkbox" 
                   name="selected_books[]" 
                   value="{{ $item->id }}" 
                   class="book-select-checkbox" 
                   onchange="updateDeleteButtonState()"
                   style="width: 16px; height: 16px; cursor: pointer; accent-color: #2d5a27;">
        </div>
    @endif

    {{-- Kapak Görseli --}}
    <a href="{{ route('show', $bookKey) }}" class="book-cover-link" style="flex-shrink: 0; width: 55px; height: 80px; display: block; overflow: hidden; border-radius: 6px; border: 1px solid #c2d8b7;">
        <img src="{{ $coverSrc ?: asset('images/default-book.png') }}" 
             alt="{{ $book->title ?? __('Book') }}"
             style="width: 100%; height: 100%; object-fit: cover;"
             onerror="this.onerror=null; this.src='{{ asset('images/default-book.png') }}';">
    </a>

    {{-- Bilgiler & Badge --}}
    <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 4px;">
        
        {{-- Başlık + Durum Rozeti --}}
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 6px; width: 100%;">
            <div style="min-width: 0; flex: 1;">
                <h4 style="margin: 0 0 2px 0; font-size: 15px; color: #1a3c11; line-height: 1.2; word-break: break-word;">
                    <a href="{{ route('show', $bookKey) }}" style="text-decoration: none; color: inherit;">
                        {{ $book->title ?? __('Unknown Book') }}
                    </a>
                </h4>
                <span style="font-size: 12px; color: #527943; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $book->author ?? __('Unknown Author') }}
                </span>
            </div>

            {{-- Durum Rozeti (read, to read, currently reading) --}}
            <span style="font-size: 10px; padding: 2px 6px; border-radius: 5px; font-weight: bold; background: #eaf3e4; color: #2d5a27; border: 1px solid #c2d8b7; white-space: nowrap; flex-shrink: 0;">
                @if($item->status === 'reading') {{ __('reading') }}
                @elseif($item->status === 'read') {{ __('read') }}
                @elseif($item->status === 'toRead' || $item->status === 'want_to_read') {{ __('to read') }}
                @else {{ __($item->status) }}
                @endif
            </span>
        </div>

        {{-- İlerleme Çubuğu ve Sayfa Bilgisi --}}
        @if($hasProgress)
            <div style="width: 100%; margin-top: 2px; margin-bottom: 2px;">
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 11px; color: #3b612d; font-weight: bold; margin-bottom: 2px;">
                    @if($pct !== null)
                        <span>%{{ $pct }}</span>
                        <span style="font-weight: normal; color: #666; font-size: 10px;">{{ $currentPage }} / {{ $totalPages }} {{ __('p.') }}</span>
                    @else
                        <span>{{ __('p.') }} {{ $currentPage }}</span>
                    @endif
                </div>
                @if($pct !== null)
                    <div style="width: 100%; height: 5px; background: #eaf3e4; border: 1px solid #c2d8b7; border-radius: 4px; overflow: hidden;">
                        <div style="width: {{ $pct }}%; height: 100%; background: #2d5a27; border-radius: 4px;"></div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Renkli Yıldızlar --}}
        @if($item->rating)
            @php
                $starColor = match((int)$item->rating) {
                     1 => '#d43b82',
                        2 => '#e67e22',
                        3 => '#fee16c',
                        4 => '#8dd04e',
                        5 => '#3a91bc',
                    default => '#e5a00d',
                };
            @endphp
            <div style="font-size: 14px; display: flex; align-items: center; gap: 2px; line-height: 1; margin-top: 2px;">
                @for($i = 1; $i <= 5; $i++)
                    <span style="color: {{ $i <= $item->rating ? $starColor : '#dcdfd5' }};">
                        ★
                    </span>
                @endfor
            </div>
        @endif

        {{-- Yorum --}}
        @if(!empty($item->review))
            <p style="margin: 4px 0 0 0; font-size: 12px; color: #333; line-height: 1.35; background: #fbfdf9; padding: 6px 8px; border-radius: 6px; border-left: 3px solid #8ec46f; word-break: break-word;">
                "{{ $item->review }}"
            </p>
        @endif
    </div>
</div>