<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewLikedNotification extends Notification
{
    use Queueable;

    public $liker;
    public $review;

    public function __construct($liker, $review)
    {
        $this->liker = $liker;
        $this->review = $review;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
{
    $book = $this->review->book ?? $this->review;

    $olKey = $book->open_library_key ?? null;
    $googleId = $book->google_book_id ?? null;

    if (!empty($olKey)) {
        $cleanOl = str_replace(['/works/', '/books/'], '', $olKey);
        $bookIdentifier = str_starts_with($cleanOl, 'OL_') ? $cleanOl : 'OL_' . $cleanOl;
    } elseif (!empty($googleId)) {
        $bookIdentifier = $googleId;
    } else {
        $bookIdentifier = $book->book_key ?? $book->id ?? '#';
    }

    return [
        'type'          => 'review_liked',
        'sender_id'     => $this->liker->id,
        'sender_name'   => $this->liker->username ?? $this->liker->name ?? 'Anonim',
        'sender_avatar' => $this->liker->avatar ?? 'profile.jpg',
        'review_id'     => $this->review->id,
        'book_id'       => $bookIdentifier, 
        'message'       => 'liked your review.'
    ];
}
}