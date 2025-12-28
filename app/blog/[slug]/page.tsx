import type { Metadata } from "next";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { BlogDetail } from "@/components/BlogDetail";
import { getBlogPost } from "@/lib/api";
import { notFound } from "next/navigation";

interface BlogDetailPageProps {
  params: {
    slug: string;
  };
}

export async function generateMetadata({ params }: BlogDetailPageProps): Promise<Metadata> {
  const post = await getBlogPost(params.slug);

  if (!post) {
    return {
      title: "Post Not Found - 3DAssetHub",
      description: "The requested blog post could not be found.",
    };
  }

  return {
    title: `${post.title} - 3DAssetHub Blog`,
    description: post.excerpt || post.title,
    openGraph: {
      title: post.title,
      description: post.excerpt || post.title,
      type: "article",
      images: post.featured_image ? [post.featured_image] : [],
      publishedTime: post.published_at,
    },
  };
}

export default async function BlogDetailPage({ params }: BlogDetailPageProps) {
  const post = await getBlogPost(params.slug);

  if (!post) {
    notFound();
  }

  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="pt-20">
        <BlogDetail post={post} />
      </main>
      <Footer />
    </div>
  );
}

