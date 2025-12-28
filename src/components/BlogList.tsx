"use client";

import { useState, useEffect } from "react";
import { Calendar, User, ArrowRight, FileText } from "lucide-react";
import { Button } from "@/components/ui/button";
import Image from "next/image";
import Link from "next/link";
import { getBlogPosts, getImageUrl, type BlogPost } from "@/lib/api";

export function BlogList() {
  const [posts, setPosts] = useState<BlogPost[]>([]);
  const [loading, setLoading] = useState(true);
  const [currentPage, setCurrentPage] = useState(1);
  const [meta, setMeta] = useState<any>(null);

  useEffect(() => {
    loadPosts(currentPage);
  }, [currentPage]);

  const loadPosts = async (page: number) => {
    setLoading(true);
    try {
      const response = await getBlogPosts({ per_page: 12, page });
      setPosts(response.data || []);
      setMeta(response.meta);
    } catch (error) {
      console.error("Error loading posts:", error);
    } finally {
      setLoading(false);
    }
  };

  if (loading && posts.length === 0) {
    return (
      <section className="py-24 bg-background relative overflow-hidden">
        <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/5 to-transparent rounded-full blur-3xl" />
        <div className="container mx-auto px-4 relative z-10">
          <div className="text-center mb-16">
            <h1 className="text-4xl md:text-5xl font-bold font-display mb-4">
              Our <span className="gradient-text">Blog</span>
            </h1>
            <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
              Tips, tutorials, and insights about 3D design
            </p>
          </div>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {[1, 2, 3, 4, 5, 6].map((i) => (
              <div key={i} className="glass-card rounded-3xl h-96 animate-pulse" />
            ))}
          </div>
        </div>
      </section>
    );
  }

  return (
    <section className="py-24 bg-background relative overflow-hidden">
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/5 to-transparent rounded-full blur-3xl" />
      
      <div className="container mx-auto px-4 relative z-10">
        <div className="text-center mb-16 space-y-4">
          <span className="inline-block glass-card px-4 py-2 rounded-full text-sm text-violet-400 font-medium">
            Latest Articles
          </span>
          <h1 className="text-4xl md:text-5xl font-bold font-display">
            Our <span className="gradient-text">Blog</span>
          </h1>
          <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
            Tips, tutorials, and insights about 3D design, SketchUp, and interior design trends.
          </p>
        </div>

        {posts.length === 0 ? (
          <div className="text-center py-16">
            <FileText className="w-16 h-16 mx-auto mb-4 text-muted-foreground" />
            <h2 className="text-2xl font-bold font-display mb-2">No Posts Yet</h2>
            <p className="text-muted-foreground">
              Check back soon for new blog posts!
            </p>
          </div>
        ) : (
          <>
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
              {posts.map((post) => (
                <Link
                  key={post.id}
                  href={`/blog/${post.slug}`}
                  className="glass-card rounded-3xl overflow-hidden hover-lift group"
                >
                  <div className="relative overflow-hidden">
                    {getImageUrl(post.featured_image) ? (
                      <Image
                        src={getImageUrl(post.featured_image)!}
                        alt={post.title}
                        width={400}
                        height={256}
                        className="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-110"
                      />
                    ) : (
                      <div className="w-full h-64 bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                        <FileText className="w-16 h-16 text-cyan-400/50" />
                      </div>
                    )}
                    <div className="absolute inset-0 bg-gradient-to-t from-background/90 via-background/20 to-transparent" />
                    <div className="absolute bottom-4 left-4 right-4">
                      <h3 className="text-xl font-bold font-display mb-2 line-clamp-2">{post.title}</h3>
                      {post.excerpt && (
                        <p className="text-sm text-muted-foreground line-clamp-2">{post.excerpt}</p>
                      )}
                    </div>
                  </div>
                  <div className="p-6 space-y-4">
                    <div className="flex items-center gap-4 text-sm text-muted-foreground">
                      {post.user && (
                        <span className="flex items-center gap-1">
                          <User className="w-4 h-4" /> {post.user.name}
                        </span>
                      )}
                      {post.published_at && (
                        <span className="flex items-center gap-1">
                          <Calendar className="w-4 h-4" /> {new Date(post.published_at).toLocaleDateString()}
                        </span>
                      )}
                    </div>
                    <Button variant="glow" className="w-full group/btn">
                      Read More
                      <ArrowRight className="w-4 h-4 transition-transform group-hover/btn:translate-x-1" />
                    </Button>
                  </div>
                </Link>
              ))}
            </div>

            {/* Pagination */}
            {meta && meta.last_page > 1 && (
              <div className="mt-12 flex justify-center gap-4">
                <Button
                  variant="outline"
                  onClick={() => setCurrentPage((p) => Math.max(1, p - 1))}
                  disabled={currentPage === 1}
                >
                  Previous
                </Button>
                <div className="flex items-center gap-2">
                  <span className="text-muted-foreground">
                    Page {meta.current_page} of {meta.last_page}
                  </span>
                </div>
                <Button
                  variant="outline"
                  onClick={() => setCurrentPage((p) => Math.min(meta.last_page, p + 1))}
                  disabled={currentPage === meta.last_page}
                >
                  Next
                </Button>
              </div>
            )}
          </>
        )}
      </div>
    </section>
  );
}

