@php
    $userBook = auth()->check()
        ? \App\Models\UserBook::with('book')
            ->where('user_id', auth()->id())
            ->where('status', 'reading')
            ->latest('updated_at')
            ->first()
        : null;

    $currentBook = $userBook ? $userBook->book : null;
@endphp

<div class="continue-reading-card" 
style="
    background: #cae28c;
    border: 2px solid #737e3d; 
    border-radius: 16px;
    padding: 14px;
    display: flex;
    flex-direction: column;
    height: 220px;
    width: 320px;
    box-sizing: border-box;">
    
    <span style="
        font-family: 'Henny Penny', cursive;
        font-size: 15px;
        color: #1a3c11;
        margin-bottom: 10px;
        display: block;
        font-weight: normal;">
        continue?
    </span>

    <div style="display: flex; gap: 12px; align-items: center; flex: 1;">
        @if($currentBook)
            <a href="{{ route('show', $currentBook->google_book_id ?? $currentBook->open_library_key) }}" style="flex-shrink: 0;">
                @if(!empty($currentBook->cover_image))
                    <img src="{{ $currentBook->cover_image }}" 
                         alt="{{ $currentBook->title }}" 
                         loading="eager"
                         decoding="sync"
                         fetchpriority="high"
                         referrerpolicy="no-referrer"
                         style="width: 90px; height: 140px; object-fit: cover; border-radius: 8px; border: 1.5px solid #2d5a27; background: #eaf3e4; display: block;">
                @else
                    <div style="width: 90px; height: 140px; background: #eaf3e4; border-radius: 8px; border: 1.5px solid #2d5a27; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        📖
                    </div>
                @endif
            </a>

            {{-- Kitap Bilgileri --}}
            <div style="overflow: hidden; flex: 1;">
                <h4 style="color: #1a3c11; font-size: 15px; margin: 0 0 3px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: bold;">
                    {{ $currentBook->title }}
                </h4>
                <p style="color: #3b612d; font-size: 12px; margin: 0 0 8px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $currentBook->author }}
                </p>
                <a href="{{ route('show', $currentBook->google_book_id ?? $currentBook->open_library_key) }}" 
                   style="display: inline-block; background: #2d5a27; color: white; text-decoration: none; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: bold;">
                    Oku →
                </a>
            </div>
        @else
            <div style="text-align: center; width: 100%; color: #3b612d; font-size: 14px;">
                <p style="margin: 0 0 8px 0;">Şu anda okuduğun bir kitap yok.</p>
                <span style="font-size: 24px;">📚</span>
            </div>
        @endif
    </div>
</div>