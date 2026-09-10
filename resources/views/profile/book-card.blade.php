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

    // Tarih Biçimlendirmeleri
    $startDate = !empty($item->started_at) ? \Carbon\Carbon::parse($item->started_at)->translatedFormat('d M Y') : '-';
    $finishDate = !empty($item->finished_at) ? \Carbon\Carbon::parse($item->finished_at)->translatedFormat('d M Y') : '-';
    $uniqueId = 'note_' . ($item->id ?? rand(1000, 9999));
@endphp

<div class="book-card-item" 
     data-status="{{ $item->status }}"
     style="background: #ffffff; border: 1.5px solid #d4e5cb; border-radius: 12px; padding: 12px; display: flex; gap: 12px; align-items: flex-start; box-shadow: 0 2px 5px rgba(0,0,0,0.03); position: relative; width: 100%; box-sizing: border-box;">
     
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

    {{-- Sol/Orta Ana Bilgiler Alanı --}}
    <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 4px;">
        
        {{-- Başlık + Durum Rozeti + Mobil Ataş Butonu --}}
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

            <div style="display: flex; align-items: center; gap: 4px; flex-shrink: 0;">
                {{-- Mobilde Tarih Notunu Açıp Kapatan Ataş Butonu --}}
                <button type="button" 
                        class="mobile-paperclip-btn" 
                        onclick="toggleMobileDateNote('{{ $uniqueId }}')"
                        title="Tarihleri Göster"
                        style="background: #fff6f8; border: 1px solid #f3c2cf; border-radius: 6px; padding: 2px 5px; cursor: pointer; display: none; font-size: 11px; line-height: 1;">
                    📎
                </button>

                {{-- Durum Rozeti --}}
                <span style="font-size: 10px; padding: 2px 6px; border-radius: 5px; font-weight: bold; background: #eaf3e4; color: #2d5a27; border: 1px solid #c2d8b7; white-space: nowrap;">
                    @if($item->status === 'reading') {{ __('reading') }}
                    @elseif($item->status === 'read') {{ __('read') }}
                    @elseif($item->status === 'toRead' || $item->status === 'want_to_read') {{ __('to read') }}
                    @else {{ __($item->status) }}
                    @endif
                </span>
            </div>
        </div>

        {{-- İlerleme Çubuğu (Artık Sonsuza Uzamıyor, Maks 220px) --}}
        @if($hasProgress)
            <div style="width: 100%; max-width: 220px; margin-top: 2px; margin-bottom: 2px;">
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

        {{-- Yorum (Taşarsa İçinde Tatlıca Kayan Alan) --}}
        @if(!empty($item->review))
            <div class="custom-review-scroll" style="margin: 4px 0 0 0; font-size: 12px; color: #333; line-height: 1.35; background: #fbfdf9; padding: 6px 8px; border-radius: 6px; border-left: 3px solid #8ec46f; max-height: 60px; overflow-y: auto; word-break: break-word;">
                "{{ $item->review }}"
            </div>
        @endif

        {{-- Mobilde Açılan Ataşlı Kağıt Bölümü --}}
        <div id="{{ $uniqueId }}_mobile" class="mobile-date-slip" style="display: none; margin-top: 8px;">
            <div class="paperclip-note" style="position: relative; background: #fffcf2; border: 1px solid #ebd9b4; border-radius: 6px; padding: 6px 10px; font-size: 11px; color: #6b583e; background-image: repeating-linear-gradient(transparent, transparent 15px, #f1e4c8 16px);">
                <span style="position: absolute; top: -7px; left: 8px; font-size: 13px;">📎</span>
                <div style="display: flex; justify-content: space-between;">
                    <span><strong>başlangıç:</strong> {{ $startDate }}</span>
                    <span><strong>bitiş:</strong> {{ $finishDate }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Masaüstü İçin Sağ Taraftaki Sabit Ataşlı Not Kağıdı --}}
    <div class="desktop-date-slip" style="flex-shrink: 0; width: 155px; align-self: center; margin-right: 4px;">
        <div style="position: relative; background: #fffcf2; border: 1px solid #ebd9b4; border-radius: 6px; padding: 8px 10px 8px 12px; font-size: 11px; color: #5c4a30; box-shadow: 1px 2px 5px rgba(0,0,0,0.04); transform: rotate(1deg); background-image: repeating-linear-gradient(transparent, transparent 17px, #f3e5ca 18px); line-height: 1.6;">
            {{-- Sevimli Pembe Ataş --}}
            <span style="position: absolute; top: -8px; right: 12px; font-size: 15px; transform: rotate(-15deg); filter: drop-shadow(0 1px 1px rgba(0,0,0,0.1));">
                📎
            </span>
            <div style="font-family: inherit;">
                <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <span style="color: #9c7b52; font-weight: bold;">başlangıç:</span> {{ $startDate }}
                </div>
                <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <span style="color: #9c7b52; font-weight: bold;">bitiş:</span> {{ $finishDate }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Masaüstü ve Mobil Geçişleri */
    @media (max-width: 768px) {
        .desktop-date-slip {
            display: none !important;
        }
        .mobile-paperclip-btn {
            display: inline-flex !important;
        }
    }

    /* İnce, tatlı yeşil kaydırma çubuğu */
    .custom-review-scroll::-webkit-scrollbar {
        width: 3px;
    }
    .custom-review-scroll::-webkit-scrollbar-thumb {
        background: #c2d8b7;
        border-radius: 3px;
    }
</style>

<script>
    if (typeof toggleMobileDateNote !== 'function') {
        function toggleMobileDateNote(id) {
            const el = document.getElementById(id + '_mobile');
            if (el) {
                el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
            }
        }
    }
</script>