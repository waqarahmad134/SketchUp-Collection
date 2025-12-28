"use client";

import { Calendar, User, ArrowLeft } from "lucide-react";
import { Button } from "@/components/ui/button";
import Image from "next/image";
import Link from "next/link";
import { getImageUrl, type BlogPost } from "@/lib/api";

interface BlogDetailProps {
  post: BlogPost;
}

export function BlogDetail({ post }: BlogDetailProps) {
  return (
    <article className="py-16 bg-background">
      <div className="container mx-auto px-4 max-w-4xl">
        {/* Back Button */}
        <Link href="/blog">
          <Button variant="ghost" className="mb-8">
            <ArrowLeft className="w-4 h-4 mr-2" />
            Back to Blog
          </Button>
        </Link>

        {/* Header */}
        <header className="mb-8">
          <h1 className="text-4xl md:text-5xl font-bold font-display mb-6 gradient-text">
            {post.title}
          </h1>
          
          <div className="flex items-center gap-6 text-muted-foreground mb-8">
            {post.user && (
              <span className="flex items-center gap-2">
                <User className="w-5 h-5" />
                {post.user.name}
              </span>
            )}
            {post.published_at && (
              <span className="flex items-center gap-2">
                <Calendar className="w-5 h-5" />
                {new Date(post.published_at).toLocaleDateString('en-US', {
                  year: 'numeric',
                  month: 'long',
                  day: 'numeric',
                })}
              </span>
            )}
          </div>
        </header>

        {/* Featured Image */}
        {getImageUrl(post.featured_image) && (
          <div className="relative w-full h-96 rounded-3xl overflow-hidden mb-12 shadow-2xl">
            <Image
              src={getImageUrl(post.featured_image)!}
              alt={post.title}
              fill
              style={{ objectFit: 'cover' }}
              priority
              className="rounded-3xl"
            />
          </div>
        )}

        {/* Content */}
        <div className="prose prose-invert prose-lg max-w-none">
          {post.content ? (
            <div 
              dangerouslySetInnerHTML={{ __html: post.content }}
              className="blog-content"
            />
          ) : post.excerpt ? (
            <p className="text-xl text-muted-foreground leading-relaxed">{post.excerpt}</p>
          ) : (
            <p className="text-xl text-muted-foreground leading-relaxed">
              {post.title}
            </p>
          )}
        </div>

        {/* Footer */}
        <div className="mt-12 pt-8 border-t border-border">
          <Link href="/blog">
            <Button variant="glow">
              <ArrowLeft className="w-4 h-4 mr-2" />
              Back to Blog
            </Button>
          </Link>
        </div>
      </div>

      <style jsx global>{`
        .blog-content {
          color: hsl(var(--foreground));
          line-height: 1.8;
        }
        .blog-content h1,
        .blog-content h2,
        .blog-content h3,
        .blog-content h4 {
          color: hsl(var(--foreground));
          font-weight: bold;
          margin-top: 2rem;
          margin-bottom: 1rem;
        }
        .blog-content h1 { font-size: 2.5rem; }
        .blog-content h2 { font-size: 2rem; }
        .blog-content h3 { font-size: 1.5rem; }
        .blog-content p {
          margin-bottom: 1.5rem;
          color: hsl(var(--muted-foreground));
        }
        .blog-content img {
          border-radius: 1rem;
          margin: 2rem 0;
        }
        .blog-content a {
          color: hsl(var(--cyan-400));
          text-decoration: underline;
        }
        .blog-content ul,
        .blog-content ol {
          margin: 1.5rem 0;
          padding-left: 2rem;
        }
        .blog-content li {
          margin-bottom: 0.5rem;
        }
        .blog-content code {
          background: hsl(var(--muted));
          padding: 0.2rem 0.4rem;
          border-radius: 0.25rem;
          font-size: 0.9em;
        }
        .blog-content pre {
          background: hsl(var(--muted));
          padding: 1rem;
          border-radius: 0.5rem;
          overflow-x: auto;
          margin: 1.5rem 0;
        }
        .blog-content blockquote {
          border-left: 4px solid hsl(var(--cyan-400));
          padding-left: 1rem;
          margin: 1.5rem 0;
          font-style: italic;
          color: hsl(var(--muted-foreground));
        }
      `}</style>
    </article>
  );
}

