import type { Metadata } from "next";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import Link from "next/link";
import { PlayCircle, Clock, ArrowRight, Video } from "lucide-react";
import Image from "next/image";

export const metadata: Metadata = {
  title: "Tutorials - 3DAssetHub | Video Guides & Step-by-Step Tutorials",
  description: "Learn 3D design techniques with our comprehensive video tutorials and step-by-step guides.",
};

const tutorials = [
  {
    id: 1,
    title: "Getting Started with SketchUp Models",
    description: "Learn how to import and use our SKP files in your SketchUp projects.",
    duration: "15 min",
    level: "Beginner",
    thumbnail: "/assets/interior-preview.jpg",
  },
  {
    id: 2,
    title: "Working with Textures and Materials",
    description: "Master texture application and material customization techniques.",
    duration: "20 min",
    level: "Intermediate",
    thumbnail: "/assets/exterior-preview.jpg",
  },
  {
    id: 3,
    title: "Optimizing 3D Models for Rendering",
    description: "Learn how to optimize your models for faster rendering and better performance.",
    duration: "25 min",
    level: "Advanced",
    thumbnail: "/assets/landscape-preview.jpg",
  },
];

export default function TutorialsPage() {
  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="pt-20">
        <section className="py-24 mesh-gradient relative overflow-hidden">
          <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/10 to-transparent rounded-full blur-3xl" />
          
          <div className="container mx-auto px-4 relative z-10">
            <div className="text-center mb-16 space-y-4">
              <div className="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                <Video className="w-4 h-4 text-cyan-400" />
                <span className="text-sm text-cyan-400 font-medium">Learn & Grow</span>
              </div>
              <h1 className="text-4xl md:text-6xl font-bold font-display">
                Video <span className="gradient-text">Tutorials</span>
              </h1>
              <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                Step-by-step video guides to help you master 3D design with our assets.
              </p>
            </div>

            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
              {tutorials.map((tutorial) => (
                <Link
                  key={tutorial.id}
                  href={`/tutorials/${tutorial.id}`}
                  className="glass-card rounded-3xl overflow-hidden hover-lift group"
                >
                  <div className="relative h-48 overflow-hidden">
                    <Image
                      src={tutorial.thumbnail}
                      alt={tutorial.title}
                      fill
                      className="object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-background/90 to-transparent" />
                    <div className="absolute inset-0 flex items-center justify-center">
                      <div className="w-16 h-16 rounded-full bg-cyan-500/80 backdrop-blur-sm flex items-center justify-center group-hover:scale-110 transition-transform">
                        <PlayCircle className="w-8 h-8 text-background" />
                      </div>
                    </div>
                    <div className="absolute bottom-4 left-4 right-4 flex items-center justify-between">
                      <span className="bg-cyan-500/20 text-cyan-400 text-xs font-bold px-3 py-1 rounded-full">
                        {tutorial.level}
                      </span>
                      <div className="flex items-center gap-1 text-xs text-foreground bg-background/80 backdrop-blur-sm px-2 py-1 rounded-full">
                        <Clock className="w-3 h-3" />
                        <span>{tutorial.duration}</span>
                      </div>
                    </div>
                  </div>
                  <div className="p-6">
                    <h3 className="text-xl font-bold font-display mb-2 group-hover:text-cyan-400 transition-colors">
                      {tutorial.title}
                    </h3>
                    <p className="text-sm text-muted-foreground mb-4 line-clamp-2">
                      {tutorial.description}
                    </p>
                    <div className="flex items-center gap-2 text-cyan-400 font-medium text-sm">
                      Watch Tutorial
                      <ArrowRight className="w-4 h-4 transition-transform group-hover:translate-x-1" />
                    </div>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </div>
  );
}

