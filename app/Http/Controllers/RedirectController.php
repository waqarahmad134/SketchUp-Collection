<?php

namespace App\Http\Controllers;

use App\Models\Redirect;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectController extends Controller
{
    /**
     * Fallback route: check the redirects table before giving up with a 404.
     */
    public function handle(Request $request): Response
    {
        $path = Redirect::normalizePath($request->path() === '/' ? '/' : '/' . $request->path());

        $redirect = Redirect::where('from_path', $path)
            ->where('is_active', true)
            ->first();

        if ($redirect) {
            $redirect->increment('hits');

            return redirect($redirect->to_path, $redirect->status_code);
        }

        abort(404);
    }
}
