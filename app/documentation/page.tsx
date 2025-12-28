import type { Metadata } from "next";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import Link from "next/link";
import { Book, FileText, Search, ArrowRight } from "lucide-react";

export const metadata: Metadata = {
  title: "Documentation - 3DAssetHub | User Guides & API Docs",
  description: "Complete documentation for using 3DAssetHub assets, API integration, and best practices.",
};

const docsCategories = [
  {
    title: "Getting Started",
    description: "Learn the basics of using 3DAssetHub",
    articles: [
      "Introduction to 3DAssetHub",
      "Creating Your Account",
      "Downloading Your First Asset",
      "Understanding File Formats",
    ],
  },
  {
    title: "Asset Usage",
    description: "How to use assets in your projects",
    articles: [
      "Importing SKP Files to SketchUp",
      "Working with Textures",
      "Optimizing Models",
      "Best Practices",
    ],
  },
  {
    title: "Licensing",
    description: "Understanding our licensing terms",
    articles: [
      "Commercial License Explained",
      "Usage Rights",
      "Attribution Requirements",
      "License FAQ",
    ],
  },
];

export default function DocumentationPage() {
  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="pt-20">
        <section className="py-24 mesh-gradient relative overflow-hidden">
          <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl" />
          
          <div className="container mx-auto px-4 relative z-10">
            <div className="text-center mb-16 space-y-4">
              <div className="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                <Book className="w-4 h-4 text-cyan-400" />
                <span className="text-sm text-cyan-400 font-medium">User Guides</span>
              </div>
              <h1 className="text-4xl md:text-6xl font-bold font-display">
                Documentation
              </h1>
              <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                Everything you need to know about using 3DAssetHub assets effectively.
              </p>
            </div>

            <div className="max-w-4xl mx-auto mb-8">
              <div className="glass-card rounded-3xl p-6 flex items-center gap-4">
                <Search className="w-6 h-6 text-muted-foreground" />
                <input
                  type="search"
                  placeholder="Search documentation..."
                  className="flex-1 bg-transparent border-none outline-none text-foreground placeholder:text-muted-foreground"
                />
              </div>
            </div>

            <div className="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
              {docsCategories.map((category, index) => (
                <div
                  key={index}
                  className="glass-card rounded-3xl p-8 hover-lift"
                >
                  <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center mb-4">
                    <FileText className="w-6 h-6 text-cyan-400" />
                  </div>
                  <h3 className="text-xl font-bold font-display mb-2">{category.title}</h3>
                  <p className="text-sm text-muted-foreground mb-6">{category.description}</p>
                  <ul className="space-y-3">
                    {category.articles.map((article, i) => (
                      <li key={i}>
                        <Link
                          href="#"
                          className="flex items-center gap-2 text-sm text-muted-foreground hover:text-cyan-400 transition-colors group"
                        >
                          <ArrowRight className="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" />
                          <span>{article}</span>
                        </Link>
                      </li>
                    ))}
                  </ul>
                </div>
              ))}
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </div>
  );
}

