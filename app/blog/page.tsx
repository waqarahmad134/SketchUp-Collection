import type { Metadata } from "next";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { BlogList } from "@/components/BlogList";

export const metadata: Metadata = {
  title: "Blog - 3DAssetHub | Tips, Tutorials & Insights",
  description: "Read our latest blog posts about 3D design, SketchUp tips, interior design trends, and more.",
};

export default function BlogPage() {
  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="pt-20">
        <BlogList />
      </main>
      <Footer />
    </div>
  );
}
