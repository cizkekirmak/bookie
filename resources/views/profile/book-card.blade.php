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

    // Tarih Biçimlendirmeleri (created_at ve updated_at üzerinden)
    $startDate = !empty($item->created_at) 
        ? \Carbon\Carbon::parse($item->created_at)->locale(app()->getLocale())->translatedFormat('d M Y') 
        : '-';

    $finishDate = ($item->status === 'read' && !empty($item->updated_at)) 
        ? \Carbon\Carbon::parse($item->updated_at)->locale(app()->getLocale())->translatedFormat('d M Y') 
        : '-';
        
    $uniqueId = 'note_' . ($item->id ?? rand(1000, 9999));

    // Ekose Deseniyle Uyumlu Renkli Canlı Temalar
    $theme = match($item->status) {
        'read' => [ // Çilek Pembesi
            'badge_bg' => '#ffe5eb',
            'badge_color' => '#b32d4e',
            'badge_border' => '#f8b4c2',
            'slip_bg' => '#fff0f3',
            'slip_border' => '#f8b6c4',
            'slip_line' => '#fcd9e2',
            'label_color' => '#c24364',
            'text_color' => '#4d202b',
            'card_border' => '#fcd4dd',
        ],
        'toRead', 'want_to_read' => [ // Tereyağı Sarısı
            'badge_bg' => '#fff4cc',
            'badge_color' => '#8f680a',
            'badge_border' => '#fbe38c',
            'slip_bg' => '#fffbe8',
            'slip_border' => '#fae58f',
            'slip_line' => '#faedb5',
            'label_color' => '#a17812',
            'text_color' => '#4d3d14',
            'card_border' => '#fce9a6',
        ],
        default => [ // Bebek / Gök Mavisi (reading / okuyor)
            'badge_bg' => '#e2f2fc',
            'badge_color' => '#21638a',
            'badge_border' => '#b9dcf7',
            'slip_bg' => '#f2f8fd',
            'slip_border' => '#bee0f8',
            'slip_line' => '#d7ebfa',
            'label_color' => '#327ba8',
            'text_color' => '#1d3f54',
            'card_border' => '#cfe6f8',
        ],
    };
@endphp

