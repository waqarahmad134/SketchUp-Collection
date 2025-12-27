import { ArrowRight } from "lucide-react";
import { Button } from "@/components/ui/button";
import interiorImg from "@/assets/interior-preview.jpg";
import exteriorImg from "@/assets/exterior-preview.jpg";
import landscapeImg from "@/assets/landscape-preview.jpg";

interface BundleCardProps {
  image: string;
  title: string;
  description: string;
  price: number;
  originalPrice: number;
  delay: string;
}

const BundleCard = ({ image, title, description, price, originalPrice, delay }: BundleCardProps) => (
  <div className={`glass-card rounded-3xl overflow-hidden hover-lift group animate-slide-in-up ${delay}`}>
    <div className="relative overflow-hidden">
      <img
        src={image}
        alt={title}
        className="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-110"
      />
      <div className="absolute inset-0 bg-gradient-to-t from-background/90 via-background/20 to-transparent" />
      <div className="absolute bottom-4 left-4 right-4">
        <h3 className="text-xl font-bold font-display mb-1">{title}</h3>
        <p className="text-sm text-muted-foreground line-clamp-2">{description}</p>
      </div>
    </div>
    <div className="p-6 space-y-4">
      <div className="flex items-center justify-between">
        <div>
          <span className="text-3xl font-bold gradient-text">${price}</span>
          <span className="text-muted-foreground line-through ml-2">${originalPrice}</span>
        </div>
        <span className="bg-cyan-500/20 text-cyan-400 text-xs font-bold px-3 py-1 rounded-full">
          {Math.round((1 - price / originalPrice) * 100)}% OFF
        </span>
      </div>
      <Button variant="glow" className="w-full group/btn">
        View Bundle
        <ArrowRight className="w-4 h-4 transition-transform group-hover/btn:translate-x-1" />
      </Button>
    </div>
  </div>
);

const BundlesSection = () => {
  const bundles = [
    {
      image: interiorImg,
      title: "Interior SKP Bundle",
      description: "Clean and optimized interior models for interior designers.",
      price: 49,
      originalPrice: 99,
      delay: "animation-delay-200",
    },
    {
      image: exteriorImg,
      title: "Exterior SKP Bundle",
      description: "Exterior architectural assets designed for professional SketchUp workflows.",
      price: 49,
      originalPrice: 99,
      delay: "animation-delay-400",
    },
    {
      image: landscapeImg,
      title: "Landscape SKP Bundle",
      description: "Landscape and outdoor assets for large-scale environments.",
      price: 49,
      originalPrice: 99,
      delay: "animation-delay-600",
    },
  ];

  return (
    <section className="py-24 bg-background relative overflow-hidden">
      {/* Background decoration */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/5 to-transparent rounded-full blur-3xl" />

      <div className="container mx-auto px-4 relative z-10">
        <div className="text-center mb-16 space-y-4">
          <span className="inline-block glass-card px-4 py-2 rounded-full text-sm text-cyan-400 font-medium">
            Premium Collections
          </span>
          <h2 className="text-4xl md:text-5xl font-bold font-display">
            Explore Our <span className="gradient-text">Asset Bundles</span>
          </h2>
          <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
            Carefully curated collections of professional-grade 3D assets for every design need.
          </p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          {bundles.map((bundle, index) => (
            <BundleCard key={index} {...bundle} />
          ))}
        </div>
      </div>
    </section>
  );
};

export default BundlesSection;
