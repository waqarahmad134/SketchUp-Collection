import type { Metadata } from "next";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { Button } from "@/components/ui/button";
import { Briefcase, Users, Zap, Heart } from "lucide-react";

export const metadata: Metadata = {
  title: "Careers - 3DAssetHub | Join Our Team",
  description: "Join the 3DAssetHub team and help shape the future of 3D asset distribution for designers.",
};

const benefits = [
  "Competitive salary & equity",
  "Remote-first culture",
  "Flexible working hours",
  "Health & dental insurance",
  "Learning & development budget",
  "Unlimited PTO",
];

const openPositions = [
  {
    title: "Senior 3D Modeler",
    department: "Content",
    type: "Full-time",
    location: "Remote",
  },
  {
    title: "Frontend Developer",
    department: "Engineering",
    type: "Full-time",
    location: "Remote",
  },
  {
    title: "Content Marketing Manager",
    department: "Marketing",
    type: "Full-time",
    location: "Remote",
  },
];

export default function CareersPage() {
  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="pt-20">
        <section className="py-24 mesh-gradient relative overflow-hidden">
          <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl" />
          
          <div className="container mx-auto px-4 relative z-10">
            <div className="text-center mb-16 space-y-4">
              <div className="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                <Briefcase className="w-4 h-4 text-cyan-400" />
                <span className="text-sm text-cyan-400 font-medium">Join Our Team</span>
              </div>
              <h1 className="text-4xl md:text-6xl font-bold font-display">
                Careers at <span className="gradient-text">3DAssetHub</span>
              </h1>
              <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                Help us build the future of 3D asset distribution for designers worldwide.
              </p>
            </div>

            <div className="grid md:grid-cols-3 gap-6 mb-16">
              <div className="glass-card rounded-3xl p-6 text-center">
                <Users className="w-12 h-12 text-cyan-400 mx-auto mb-4" />
                <h3 className="text-xl font-bold font-display mb-2">Great Team</h3>
                <p className="text-sm text-muted-foreground">
                  Work with passionate designers and developers
                </p>
              </div>
              <div className="glass-card rounded-3xl p-6 text-center">
                <Zap className="w-12 h-12 text-violet-400 mx-auto mb-4" />
                <h3 className="text-xl font-bold font-display mb-2">Fast Growth</h3>
                <p className="text-sm text-muted-foreground">
                  Be part of a rapidly growing startup
                </p>
              </div>
              <div className="glass-card rounded-3xl p-6 text-center">
                <Heart className="w-12 h-12 text-cyan-400 mx-auto mb-4" />
                <h3 className="text-xl font-bold font-display mb-2">Make Impact</h3>
                <p className="text-sm text-muted-foreground">
                  Help thousands of designers worldwide
                </p>
              </div>
            </div>

            <div className="max-w-4xl mx-auto space-y-8">
              <div>
                <h2 className="text-3xl font-bold font-display mb-6">Open Positions</h2>
                <div className="space-y-4">
                  {openPositions.map((position, index) => (
                    <div
                      key={index}
                      className="glass-card rounded-3xl p-6 hover-lift"
                    >
                      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                          <h3 className="text-xl font-bold font-display mb-2">
                            {position.title}
                          </h3>
                          <div className="flex flex-wrap gap-3 text-sm text-muted-foreground">
                            <span>{position.department}</span>
                            <span>•</span>
                            <span>{position.type}</span>
                            <span>•</span>
                            <span>{position.location}</span>
                          </div>
                        </div>
                        <Button variant="heroOutline">Apply Now</Button>
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              <div className="glass-card rounded-3xl p-8">
                <h2 className="text-2xl font-bold font-display mb-6">Benefits & Perks</h2>
                <div className="grid md:grid-cols-2 gap-4">
                  {benefits.map((benefit, index) => (
                    <div key={index} className="flex items-center gap-3">
                      <div className="w-2 h-2 rounded-full bg-cyan-400" />
                      <span className="text-muted-foreground">{benefit}</span>
                    </div>
                  ))}
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

