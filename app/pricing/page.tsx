import type { Metadata } from "next";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { Button } from "@/components/ui/button";
import { Check, Sparkles, Zap } from "lucide-react";

export const metadata: Metadata = {
  title: "Pricing - 3DAssetHub | Affordable 3D Asset Plans",
  description: "Choose the perfect plan for your design needs. Get access to premium SketchUp models, textures, and exclusive bundles.",
};

const pricingPlans = [
  {
    name: "Starter",
    price: "$29",
    description: "Perfect for individual designers",
    features: [
      "100+ Premium SKP Models",
      "5GB Textures Library",
      "Commercial License",
      "Email Support",
      "Lifetime Access",
    ],
    popular: false,
  },
  {
    name: "Professional",
    price: "$49",
    description: "Best for professional designers",
    features: [
      "1TB+ Premium SKP Bundle",
      "500GB Furniture Models",
      "10GB SKM Textures",
      "60GB Bonus Pack",
      "Priority Support",
      "Commercial License",
      "Lifetime Access",
    ],
    popular: true,
  },
  {
    name: "Enterprise",
    price: "$99",
    description: "For teams and agencies",
    features: [
      "Everything in Professional",
      "Unlimited Downloads",
      "Team Collaboration",
      "Custom Asset Requests",
      "Dedicated Support",
      "White-label License",
      "Early Access to New Assets",
    ],
    popular: false,
  },
];

export default function PricingPage() {
  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="pt-20">
        <section className="py-24 mesh-gradient relative overflow-hidden">
          <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl" />
          
          <div className="container mx-auto px-4 relative z-10">
            <div className="text-center mb-16 space-y-4">
              <span className="inline-block glass-card px-4 py-2 rounded-full text-sm text-cyan-400 font-medium">
                Pricing Plans
              </span>
              <h1 className="text-4xl md:text-6xl font-bold font-display">
                Choose Your <span className="gradient-text">Plan</span>
              </h1>
              <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                Flexible pricing options for designers of all levels. All plans include lifetime access.
              </p>
            </div>

            <div className="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
              {pricingPlans.map((plan, index) => (
                <div
                  key={index}
                  className={`glass-card rounded-3xl p-8 hover-lift relative ${
                    plan.popular ? "border-2 border-cyan-500/50" : ""
                  }`}
                >
                  {plan.popular && (
                    <div className="absolute -top-4 left-1/2 -translate-x-1/2">
                      <span className="bg-gradient-to-r from-cyan-500 to-violet-500 text-background text-xs font-bold px-4 py-1 rounded-full">
                        Most Popular
                      </span>
                    </div>
                  )}
                  
                  <div className="space-y-6">
                    <div>
                      <h3 className="text-2xl font-bold font-display mb-2">{plan.name}</h3>
                      <p className="text-muted-foreground text-sm">{plan.description}</p>
                    </div>
                    
                    <div>
                      <span className="text-5xl font-bold gradient-text">{plan.price}</span>
                      <span className="text-muted-foreground ml-2">one-time</span>
                    </div>

                    <ul className="space-y-3">
                      {plan.features.map((feature, i) => (
                        <li key={i} className="flex items-start gap-3">
                          <Check className="w-5 h-5 text-cyan-400 flex-shrink-0 mt-0.5" />
                          <span className="text-sm">{feature}</span>
                        </li>
                      ))}
                    </ul>

                    <Button
                      variant={plan.popular ? "hero" : "heroOutline"}
                      className="w-full"
                      size="lg"
                    >
                      Get Started
                    </Button>
                  </div>
                </div>
              ))}
            </div>

            <div className="mt-16 text-center">
              <p className="text-muted-foreground mb-4">All plans include:</p>
              <div className="flex flex-wrap justify-center gap-6">
                <div className="flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                  <Zap className="w-4 h-4 text-cyan-400" />
                  <span className="text-sm">Instant Download</span>
                </div>
                <div className="flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                  <Sparkles className="w-4 h-4 text-violet-400" />
                  <span className="text-sm">Regular Updates</span>
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

