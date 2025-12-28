import type { Metadata } from "next";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { Button } from "@/components/ui/button";
import { Users, MessageSquare, Award, Share2, Twitter, Instagram, Youtube, Linkedin } from "lucide-react";

export const metadata: Metadata = {
  title: "Community - 3DAssetHub | Join Our Designer Community",
  description: "Connect with thousands of designers, share your work, and get inspired in our vibrant community.",
};

const communityStats = [
  { icon: Users, value: "50K+", label: "Active Members" },
  { icon: MessageSquare, value: "10K+", label: "Discussions" },
  { icon: Award, value: "5K+", label: "Projects Shared" },
];

const socialLinks = [
  { icon: Twitter, name: "Twitter", href: "#", color: "text-blue-400" },
  { icon: Instagram, name: "Instagram", href: "#", color: "text-pink-400" },
  { icon: Youtube, name: "YouTube", href: "#", color: "text-red-400" },
  { icon: Linkedin, name: "LinkedIn", href: "#", color: "text-blue-500" },
];

export default function CommunityPage() {
  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="pt-20">
        <section className="py-24 mesh-gradient relative overflow-hidden">
          <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl" />
          
          <div className="container mx-auto px-4 relative z-10">
            <div className="text-center mb-16 space-y-4">
              <div className="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                <Users className="w-4 h-4 text-cyan-400" />
                <span className="text-sm text-cyan-400 font-medium">Join Us</span>
              </div>
              <h1 className="text-4xl md:text-6xl font-bold font-display">
                Our <span className="gradient-text">Community</span>
              </h1>
              <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                Connect with designers worldwide, share your work, and grow together.
              </p>
            </div>

            <div className="grid md:grid-cols-3 gap-6 mb-16 max-w-4xl mx-auto">
              {communityStats.map((stat, index) => (
                <div
                  key={index}
                  className="glass-card rounded-3xl p-6 text-center"
                >
                  <div className="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                    <stat.icon className="w-8 h-8 text-cyan-400" />
                  </div>
                  <p className="text-4xl font-bold font-display gradient-text mb-2">
                    {stat.value}
                  </p>
                  <p className="text-sm text-muted-foreground">{stat.label}</p>
                </div>
              ))}
            </div>

            <div className="max-w-4xl mx-auto space-y-8">
              <div className="glass-card rounded-3xl p-8 text-center">
                <h2 className="text-3xl font-bold font-display mb-4">
                  Join the Conversation
                </h2>
                <p className="text-muted-foreground mb-6 max-w-2xl mx-auto">
                  Connect with fellow designers, share your projects, ask questions, 
                  and get feedback from the community.
                </p>
                <div className="flex flex-wrap justify-center gap-4">
                  <Button variant="hero" size="lg">
                    Join Discord
                  </Button>
                  <Button variant="heroOutline" size="lg">
                    Visit Forum
                  </Button>
                </div>
              </div>

              <div className="glass-card rounded-3xl p-8">
                <h2 className="text-2xl font-bold font-display mb-6">Follow Us</h2>
                <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                  {socialLinks.map((social, index) => (
                    <a
                      key={index}
                      href={social.href}
                      className="glass-card rounded-2xl p-6 hover-lift text-center group"
                    >
                      <social.icon className={`w-8 h-8 mx-auto mb-3 ${social.color} group-hover:scale-110 transition-transform`} />
                      <p className="font-semibold">{social.name}</p>
                    </a>
                  ))}
                </div>
              </div>

              <div className="glass-card rounded-3xl p-8">
                <div className="flex items-center gap-4 mb-6">
                  <Share2 className="w-6 h-6 text-cyan-400" />
                  <h2 className="text-2xl font-bold font-display">Share Your Work</h2>
                </div>
                <p className="text-muted-foreground mb-6">
                  Tag us on social media with #3DAssetHub to get featured! We love seeing 
                  what you create with our assets.
                </p>
                <Button variant="heroOutline" size="lg">
                  View Gallery
                </Button>
              </div>
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </div>
  );
}