<div class="book-card-item" 
     data-status="{{ $item->status }}"
     style="background: #ffffff; border: 2px solid {{ $theme['card_border'] }}; border-radius: 14px; padding: 12px; display: flex; gap: 12px; align-items: flex-start; box-shadow: 0 3px 8px rgba(0,0,0,0.03); position: relative; width: 100%; box-sizing: border-box; margin-bottom: 12px;">
     
    @if($isOwnProfile ?? false)
        <div style="display: flex; align-items: center; justify-content: center; flex-shrink: 0; padding-top: 4px;">
            <input type="checkbox" 
                   name="selected_books[]" 
                   value="{{ $item->id }}" 
                   class="book-select-checkbox" 
                   onchange="updateDeleteButtonState()"
                   style="width: 17px; height: 17px; cursor: pointer; accent-color: #2d5a27;">
        </div>
    @endif

    {{-- Kapak Görseli --}}
    <a href="{{ route('show', $bookKey) }}" class="book-cover-link" style="flex-shrink: 0; width: 62px; height: 90px; display: block; overflow: hidden; border-radius: 8px; border: 1.5px solid #dcdfd5; box-shadow: 0 2px 5px rgba(0,0,0,0.06);">
        <img src="{{ $coverSrc ?: asset('images/default-book.png') }}" 
             alt="{{ $book->title ?? __('Book') }}" 
             style="width: 100%; height: 100%; object-fit: cover; display: block;"
             onerror="this.onerror=null; this.src='{{ asset('images/default-book.png') }}';">
    </a>

    {{-- Bilgiler & Orta Alan --}}
    <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 4px; overflow: hidden;">
        
        {{-- 1. Kitap Başlığı ve Yazar (Taşmayı önleyen ve alt satıra geçen yapı) --}}
        <div style="width: 100%; min-width: 0;">
            <h4 style="margin: 0 0 2px 0; font-size: 16px; font-family: 'Unkempt', cursive; font-weight: bold; color: #1a3c11; line-height: 1.25; white-space: normal; overflow-wrap: anywhere; word-break: normal;">
                <a href="{{ route('show', $bookKey) }}" style="text-decoration: none; color: inherit; display: block;">
                    {{ $book->title ?? __('Unknown Book') }}
                </a>
            </h4>
            <span style="font-size: 13.5px; font-family: 'Unkempt', cursive; color: #527943; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                {{ $book->author ?? __('Unknown Author') }}
            </span>
        </div>

        {{-- 2. Durum Rozeti ve Ataş Butonu (Başlığın altında, en sağa yaslı) --}}
        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 6px; width: 100%; margin-top: 2px;">
            <button type="button" 
                    class="mobile-paperclip-btn" 
                    onclick="toggleMobileDateNote('{{ $uniqueId }}')"
                    title="{{ __('Show Dates') }}"
                    style="background: {{ $theme['badge_bg'] }}; border: 1.5px solid {{ $theme['badge_border'] }}; border-radius: 8px; padding: 2px 6px; cursor: pointer; display: none; font-size: 13px; line-height: 1;">
                📎
            </button>

            <span style="font-size: 11.5px; font-family: 'Unkempt', cursive; padding: 2px 8px; border-radius: 10px; font-weight: bold; background: {{ $theme['badge_bg'] }}; color: {{ $theme['badge_color'] }}; border: 1.5px solid {{ $theme['badge_border'] }}; white-space: nowrap;">
                @if($item->status === 'reading') {{ __('currently reading') }}
                @elseif($item->status === 'read') {{ __('read') }}
                @elseif($item->status === 'toRead' || $item->status === 'want_to_read') {{ __('to read') }}
                @else {{ __($item->status) }}
                @endif
            </span>
        </div>

        {{-- İlerleme Çubuğu --}}
        @if($hasProgress)
            <div style="width: 100%; max-width: 240px; margin-top: 2px; margin-bottom: 2px;">
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; font-family: 'Unkempt', cursive; color: #3b612d; font-weight: bold; margin-bottom: 2px;">
                    @if($pct !== null)
                        <span>%{{ $pct }}</span>
                        <span style="font-weight: normal; color: #666; font-size: 11.5px;">{{ $currentPage }} / {{ $totalPages }} {{ __('p.') }}</span>
                    @else
                        <span>{{ __('p.') }} {{ $currentPage }}</span>
                    @endif
                </div>
                @if($pct !== null)
                    <div style="width: 100%; height: 5px; background: #eaf3e4; border: 1px solid #c2d8b7; border-radius: 5px; overflow: hidden;">
                        <div style="width: {{ $pct }}%; height: 100%; background: #2d5a27; border-radius: 5px;"></div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Yıldızlar --}}
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
                    <span style="color: {{ $i <= $item->rating ? $starColor : '#e0e0e0' }};">★</span>
                @endfor
            </div>
        @endif

        {{-- Yorum (Alıntı) --}}
        @if(!empty($item->review))
            <div class="custom-review-scroll" style="margin: 4px 0 0 0; font-family: 'Unkempt', cursive; font-size: 13.5px; color: #333; line-height: 1.35; background: #fafaf7; padding: 6px 9px; border-radius: 8px; border-left: 3.5px solid {{ $theme['badge_border'] }}; max-height: 64px; overflow-y: auto; word-break: break-word;">
                "{{ $item->review }}"
            </div>
        @endif

        {{-- Mobilde Açılan Renkli Not Kağıdı --}}
        <div id="{{ $uniqueId }}_mobile" class="mobile-date-slip" style="display: none; margin-top: 8px; width: 100%;">
            <div style="position: relative; background: {{ $theme['slip_bg'] }}; border: 1.5px dashed {{ $theme['slip_border'] }}; border-radius: 10px; padding: 8px 12px; font-size: 13px; font-family: 'Unkempt', cursive; color: {{ $theme['text_color'] }}; box-shadow: 0 2px 5px rgba(0,0,0,0.03); background-image: repeating-linear-gradient(transparent, transparent 18px, {{ $theme['slip_line'] }} 19px);">
                <span style="position: absolute; top: -9px; right: 12px; font-size: 15px; line-height: 1; transform: rotate(15deg);">📎</span>
                <div style="display: flex; flex-direction: column; gap: 3px;">
                    <div>
                        <span style="color: {{ $theme['label_color'] }}; font-weight: bold;">{{ __('started:') }}</span> 
                        <span style="font-weight: 500; margin-left: 4px;">{{ $startDate }}</span>
                    </div>
                    <div>
                        <span style="color: {{ $theme['label_color'] }}; font-weight: bold;">{{ __('finished:') }}</span> 
                        <span style="font-weight: 500; margin-left: 4px;">{{ $finishDate }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Masaüstü İçin Sağ Taraftaki Sabit Ataşlı Not Kağıdı --}}
    <div class="desktop-date-slip" style="flex-shrink: 0; width: 165px; align-self: center; margin-right: 4px;">
        <div style="position: relative; background: {{ $theme['slip_bg'] }}; border: 1.5px dashed {{ $theme['slip_border'] }}; border-radius: 10px; padding: 10px 12px; font-size: 13.5px; font-family: 'Unkempt', cursive; color: {{ $theme['text_color'] }}; box-shadow: 1px 3px 8px rgba(0,0,0,0.04); transform: rotate(1deg); line-height: 1.6; background-image: repeating-linear-gradient(transparent, transparent 18px, {{ $theme['slip_line'] }} 19px);">
            <span style="position: absolute; top: -9px; right: 10px; font-size: 17px; line-height: 1; transform: rotate(-12deg); filter: drop-shadow(0 1px 1px rgba(0,0,0,0.12));">📎</span>
            <div style="display: flex; flex-direction: column; gap: 2px;">
                <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <span style="color: {{ $theme['label_color'] }}; font-weight: bold;">{{ __('started:') }}</span> {{ $startDate }}
                </div>
                <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <span style="color: {{ $theme['label_color'] }}; font-weight: bold;">{{ __('finished:') }}</span> {{ $finishDate }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .desktop-date-slip {
            display: none !important;
        }
        .mobile-paperclip-btn {
            display: inline-flex !important;
        }
    }

    .custom-review-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .custom-review-scroll::-webkit-scrollbar-thumb {
        background: #d0ded0;
        border-radius: 4px;
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