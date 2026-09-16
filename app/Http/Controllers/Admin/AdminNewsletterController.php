<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminNewsletterController extends Controller
{
    public function index(Request $request): View
    {
        $subscribers = NewsletterSubscriber::query()
            ->when($request->filled('q'), fn ($q) => $q->where('email', 'like', '%' . $request->q . '%'))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->latest()
            ->paginate(25);

        return view('admin.newsletter.index', [
            'subscribers' => $subscribers,
            'activeCount' => NewsletterSubscriber::where('is_active', true)->count(),
        ]);
    }

    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return back()->with('status', 'Subscriber removed.');
    }

    public function export(): StreamedResponse
    {
        $filename = 'newsletter-subscribers-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Email', 'Name', 'Active', 'Subscribed At']);

            NewsletterSubscriber::orderBy('id')->chunk(500, function ($chunk) use ($handle) {
                foreach ($chunk as $subscriber) {
                    fputcsv($handle, [
                        $subscriber->email,
                        $subscriber->name,
                        $subscriber->is_active ? 'yes' : 'no',
                        $subscriber->created_at,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
