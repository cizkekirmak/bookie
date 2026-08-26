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

<div class="adminRecommendation" 
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
        admin recommends!
    </span>

    <div style="display: flex; gap: 12px; align-items: center; flex: 1;">
        {{-- Kapak Görseli --}}
        <a href="{{ route('show', $adminBookKey) }}" style="flex-shrink: 0; width: 90px; height: 135px; display: block; overflow: hidden; border-radius: 8px; border: 1.5px solid #2d5a27;">
            @if(!empty($coverSrc))
                <img src="{{ $coverSrc }}" 
                     alt="{{ $adminRecommendation->title ?? 'Book Cover' }}"
                     style="width: 100%; height: 100%; object-fit: cover; display: block;"
                     onerror="this.onerror=null; this.src='https://covers.openlibrary.org/b/id/10849922-M.jpg';">
            @else
                <div style="width: 100%; height: 100%; background: #eaf3e4; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    📖
                </div>
            @endif
        </a>

        {{-- Kitap Bilgileri --}}
        <div style="overflow: hidden; flex: 1; display: flex; flex-direction: column; justify-content: space-between; height: 135px;">
            <div>
                <h4 style="color: #1a3c11; font-size: 15px; margin: 0 0 3px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: bold;">
                    {{ $adminRecommendation->title }}
                </h4>
                <p style="color: #3b612d; font-size: 12px; margin: 0 0 6px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $adminRecommendation->authors ?? $adminRecommendation->author }}
                </p>
            </div>

            @if(!empty($adminRecommendation->admin_note))
                <p style="font-size: 11px; color: #1a3c11; font-style: italic; margin: 0; line-height: 1.3; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                    "{{ $adminRecommendation->admin_note }}"
                </p>
            @endif

            <a href="{{ route('show', $adminBookKey) }}" class="btn-action-view" style="align-self: flex-start; background: #255719; color: #fff; text-decoration: none; padding: 6px 14px; border-radius: 12px; font-size: 13px; font-weight: bold;">
                Kitaba Git →
            </a>
        </div>
    </div>
</div>
@endif