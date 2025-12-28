<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index()
    {
        $content = $this->generateRobotsTxt();

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }

    private function generateRobotsTxt(): string
    {
        $appUrl = config('app.url');
        
        return <<<TXT
# robots.txt for SketchUp Collection

User-agent: *
Allow: /

# Disallow admin and API endpoints
Disallow: /admin
Disallow: /api/
Disallow: /login
Disallow: /register

# Disallow search and filter pages with parameters
Disallow: /*?*sort=
Disallow: /*?*filter=
Disallow: /*?*page=

# Allow specific important paths
Allow: /bundles
Allow: /blog
Allow: /categories

# Crawl-delay (optional, adjust as needed)
Crawl-delay: 1

# Sitemap location
Sitemap: {$appUrl}/sitemap.xml
TXT;
    }
}

