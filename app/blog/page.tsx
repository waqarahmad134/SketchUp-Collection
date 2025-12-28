import type { Metadata } from "next";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import Link from "next/link";
import { Calendar, ArrowRight, BookOpen } from "lucide-react";
import Image from "next/image";

export const metadata: Metadata = {
  title: "Blog - 3DAssetHub | Design Tips, Tutorials & News",
  description: "Read our latest blog posts about 3D design, SketchUp tips, tutorials, and industry news.",
};

const blogPosts = [
  {
    id: 1,
    title: "10 Essential SketchUp Tips for Interior Designers",
    excerpt: "Master these professional techniques to speed up your workflow and create stunning visualizations.",
    date: "December 15, 2024",
    category: "Tutorials",
    image: "/assets/interior-preview.jpg",
  },
  {
    id: 2,
    title: "How to Optimize Your 3D Models for Better Performance",
    excerpt: "Learn the best practices for keeping your SketchUp models lightweight and fast.",
    date: "December 10, 2024",
    category: "Tips",
    image: "/assets/exterior-preview.jpg",
  },
  {
    id: 3,
    title: "The Future of 3D Design: Trends to Watch in 2025",
    excerpt: "Explore the emerging trends that will shape the 3D design industry in the coming year.",
    date: "December 5, 2024",
    category: "News",
    image: "/assets/landscape-preview.jpg",
  },
];

export default function BlogPage() {
  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="pt-20">
        <section className="py-24 mesh-gradient relative overflow-hidden">
          <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/10 to-transparent rounded-full blur-3xl" />
          
          <div className="container mx-auto px-4 relative z-10">
            <div className="text-center mb-16 space-y-4">
              <div className="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                <BookOpen className="w-4 h-4 text-cyan-400" />
                <span className="text-sm text-cyan-400 font-medium">Design Blog</span>
              </div>
              <h1 className="text-4xl md:text-6xl font-bold font-display">
                Design <span className="gradient-text">Blog</span>
              </h1>
              <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                Tips, tutorials, and insights for 3D designers and architects.
              </p>
            </div>

            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
              {blogPosts.map((post) => (
                <Link
                  key={post.id}
                  href={`/blog/${post.id}`}
                  className="glass-card rounded-3xl overflow-hidden hover-lift group"
                >
                  <div className="relative h-48 overflow-hidden">
                    <Image
                      src={post.image}
                      alt={post.title}
                      fill
                      className="object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div className="absolute top-4 left-4">
                      <span className="bg-cyan-500/20 text-cyan-400 text-xs font-bold px-3 py-1 rounded-full">
                        {post.category}
                      </span>
                    </div>
                  </div>
                  <div className="p-6 space-y-4">
                    <div className="flex items-center gap-2 text-sm text-muted-foreground">
                      <Calendar className="w-4 h-4" />
                      <span>{post.date}</span>
                    </div>
                    <h3 className="text-xl font-bold font-display group-hover:text-cyan-400 transition-colors">
                      {post.title}
                    </h3>
                    <p className="text-muted-foreground text-sm line-clamp-2">
                      {post.excerpt}
                    </p>
                    <div className="flex items-center gap-2 text-cyan-400 font-medium text-sm">
                      Read More
                      <ArrowRight className="w-4 h-4 transition-transform group-hover:translate-x-1" />
                    </div>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </div>
  );
}

