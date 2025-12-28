"use client";

import { ArrowRight, Calendar, User, FileText } from "lucide-react";
import { Button } from "@/components/ui/button";
import Image from "next/image";
import Link from "next/link";
import { useEffect, useState } from "react";
import { getBlogPosts, getImageUrl, type BlogPost } from "@/lib/api";

interface BlogCardProps {
  post: BlogPost;
  delay?: string;
}

const BlogCard = ({ post, delay = "" }: BlogCardProps) => (
  <Link 
    href={`/blog/${post.slug}`} 
    className={`glass-card rounded-3xl overflow-hidden hover-lift group animate-slide-in-up ${delay}`}
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
);

const BlogSection = () => {
  const [posts, setPosts] = useState<BlogPost[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getBlogPosts({ limit: 3, featured: true }).then((response) => {
      setPosts(response.data || []);
      setLoading(false);
    });
  }, []);

  if (loading) {
    return (
      <section className="py-24 bg-background relative overflow-hidden">
        <div className="container mx-auto px-4 relative z-10">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold font-display">
              Latest <span className="gradient-text">Blog Posts</span>
            </h2>
          </div>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {[1, 2, 3].map((i) => (
              <div key={i} className="glass-card rounded-3xl h-96 animate-pulse" />
            ))}
          </div>
        </div>
      </section>
    );
  }

  if (posts.length === 0) {
    return null; // Don't show section if no posts
  }

  return (
    <section id="blog" className="py-24 bg-background relative overflow-hidden">
      {/* Background decoration */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/5 to-transparent rounded-full blur-3xl" />

      <div className="container mx-auto px-4 relative z-10">
        <div className="text-center mb-16 space-y-4">
          <span className="inline-block glass-card px-4 py-2 rounded-full text-sm text-violet-400 font-medium">
            Latest Articles
          </span>
          <h2 className="text-4xl md:text-5xl font-bold font-display">
            From Our <span className="gradient-text">Blog</span>
          </h2>
          <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
            Stay updated with the latest tips, tutorials, and insights about 3D design and SketchUp.
          </p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          {posts.map((post, index) => (
            <BlogCard 
              key={post.id} 
              post={post} 
              delay={`animation-delay-${(index + 1) * 200}`}
            />
          ))}
        </div>

        <div className="text-center mt-12">
          <Button variant="glow" size="lg" asChild>
            <Link href="/blog">
              View All Posts
              <ArrowRight className="w-5 h-5 ml-2" />
            </Link>
          </Button>
        </div>
      </div>
    </section>
  );
};

export default BlogSection;

