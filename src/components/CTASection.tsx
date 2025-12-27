import { ArrowRight, Sparkles } from "lucide-react";
import { Button } from "@/components/ui/button";

const CTASection = () => {
  return (
    <section className="py-24 mesh-gradient relative overflow-hidden">
      {/* Animated background elements */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute top-1/2 left-1/4 w-[500px] h-[500px] bg-cyan-500/10 rounded-full blur-3xl animate-float" />
        <div className="absolute top-1/2 right-1/4 w-[400px] h-[400px] bg-violet-500/10 rounded-full blur-3xl animate-float-delayed" />
      </div>

      <div className="container mx-auto px-4 relative z-10">
        <div className="max-w-4xl mx-auto text-center space-y-8">
          <div className="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
            <Sparkles className="w-4 h-4 text-cyan-400" />
            <span className="text-sm text-muted-foreground">Limited Time Offer</span>
          </div>

          <h2 className="text-4xl md:text-6xl font-bold font-display leading-tight">
            Start Creating <span className="gradient-text">Amazing</span>
            <br />
            <span className="gradient-text">Designs</span> Today
          </h2>

          <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
            Join 50,000+ designers who trust our premium 3D asset library. 
            Get 50% off on all bundles for a limited time.
          </p>

          <div className="flex flex-wrap justify-center gap-4 pt-4">
            <Button variant="hero" size="xl" className="group">
              Get Started Now
              <ArrowRight className="w-5 h-5 transition-transform group-hover:translate-x-1" />
            </Button>
            <Button variant="heroOutline" size="xl">
              Browse All Assets
            </Button>
          </div>

          <div className="flex flex-wrap justify-center gap-8 pt-8 text-sm text-muted-foreground">
            <span className="flex items-center gap-2">
              <span className="w-2 h-2 rounded-full bg-cyan-500" />
              Instant Download
            </span>
            <span className="flex items-center gap-2">
              <span className="w-2 h-2 rounded-full bg-violet-500" />
              Commercial License
            </span>
            <span className="flex items-center gap-2">
              <span className="w-2 h-2 rounded-full bg-cyan-400" />
              Lifetime Access
            </span>
          </div>
        </div>
      </div>
    </section>
  );
};

export default CTASection;
