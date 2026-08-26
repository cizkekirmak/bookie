<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReviewLike;  
use App\Models\UserBook;    
use App\Models\Book;
use App\Models\Review;
use App\Notifications\ReviewLikedNotification;

class ReviewController extends Controller
{
    public function store(Request $request, $bookId) {
        $request->validate([
            "comment" => "required|string|max:500",
        ]);

        Review::updateOrCreate(
            [
                "user_id" => auth()->id(),
                "book_id" => $bookId,
            ],
            [
                "comment" => $request->comment,
            ]
        );

        return back()->with("success", "your review is saved.");
    }

    public function toggleLike($review)
    {
        $userId = auth()->id();

        $reviewId = is_object($review) ? $review->id : $review;

        $like = ReviewLike::where('user_id', $userId)
                          ->where('review_id', $reviewId)
                          ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            ReviewLike::create([
                'user_id' => $userId,
                'review_id' => $reviewId,
            ]);
            $liked = true;

            $reviewModel = UserBook::find($reviewId);
            if ($reviewModel && $reviewModel->user_id !== $userId && class_exists('App\Notifications\ReviewLikedNotification')) {
                $reviewModel->user->notify(new \App\Notifications\ReviewLikedNotification(auth()->user(), $reviewModel));
            }
        }

        $likesCount = ReviewLike::where('review_id', $reviewId)->count();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $likesCount
        ]);
    }
}

