<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminRecommendation;

class AdminRecommendationController extends Controller
{
    private function checkAdmin()
    {
        if (!auth()->check() || auth()->user()->email !== "bookieapp.info@gmail.com") 
        {
            abort(403, "you cant come here !!");
        }
    }

    public function index() {
        $recommendation = AdminRecommendation::latest()->first();
        return view('adminRecommendation', compact('recommendation'));
    }

    public function store(Request $request) {
        $request->validate([
            "book_key" => "required|string",
            "title" => "required|string",
            "authors" => "nullable|string",
            "cover_url" => "nullable|string",
            "admin_note"=> "nullable|string",
        ]);

        adminRecommendation::updateOrCreate(
            ["id" => 1],
            [
                "book_key" => $request->book_key,
                "title" => $request->title,
                "authors" => $request->authors,
                "cover_url" => $request->cover_url,
                "admin_note"=> $request->admin_note,
            ]
        ); 
        return back()->with("success", "recommendation is updated."); 
    }
}
