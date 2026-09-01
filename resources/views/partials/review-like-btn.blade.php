@php
    $isLiked = $review->isLikedBy(auth()->user());
    $count = $review->likes()->count();
@endphp

<div class="review-like-wrapper" style="display: inline-flex; align-items: center; gap: 6px;">
    <button type="button" 
            onclick="toggleReviewLike({{ $review->id }}, this)" 
            data-review-id="{{ $review->id }}"
            style="background: transparent; border: none; cursor: pointer; padding: 0; line-height: 1; outline: none; transition: transform 0.15s ease;"
            onmouseenter="this.style.transform='scale(1.2)';"
            onmouseleave="this.style.transform='scale(1)';"
    >
        <img class="like-heart-img"
            src="{{ $isLiked ? asset('images/dolukalp.png') : asset('images/boskalp.png') }}"
            alt="{{ __('Like') }}"
            style="width: 23px; height: 23px; object-fit: contain; vertical-align: middle;">
    </button>
    
    <span class="like-count-display" style="font-size: 13px; font-weight: bold; color: #1b3711; min-width: 12px;">
        {{ $count }}
    </span>
</div>