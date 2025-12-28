import type { Metadata } from "next";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { Button } from "@/components/ui/button";
import { Download, FileBox, Gift } from "lucide-react";

export const metadata: Metadata = {
  title: "Free Assets - 3DAssetHub | Free SketchUp Models & Textures",
  description: "Download free premium 3D assets including SketchUp models and textures. No credit card required.",
};

const freeAssets = [
  {
    category: "Furniture",
    count: "50+",
    description: "Free furniture models for your projects",
  },
  {
    category: "Textures",
    count: "100+",
    description: "High-quality texture files",
  },
  {
    category: "Architecture",
    count: "30+",
    description: "Basic architectural elements",
  },
  {
    category: "Landscape",
    count: "25+",
    description: "Trees, plants, and outdoor elements",
  },
];

export default function FreeAssetsPage() {
  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="pt-20">
        <section className="py-24 mesh-gradient relative overflow-hidden">
          <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/10 to-transparent rounded-full blur-3xl" />
          
          <div className="container mx-auto px-4 relative z-10">
            <div className="text-center mb-16 space-y-4">
              <div className="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                <Gift className="w-4 h-4 text-cyan-400" />
                <span className="text-sm text-cyan-400 font-medium">Free Resources</span>
              </div>
              <h1 className="text-4xl md:text-6xl font-bold font-display">
                Free <span className="gradient-text">Assets</span>
              </h1>
              <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                Download premium-quality 3D assets completely free. No credit card required.
              </p>
            </div>

            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
              {freeAssets.map((asset, index) => (
                <div
                  key={index}
                  className="glass-card rounded-3xl p-6 hover-lift text-center"
                >
                  <div className="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                    <FileBox className="w-8 h-8 text-cyan-400" />
                  </div>
                  <h3 className="text-xl font-bold font-display mb-2">{asset.category}</h3>
                  <p className="text-3xl font-bold gradient-text mb-2">{asset.count}</p>
                  <p className="text-sm text-muted-foreground">{asset.description}</p>
                </div>
              ))}
            </div>

            <div className="text-center space-y-6">
              <div className="glass-card rounded-3xl p-8 max-w-2xl mx-auto">
                <h2 className="text-2xl font-bold font-display mb-4">
                  Get Started with Free Assets
                </h2>
                <p className="text-muted-foreground mb-6">
                  Sign up for free and instantly access our collection of premium free assets. 
                  No credit card required, no hidden fees.
                </p>
                <div className="flex flex-wrap justify-center gap-4">
                  <Button variant="hero" size="lg" className="group">
                    <Download className="w-5 h-5" />
                    Download Free Assets
                  </Button>
                  <Button variant="heroOutline" size="lg">
                    View All Free Assets
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </div>
  );
}

