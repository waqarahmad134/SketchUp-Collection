import { Helmet } from "react-helmet-async";
import Navbar from "@/components/Navbar";
import HeroSection from "@/components/HeroSection";
import StatsSection from "@/components/StatsSection";
import BundlesSection from "@/components/BundlesSection";
import FeaturesSection from "@/components/FeaturesSection";
import TestimonialsSection from "@/components/TestimonialsSection";
import CTASection from "@/components/CTASection";
import Footer from "@/components/Footer";

const Index = () => {
  return (
    <>
      <Helmet>
        <title>3DAssetHub - Premium 3D Assets for Designers | SketchUp Models & Textures</title>
        <meta
          name="description"
          content="Access 1TB+ of premium SketchUp models, textures, and exclusive designer bundles. Elevate your 3D projects with professional-grade assets trusted by 50,000+ designers."
        />
        <meta name="keywords" content="3D assets, SketchUp models, SKP files, interior design, architecture, 3D textures, design bundles" />
        <meta property="og:title" content="3DAssetHub - Premium 3D Assets for Designers" />
        <meta property="og:description" content="Access 1TB+ of premium SketchUp models and textures. 50% off all bundles." />
        <meta property="og:type" content="website" />
        <link rel="canonical" href="https://3dassethub.com" />
      </Helmet>

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
          <CTASection />
        </main>
        <Footer />
      </div>
    </>
  );
};

export default Index;
