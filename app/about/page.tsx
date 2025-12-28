import type { Metadata } from "next";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { Users, Target, Award, Heart } from "lucide-react";

export const metadata: Metadata = {
  title: "About Us - 3DAssetHub | Our Story & Mission",
  description: "Learn about 3DAssetHub, our mission to empower designers, and our commitment to providing premium 3D assets.",
};

const values = [
  {
    icon: Target,
    title: "Our Mission",
    description: "To empower designers worldwide with premium 3D assets that accelerate their creative process.",
  },
  {
    icon: Users,
    title: "Our Community",
    description: "Serving 50,000+ designers globally with high-quality SketchUp models and textures.",
  },
  {
    icon: Award,
    title: "Quality First",
    description: "Every asset is carefully curated and optimized for professional use.",
  },
  {
    icon: Heart,
    title: "Designer-Focused",
    description: "Built by designers, for designers. We understand your workflow needs.",
  },
];

export default function AboutPage() {
  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="pt-20">
        <section className="py-24 mesh-gradient relative overflow-hidden">
          <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl" />
          
          <div className="container mx-auto px-4 relative z-10">
            <div className="text-center mb-16 space-y-4 max-w-3xl mx-auto">
              <h1 className="text-4xl md:text-6xl font-bold font-display">
                About <span className="gradient-text">3DAssetHub</span>
              </h1>
              <p className="text-xl text-muted-foreground">
                We're on a mission to revolutionize how designers access and use 3D assets.
              </p>
            </div>

            <div className="glass-card rounded-3xl p-8 md:p-12 mb-16 max-w-4xl mx-auto">
              <h2 className="text-3xl font-bold font-display mb-6">Our Story</h2>
              <div className="space-y-4 text-muted-foreground">
                <p>
                  3DAssetHub was born from a simple frustration: finding high-quality 3D assets 
                  for SketchUp was time-consuming and expensive. As designers ourselves, we knew 
                  there had to be a better way.
                </p>
                <p>
                  Today, we've built the largest curated collection of premium SketchUp models, 
                  textures, and bundles. Our library serves over 50,000 designers worldwide, 
                  helping them bring their creative visions to life faster.
                </p>
                <p>
                  We believe every designer deserves access to professional-grade assets without 
                  breaking the bank. That's why we offer lifetime access, commercial licenses, 
                  and continuously expand our collection.
                </p>
              </div>
            </div>

            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
              {values.map((value, index) => (
                <div
                  key={index}
                  className="glass-card rounded-3xl p-6 hover-lift text-center"
                >
                  <div className="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                    <value.icon className="w-8 h-8 text-cyan-400" />
                  </div>
                  <h3 className="text-xl font-bold font-display mb-3">{value.title}</h3>
                  <p className="text-sm text-muted-foreground">{value.description}</p>
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

