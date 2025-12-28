import Navbar from "@/components/Navbar";
import HeroSection from "@/components/HeroSection";
import StatsSection from "@/components/StatsSection";
import BundlesSection from "@/components/BundlesSection";
import FeaturesSection from "@/components/FeaturesSection";
import TestimonialsSection from "@/components/TestimonialsSection";
import BlogSection from "@/components/BlogSection";
import CTASection from "@/components/CTASection";
import Footer from "@/components/Footer";

export default function Home() {
  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main>
        <HeroSection />
        <StatsSection />
        <section id="bundles">
          <BundlesSection />
        </section>
        <section id="features">
          <FeaturesSection />
        </section>
        <section id="testimonials">
          <TestimonialsSection />
        </section>
        <BlogSection />
        <CTASection />
      </main>
      <Footer />
    </div>
  );
}

