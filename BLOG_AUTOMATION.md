# Daily Blog Automation

A scheduled job prepares one SEO blog post per day (long-tail SketchUp keyword,
1300-1800 words, featured image) and drops it here as JSON. This Laravel app
imports and publishes it automatically.

## How it works

1. The daily job writes `database/pending-posts/YYYY-MM-DD-<slug>.json`
   plus the featured image into `database/pending-posts/images/`.
2. Every day at 02:00 server time, `php artisan blog:import-pending` (Laravel
   scheduler) imports new JSON files as **published** posts and moves them to
   `database/pending-posts/imported/`.
3. New posts appear automatically in `/blog` and in `sitemap.xml`
   (SitemapController lists all published posts), which is what gets them
   discovered and indexed.

## JSON schema

```json
{
  "title": "How to Use SketchUp Free for Beginners Step by Step",
  "slug": "how-to-use-sketchup-free-for-beginners",
  "excerpt": "Short summary, ~160 characters.",
  "content": "<p>Full article HTML...</p>",
  "meta_title": "SEO title, ~60 characters",
  "meta_description": "SEO description, ~155 characters",
  "featured_image": "2026-09-17-how-to-use-sketchup-free.jpg",
  "category": "Tutorials",
  "tags": ["sketchup free", "beginners", "tutorial"]
}
```

- `slug` is optional (auto-generated from title, made unique if taken).
- `featured_image` must match a file in `database/pending-posts/images/`.
  It is moved to the public disk; the post stores the new path.
- `category` and `tags` are created if they do not exist.

## Server setup (one time)

1. Make sure the scheduler runs every minute (cPanel > Cron Jobs, or crontab):
   ```
   * * * * * cd /home/u442793684.sketchup/public_html && php artisan schedule:run >> /dev/null 2>&1
   ```
   Adjust the path to wherever the app is deployed.
2. `php artisan storage:link` so featured images resolve publicly.
3. Pushes to `main` auto-deploy via `.github/workflows/main.yml`.

## Getting posts indexed

Publishing + sitemap inclusion gets posts discovered. To speed up indexing:

1. Google Search Console > Sitemaps > submit `https://<domain>/sitemap.xml` (once).
2. For each new post: Search Console > URL Inspection > paste the post URL >
   **Request indexing**.
3. (Optional, advanced) Google Indexing API with a service account for
   programmatic indexing requests.

## Content rules the daily job follows

- Long-tail SketchUp keywords only, one per post, tracked in the keyword bank.
- 1300-1800 words, original, AdSense-safe (no thin, scraped, or deceptive content).
- Human voice: concrete steps, specific numbers, varied rhythm. Never an em-dash.
- Featured image generated per post (16:9), topical to the article.
- Internal links to `/bundles` and related `/blog` posts where natural.
