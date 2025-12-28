import type { Metadata } from "next";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { Award, Check, X } from "lucide-react";

export const metadata: Metadata = {
  title: "License Agreement - 3DAssetHub",
  description: "Understand your licensing rights and restrictions for 3DAssetHub assets.",
};

const allowed = [
  "Use in commercial projects",
  "Use in client work",
  "Modify and customize assets",
  "Use in multiple projects",
  "Lifetime access to purchased assets",
];

const notAllowed = [
  "Resell or redistribute assets",
  "Share account access",
  "Create asset libraries for resale",
  "Use assets in products for resale (templates, themes)",
  "Remove copyright notices",
];

export default function LicensePage() {
  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="pt-20">
        <section className="py-24 mesh-gradient relative overflow-hidden">
          <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/10 to-transparent rounded-full blur-3xl" />
          
          <div className="container mx-auto px-4 relative z-10">
            <div className="text-center mb-16 space-y-4">
              <div className="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                <Award className="w-4 h-4 text-cyan-400" />
                <span className="text-sm text-cyan-400 font-medium">Commercial License</span>
              </div>
              <h1 className="text-4xl md:text-6xl font-bold font-display">
                License <span className="gradient-text">Agreement</span>
              </h1>
              <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                Understand what you can and cannot do with 3DAssetHub assets.
              </p>
            </div>

            <div className="max-w-4xl mx-auto space-y-8">
              <div className="glass-card rounded-3xl p-8">
                <h2 className="text-2xl font-bold font-display mb-6">What&apos;s Included</h2>
                <p className="text-muted-foreground mb-6">
                  When you purchase assets from 3DAssetHub, you receive a commercial license that allows you to:
                </p>
                <div className="space-y-3">
                  {allowed.map((item, index) => (
                    <div key={index} className="flex items-start gap-3">
                      <div className="w-6 h-6 rounded-full bg-cyan-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <Check className="w-4 h-4 text-cyan-400" />
                      </div>
                      <span className="text-muted-foreground">{item}</span>
                    </div>
                  ))}
                </div>
              </div>

              <div className="glass-card rounded-3xl p-8">
                <h2 className="text-2xl font-bold font-display mb-6">What&apos;s Not Allowed</h2>
                <p className="text-muted-foreground mb-6">
                  To protect our assets and community, the following are prohibited:
                </p>
                <div className="space-y-3">
                  {notAllowed.map((item, index) => (
                    <div key={index} className="flex items-start gap-3">
                      <div className="w-6 h-6 rounded-full bg-red-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <X className="w-4 h-4 text-red-400" />
                      </div>
                      <span className="text-muted-foreground">{item}</span>
                    </div>
                  ))}
                </div>
              </div>

              <div className="glass-card rounded-3xl p-8">
                <h2 className="text-2xl font-bold font-display mb-4">License Types</h2>
                <div className="space-y-4">
                  <div>
                    <h3 className="text-lg font-semibold mb-2">Standard Commercial License</h3>
                    <p className="text-muted-foreground text-sm">
                      Included with all purchases. Allows commercial use in client projects, presentations, 
                      and personal work. Perfect for designers, architects, and 3D artists.
                    </p>
                  </div>
                  <div>
                    <h3 className="text-lg font-semibold mb-2">Extended License</h3>
                    <p className="text-muted-foreground text-sm">
                      Available for Enterprise plans. Allows use in products for resale, templates, 
                      and larger commercial applications. Contact us for more information.
                    </p>
                  </div>
                </div>
              </div>

              <div className="glass-card rounded-3xl p-8">
                <h2 className="text-2xl font-bold font-display mb-4">Questions?</h2>
                <p className="text-muted-foreground">
                  If you have questions about licensing or need clarification on what&apos;s allowed, 
                  please contact us at{" "}
                  <a href="mailto:license@3dassethub.com" className="text-cyan-400 hover:underline">
                    license@3dassethub.com
                  </a>
                </p>
              </div>
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </div>
  );
}

