<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCommentController extends Controller
{
    public function index(Request $request): View
    {
        $comments = Comment::with('post')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(25);

        return view('admin.comments.index', [
            'comments' => $comments,
            'pendingCount' => Comment::where('status', 'pending')->count(),
        ]);
    }

    public function approve(Comment $comment): RedirectResponse
    {
        $comment->update(['status' => 'approved']);

        return back()->with('status', 'Comment approved.');
    }

    public function spam(Comment $comment): RedirectResponse
    {
        $comment->update(['status' => 'spam']);

        return back()->with('status', 'Comment marked as spam.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('status', 'Comment deleted.');
    }
}
