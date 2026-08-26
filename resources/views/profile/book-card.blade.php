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
@endphp

<div class="book-card-item" 
     data-status="{{ $item->status }}"
     style="background: #ffffff; border: 1.5px solid #d4e5cb; border-radius: 12px; padding: 14px; display: flex; gap: 16px; align-items: flex-start; box-shadow: 0 2px 5px rgba(0,0,0,0.03); position: relative;">
     
    @if($isOwnProfile ?? false)
        <div style="display: flex; align-items: center; justify-content: center; flex-shrink: 0; padding-top: 4px;">
            <input type="checkbox" 
                   name="selected_books[]" 
                   value="{{ $item->id }}" 
                   class="book-select-checkbox" 
                   onchange="updateDeleteButtonState()"
                   style="width: 18px; height: 18px; cursor: pointer; accent-color: #2d5a27;">
        </div>
    @endif

    {{-- Kapak Görseli --}}
    <a href="{{ route('show', $bookKey) }}" style="flex-shrink: 0; width: 70px; height: 100px; display: block; overflow: hidden; border-radius: 6px; border: 1px solid #c2d8b7;">
        <img src="{{ $coverSrc ?: asset('images/default-book.png') }}" 
             alt="{{ $book->title ?? 'Kitap' }}"
             style="width: 100%; height: 100%; object-fit: cover;"
             onerror="this.onerror=null; this.src='{{ asset('images/default-book.png') }}';">
    </a>

    {{-- Bilgiler & Badge --}}
    <div style="flex: 1; min-width: 0;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px; gap: 8px;">
            <div>
                <h4 style="margin: 0 0 2px 0; font-size: 16px; color: #1a3c11;">
                    <a href="{{ route('show', $bookKey) }}" style="text-decoration: none; color: inherit;">
                        {{ $book->title ?? 'Bilinmeyen Kitap' }}
                    </a>
                </h4>
                <span style="font-size: 13px; color: #527943;">
                    {{ $book->author ?? 'Bilinmeyen Yazar' }}
                </span>
            </div>

            <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                <span style="font-size: 11px; padding: 3px 8px; border-radius: 6px; font-weight: bold; background: #eaf3e4; color: #2d5a27; border: 1px solid #c2d8b7;">
                    @if($item->status === 'reading') currently reading
                    @elseif($item->status === 'read') read
                    @elseif($item->status === 'toRead') to read
                    @else {{ $item->status }}
                    @endif
                </span>
            </div>
        </div>

        {{-- Renkli Yıldızlar --}}
        @if($item->rating)
            @php
                $starColor = match((int)$item->rating) {
                    5 => '#e5a00d', // 5 Yıldız: Altın Sarısı
                    4 => '#7fa638', // 4 Yıldız: Fıstık Yeşili
                    3 => '#e07b22', // 3 Yıldız: Sıcak Turuncu
                    2 => '#d45d43', // 2 Yıldız: Mercan
                    1 => '#c23b3b', // 1 Yıldız: Kırmızı
                    default => '#e5a00d',
                };
            @endphp
            <div style="margin-bottom: 6px; font-size: 15px; display: flex; align-items: center; gap: 2px;">
                @for($i = 1; $i <= 5; $i++)
                    <span style="color: {{ $i <= $item->rating ? $starColor : '#dcdfd5' }}; line-height: 1;">
                        ★
                    </span>
                @endfor
            </div>
        @endif

        {{-- Yorum --}}
        @if(!empty($item->review))
            <p style="margin: 0; font-size: 13px; color: #333; line-height: 1.4; background: #fbfdf9; padding: 8px 10px; border-radius: 6px; border-left: 3px solid #8ec46f;">
                "{{ $item->review }}"
            </p>
        @endif
    </div>
</div>