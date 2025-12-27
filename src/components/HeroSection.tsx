import { Box, Download, Sparkles, Star, Users, Zap } from "lucide-react";
import { Button } from "@/components/ui/button";
import heroImage from "@/assets/hero-3d-workspace.jpg";

const HeroSection = () => {
  return (
    <section className="relative min-h-screen mesh-gradient overflow-hidden">
      {/* Animated background orbs */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute top-20 left-10 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl animate-float" />
        <div className="absolute bottom-20 right-10 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl animate-float-delayed" />
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-500/5 rounded-full blur-3xl animate-pulse-glow" />
      </div>

      <div className="container relative z-10 mx-auto px-4 pt-32 pb-20">
        <div className="grid lg:grid-cols-2 gap-12 items-center">
          {/* Left content */}
          <div className="space-y-8 animate-slide-in-up">
            <div className="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
              <Sparkles className="w-4 h-4 text-cyan-400" />
              <span className="text-sm text-muted-foreground">Premium 2025 Collection</span>
              <span className="bg-gradient-to-r from-cyan-500 to-violet-500 text-background text-xs font-bold px-2 py-0.5 rounded-full">
                50% OFF
              </span>
            </div>

            <h1 className="text-5xl md:text-6xl lg:text-7xl font-bold font-display leading-tight">
              <span className="text-foreground">Premium</span>{" "}
              <span className="gradient-text">3D Assets</span>
              <br />
              <span className="text-foreground">for Designers</span>
            </h1>

            <p className="text-xl text-muted-foreground max-w-lg">
              Access 1TB+ of professional SketchUp models, textures, and exclusive designer bundles. 
              Elevate your projects instantly.
            </p>

            {/* Feature list */}
            <div className="glass-card p-6 rounded-2xl space-y-4 max-w-md">
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                  <Box className="w-5 h-5 text-cyan-400" />
                  <span className="font-medium">1TB+ Premium SKP Bundle</span>
                </div>
                <div className="text-right">
                  <span className="text-2xl font-bold gradient-text">$49</span>
                  <span className="text-muted-foreground line-through ml-2">$98</span>
                </div>
              </div>
              <div className="flex items-center gap-3 text-muted-foreground">
                <Download className="w-4 h-4" />
                <span>500GB Furniture SKP Models</span>
              </div>
              <div className="flex items-center gap-3 text-muted-foreground">
                <Zap className="w-4 h-4" />
                <span>10GB SKM Textures</span>
              </div>
              <div className="flex items-center gap-3 text-muted-foreground">
                <Sparkles className="w-4 h-4" />
                <span>60GB Designer-Exclusive Bonus Pack</span>
              </div>
            </div>

            <div className="flex flex-wrap gap-4">
              <Button variant="hero" size="xl">
                Get Started Now
              </Button>
              <Button variant="heroOutline" size="xl">
                Preview Assets
              </Button>
            </div>

            {/* Social proof */}
            <div className="flex items-center gap-4 pt-4">
              <div className="flex -space-x-3">
                {[1, 2, 3, 4].map((i) => (
                  <div
                    key={i}
                    className="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-500 to-violet-500 border-2 border-background flex items-center justify-center text-xs font-bold"
                  >
                    {String.fromCharCode(64 + i)}
                  </div>
                ))}
              </div>
              <div>
                <div className="flex items-center gap-1">
                  {[1, 2, 3, 4, 5].map((i) => (
                    <Star key={i} className="w-4 h-4 fill-cyan-400 text-cyan-400" />
                  ))}
                  <span className="font-semibold ml-1">5.0</span>
                </div>
                <p className="text-sm text-muted-foreground">10,000+ happy designers</p>
              </div>
            </div>
          </div>

          {/* Right content - Hero image */}
          <div className="relative animate-slide-in-up animation-delay-400">
            <div className="relative">
              <div className="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-violet-500/20 rounded-3xl blur-2xl" />
              <img
                src={heroImage}
                alt="3D Asset Workspace"
                className="relative rounded-3xl shadow-2xl border border-border hover-lift"
              />
              
              {/* Floating badge */}
              <div className="absolute -bottom-6 -left-6 glass-card p-4 rounded-2xl animate-float">
                <div className="flex items-center gap-3">
                  <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center">
                    <Users className="w-6 h-6 text-background" />
                  </div>
                  <div>
                    <p className="font-bold text-lg">50K+</p>
                    <p className="text-sm text-muted-foreground">Active Users</p>
                  </div>
                </div>
              </div>

              {/* Floating badge right */}
              <div className="absolute -top-4 -right-4 glass-card p-4 rounded-2xl animate-float-delayed">
                <div className="flex items-center gap-3">
                  <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-cyan-500 flex items-center justify-center">
                    <Download className="w-6 h-6 text-background" />
                  </div>
                  <div>
                    <p className="font-bold text-lg">1TB+</p>
                    <p className="text-sm text-muted-foreground">Assets</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default HeroSection;
